<?php

declare(strict_types=1);

use Tester\Assert;


require __DIR__ . '/bootstrap.php';


$dataset = [
	// BAD
	'bad/FinalBad.php' => [['FinalBad\\B::test() overriding final method FinalBad\\A::test()', 'ERROR']],
	'bad/ParamsCountBad1.php' => [['ParamsCountBad1\\B::test() greater number or required parameters than ParamsCountBad1\\A::test()', 'ERROR']],
	'bad/ParamsCountBad2.php' => [['ParamsCountBad2\\B::test() greater number or required parameters than ParamsCountBad2\\A::test()', 'ERROR']],
	'bad/ParamsCountBad3.php' => [['ParamsCountBad3\\B::test() greater number or required parameters than ParamsCountBad3\\A::test()', 'ERROR']],
	'bad/ParamsCountBad4.php' => [['ParamsCountBad4\\B::test() missing parameter $a1', 'ERROR']],
	'bad/ParamsCountBad5.php' => [['ParamsCountBad5\\B::test() missing parameter $b', 'ERROR']],
	'bad/ParamsReferenceBad1.php' => [['ParamsReferenceBad1\\B::test() passing by reference of parameter $a is not compatible with ParamsReferenceBad1\\A::test()', 'ERROR']],
	'bad/ParamsReferenceBad2.php' => [['ParamsReferenceBad2\\B::test() passing by reference of parameter $a is not compatible with ParamsReferenceBad2\\A::test()', 'ERROR']],
	'bad/ParamsTypeBad1.php' => [['ParamsTypeBad1\\B::test() type of parameter $a is not compatible with ParamsTypeBad1\\A::test()', 'ERROR']],
	'bad/ParamsTypeBad2.php' => [['ParamsTypeBad2\\B::test() type of parameter $a is not compatible with ParamsTypeBad2\\A::test()', 'ERROR']],
	'bad/ParamsTypeBad3.php' => [['ParamsTypeBad3\\B::test() type of parameter $a is not compatible with ParamsTypeBad3\\A::test()', 'ERROR']],
	'bad/ParamsTypeBad4.php' => [['ParamsTypeBad4\\B::test() type of parameter $a is not compatible with ParamsTypeBad4\\A::test()', 'ERROR']],
	'bad/ParamsTypeBad5.php' => [['ParamsTypeBad5\\B::test() type of parameter $a is not compatible with ParamsTypeBad5\\A::test()', 'ERROR']],
	'bad/ParamsTypeBad6.php' => [['ParamsTypeBad6\\B::test() type of parameter $a is not compatible with ParamsTypeBad6\\A::test()', 'ERROR']],
	'bad/ParamsTypeBad7.php' => [['ParamsTypeBad7\\B::test() type of parameter $a is not compatible with ParamsTypeBad7\\A::test()', 'ERROR']],
	'bad/ParamsVariadicBad1.php' => [
		['ParamsVariadicBad1\\B::test() greater number or required parameters than ParamsVariadicBad1\\A::test()', 'ERROR'],
		['ParamsVariadicBad1\\B::test() variadic parameter $a is not compatible with ParamsVariadicBad1\\A::test()', 'ERROR'],
	],
	'bad/ParamsVariadicBad2.php' => [['ParamsVariadicBad2\\B::test() variadic parameter $a is not compatible with ParamsVariadicBad2\\A::test()', 'ERROR']],
	'bad/ReferenceBad1.php' => [['ReferenceBad1\\B::test() must return by reference as ReferenceBad1\\A::test()', 'ERROR']],
	'bad/ReturnBad1.php' => [['ReturnBad1\\B::test() return type is not compatible with ReturnBad1\\A::test()', 'ERROR']],
	'bad/ReturnBad2.php' => [['ReturnBad2\\B::test() return type is not compatible with ReturnBad2\\A::test()', 'ERROR']],
	'bad/ReturnBad3.php' => [['ReturnBad3\\B::test() return type is not compatible with ReturnBad3\\A::test()', 'ERROR']],
	'bad/ReturnBad4.php' => [['ReturnBad4\\B::test() return type is not compatible with ReturnBad4\\A::test()', 'ERROR']],
	'bad/ReturnBad5.php' => [['ReturnBad5\\B::test() return type is not compatible with ReturnBad5\\A::test()', 'ERROR']],

	// OK
	'ok/FinalOk.php' => [],
	'ok/ParamsCountOk1.php' => [],
	'ok/ParamsCountOk2.php' => [],
	'ok/ParamsCountOk3.php' => [],
	'ok/ParamsCountOk4.php' => [],
	'ok/ParamsCountOk5.php' => [],
	'ok/ParamsReferenceOk1.php' => [],
	'ok/ParamsReferenceOk2.php' => [],
	'ok/ParamsReferenceOk3.php' => [],
	'ok/ParamsTypeOk1.php' => [],
	'ok/ParamsTypeOk2.php' => [],
	'ok/ParamsTypeOk3.php' => [],
	'ok/ParamsTypeOk5.php' => [],
	'ok/ParamsVariadicOk1.php' => [],
	'ok/ParamsVariadicOk2.php' => [],
	'ok/ReferenceOk1.php' => [],
	'ok/ReferenceOk2.php' => [],
	'ok/ReturnOk1.php' => [],
	'ok/ReturnOk2.php' => [],
	'ok/ReturnOk3.php' => [],
	'ok/ReturnOk4.php' => [],
	'ok/ReturnOk5.php' => [],
	'ok/ReturnOk6.php' => [],
];


foreach ($dataset as $file => $expected) {
	echo $file, "\n";

	$reporter = new LogReporter;
	$collector = new Nette\TypeFixer\Collector($reporter);
	$classes = $collector->collect([__DIR__ . '/fixtures/' . $file]);

	$analyzer = new Nette\TypeFixer\Analyzer($reporter);
	$analyzer->analyze($classes);

	Assert::same($expected, $reporter->log, $file);
}
