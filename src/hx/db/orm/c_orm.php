<?php
namespace hx\db\orm;

use hx\c_base_class;
use hx\db\i_trans;
use hx\db\i_db;
use hx\db\i_bindx;
use hx\db\mysqli\c_bind_parameter;
use hx\db\i_query;
use hx\fun\array\c_array;
use hx\reflection\i_reflection_property;
use hx\c_ok_error;
use hx\db\i_query_status;

class c_order_by extends c_base_class
{
	private c_orm $c_orm;
	private i_reflection_property $order_by;

	public function __construct (\WeakReference $w)
	{
		$this->c_orm = $w->get();
		$this->order_by = gf()->reflection->property($this->c_orm,'order_by');
	}

	public function asc (string $order): self
	{
		if ($this->order_by->get()->count() === 0 && empty($order) === FALSE)
		{
			$this->order_by->get()->push(" order by " . $order . " asc");
		}
		else
		{
			$this->order_by->get()->push($order . ' asc');
		}
		return $this;
	}

	public function desc (string $order): self
	{
		if ($this->order_by->get()->count() === 0 && empty($order) === FALSE)
		{
			$this->order_by->get()->push(" order by " . $order . " desc");
		}
		else
		{
			$this->order_by->get()->push($order . ' desc');
		}
		return $this;
	}

	public function by (): c_orm
	{
		return $this->c_orm;
	}
}

abstract class c_orm extends c_base_class
{
	private ?string $table_name = null;
	private ?string $connection_key = null;
	private ?string $database_name = null;
	private ?string $database_env_json_file_path = null;
	private ?c_array $where = null;
	private ?c_array $field = null;
	private ?c_array $from = null;
	private ?c_array $limit = null;
	private ?c_array $group_by = null;
	private ?i_trans $it = null;
	private ?i_db $db = null;
	protected ?c_array $order_by = null;
	protected ?c_array $join = null;

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
	 * @param 	array $insert
	 * @return 	\hx\db\i_query
	 * @throws	\Exception
	 * 
	 */
	public function insert (array $insert): i_query
	{
		$this->ini_db_config();

		/**
		 * @var c_array $insert
		 */
		$insert = gf()->fun->array->new_with_array($insert);
		if ($insert->count() === 0)
		{
			gf()->exception->throw(130000,'You will get an error when passing an empty array to the ORM insert method');
		}

		$this->dc()->sql->insert = "insert into " . $this->get_table_name() . "(";
		$insert->for_each(function ($k , $v)
		{
			$this->dc()->sql->insert .= $k . ",";
		});
		$this->dc()->sql->insert = rtrim($this->dc()->sql->insert,',') . ") values (";
		$insert->for_each(function ($k , $v)
		{
			$this->dc()->sql->insert .= '?,';
		});
		$this->dc()->sql->insert = rtrim($this->dc()->sql->insert,',') . ")";
		$this->dc()->sql->insert_status = null;

		$this->it->auto(function (i_trans $it) use ( $insert)
		{
			/**
			 * @var i_bindx $bindx
			 */
			$bindx = $it->query($this->dc()->sql->insert);
			$insert->for_each(function ($k , $v) use ( $it , $bindx)
			{
				$bindx->ax($v);
			});

			try
			{
				$this->dc()->sql->insert_status = $bindx->go();
			}
			catch (\Throwable $e)
			{
				gf()->exception->throw_with_wrap(130001, $e);
			}
		});

		return $this->dc()->sql->insert_status;
	}

	public function get_order_by (): string
	{
		return $this->order_by->implode(',');
	}

	public function order_by (string ...$order_by): self
	{
		if ($this->ini_db_config()->order_by->count() === 0 && count($order_by) > 0)
		{
			$this->order_by->push(" order by " . array_shift($order_by));
		}
		$this->order_by->push(...$order_by);

		return $this;
	}

	public function order (): c_order_by
	{
		return new c_order_by($this->ini_db_config()->make_weak_reference());
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
		$sql .= " " . $this->get_order_by();

		$this->sql_free();

		return $this->it->query($sql);
	}

	private function get_join (): string
	{
		return $this->join->implode(' ');
	}

	public function join ()
	{
		return new class($this->ini_db_config()->make_weak_reference()) extends c_base_class
		{
			private c_orm $c_orm;
			private i_reflection_property $join;
			private ?string $join_table = null;

			public function __construct (\WeakReference $w)
			{
				$this->c_orm = $w->get();
				$this->join = gf()->reflection->property($this->c_orm,'join');
			}

			public function left ($table)
			{
				$this->join_table = $table;
				return $this;
			}

			public function on (string $ta , string $tb): c_orm
			{
				$this->join->get()->push('left join ',$this->join_table," on ",$ta,' = ',$tb);

				return $this->c_orm;
			}
		};
	}

	public function field (string ...$fields): self
	{
		$this->ini_db_config()->field->push(...$fields);

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
				
				$this->kxv 		= gf()->fun->array->new_with_array([ $k,$x,$v])->map(fn ($a) => trim($a))->get();
				$this->kxv[1] 	= ' ' . $this->kxv[1] . ' ';

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
		$this->join->free();$this->field->free();$this->from->free();$this->where->free();$this->order_by->free();$this->limit->free();$this->group_by->free();$this->dc()->del('sql');

		return $this;
	}
	private function sql_ini (): self
	{
		$this->field 	??= gf()->fun->array->new();
		$this->from 	??= gf()->fun->array->new();
		$this->where 	??= gf()->fun->array->new();
		$this->order_by ??= gf()->fun->array->new();
		$this->limit 	??= gf()->fun->array->new();
		$this->group_by ??= gf()->fun->array->new();
		$this->join 	??= gf()->fun->array->new();
		$this->order_by ??= gf()->fun->array->new();
		
		return $this;
	}
	/* > */
	private function ini_db_config (): self
	{
		$this->database_env_json_file_path ??= $this->on_set_open_with_env_json();
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

	protected function on_set_open_with_env_json (): string
	{
		return gf()->config->mysql->get_mysql_config_env_file_path();
	}

	protected abstract function on_set_connnection_key (): string;

	protected abstract function on_db_driver (): i_db;
}