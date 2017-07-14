<?php

declare(strict_types=1);

namespace Nette\TypeFixer;

use Go\ParserReflection;
use Nette\CommandLine\Console;
use PhpParser;


final class Collector
{
	/** @var ParserReflection\ReflectionClass[] */
	private $classes;

	/** @var Console */
	private $console;


	public function __construct()
	{
		$this->console = new Console;
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
			echo $fileName . str_repeat(' ', 30) . "\r";
			try {
				$file = new ParserReflection\ReflectionFile($fileName, null, $reflectionContext);
			} catch (PhpParser\Error $e) {
				echo $this->console->color('white/red', 'ERROR:') . " {$e->getMessage()} in file $fileName\n";
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
					echo $this->console->color('white/red', 'WARNING:') . " duplicate class $name found in {$class->getFileName()} and {$this->classes[$name]->getFileName()}\n";
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
				echo $this->console->color('white/red', 'ERROR:') . " unable to complete class {$class->getName()}: {$e->getMessage()}\n";
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
