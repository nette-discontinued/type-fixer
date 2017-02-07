<?php

namespace ParamsReferenceOk3;

class A
{
	function test(&$a = null)
	{
	}
}


class B extends A
{
	function test(&$a = null)
	{
	}
}
