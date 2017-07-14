<?php

declare(strict_types=1);

namespace Nette\TypeFixer;

use Go\ParserReflection;
use PhpParser;


final class Analyzer
{
	/** @var array */
	private $filePatches = [];

	/** @var array */
	private $analyzed = [];

	/** @var bool */
	private $dryRun;

	/** @var Reporter */
	private $reporter;


	public function __construct(Reporter $reporter)
	{
		$this->reporter = $reporter;
	}


	/**
	 * @param ParserReflection\ReflectionClass[]
	 */
	public function analyze(array $classes, bool $dryRun = true): void
	{
		$this->dryRun = $dryRun;
		foreach ($classes as $class) {
			foreach ($class->getMethods() as $method) {
				if ($method->getDeclaringClass()->getName() === $class->getName()) {
					$this->analyzeMethod($method);
				}
			}
		}

		foreach (array_filter($this->filePatches) as $file => $patches) {
			$this->applyPatches($file, $patches);
		}
	}


	private function analyzeMethod(ParserReflection\ReflectionMethod $method): void
	{
		try {
			$proto = $method->getPrototype();
		} catch (\ReflectionException $e) {
		}
		if (isset($proto)) {
			$this->compare($method, $proto);
		}

		$interfaces = $method->getDeclaringClass()->getInterfaces();
		foreach ($interfaces as $interface) {
			if ($interface->hasMethod($method->getName())) {
				$this->compare($method, $interface->getMethod($method->getName()));
			}
		}
	}


	private function compare(ParserReflection\ReflectionMethod $method, \ReflectionMethod $protoMethod): void
	{
		$protoClass = $protoMethod->getDeclaringClass()->getName();
		$protoName = $protoClass . '::' . $method->getName() . '()';
		$methodName = $method->getDeclaringClass()->getName() . '::' . $method->getName() . '()';
		$patches = &$this->filePatches[$method->getFileName()];
		$oldPatches = $patches;

		$analyzed = &$this->analyzed[$method->getDeclaringClass()->getName()][$protoClass][$method->getName()];
		if ($analyzed) {
			return;
		}
		$analyzed = true;

		if ($protoMethod->isFinal()) {
			$this->reporter->add("$methodName overriding final method $protoName", Reporter::TYPE_ERROR);
		}

		if ($protoMethod->returnsReference() && !$method->returnsReference()) {
			$this->reporter->add("$methodName must return by reference as $protoName", Reporter::TYPE_ERROR);
		}

		if (!$this->dryRun && $protoMethod->getReturnType() && !$method->getReturnType()) {
			$patches[$this->findReturnTypeHintTokenPos($method)] = ': '
				. ($protoMethod->getReturnType()->allowsNull() ? '?' : '')
				. ($protoMethod->getReturnType()->isBuiltin() ? '' : '\\')
				. $this->getType($protoMethod);

		} elseif ($protoMethod->getReturnType()
			&& (!$method->getReturnType()
				|| (!$protoMethod->getReturnType()->allowsNull() && $method->getReturnType()->allowsNull())
				|| ($this->getType($method) !== $this->getType($protoMethod))
			)
		) {
			$this->reporter->add("$methodName return type is not compatible with $protoName", Reporter::TYPE_ERROR);
		}

		if (!$method->isConstructor()) {
			if ($protoMethod->getNumberOfRequiredParameters() < $method->getNumberOfRequiredParameters()) {
				$this->reporter->add("$methodName greater number or required parameters than $protoName", Reporter::TYPE_ERROR);
			}

			$params = $method->getParameters();
			foreach ($protoMethod->getParameters() as $pos => $protoParam) {
				if (!isset($params[$pos])) {
					$this->reporter->add("$methodName missing parameter \${$protoParam->getName()}", Reporter::TYPE_ERROR);
					continue;
				}
				$param = $params[$pos];

				if ($protoParam->isPassedByReference() !== $param->isPassedByReference()) {
					$this->reporter->add("$methodName passing by reference of parameter \${$param->getName()} is not compatible with $protoName", Reporter::TYPE_ERROR);
				}

				if ($protoParam->isVariadic() !== $param->isVariadic()) {
					$this->reporter->add("$methodName variadic parameter \${$param->getName()} is not compatible with $protoName", Reporter::TYPE_ERROR);
				}

				if (!$this->dryRun && $protoParam->hasType() && !$param->hasType()) {
					$tokenPos = $method->getClassMethodNode()->getParams()[$pos]->getAttribute('startTokenPos');
					$patches[$tokenPos] =
						($protoParam->allowsNull() && (!$protoParam->isDefaultValueAvailable() || $protoParam->getDefaultValue() !== null) ? '?' : '')
						. ($protoParam->getType()->isBuiltin() ? '' : '\\')
						. $this->getType($protoParam) . ' ';

				} elseif (($this->getType($protoParam) !== $this->getType($param))
					|| ($protoParam->hasType() && $protoParam->getType()->allowsNull()
						&& !($param->hasType() && $param->getType()->allowsNull()))
				) {
					$this->reporter->add("$methodName type of parameter \${$param->getName()} is not compatible with $protoName", Reporter::TYPE_ERROR);
				}
			}
		}

		if ($oldPatches !== $patches) {
			$this->reporter->add("$methodName added missing type hints", Reporter::TYPE_FIXED);
		}
	}


	private function getType($subject): string
	{
		$type = $subject instanceof \ReflectionMethod
			? (string) $subject->getReturnType()
			: (string) $subject->getType();

		if (strcasecmp($type, 'self') === 0) {
			return $subject->getDeclaringClass()->getName();
		} elseif (strcasecmp($type, 'parent') === 0) {
			return $subject->getDeclaringClass()->getParentClass()->getName();
		} else {
			return $type;
		}
	}


	private function findReturnTypeHintTokenPos(ParserReflection\ReflectionMethod $method): int
	{
		$lexer = new PhpParser\Lexer;
		$lexer->startLexing(file_get_contents($method->getFileName()));
		$tokens = $lexer->getTokens();
		$node = $method->getClassMethodNode();
		$pos = $node->getParams()
			? array_slice($node->getParams(), -1)[0]->getAttribute('endTokenPos')
			: $node->getAttribute('startTokenPos');
		while ($tokens[++$pos] !== ')') {
		}
		return $pos + 1;
	}


	private function applyPatches(string $file, array $patches): void
	{
		$lexer = new PhpParser\Lexer;
		$lexer->startLexing(file_get_contents($file));
		$s = '';
		foreach ($lexer->getTokens() as $pos => $token) {
			if (isset($patches[$pos])) {
				$s .= $patches[$pos];
			}
			$s .= is_array($token) ? $token[1] : $token;
		}
		file_put_contents($file, $s);
	}
}
