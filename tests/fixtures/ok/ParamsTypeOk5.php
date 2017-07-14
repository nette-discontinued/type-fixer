<?php

namespace ParamsTypeOk5;

class A
{
	function test(self $a)
	{
	}
}


class B extends A
{
	function test(A $a)
	{
	}
}
