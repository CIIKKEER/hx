<?php
namespace hx;

use hx\fun\c_fun;
use hx\db\c_db;
use hx\db\mysqli\c_mysqli;
use hx\c_base_class;
use hx\config\c_config;
use hx\exception\c_exception;
use hx\test\c_test;
use hx\cache\c_cache;
use hx\cli\c_cli;
use hx\os\c_os;
use hx\route\c_route;
use hx\fun\stdclass\c_stdclass;
use hx\reflection\c_reflection;
use hx\reflection\i_reflection_property;

/**
 * @author 		Administrator
 * @property 	c_fun 			$fun
 * @property 	c_db 			$db
 * @property 	c_config 		$config
 * @property 	c_exception 	$exception
 * @property 	c_version		$version
 * @property	c_test			$test
 * @property	c_cache			$cache
 * @property	c_cli			$cli
 * @property	c_os			$os	
 * @property	c_route			$route
 * @property	c_reflection	$reflection
 *
 *
 */
class hx extends c_base_class
{

	public function __get ($k)
	{
		/* < */
		return $this->ado('fun'			, c_fun::class														, $k)
					->ado('db'			, c_db::class														, $k)
					->ado('cache'		, c_cache::class													, $k)
					->ado('config'		, c_config::class													, $k)
					->ado('version'		, c_version::class													, $k)
					->ado('test'		, c_test::class														, $k)
					->ado('cli'			, c_cli::class														, $k)
					->ado('os'			, c_os::class														, $k)
					->ado('route'		, c_route::class													, $k)
					->ado('reflection'	, c_reflection::class												, $k)
					->ado('exception'	, function (){return (new c_exception())->set_exception_handler();}	, $k)
					->$k;
		/* > */
	}
}

class c_version extends c_base_class
{
	const version = '1.0.66';
	const author = 'BREEZZEER';
	const email = 'lch1025@qq.com';
	const license = 'Apache License';
	const description = 'ciikkeer/hx => php helper library';

	public function about (): c_stdclass
	{
		return gf()->fun->stdclass->new_with_array(gf()->reflection->class($this)
			->getConstants());
		
		
	}
}


