<?php
namespace hx\fun;

use hx\fun\debug\c_debug;
use hx\fun\stdclass\c_stdclass;
use hx\c_base_class;
use hx\fun\file\c_ini;
use hx\fun\file\c_file;
use hx\fun\json\c_json;

class c_fun extends c_base_class
{
	public c_debug $debug;
	public c_stdclass $stdclass;
	public c_file $file;
	public c_json $json;

	public function __construct ()
	{
		$this->debug = new c_debug();
		$this->stdclass = new c_stdclass();
		$this->file = new c_file();
		$this->json = new c_json();
	}
}