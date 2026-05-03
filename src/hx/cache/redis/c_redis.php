<?php
/* < */declare(strict_types = 1);/* > */

/* Copyright 2026 BREEZZEER
 * SPDX-License-Identifier: Apache-2.0
 *
 *
 */
namespace hx\cache\redis;

use hx\c_base_class;
use hx\fun\stdclass\c_stdclass;

interface i_redis_type
{

	public function list (string $k): i_redis_set_get_for_list;

	public function s (): i_redis_set_get;
}

interface i_redis_set_get
{

	public function set (string $k , $v): self;

	public function get (string $k);
}

interface i_redis_set_get_for_list
{

	public function push ($v): self;

	public function pop (): mixed;

	public function popb (int $timeout): mixed;

	public function count (): int;

	/**
	 *
	 * @param callable (string $list_name,mixed $list_value) : bool $on_for_each
	 * @return self
	 * 
	 */
	public function for_each (callable $on_for_each): self;
}

interface i_redis_connect
{

	public function open_with_json_file (string $file): self;

	public function close (): self;

	public function connect (string $k = 'default'): i_redis_type;
}

/**
 * 
 * @author 		Administrator
 * @property	c_redis_s 		$s
 * @property	c_redis_list	$list
 *
 */
class c_redis extends c_base_class implements i_redis_connect
{
	private ?c_stdclass $config = null;
	private ?c_stdclass $env_config_json = null;

	/**
	 * @var \Redis $rc
	 */
	public ?\Redis $rc = null;

	public function __destruct ()
	{
		$this->close();
	}

	/* <
	 * 
	 */
	public function __get ($k)
	{
		return $this->ado('s',$this->make_type_for_redis()->s(),$k)->ado('list', $this->make_type_for_redis()->list(''), $k)->$k;
	}
	/* > */
	public function close (): i_redis_connect
	{
		$this->rc === null ? null : $this->rc->close();
		return $this;
	}

	public function open_with_json_file (string $file): self
	{
		$this->env_config_json = gf()->fun->file->ini->open_with_json($file);
		return $this;
	}

	public function connect (string $k = 'default'): i_redis_type
	{
		$this->config = gf()->fun->stdclass->new_with_stdclass($this->env_config_json->redis->$k);
		$this->rc = new \Redis();
		$this->rc->connect($this->config->host,$this->config->port,$this->config->timeout);
		$this->rc->auth($this->config->password);

		return $this->make_type_for_redis();
	}

	/**
	 * 
	 * @return 	i_redis_type
	 * @throws	\Exception
	 * 
	 */
	private function make_type_for_redis (): i_redis_type
	{
		if (null === $this->rc)
		{
			gf()->exception->throw(60000000,'redis server has disconnected');
		}

		return new c_redis_type($this->make_weak_reference());
	}

	/**
	 * @param 	callable 	(c_redis $r,i_redis_type $rt) : void $on_connect_ex
	 * @param	string 		$k
	 * @return 	self
	 * 
	 */
	public function connect_ex (callable $on_connect_ex = NULL , string $k = 'default'): self
	{
		$on_connect_ex === null ? $this->connect($k) : $on_connect_ex($this,$this->connect($k));
		return $this;
	}
}

class c_redis_type extends c_base_class implements i_redis_type
{
	public c_redis $c_redis;

	public function __construct (\WeakReference $c_redis)
	{
		$this->c_redis = $c_redis->get();
	}

	public function s (): i_redis_set_get
	{
		return new c_redis_s($this->make_weak_reference());
	}

	public function list (string $k): i_redis_set_get_for_list
	{
		return new c_redis_list($this->make_weak_reference(),$k);
	}
}

class c_redis_list extends c_base_class implements i_redis_set_get_for_list
{
	private c_redis_type $c_redis_type;
	private string $k;

	public function __construct (\WeakReference $c_redis_type , string $k)
	{
		$this->c_redis_type = $c_redis_type->get();
		$this->k = $k;
	}

	/**
	 * 
	 * @see \hx\cache\redis\i_redis_set_get_for_list::count()
	 * @throws \Exception
	 * 
	 */
	public function count (): int
	{
		return $this->c_redis_type->c_redis->rc->lLen($this->k);
	}

	public function pop (): mixed
	{
		return $this->c_redis_type->c_redis->rc->rPop($this->k);
	}

	public function push ($v): self
	{
		$this->c_redis_type->c_redis->rc->lPush($this->k,$v);
		return $this;
	}

	/* < pop all data in the current list
	 * 
	 */
	public function for_each (callable $on_for_each): self
	{
		for (;;)
		{
			$list_name = null;$list_value = null;$v = $this->popb() ;if (count($v) > 0)
			{
				list ($list_name, $list_value) = $v; 
			}			
			if(true === $on_for_each (strval($list_name),$list_value) || count($v)===0 )
			{
				break;
			}
		}

		return $this;
	}

	public function popb (int $timeout = 1): mixed
	{
		$r = $this->c_redis_type->c_redis->rc->brPop($this->k,$timeout);if (is_array($r) === false)
		{
			$r = [ ];
		}
		return $r;
	}
	/* > */

	public function with_key (string $k): self
	{
		$this->k = $k;
		return $this;
	}
}

class c_redis_s extends c_base_class implements i_redis_set_get
{
	private c_redis_type $c_redis_type;

	public function __construct (\WeakReference $c_redis_type)
	{
		$this->c_redis_type = $c_redis_type->get();
	}

	public function set (string $k , $v): self
	{
		$this->c_redis_type->c_redis->rc->set($k,$v);
		return $this;
	}

	/**
	 * 
	 * {@inheritDoc}
	 * @see \hx\cache\redis\i_redis_set_get::get()
	 * 
	 */
	public function get (string $k)
	{
		try
		{
			return $this->c_redis_type->c_redis->rc->get($k);
		}
		catch (\Throwable $e)
		{
			return gf()->exception->throw(60000001,$e->getMessage());
		}
	}
}


