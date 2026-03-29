<?php
namespace hx;

abstract class c_base_class extends \stdclass
{

	public static function new ()
	{
		return new static();
	}
}