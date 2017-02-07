<?php

declare(strict_types=1);

namespace Nette\TypeFixer;

use Go\ParserReflection;
use PhpParser;


final class ReflectionContext extends ParserReflection\ReflectionContext
{
	public function __construct(ParserReflection\LocatorInterface $locator)
	{
		parent::__construct($locator);
		$lexer = new PhpParser\Lexer(['usedAttributes' => [
			'comments', 'startLine', 'endLine', 'startFilePos', 'endFilePos', 'startTokenPos', 'endTokenPos',
		]]);
		$this->parser = new PhpParser\Parser\Php7($lexer);
	}


	public function getClassReflection($name)
	{
		if ((class_exists($name, false)
				|| interface_exists($name, false)
				|| trait_exists($name, false)
			) && !(new \ReflectionClass($name))->isUserDefined()
		) {
			return new \ReflectionClass($name);
		}
		return new ParserReflection\ReflectionClass($name, null, $this);
	}


	public function locateClassFile($name)
	{
		$file = $this->locator->locateClass($name);
		if (!$file) {
			throw new \InvalidArgumentException("Class $name was not found by locator");
		}
		return $file;
	}
}
