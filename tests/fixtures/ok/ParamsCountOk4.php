<?php

namespace ParamsCountOk4;

class A
{
	function test($a1)
	{
	}
}


class B extends A
{
	function test($a1, $a2 = 2)
	{
	}
}
