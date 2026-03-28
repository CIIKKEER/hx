<?php
namespace hx\fun;

use hx\fun\debug\c_debug;
use hx\fun\stdclass\c_stdclass;
use hx\c_base_class;

class c_fun extends c_base_class
{
	public c_debug $debug;
	public c_stdclass $stdclass;
	public function __construct ()
	{
		$this->debug = new c_debug();
		$this->stdclass = new c_stdclass();
	}
}