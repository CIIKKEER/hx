<?php
declare(strict_types = 1)
	;
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
use hx\db\mysqli\c_trans;
use hx\fun\stdclass\c_stdclass;
use function hx\route\to_string;

/**
 * 
 * @author 		Administrator
 * 
 * 
 */
interface i_sql
{

	public function sql (string $sql): i_bindx;
}

enum e_join_type
{
	case left;
	case righ;
	case inner;
}

interface i_where
{

	public function and (string $k , $x , $v): self;

	public function or (string $k , $x , $v): self;

	public function like_left (string $k , mixed $v , bool $and_or = true): self;

	public function like (string $k , mixed $v , bool $and_or = true): self;

	public function like_right (string $k , mixed $v , bool $and_or = true): self;

	public function is_null (string $k , bool $and_or = true): self;

	public function is_not_null (string $k , bool $and_or = true): self;

	public function between (string $k , $a , $b , bool $and_or = true): self;

	public function in (string $k , array $v , bool $and_or = true): self;

	public function not_in (string $k , array $v , bool $and_or = true): self;

	public function column (string $a , string $x , string $b , bool $and_or = true): self;

	public function raw (string $sql , bool $and_or = true): self;

	public function select (): i_bindx;

	public function update (): i_update;

	public function insert (): i_insert;

	public function delete (): i_delete;

	public function group (): i_group_by;

	public function order (): i_order_by;
}

interface i_group_by
{

	public function by (string ...$fields): c_orm;
}

class c_group_by extends c_base_class implements i_group_by
{
	private c_orm $c_orm;
	private c_array $group_by;

	public function __construct (\WeakReference $w)
	{
		$this->c_orm = $w->get();
		$this->group_by = gf()->reflection->property($this->c_orm,'group_by')->get();
	}

	public function by (string ...$fields): c_orm
	{
		$this->group_by->push(...$fields);
		return $this->c_orm;
	}
}

class c_select extends c_base_class
{
	private c_orm $c_orm;
	private c_trans $it;

	public function __construct (\WeakReference $w)
	{
		$this->c_orm = $w->get();
		$this->it = gf()->reflection->property($this->c_orm,'it')->get();
	}

	public function done (): i_bindx
	{
		$sql = '';
		$sql .= "select ";
		$sql .= " " . $this->c_orm->get_field();
		$sql .= " " . $this->c_orm->get_from();
		$sql .= " " . $this->c_orm->get_join();
		$sql .= " " . $this->c_orm->get_where();
		$sql .= " " . $this->c_orm->get_group_by();
		$sql .= " " . $this->c_orm->get_order_by();
		$sql .= " " . $this->c_orm->get_limit();

		# bind parameter
		#
		#
		$bindx = $this->it->query($sql);
		$bindx = $this->c_orm->get_where_bindx($bindx);
		$bindx = $this->c_orm->get_limit_bindx($bindx);
		$this->c_orm->sql_free();
		return $bindx;
	}
}

interface i_limit
{

	public function offset (int $start , int $size): c_orm;
}

class c_limit extends c_base_class implements i_limit
{
	private c_orm $c_orm;
	private c_array $limit;
	private int $size;
	private int $start;

	public function __construct (\WeakReference $w)
	{
		$this->c_orm = $w->get();
		$this->limit = gf()->reflection->property($this->c_orm,'limit')->get();
		$this->size = 0;
		$this->start = 0;
	}

	private function size (int $size): c_orm
	{
		$this->size = $size;
		$this->limit->push($this->start,$this->size);
		return $this->c_orm;
	}

	private function start (int $start): self
	{
		$this->start = $start;
		return $this;
	}

	public function offset (int $start , int $size): c_orm
	{
		return $this->start($start)->size($size);
	}
}

class c_where extends c_base_class implements i_where
{
	private c_orm $c_orm;
	private c_array $where;
	private c_stdclass $kxv;
	private static ?c_array $where_keyword = null;

	public function __construct (\WeakReference $w)
	{
		$this->c_orm = $w->get();
		$this->where = gf()->reflection->property($this->c_orm,'where')->get();
		$this->kxv = gf()->fun->stdclass->new();

		# keyword
		#
		#
		self::$where_keyword ??= gf()->fun->array->push();
	}

	private function kxv (string $k , $x , $v): c_stdclass
	{
		return gf()->fun->stdclass->new()->push($k,$x,$v);
	}

	/* <
	 * 
	 */
	public function and (string $k , $x , $v): self
	{
		$this->kxv->push([ 'p' => $this->kxv($k,$x,is_array($v)?'(?)':'?')->to_array()->unshift('and')->get(),'v' => $v]);

		return $this;
	}

	public function or (string $k , $x , $v): self
	{
		$this->kxv->push([ 'p' => $this->kxv($k,$x,is_array($v)?'(?)':'?')->to_array()->unshift('or')->get(),'v' => $v]);

		return $this;
	}
 
	private function is_where_condition_keyword_ok (c_array $keyword): bool
	{		 
		return true;
	}

	/**
	 * @throws \Exception : if the field name in the WHERE condition is incorrect then i will throw a standard exception
	 * 
	 *  
	 */
	private function done (): c_orm
	{
		/* make parameter place holder
		 * 
		 */
		$this->kxv->for_each(function ($k , $v)
		{
			if ($v['v'] === null)
			{
				unset($v['p'][3]);
				unset($v['v']);
			}

			$this->where->push($v);
		});
		$this->kxv->free();

		return $this->c_orm;
	}

	public function like_right (string $k , mixed $v , bool $and_or = true): self
	{
		return $and_or ? $this->and($k,'like',$v . '%') : $this->or($k,'like',$v . '%');
	}

	public function like_left (string $k , mixed $v , bool $and_or = true): self
	{
		return $and_or ? $this->and($k,'like','%' . $v) : $this->or($k,'like','%' . $v);
	}

	public function like (string $k , mixed $v , bool $and_or = true): self
	{
		return $and_or ? $this->and($k,'like','%' . $v . '%') : $this->or($k,'like','%' . $v . '%');
	}

	public function is_null (string $k , bool $and_or = true): self
	{
		return $and_or ? $this->and($k,'is null',null) : $this->or($k,'is null',null);
	}

	public function is_not_null (string $k , bool $and_or = true): self
	{
		return $and_or ? $this->and($k,'is not null',null) : $this->or($k,'is not null',null);
	}

	public function between (string $k , $a , $b , bool $and_or = true): self
	{
		return $and_or ? $this->and($k,'between',$a)->and('','',$b) : $this->or($k,'between',$a)->and('','',$b);
	}

	public function in (string $k , array $v , bool $and_or = true): self
	{
		return $and_or ? $this->and($k,'in',$v) : $this->or($k,'in',$v);
	}

	public function not_in (string $k , array $v , bool $and_or = true): self
	{
		return $and_or ? $this->and($k,'not in',$v) : $this->or($k,'not in',$v);
	}

	public function column (string $a , string $x , string $b , bool $and_or = true): self
	{
		return $and_or ? $this->and($a . ' ' . $x . ' ' . $b,'',null) : $this->or($a . ' ' . $x . ' ' . $b,'',null);
	}

	public function raw (string $sql , bool $and_or = true): self
	{
		return $and_or ? $this->and($sql,'',NULL) : $this->or($sql,"",NULL);
	}
	public function select (): i_bindx
	{
		return $this->done()->select();
	}

	public function update (): i_update
	{
		return $this->done()->update();
	}

	public function insert (): i_insert
	{
		return $this->done()->insert();
	}

	public function delete (): i_delete
	{
		return $this->done()->delete(); 
	}
	public function order (): i_order_by
	{
		return $this->done()->order();
	}
	public function group (): i_group_by
	{
		return $this->done()->group();
	}
}

interface i_order_by
{

	public function asc (string $order): self;

	public function desc (string $order): self;

	public function by (): c_orm;
}

class c_order_by extends c_base_class implements i_order_by
{
	private c_orm $c_orm;
	private c_array $order_by;

	public function __construct (\WeakReference $w)
	{
		$this->c_orm = $w->get();
		$this->order_by = gf()->reflection->property($this->c_orm,'order_by')->get();
	}

	public function asc (string $order): self
	{
		$this->order_by->push([ $order,'asc']);

		return $this;
	}

	public function desc (string $order): self
	{
		$this->order_by->push([ $order,'desc']);
		return $this;
	}

	private function is_orderby_field_name_ok (c_array $field): bool
	{
		return $this->c_orm->is_field_name_ok(...$field->get());
	}

	/** <
	 * 
	 * @return c_orm
	 * @throws \Exception
	 * 
	 */
	public function by (): c_orm
	{
		$order_by_field = $this->order_by->column(0);if ($this->is_orderby_field_name_ok($order_by_field) === FALSE)/* > */
		{
			return gf()->exception->throw(130007,'the field name ' . $order_by_field->to_string() . 'in order by condition is incorrect');
		}

		return $this->c_orm;
	}
}

interface i_update
{

	public function done (array $set): i_bindx;
}

interface i_insert
{

	public function done (array $insert): i_bindx;
}

interface i_delete
{

	public function done (): i_bindx;
}

class c_delete extends c_base_class implements i_delete
{
	private c_orm $c_orm;
	private c_trans $it;

	public function __construct (\WeakReference $w)
	{
		$this->c_orm = $w->get();
		$this->it = gf()->reflection->property($this->c_orm,'it')->get();
	}

	public function done (): i_bindx
	{
		if (empty($this->c_orm->get_where()))
		{
			gf()->exception->throw(130008,'the condition of the deleted SQL statement is missing');
		}

		$bindx = $this->c_orm->get_where_bindx($this->it->query("delete from " . $this->c_orm->get_table_name() . " " . $this->c_orm->get_where()));
		$this->c_orm->sql_free();
		return $bindx;
	}
}

class c_insert extends c_base_class implements i_insert
{
	private c_orm $c_orm;
	private c_trans $it;

	public function __construct (\WeakReference $w)
	{
		$this->c_orm = $w->get();
		$this->it = gf()->reflection->property($this->c_orm,'it')->get();
	}

	public function done (array $insert): i_bindx
	{
		/**
		 * @var c_array $insert
		 */
		$insert = gf()->fun->array->new_with_array($insert);
		if ($this->c_orm->is_field_name_ok(...$insert->keys()
			->get()) === FALSE)
		{
			gf()->exception->throw(130009,'failed to check field validation status');
		}

		if ($insert->count() === 0)
		{
			gf()->exception->throw(130000,'You will get an error when passing an empty array to the ORM insert method');
		}

		$sql = "insert into " . $this->c_orm->get_table_name() . "(";
		$insert->for_each(function ($k , $v) use ( &$sql)
		{
			$sql .= $k . ",";
		});
		$sql = rtrim($sql,',') . ") values (";
		$insert->for_each(function ($k , $v) use ( &$sql)
		{
			$sql .= '?,';
		});
		$sql = rtrim($sql,',') . ")";

		/**
		 * @var i_bindx $bindx
		 */
		$bindx = $this->it->query($sql);
		$insert->for_each(function ($k , $v) use ( $bindx)
		{
			$bindx->ax($v);
		});

		$insert->free();
		$this->c_orm->sql_free();
		return $bindx;
	}
}

class c_update extends c_base_class implements i_update
{
	private c_orm $c_orm;
	private c_trans $it;

	public function __construct (\WeakReference $w)
	{
		$this->c_orm = $w->get();
		$this->it = gf()->reflection->property($this->c_orm,'it')->get();
	}

	/**
	 * 
	 * @param 	array $set
	 * @return 	\hx\db\i_bindx
	 * @throws	\Exception
	 * 
	 */
	public function done (array $set): i_bindx
	{
		/** <
		 * 
		 * @var c_array $update
		 */
		$update = gf()->fun->array->new_with_array($set);$update_field_name =$update->keys(); if ($this->c_orm->is_field_name_ok(...$update_field_name->get()) === FALSE)
		{
			gf()->exception->throw(130004, gf()->fun->cc->cyan($update_field_name->to_string_without_newlines())->get().' failed to check field validation status');
		}

		if ($update->count() === 0)
		{
			gf()->exception->throw(130003,'the content of the field to be updated cannot be empty');
		}
		
		# where
		#
		#
		if(empty($this->c_orm->get_where()))
		{
			gf()->exception->throw(130010, 'the updated condition is missing');
		}
		
		# update set ...
		#
		#
		$data = $this->dc()->new();$data->sql = "update " . $this->c_orm->get_table_name() . " set";$update->for_each(function ($k , $v) use ( $data)
		{
			$data->sql .=  ' '.$k . ' = ? ,';
		});
		$data->sql = rtrim($data->sql,',');

		# where condition
		#
		#
		$data->sql.= $this->c_orm->get_where();
		
		# bindx
		#
		#
		$data->update = $update;$data->bindx = $this->it->query($data->sql);$data->update->for_each(function ($k , $v) use ( $data)
		{
			$data->bindx->ax($v);
		});		
		$this->c_orm->get_where_bindx($data->bindx);
	
		$update->free();$this->c_orm->sql_free();
		
		return $data->bindx;
		/* > */
	}
}

class c_table_metadata_description extends c_base_class
{
	private static ?c_stdclass $md = NULL;
	private c_orm $c_orm;

	public function __construct (\WeakReference $w)
	{
		$this->c_orm = $w->get();
	}

	public function get (): c_array
	{
		self::$md ??= gf()->fun->stdclass->new();
		if (self::$md->exist($this->c_orm->get_table_name()) === FALSE)
		{
			self::$md->set($this->c_orm->get_table_name()/* the full name of this table strictly contains a database prefix.*/,$this->c_orm->get_all_fields_in_current_table());
		}
		return self::$md->get($this->c_orm->get_table_name());
	}
}

/**
 * 
 * @author Administrator
 * @property	i_sql			$query
 */
abstract class c_orm extends c_base_class
{
	private ?string $table_name = null;
	private ?string $connection_key = null;
	private ?string $database_name = null;
	private ?string $database_env_json_file_path = null;
	private ?c_array $field = null;
	private ?c_array $from = null;
	protected ?c_array $group_by = null;
	private ?i_db $db = null;
	private ?c_table_metadata_description $tmd = null;
	protected ?c_array $limit = null;
	protected ?c_array $where = null;
	protected ?i_trans $it = null;
	protected ?c_array $order_by = null;
	protected ?c_array $join = null;

	public function update (): i_update
	{
		return new c_update($this->ini_db_config()->make_weak_reference());
	}

	/** <
	 * 
	 * @param 	array $insert
	 * @return 	\hx\db\i_query
	 * @throws	\Exception
	 * 
	 */
	public function insert ( ): i_insert
	{
		return  new c_insert($this->ini_db_config()->make_weak_reference());
	}
	/* > */
	public function get_all_fields_in_current_table (): c_array
	{
		/** <
		 * @var c_array $ar
		 */
		$ar = gf()->fun->array->new();$this->it->query("show fields from " . $this->get_table_name())->go()->for_each(function ($k , $v) use ( $ar)/* > */
		{
			$ar->push($this->get_table_name() . '.' . $v->get('Field'));
		});

		return $ar;
	}

	public function is_field_name_ok (...$field): bool
	{
		/** <
		 * @var c_array $all_fields
		 * 
		 * 
		 */
		
		$all_fields = $this->tmd->get();foreach ($field as $v)/* > */
		{
			$ar_name = explode('.',$v);
			$v = $this->get_table_name() . '.' . array_pop($ar_name);
			if ($all_fields->search($v)->ok() === FALSE)
			{
				return false;
			}
		}
		return true;
	}

	public function get_database_name (): string
	{
		return $this->database_name;
	}

	public function get_limit (): string
	{
		if ($this->limit->count() === 0)
		{
			return '';
		}

		return 'limit ?,?';
	}

	public function get_limit_bindx (i_bindx $bindx): i_bindx
	{
		/* <
		 * 
		 */
		if ($this->limit->count() > 0)
		{
			$bindx->ax($this->limit->value_with_index(0))->ax($this->limit->value_with_index(1));
		}
		/* > */

		return $bindx;
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

	public function get_order_by (): string
	{
		if ($this->order_by->count() === 0)
		{
			return '';
		}

		/* < 
		 * 
		 */
		$order_by = 'order by ';$this->order_by->for_each(function ($k , $v) use ( &$order_by)
		{
			$order_by .= gf()->fun->array->new_with_array($v)->implode(' ') . ",";
		});
		/* > */

		return rtrim($order_by,',');
	}

	public function order (): i_order_by
	{
		return new c_order_by($this->ini_db_config()->make_weak_reference());
	}

	public function get_where_bindx (i_bindx &$bindx): i_bindx
	{
		$this->where->for_each(function ($k , $v) use ( $bindx)
		{
			if (array_key_exists('v',$v))
			{
				$bindx->ax($v['v']);
			}
		});
		return $bindx;
	}

	public function limit (): i_limit
	{
		return new c_limit($this->ini_db_config()->make_weak_reference());
	}

	public function select (): i_bindx
	{
		return (new c_select($this->ini_db_config()->make_weak_reference()))->done();
	}

	public function get_join (): string
	{
		return $this->join->implode(' ');
	}

	public function join ()
	{
		return new class($this->ini_db_config()->make_weak_reference()) extends c_base_class
		{
			private c_orm $c_orm;
			private c_array $join;
			private ?string $join_table = null;
			private ?e_join_type $e_join_type = null;

			public function __construct (\WeakReference $w)
			{
				$this->c_orm = $w->get();
				$this->join = gf()->reflection->property($this->c_orm,'join')->get();
			}

			public function left ($table)
			{
				$this->join_table = $table;
				$this->e_join_type = e_join_type::left;
				return $this;
			}

			public function right ($table)
			{
				$this->join_table = $table;
				$this->e_join_type = e_join_type::righ;
				return $this;
			}

			public function inner ($table)
			{
				$this->join_table = $table;
				$this->e_join_type = e_join_type::inner;
				return $this;
			}

			public function on (string $a , string $b): c_orm
			{
				match ($this->e_join_type) {
					e_join_type::left => $this->join->push('left join ',$this->join_table," on ",$a,' = ',$b) ,
					e_join_type::righ => $this->join->push('right join ',$this->join_table," on ",$a,' = ',$b) ,
					e_join_type::inner => $this->join->push('inner join ',$this->join_table," on ",$a,' = ',$b)
				};

				return $this->c_orm;
			}
		};
	}

	public function __get ($k): mixed
	{
		if ($k === 'query')
		{
			return $this->query();
		}
		return $this;
	}

	public function field (string ...$fields): self
	{
		$this->ini_db_config()->field->push(...$fields);

		return $this;
	}

	public function get_field (): string
	{
		if ($this->field->count() === 0)
		{
			$this->field->push('*');
		}

		return rtrim($this->field->implode(','));
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

	public function group (): i_group_by
	{
		return new c_group_by($this->ini_db_config()->make_weak_reference());
	}

	public function get_group_by (): string
	{
		if ($this->group_by->count() === 0)
		{
			return '';
		}

		return ' group by ' . implode(',',$this->group_by->get());
	}

	public function get_where (): string
	{
		if ($this->where->count() === 0)
		{
			return '';
		}

		/**
		 * @var c_array $where_p
		 */
		$data = $this->dc()->new();
		$data->where_p = gf()->fun->array->new();
		$this->where->for_each(function ($k , $v) use ( $data)
		{
			$data->where_p->push(...$v['p']);
		});
		$data->where_p->unshift('where',1,'=',1);

		return $data->where_p->implode(' ');
	}

	public function delete (): i_delete
	{
		return new c_delete($this->ini_db_config()->make_weak_reference());
	}

	public function where (): i_where
	{
		return new c_where($this->ini_db_config()->make_weak_reference());
	}

	public function query ()
	{
		return new class($this->make_weak_reference()) extends c_base_class implements i_sql
		{
			private c_orm $c_orm;
			private c_trans $it;

			public function __construct (\WeakReference $w)
			{
				$this->c_orm = $w->get();
				$this->it = gf()->reflection->property($this->c_orm,'it')->get();
			}

			public function auto (string $sql , callable $on_query)
			{
				$this->it->auto(function (i_trans $it) use ( $sql , $on_query)
				{
					return $on_query($it->query($sql));
				});
			}

			public function sql ($sql): i_bindx
			{
				return $this->it->query($sql);
			}
		};
	}

	/* >
	 * <
	 *  
	 */
	public function sql_free (): self
	{
		
		$this->join->free();$this->field->free();$this->from->free();$this->where->free();$this->order_by->free();$this->limit->free();$this->group_by->free();

		return $this;
	}
	private function ini_sql (): self
	{
		$this->field 	??= gf()->fun->array->new();
		$this->from 	??= gf()->fun->array->new();
		$this->join 	??= gf()->fun->array->new();
		$this->where 	??= gf()->fun->array->new();
		$this->order_by ??= gf()->fun->array->new();
		$this->group_by ??= gf()->fun->array->new();
		$this->limit 	??= gf()->fun->array->new();
		
		return $this;
	}
	/* > */
	private function ini_db_config (): self
	{
		$this->database_env_json_file_path ??= $this->on_set_open_with_env_json();
		if ($this->connection_key === NULL)
		{
			$this->set_connection_key($this->on_set_connection_key());
		}

		return $this->ini_sql()->ini_table_matedata_description();
	}

	private function ini_table_matedata_description (): self
	{
		if ($this->tmd === NULL)
		{
			$this->tmd = new c_table_metadata_description($this->make_weak_reference());
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

	private function set_connection_key (string $connection_key = 'default'): self
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

	protected abstract function on_set_connection_key (): string;

	protected abstract function on_db_driver (): i_db;
}