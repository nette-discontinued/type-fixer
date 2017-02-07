<?php

namespace ParamsReferenceOk1;

class A
{
	function test()
	{
	}
}


class B extends A
{
	function test(&$a = null)
	{
	}
}
