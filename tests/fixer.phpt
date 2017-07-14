<?php
declare(strict_types=1);

use Tester\Assert;


require __DIR__ . '/bootstrap.php';


$dataset = [
	'ParamsTypeBad2.php' => [['ParamsTypeBad2\\B::test() added missing type hints', 'FIXED']],
	'ReturnBad1.php' => [['ReturnBad1\\B::test() added missing type hints', 'FIXED']],
	'ReturnBad2.php' => [['ReturnBad2\\B::test() added missing type hints', 'FIXED']],
];


foreach ($dataset as $file => $expected) {
	echo $file, "\n";

	$reporter = new LogReporter;
	$collector = new Nette\TypeFixer\Collector($reporter);
	$fixtureFile = __DIR__ . '/fixtures/bad/' . $file;
	$classes = $collector->collect([$fixtureFile]);

	$orig = file_get_contents($fixtureFile);
	$analyzer = new Nette\TypeFixer\Analyzer($reporter);
	$analyzer->analyze($classes, false);
	$fixed = file_get_contents($fixtureFile);
	file_put_contents($fixtureFile, $orig);

	Assert::same($expected, $reporter->log, $file);
	Assert::same(file_get_contents(__DIR__ . '/fixtures/fixed/' . $file), $fixed, $file);
}
