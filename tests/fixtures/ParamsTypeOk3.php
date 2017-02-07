<?php

namespace ParamsTypeOk3;

class A
{
	function test()
	{
	}
}


class B extends A
{
	function test(int $a = null)
	{
	}
}
