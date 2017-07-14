<?php

namespace ParamsTypeBad5;

class A
{
	function test(?int $a)
	{
	}
}


class B extends A
{
	function test(int $a)
	{
	}
}
