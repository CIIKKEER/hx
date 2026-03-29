<?php
namespace hx\db;

use hx\db\mysqli\c_mysql_connection_info;

interface i_db
{

	public function open (c_mysql_connection_info $c_mysql_connection_info = null): i_db;

	public function close ();
}