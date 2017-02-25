<?php

namespace ParamsTypeBad6;

class A
{
	function test($a = null)
	{
	}
}


class B extends A
{
	function test(int $a = null)
	{
	}
}
