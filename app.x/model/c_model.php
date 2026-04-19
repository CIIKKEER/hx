<?php
namespace appx\model;

use hx\c_base_class;
use appx\c_hx_quick;
use hx\db\i_trans;
use hx\db\i_bindx;

abstract class c_model extends c_hx_quick
{
	protected ?string $table_name = null;
	protected ?string $connection_key = null;
	protected ?i_trans $it = null;
	protected ?string $database_name = null;

	public function get_table_name (): string
	{
		if ($this->table_name === NULL)
		{
			$ar = explode('\\',get_class($this));
			$this->table_name = array_pop($ar);
		}
		return $this->database_name === null ? '' : $this->database_name . '.' . $this->table_name;
	}

	/**
	 * 
	 * @param string ...$fields
	 */
	public function select (string ...$fields): i_bindx
	{
		if ($this->connection_key === NULL)
		{
			gf()->exception->throw(860000,'database model mysql connection key mission');
		}

		# default => *
		#
		#
		$fields = count($fields) === 0 ? [ '*'] : $fields;

		return $this->it->query("select " . implode(' , ',$fields) . ' from ' . $this->get_table_name());
	}

	protected function set_database_name ($database_name = null): self
	{
	}

	protected function set_connnection_key (string $connection_key = 'default'): self
	{
		$this->connection_key = $connection_key;
		$this->it = $this->db->mysqli->connect($this->connection_key);
		$this->database_name = $this->db->mysqli->get_connection_info()->{$connection_key}->database();

		return $this;
	}
}