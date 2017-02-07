<?php

namespace ParamsReferenceBad1;

class A
{
	function test($a)
	{
	}
}


class B extends A
{
	function test(&$a)
	{
	}
}
