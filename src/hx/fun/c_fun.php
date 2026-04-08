<?php
namespace hx\fun;

use hx\fun\debug\c_debug;
use hx\fun\stdclass\c_stdclass;
use hx\c_base_class;
use hx\fun\file\c_ini;
use hx\fun\file\c_file;
use hx\fun\json\c_json;
use hx\fun\array\c_array;
use hx\fun\weakrefence\c_weakrefence;

/**
 * @property c_debug 		$debug
 * @property c_stdclass 	$stdclass
 * @property c_file 		$file
 * @property c_json 		$json
 * @property c_array 		$array
 *
 *
 *
 */
class c_fun extends c_base_class
{

	public function __get ($k)
	{
		/* < */
		return $this->ado('debug'		, c_debug::class)
					->ado('stdclass'	, c_stdclass::class)
					->ado('file'		, c_file::class) 
					->ado('json'		, c_json::class)
					->ado('array'		, c_array::class)
					->$k;
		/* > */
	}

	public function test ()
	{
		return new class() extends c_base_class
		{

			public function running_count (string $tag = '')
			{
				static $i = 0;
				gf()->fun->debug->echo_with_nl('[' . $tag . '] ' . $i ++);
			}
		};
	}
}