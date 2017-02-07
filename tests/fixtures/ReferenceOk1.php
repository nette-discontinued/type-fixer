<?php

namespace ReferenceOk1;

class A
{
	function test()
	{
	}
}


class B extends A
{
	function &test()
	{
	}
}
