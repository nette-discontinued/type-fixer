<?php

namespace ParamsReferenceOk2;

class A
{
	function test(&$a)
	{
	}
}


class B extends A
{
	function test(&$a)
	{
	}
}
