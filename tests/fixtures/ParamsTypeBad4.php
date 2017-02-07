<?php

namespace ParamsTypeBad4;

class A
{
	function test(B $a)
	{
	}
}


class B extends A
{
	function test(A $a)
	{
	}
}
