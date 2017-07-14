<?php

namespace ParamsCountBad5;

// triggers error since PHP 7.2

interface I
{
	function test($a);
}


class A implements I
{
	function test($a, $b = NULL)
	{
	}
}



class B extends A
{
	function test($a)
	{
	}
}
