<?php

namespace ParamsTypeBad3;

class A
{
	function test(array $a)
	{
	}
}


class B extends A
{
	function test(int $a)
	{
	}
}
