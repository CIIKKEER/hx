<?php
namespace hx\db\pdo;

use hx\c_base_class;
use hx\db\i_db;
use hx\db\mysqli\c_mysql_connection_info;

class c_pdo extends c_base_class implements i_db
{

	public function open_with_env_json (string $env_file_path): i_db
	{
		return $this;
	}

	public function get_db_information (): string
	{
		return '';
	}
	public function open_with_mysql_connection_info (c_mysql_connection_info $conn): i_db
	{
	}

	public function close (): i_db
	{
	}

	public function connect (string $connection_key = 'default'): i_db
	{
	}

}