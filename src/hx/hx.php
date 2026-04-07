<?php
namespace hx;

use hx\fun\c_fun;
use hx\db\c_db;
use hx\db\mysqli\c_mysqli;
use hx\c_base_class;
use hx\config\c_config;
use hx\exception\c_exception;

/**
 * @author Administrator
 * @property c_fun $fun
 * @property c_db $db
 * @property c_config $config
 * @property c_exception $exception
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
					->ado('exception'	, function (){return (new c_exception())->set_exception_handler();})
					->$k;
		/* > */
	}
}



