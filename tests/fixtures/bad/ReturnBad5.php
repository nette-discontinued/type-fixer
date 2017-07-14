<?php

namespace ReturnBad5;

class A
{
	function test(): self
	{
	}
}


class B extends A
{
	function test(): self
	{
	}
}
