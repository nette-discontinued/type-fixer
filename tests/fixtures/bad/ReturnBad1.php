<?php

namespace ReturnBad1;

class A
{
	function test(): int
	{
	}
}


class B extends A
{
	function test()
	{
	}
}
