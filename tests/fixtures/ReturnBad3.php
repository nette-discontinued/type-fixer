<?php

namespace ReturnBad3;

class A
{
	function test(): int
	{
	}
}


class B extends A
{
	function test(): string
	{
	}
}
