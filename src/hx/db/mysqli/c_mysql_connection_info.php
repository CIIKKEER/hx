<?php
namespace hx\db\mysqli;

use hx\fun\stdclass\c_stdclass;

class c_mysql_connection_info
{
	private string $m_ip;
	private int $m_port;
	private string $m_user;
	private string $password;

	public function __construct ()
	{
		$this->m_ip = $this->get_with_default()->hostname;
		$this->m_port = $this->get_with_default()->port;
		$this->m_user = $this->get_with_default()->username;
		$this->password = $this->get_with_default()->password;
	}

	public final function ip (): string
	{
		return $this->m_ip;
	}

	public final function port (): int
	{
		return $this->m_port;
	}

	public final function user (): string
	{
		return $this->m_user;
	}

	public final function passowrd (): string
	{
		return $this->password;
	}

	public final function get (): c_stdclass
	{
		return gf()->config->get()->mysql;
	}

	public final function get_with_default (): c_stdclass
	{
		return $this->get()->default;
	}
}