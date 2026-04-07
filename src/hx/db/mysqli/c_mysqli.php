<?php
namespace hx\db\mysqli;

use hx\db\i_db;
use hx\c_base_class;
use hx\db\mysqli\c_mysql_connection_info;
use function hx\gf_hx;
use hx\fun\stdclass\c_stdclass;

/**
 *
 * @desc	read mysql database data by mysql native driver mode
 * @author 	Administrator
 * 
 * 
 */
class c_mysqli extends c_base_class implements i_db
{

	public function __construct ()
	{
		$this->m_mysql_connection_info = new c_mysql_connection_info();
		$this->m_mysqli = new \mysqli();
	}

	public function __get ($k): c_mysqli
	{
		return $this->new()->open($this->m_mysql_connection_info->$k);
	}

	public function open_with_env_json (string $env_file_path): i_db
	{
		try
		{
			$this->m_mysql_connection_info = gf()->config->mysql->get_with_env_json($env_file_path);
		}
		catch (\Exception $e)
		{
			gf()->exception->throw_with_wrap(1000001,$e);
		}

		return $this;
	}

	public function get_db_information (): string
	{
		return $this->m_mysqli->get_server_info() . ' ' . $this->m_mysqli->host_info;
	}

	public function close (): i_db
	{
		$this->m_mysqli->close();
		return $this;
	}

	public function open_with_mysql_connection_info (c_mysql_connection_info $conn): c_mysqli
	{
		$this->m_mysql_connection_info = $conn;
		return $this;
	}

	private function open (c_mysql_connection_info $conn): i_db
	{
		$this->m_mysqli = new \mysqli();

		try
		{
			$this->m_mysqli->connect($conn->ip(),$conn->user(),$conn->password(),null,$conn->port());
		}
		catch (\mysqli_sql_exception $e)
		{
			gf()->fun->debug->die($e->getMessage());
		}

		return $this;
	}

	public function connect (string $connection_key = 'default'): i_db
	{
		return $this->__get($connection_key);
	}
}