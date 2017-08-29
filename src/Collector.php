<?php

declare(strict_types=1);

namespace Nette\TypeFixer;

use Go\ParserReflection;
use PhpParser;


final class Collector
{
	/** @var ParserReflection\ReflectionClass[] */
	private $classes;

	/** @var Reporter */
	private $reporter;


	public function __construct(Reporter $reporter)
	{
		$this->reporter = $reporter;
	}


	/**
	 * @return ParserReflection\ReflectionClass[]
	 */
	public function collect(iterable $iterator): array
	{
		$this->parseFiles($iterator);
		$this->completeClasses();
		return array_values($this->classes);
	}


	private function parseFiles(iterable $iterator): void
	{
		$locator = new ParserReflection\Locator\CallableLocator([$this, 'locateClass']);
		$reflectionContext = new ReflectionContext($locator);

		foreach ($iterator as $fileName) {
			$fileName = (string) $fileName;
			$this->reporter->progress($fileName);
			try {
				$file = new ParserReflection\ReflectionFile($fileName, null, $reflectionContext);
			} catch (PhpParser\Error $e) {
				$this->reporter->add("{$e->getMessage()} in file $fileName", Reporter::TYPE_ERROR);
				continue;
			}
			$this->scanForClasses($file);
		}
	}


	private function scanForClasses(ParserReflection\ReflectionFile $file): void
	{
		foreach ($file->getFileNamespaces() as $namespace) {
			foreach ($namespace->getClasses() as $class) {
				$name = $class->getName();
				if (isset($this->classes[$name])) {
					$this->reporter->add("duplicate class $name found in {$class->getFileName()} and {$this->classes[$name]->getFileName()}", Reporter::TYPE_WARNING);
				}
				$this->classes[$name] = $class;
			}
		}
	}


	private function completeClasses(): void
	{
		foreach ($this->classes as $name => $class) {
			try {
				$class->getTraits();
				$class->getMethods();
			} catch (\InvalidArgumentException $e) {
				unset($this->classes[$name]);
				$this->reporter->add("unable to complete class {$class->getName()}: {$e->getMessage()}", Reporter::TYPE_ERROR);
			}
		}
	}


	public function locateClass(string $name): ?string
	{
		return isset($this->classes[$name])
			? $this->classes[$name]->getFileName()
			: null;
	}
}
