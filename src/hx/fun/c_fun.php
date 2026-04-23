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
use hx\fun\debug\c_console_color;
use hx\fun\cipher\c_cipher;
use hx\fun\time\c_time;
use hx\fun\time\c_hrtime;
use hx\fun\jwt\c_jwt;

/**
 * @property c_debug 			$debug
 * @property c_stdclass 		$stdclass
 * @property c_file 			$file
 * @property c_json 			$json
 * @property c_array 			$array
 * @property c_console_color	$cc
 * @property c_cipher			$cipher
 * @property c_time				$time
 * @property c_jwt				$jwt
 * @property c_regx				$regx
 *
 */
class c_fun extends c_base_class
{

	public function __get ($k)
	{
		/* < */
		return $this->ado('debug'		, c_debug::class			, $k)
					->ado('stdclass'	, c_stdclass::class			, $k)
					->ado('file'		, c_file::class				, $k) 
					->ado('json'		, c_json::class				, $k)
					->ado('array'		, c_array::class			, $k)
					->ado('cc'			, c_console_color::class	, $k)
					->ado('cipher'		, c_cipher::class			, $k)
					->ado('time'		, c_time::class				, $k)
					->ado('jwt'			, c_jwt::class				, $k)
					->ado('regx'		, c_regx::class				, $k)
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
				gf()->fun->debug->echo_with_nl('[' . $tag . '] ' . $i++);
			}

			public function elapse ()
			{
				return new c_hrtime();
			}
		};
	}
}