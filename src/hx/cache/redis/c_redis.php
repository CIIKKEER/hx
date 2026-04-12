<?php
namespace hx\cache\redis;

use hx\c_base_class;
use hx\fun\stdclass\c_stdclass;
use Credis_Client;

interface i_redis_type
{

	public function list (string $k): i_redis_set_get_for_list;

	public function s (): i_redis_set_get;
}

interface i_redis_set_get
{

	public function set (string $k , $v): self;

	public function get (string $k): mixed;
}

interface i_redis_set_get_for_list
{

	public function push ($v): self;

	public function pop (): mixed;

	public function count (): int;

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
 *
 */
class c_redis extends c_base_class implements i_redis_connect
{
	private ?c_stdclass $config = null;

	/**
	 * 
	 * @var \Credis_Client $rc
	 */
	public ?\Credis_Client $rc = null;

	public function __destruct ()
	{
		$this->close();
	}

	/* <
	 * 
	 */
	public function __get ($k)
	{
		return $this->ado('s',$this->make_type_for_redis()->s(),$k)
					->$k;
	}
	/* > */
	public function close (): i_redis_connect
	{
		$this->rc === null ? null : $this->rc->close(true);

		return $this;
	}

	public function open_with_json_file (string $file): self
	{
		$this->config = gf()->fun->file->ini->open_with_json($file);
		return $this;
	}

	public function connect (string $k = 'default'): i_redis_type
	{
		$this->config = gf()->fun->stdclass->new_with_stdclass($this->config->redis->$k);
		$this->rc = new \Credis_Client($this->config->host,$this->config->port,null,'',0,$this->config->password);

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
	 * @param	string $k
	 * @param 	callable (c_redis $r,i_redis_type $rt) : void $on_connect_ex
	 * @return 	self
	 * 
	 */
	public function connect_ex (callable $on_connect_ex = NULL , string $k = 'default'): self
	{
		$on_connect_ex === null ? null : $on_connect_ex($this,$this->connect($k));
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
	public c_redis_type $c_redis_type;
	private string $k;

	public function __construct (\WeakReference $c_redis_type , string $k)
	{
		$this->c_redis_type = $c_redis_type->get();
		$this->k = $k;
	}

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

	public function for_each (callable $on_for_each): self
	{
		for (;;)
		{
			/* < pop all data in the current list
			 * 
			 */
			$k = $this->count();if ($k > 0)
			{
				$ok = $on_for_each($k,$this->pop());if ($ok === TRUE)
				{
					break;
				}
				/* > */
			}
			else
			{
				break;
			}
		}

		return $this;
	}
}

class c_redis_s extends c_base_class implements i_redis_set_get
{
	public c_redis_type $c_redis_type;

	public function __construct (\WeakReference $c_redis_type)
	{
		$this->c_redis_type = $c_redis_type->get();
	}

	public function set (string $k , $v): self
	{
		$this->c_redis_type->c_redis->rc->set($k,$v);
		return $this;
	}

	public function get (string $k): mixed
	{
		return $this->c_redis_type->c_redis->rc->get($k);
	}
}


