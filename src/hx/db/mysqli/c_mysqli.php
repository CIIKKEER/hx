<?php
namespace hx\db\mysqli;

use hx\db\i_db;
use hx\c_base_class;
use hx\db\mysqli\c_mysql_connection_info;
use function hx\gf_hx;

class c_mysqli extends c_base_class implements i_db
{
	private c_mysql_connection_info $m_mysql_connection_info;
	private \mysqli $m_mysqli;

	public function close ()
	{
	}

	public function open (c_mysql_connection_info $c_mysql_connection_info = NULL): i_db
	{
		# open the MySQL server using the determined connection information
		#
		#
		if ($c_mysql_connection_info === NULL)
		{
			$this->m_mysql_connection_info = new c_mysql_connection_info();
		}
		else
		{
			$this->m_mysql_connection_info = $c_mysql_connection_info;
		}

		;
		$this->m_mysqli = new \mysqli();
		
		try 
		{
			if ($this->m_mysqli->connect($this->m_mysql_connection_info->ip(),$this->m_mysql_connection_info->user(),$this->m_mysql_connection_info->passowrd(),null,$this->m_mysql_connection_info->port()) === true)
			{
				gf()->fun->debug->die('sssssssssssssssssssssssssss');
			}
		}
		catch (\mysqli_sql_exception $e)
		{
			gf()->fun->debug->die($e->getMessage());
		}

		return $this;
	}

	public function __construct ()
	{
	}
}