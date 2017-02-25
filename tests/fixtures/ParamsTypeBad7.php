<?php

namespace ParamsTypeBad7;

class A
{
	function test(self $a)
	{
	}
}


class B extends A
{
	function test(self $a)
	{
	}
}
