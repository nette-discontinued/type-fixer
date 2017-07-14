<?php

declare(strict_types=1);

namespace Nette\TypeFixer;

use Nette;
use Nette\CommandLine\Parser as CommandLine;


if (@!include __DIR__ . '/../vendor/autoload.php') {
	echo 'Install packages using `composer update`';
	exit(1);
}

set_exception_handler(function ($e) {
	echo "Error: {$e->getMessage()}\n";
	echo $e;
	die(2);
});

set_error_handler(function ($severity, $message, $file, $line) {
	if (($severity & error_reporting()) === $severity) {
		throw new \ErrorException($message, 0, $severity, $file, $line);
	}
	return false;
});


echo '
Nette Type Fixer v0.1
---------------------
';

$cmd = new CommandLine(<<<'XX'
Usage:
    typefixer [options] <directory>

Options:
    -i | --ignore <mask>  Directories to ignore
    -f | --fix            Fixes files


XX
, [
	'path' => [CommandLine::REALPATH => true],
	'--ignore' => [CommandLine::REPEATABLE => true],
]);

if ($cmd->isEmpty()) {
	$cmd->help();
	exit;
}

$options = $cmd->parse();

$finder = Nette\Utils\Finder::findFiles('*.php', '*.phpt')
	->from($options['path'])
	->exclude('.git', ...$options['--ignore']);

$reporter = new class implements Reporter {

	public function add(string $message, string $type): void
	{
		$console = new Nette\CommandLine\Console;
		echo $console->color($type === self::TYPE_ERROR ? 'white/red' : 'white/blue', $type . ':'), " $message\n";
	}


	public function progress(string $message): void
	{
		echo $message . str_repeat(' ', 30) . "\r";
	}
};

$collector = new Collector($reporter);
$classes = $collector->collect($finder);

$analyzer = new Analyzer($reporter);
$analyzer->analyze($classes, !$options['--fix']);
