<?php

namespace ReturnBad2;

class A
{
	function test(): ?int
	{
	}
}


class B extends A
{
	function test()
	{
	}
}
