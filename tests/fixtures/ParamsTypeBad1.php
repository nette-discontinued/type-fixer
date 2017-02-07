<?php

namespace ParamsTypeBad1;

class A
{
	function test($a)
	{
	}
}


class B extends A
{
	function test(array $a)
	{
	}
}
