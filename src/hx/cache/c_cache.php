<?php
namespace hx\cache;

use hx\c_base_class;
use hx\cache\redis\c_redis;

/**
 * 
 * @author 		Administrator
 * @property 	c_redis 			$redis
 *
 */
class c_cache extends c_base_class
{

	public function __get ($k)
	{
		/* < */
		return	$this->ado('redis', c_redis::class, $k)
				->$k;
		/* > */
	}
}