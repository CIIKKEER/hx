<?php
namespace hx\route;

use hx\c_base_class;

class c_route extends c_base_class
{
	public const version = '1.0.0';

	public function about (): string
	{
		return self::version;
	}
}