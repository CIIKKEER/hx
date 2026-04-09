<?php
namespace hx;

use hx\fun\c_fun;
use hx\db\c_db;
use hx\db\mysqli\c_mysqli;
use hx\c_base_class;
use hx\config\c_config;
use hx\exception\c_exception;
use hx\test\c_test;

/**
 * @author 		Administrator
 * @property 	c_fun 			$fun
 * @property 	c_db 			$db
 * @property 	c_config 		$config
 * @property 	c_exception 	$exception
 * @property 	c_version		$version
 * @property	c_test			$test
 * 		
 *
 *
 *
 */
class hx extends c_base_class
{

	public function __get ($k)
	{
		/* < */
		return $this->ado('fun'			, c_fun::class)
					->ado('db'			, c_db::class)
					->ado('config'		, c_config::class)
					->ado('version'		, c_version::class)
					->ado('test'		, c_test::class)
					->ado('exception'	, function (){return (new c_exception())->set_exception_handler();})
					->$k;
		/* > */
	}
}

class c_version extends c_base_class
{
	const version = '1.0.0';
	const author = 'BREEZZEER';
	const email = 'lch1025@qq.com';
	const license = 'Apache License';
	const description ='ciikkeer/hx => php helper library';
}


