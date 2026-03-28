<?php
namespace hx\db\mysqli;

use hx\db\i_db;
use hx\c_base_class;
use hx\db\c_mysql_connection_info;

class c_mysqli extends c_base_class implements i_db
{
	private c_mysql_connection_info $m_mysql_connection_info;
	public function close ()
	{
	}
	public function open (): i_db
	{
		$this->m_mysql_connection_info = new c_mysql_connection_info();
		return $this;
	}
}