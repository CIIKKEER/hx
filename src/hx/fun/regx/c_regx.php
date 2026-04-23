<?php
namespace hx\fun;

use hx\c_base_class;

class c_regx extends c_base_class
{

	public function preg_grep (string $pattern , string $subject): bool
	{
		return boolval(preg_grep($pattern,$array));
	}
}