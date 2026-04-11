<?php
namespace hx\db\mysqli;

use hx\fun\stdclass\c_stdclass;
use hx\c_base_class;

/**
 *
 * @author Administrator
 * @property c_mysql_connection_info $default
 *
 *
 *
 *
 */
class c_mysql_connection_info extends c_base_class implements i_mysql_connection_info
{

	public function set_mysql_connection_info ($conn): i_mysql_connection_info
	{
		$this->m_mysql_connection_info = $conn->mysql;

		return $this;
	}

	public function ip (): string
	{
		return $this->m_ip;
	}

	public function port (): int
	{
		return $this->m_port;
	}

	public function user (): string
	{
		return $this->m_user;
	}

	public function password (): string
	{
		return $this->m_password;
	}

	private function get_mysql_with_key ($key = 'default')
	{
		/* < */
		$o 				= $this->new();
		$o->m_ip 		= $this->m_mysql_connection_info->{$key}->hostname;
		$o->m_port 		= $this->m_mysql_connection_info->{$key}->port;
		$o->m_user 		= $this->m_mysql_connection_info->{$key}->username;
		$o->m_password 	= $this->m_mysql_connection_info->{$key}->password;
		/* > */

		return $o;
	}

	public function __get ($k)
	{
		return $this->get_mysql_with_key($k);
	}
}

interface i_mysql_connection_info
{

	public function set_mysql_connection_info ($conn): i_mysql_connection_info;

	public function ip (): string;

	public function port (): int;

	public function user (): string;

	public function password (): string;
}