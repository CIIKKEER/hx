<?php
namespace hx\fun\cipher;

use hx\c_base_class;

/**
 * 
 * @author 		Administrator
 * @property 	c_rand 			$rand
 * @property 	c_md5			$md5
 * 
 * 
 * 
 * 
 */
class c_cipher extends c_base_class
{

	public function __get ($k)
	{
		/* < */
		return $this->ado('rand', c_rand::class	, $k)
					->ado('md5'	, c_md5::class	, $k)					
					->$k;
		/* > */
	}
}

class c_md5 extends c_base_class
{

	public function create (string $s): string
	{
		return md5($s);
	}
}

class c_rand extends c_base_class
{

	public function create (): int
	{
		return rand();
	}

	public function uuid ()
	{
		return new class() extends c_base_class
		{

			public function __construct ()
			{
				$this->v4 = $this->v4();
			}

			public function md5 (): string
			{
				return gf()->fun->cipher->md5->create($this->v4);
			}

			public function create (): string
			{
				$data = random_bytes(16);
				$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
				$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
				return bin2hex($data);
			}

			public function v4 (): string
			{
				return vsprintf('%s%s-%s-%s-%s-%s%s%s',str_split(bin2hex($this->create()),4));
			}
		};
		return md5(uniqid('',true) . random_bytes(16));
	}
}