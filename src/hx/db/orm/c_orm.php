<?php
namespace hx\db\orm;

use hx\c_base_class;
use hx\db\i_trans;
use hx\db\i_db;
use hx\db\i_bindx;
use hx\db\mysqli\c_bind_parameter;
use hx\db\i_query;

abstract class c_orm extends c_base_class
{
	protected ?string $table_name = null;
	protected ?string $connection_key = null;
	protected ?string $database_name = null;
	protected ?string $database_env_json_file_path = null;
	protected ?i_trans $it = null;
	protected ?i_db $db = null;
	private ?string $sql_where = null;

	public function set_sql_where (string $sql_where = NULL): self
	{
	}

	public function get_table_name (): string
	{
		if ($this->table_name === NULL)
		{
			$ar = explode('\\',get_class($this));
			$this->table_name = array_pop($ar);
		}
		return $this->database_name === null ? $this->table_name : $this->database_name . '.' . $this->table_name;
	}

	/**
	 *
	 * @param string ...$fields
	 * 
	 */
	public function select (array $fields = [ ]): i_bindx
	{
		$this->ini_db_config();

		# default => *
		#
		#
		$fields = count($fields) === 0 ? [ '*'] : $fields;
		return $this->it->query("select " . implode(' , ',$fields) . ' from ' . $this->get_table_name());
	}

	public function find_with_id (int $id , array ...$fields): i_query
	{
		return $this->where('id',$id)->go();
	}

	private function ini_db_config (): self
	{
		if ($this->database_env_json_file_path === NULL)
		{
			$this->database_env_json_file_path = $this->on_set_open_with_env_json();
		}

		if ($this->connection_key === NULL)
		{
			$this->set_connnection_key($this->on_set_connnection_key());
		}
		return $this;
	}

	private function set_open_with_env_json (string $json_file): self
	{
		$this->database_env_json_file_path = $json_file;
		return $this;
	}

	private function set_database_name ($database_name = null): self
	{
		$this->database_name = $database_name;
		return $this;
	}

	private function set_connnection_key (string $connection_key = 'default'): self
	{
		$this->connection_key = in_array($connection_key,[ null,''],true) ? 'default' : $connection_key;
		if ($this->db === NULL)
		{
			$this->db = $this->on_db_driver()->open_with_env_json($this->database_env_json_file_path);
		}
		$this->it = $this->db->connect($this->connection_key);
		$this->set_database_name($this->db->get_connection_info()->{$this->connection_key}->database());

		return $this;
	}

	protected abstract function on_set_database_name (): string;

	protected abstract function on_set_connnection_key (): string;

	protected abstract function on_set_open_with_env_json (): string;

	protected abstract function on_db_driver (): i_db;
}