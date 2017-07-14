<?php

declare(strict_types=1);

// The Nette Tester command-line runner can be
// invoked through the command: ../vendor/bin/tester .

if (@!include __DIR__ . '/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer install`';
	exit(1);
}


Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');


function test(\Closure $function)
{
	$function();
}


class LogReporter implements Nette\TypeFixer\Reporter
{
	public $log = [];


	public function add(string $message, string $type): void
	{
		$this->log[] = [$message, $type];
	}


	public function progress(string $message): void
	{
	}
}
