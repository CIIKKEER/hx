<?php
namespace hx;

use hx\fun\c_fun;
use hx\db\c_db;
use hx\db\mysqli\c_mysqli;
use hx\c_base_class;

class hx extends c_base_class
{
	public c_fun $fun;
	public c_db $db;
	public function __construct ()
	{
		$this->fun = new c_fun();
		$this->db = new c_db(new c_mysqli());
	}
	public function __destruct ()
	{
		;
	}
	public static function new (): hx
	{
		return new static();
	}
}
function gf_hx (): hx
{
	return hx::new();
}



