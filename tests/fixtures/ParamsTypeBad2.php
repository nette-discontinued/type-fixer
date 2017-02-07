<?php

namespace ParamsTypeBad2;

class A
{
	function test(array $a)
	{
	}
}


class B extends A
{
	function test($a)
	{
	}
}
