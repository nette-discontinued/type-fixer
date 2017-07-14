<?php

namespace ParamsTypeBad2;

// OK since 7.2

class A
{
	function test(array $a)
	{
	}
}


class B extends A
{
	function test(array $a)
	{
	}
}
