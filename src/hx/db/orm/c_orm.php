<?php
namespace hx\db\orm;

use hx\c_base_class;
use hx\db\i_trans;
use hx\db\i_db;
use hx\db\i_bindx;
use hx\db\mysqli\c_bind_parameter;
use hx\db\i_query;
use hx\fun\array\c_array;

abstract class c_orm extends c_base_class
{
	private ?string $table_name = null;
	private ?string $connection_key = null;
	private ?string $database_name = null;
	private ?string $database_env_json_file_path = null;
	private ?c_array $where = null;
	private ?c_array $field = null;
	private ?c_array $from = null;
	private ?c_array $order_by = null;
	private ?c_array $limit = null;
	private ?c_array $group_by = null;
	protected ?c_array $join = null;
	private ?i_trans $it = null;
	private ?i_db $db = null;

	public function get_table_name (): string
	{
		if ($this->table_name === NULL)
		{
			$ar = explode('\\',get_class($this));
			$this->table_name = array_pop($ar);
		}
		return $this->database_name === null ? $this->table_name : $this->database_name . '.' . $this->table_name;
	}

	public function select (): i_bindx
	{
		$this->ini_db_config();

		$sql = '';
		$sql .= "select ";
		$sql .= " " . $this->get_field();
		$sql .= " " . $this->get_from();
		$sql .= " " . $this->get_join();
		$sql .= " " . $this->get_where();

		$this->sql_free();

		return $this->it->query($sql);
	}

	private function get_join (): string
	{
		return $this->join->implode(' ');
	}

	public function join ()
	{
		$this->ini_db_config();
		return new class($this->make_weak_reference()) extends c_base_class
		{
			private c_orm $c_orm;
			private mixed $join;
			private ?string $join_table = null;

			public function __construct (\WeakReference $w)
			{
				$this->c_orm = $w->get();
				$prop = new \ReflectionProperty($this->c_orm::class,'join');
				$prop->setAccessible(TRUE);
				$this->join = $prop->getValue(...);
			}

			public function left ($table)
			{
				$this->join_table = $table;
				return $this;
			}

			public function on (string $ta , string $tb): c_orm
			{
				($this->join)($this->c_orm)->push('left join ',$this->join_table," on ",$ta,' = ',$tb);
				unset($this->join);
				return $this->c_orm;
			}
		};
	}

	public function field (string ...$fields): self
	{
		$this->ini_db_config();
		$this->field->push(...$fields);

		return $this;
	}

	private function get_field (): string
	{
		if ($this->field->count() === 0)
		{
			$this->field->push('*');
		}
		return $this->field->implode(' , ');
	}

	public function get_from (): string
	{
		if ($this->from->count() === 0)
		{
			$this->from->push($this->get_table_name());
		}

		return " from " . $this->from->implode(' ');
	}

	public function from ($table): self
	{
		$this->ini_db_config()->from->push($table);

		return $this;
	}

	/* <
	 * 
	 */
	public function get_where (): string
	{
		$this->dc()->sql->where = '';
		$this->where->for_each(function ($k , $v)
		{
			$this->dc()->sql->where .= implode('',$v);
		});
		return $this->where->empty() === true ? '' : ' where ' . $this->dc()->sql->where;
	}
	
	public function where():self
	{
		$this->ini_db_config()->where->push([ 1,' = ',1]);
				 
		return $this;		
	}
	public function and ($k , $x ,... $v ): self
	{
		return $this->where_and($k, $x, ...$v);
	}
	private function where_and ($k , $x ,... $v ): self
	{
		$this->ini_db_config()->where->push($this->where_x()->kxv($k,$x,...$v)->and());
		return $this;
	}
	public function or ($k , $x , ...$v): self
	{
		return $this->where_or($k, $x, ...$v);
	}
	private function where_or ($k , $x , ...$v): self
	{
		$this->ini_db_config()->where->push($this->where_x()->kxv($k,$x,...$v)->or());
		return $this;
	}
	private function where_x ()
	{
		return new class() extends c_base_class
		{

			public function kxv ($k , $x , ...$v): self
			{
				if (func_num_args() === 2)
				{
					$v = $x;
					$x = ' = ';
				}
				else
				{
					$v = array_shift($v);
				}
				$this->kxv 		= array_map ( function ($a) { return trim($a);},[ $k,$x,$v ]);
				$this->kxv[1]	= ' ' . $this->kxv[1] . ' ';

				return $this;
			}

			public function and (): array
			{
				return array_merge([ ' and '],$this->kxv);
			}

			public function or (): array
			{
				return array_merge([ ' or '],$this->kxv);
			}
		};
	}
	/* >
	 * <
	 *  
	 */
	private function sql_free (): self
	{
		$this->field->free();$this->from->free();$this->where->free();$this->order_by->free();$this->limit->free();$this->group_by->free();$this->dc()->del('sql');

		return $this;
	}
	/* > */
	private function sql_ini (): self
	{
		if ($this->field === null)
		{
			$this->field = gf()->fun->array->new();
		}

		if ($this->from === NULL)
		{
			$this->from = gf()->fun->array->new();
		}

		if ($this->where === NULL)
		{
			$this->where = gf()->fun->array->new();
		}

		if ($this->order_by === null)
		{
			$this->order_by = gf()->fun->array->new();
		}

		if ($this->limit === null)
		{
			$this->limit = gf()->fun->array->new();
		}

		if ($this->group_by === null)
		{
			$this->group_by = gf()->fun->array->new();
		}

		if ($this->join === NULL)
		{
			$this->join = gf()->fun->array->new();
		}

		return $this;
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

		$this->sql_ini();

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

	protected abstract function on_set_connnection_key (): string;

	protected abstract function on_set_open_with_env_json (): string;

	protected abstract function on_db_driver (): i_db;
}