<?php
namespace hx\db\mysqli;

use hx\c_base_class;

class c_mysql_connection_info
{
	private string $m_ip;
	private string $m_port;
	private string $m_user;
	private string $m_passowrd;
	public function __construct (string $ip , string $port , string $user , string $password)
	{
		$this->m_ip = $ip;
		$this->m_port = $port;
		$this->m_user = $user;
		$this->m_passowrd = $password;
	}
	public final function ip ()
	{
		return $this->m_ip;
	}
	public final function port ()
	{
		return $this->m_port;
	}
	public final function user ()
	{
		return $this->m_user;
	}
	public final function passowrd ()
	{
		return $this->m_passowrd;
	}
}