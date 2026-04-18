<?php
namespace hx\fun\jwt;

use hx\c_base_class;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class c_jwt extends c_base_class
{
	private ?string $key = null;

	public function set_key ($k): self
	{
		$this->key = $k;
		return $this;
	}

	/**
	 * 
	 * @param 	array $payload
	 * @return 	string
	 * @throws 	\Exception
	 * 
	 */
	public function encoder_with_key (array $payload): string
	{
		try
		{
			return JWT::encode($payload,$this->key,'HS256');
		}
		catch (\Throwable $e)
		{
			gf()->exception->throw(880010,$e->getMessage());
		}
	}

	/**
	 * 
	 * @param 	string $jwt
	 * @return 	mixed
	 */
	public function decoder (string $jwt): mixed
	{
		try
		{
			return JWT::decode($jwt,new Key($this->key,'HS256'));
		}
		catch (\Throwable $e)
		{
			return false;
		}
	}
}