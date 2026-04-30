<?php
namespace hx\fun\jwt;

use hx\c_base_class;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class c_jwt extends c_base_class
{
	private ?string $key = null;

	/**
	 * @desc	your default JWT key string
	 * @param 	string $k
	 * @return 	self
	 */
	public function set_key (string $k): self
	{
		$this->key = $k;
		return $this;
	}

	/**
	 * @desc 	you can use the standard JWT component to encode the input payload data into an encrypted string
	 * @param 	array $payload
	 * @return 	string
	 * @throws 	\Exception
	 * 
	 */
	public function encoder (array $payload): string
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
	 * @desc 	i will decode the encrypted JWT string content using the default key string. If the content is decoded successfully, a standard string will be returned, otherwise, the boolean value fasle will be returned 
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