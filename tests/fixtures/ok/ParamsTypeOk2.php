<?php

namespace ParamsTypeOk2;

class A
{
	function test(int $a)
	{
	}
}


class B extends A
{
	function test(?int $a)
	{
	}
}
