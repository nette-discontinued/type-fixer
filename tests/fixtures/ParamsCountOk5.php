<?php

namespace ParamsCountOk5;

class A
{
	function test($a1, $a2)
	{
	}
}


class B extends A
{
	function test($a1, $a2 = 2)
	{
	}
}
