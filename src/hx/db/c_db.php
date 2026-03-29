<?php
namespace hx\db;

use hx\db\i_db;
use hx\db\mysqli\c_mysqli;

class c_db extends \stdClass
{
	private i_db $m_db;

	function __construct ()
	{
	}

	function new_with_mysqli (): i_db
	{
		$this->m_db = new c_mysqli();
		return $this->m_db;
	}
}