<?php
namespace hx\db;

use hx\db\i_db;

class c_db extends \stdClass
{
	public i_db $db;
	function __construct (i_db $db)
	{
		$this->db = $db;
	}
}