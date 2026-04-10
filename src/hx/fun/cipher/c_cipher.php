<?php
namespace hx\fun\cipher;

use hx\c_base_class;

/**
 * 
 * @author Administrator
 * @property c_rand $rand
 *
 */
class c_cipher extends c_base_class
{

	public function __get ($k)
	{
		/* < */
		return $this->ado('rand'		, c_rand::class			, $k)		
					->$k;
		/* > */
	}
}

class c_rand extends c_base_class
{

	public function create (): int
	{
		return rand();
	}

	public function uuid (): string
	{
		return md5(uniqid('', true) . random_bytes(16));
	}
}