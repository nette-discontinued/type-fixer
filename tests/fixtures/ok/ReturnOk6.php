<?php

namespace ReturnOk6;

class A
{
	function test(): self
	{
	}
}


class B extends A
{
	function test(): A
	{
	}
}
