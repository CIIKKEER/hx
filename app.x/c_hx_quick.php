<?php
namespace appx;

use hx\hx;
use hx\db\c_db;
use hx\fun\c_fun;
use hx\c_base_class;
use hx\cache\c_cache;
use hx\cache\redis\c_redis;

class c_hx_quick extends c_base_class
{
	protected hx $hx;
	protected c_db $db;
	protected c_fun $fun;
	protected c_redis $redis;

	public function __construct ()
	{
		$this->hx = gf();
		$this->db = gf()->db;
		$this->fun = gf()->fun;
		$this->redis = gf()->cache->redis;
	}
}