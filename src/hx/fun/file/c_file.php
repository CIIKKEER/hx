<?php
namespace hx\fun\file;

use hx\c_base_class;

class c_file extends c_base_class
{
	public c_ini $ini;

	public function __construct ()
	{
		$this->ini = new c_ini();
	}
	
	public function get_contents ($filename)
	{
		return file_get_contents($filename);
	}
}