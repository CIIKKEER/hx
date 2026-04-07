<?php
namespace hx\db;

use hx\db\mysqli\c_mysql_connection_info;

interface i_db
{

	public function open_with_env_json (string $env_file_path): i_db;

	public function open_with_mysql_connection_info (c_mysql_connection_info $conn): i_db;

	public function connect (string $connection_key = 'default'): i_db;

	public function get_db_information (): string;

	public function close (): i_db;
}