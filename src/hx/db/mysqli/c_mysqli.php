<?php
namespace hx\db\mysqli;

use hx\db\i_db;
use hx\db\i_query;
use hx\db\mysqli;
use hx\c_base_class;
use hx\db\mysqli\c_mysql_connection_info;
use function hx\gf_hx;
use hx\fun\stdclass\c_stdclass;
use hx\db\i_bindx;
use hx\db\i_transaction;

/**
 *
 * @desc	read mysql database data by mysql native driver mode
 * @author 	Administrator
 * 
 * 
 */
class c_mysqli extends c_base_class implements i_db
{
	public ?c_mysql_connection_info $m_mysql_connection_info = null;

	/**
	 * 
	 * @var \mysqli m_mysqli
	 */
	public ?\mysqli $m_mysqli = null;

	public function __destruct ()
	{
		if ($this->m_mysqli !== null)
		{
			$this->m_mysqli->close();
		}
	}

	public function open_with_env_json (string $env_file_path): i_db
	{
		try
		{
			$this->m_mysql_connection_info = gf()->config->mysql->get_with_env_json($env_file_path);
		}
		catch (\Exception $e)
		{
			gf()->exception->throw_with_wrap(1000001,$e);
		}
		return $this;
	}

	public function get_db_information (): string
	{
		return $this->m_mysqli->get_server_info() . ' ' . $this->m_mysqli->host_info;
	}

	/**
	 * @return c_mysqli
	 * 
	 */
	public function open_with_mysql_connection_info (c_mysql_connection_info $conn): c_mysqli
	{
		$this->m_mysql_connection_info = $conn;
		return $this;
	}

	private function open (c_mysql_connection_info $conn): c_mysqli
	{
		$this->m_mysqli = new \mysqli();

		try
		{
			$this->m_mysqli->connect($conn->ip(),$conn->user(),$conn->password(),null,$conn->port());
		}
		catch (\mysqli_sql_exception $e)
		{
			gf()->fun->debug->die($e->getMessage());
		}
		return $this;
	}

	public function connect (string $connection_key = 'default'): i_transaction
	{
		return new c_transaction($this->new()->open($this->m_mysql_connection_info->$connection_key));
	}

	public function query (string $sql): i_bindx
	{
		return new c_bind_parameter($this->make_weak_reference(),$sql);
	}
}

class c_transaction extends c_base_class implements i_transaction
{
	private c_mysqli $c_mysqli;

	public function __construct (c_mysqli $c_mysqli)
	{
		$this->c_mysqli = $c_mysqli;
	}

	public function auto (callable $on_transaction): i_transaction
	{
		/* < begin transaction
		 * 
		 */
		$this->c_mysqli->m_mysqli->begin_transaction();$on_transaction($this->c_mysqli) === false ? $this->c_mysqli->m_mysqli->rollback() : $this->c_mysqli->m_mysqli->commit();
		return $this;
		/* > */
	}
}

class c_bind_parameter extends c_base_class implements i_bindx
{
	/* < */
	private string 			$sql;
	private string 			$sqlx;
	private string 			$sql_raw; 
	private int 			$index;
	private c_stdclass 		$px;
	private c_stdclass		$pb;
	private c_stdclass 		$bind_parameter;
	
	/**
	 * 
	 * @var \mysqli_stmt
	 */
	public ? \mysqli_stmt 	$stmt = NULL;
	
	/**
	 * 
	 * @var \mysqli
	 */
	public \mysqli 			$mysqli;
	
	public function __construct (\WeakReference $mysqli , string $sql)
	{
		$this->mysqli 				= $mysqli->get()->m_mysqli;
		$this->sql_raw				= $sql;
		$this->sql 					= $sql;
		$this->sqlx					= '';
		$this->px 					= gf()->fun->stdclass->new();
		$this->pb					= gf()->fun->stdclass->new();
		$this->bind_parameter		= gf()->fun->stdclass->new();
		$this->index 				= 0;
	}
	/* > */
	public function ai (int $i): i_bindx
	{
		$this->px->{$this->index++} = $i;
		return $this;
	}

	public function aia (array $ia): i_bindx
	{
		$this->px->{$this->index++} = $ia;
		return $this;
	}

	public function ad (float $d): i_bindx
	{
		$this->px->{$this->index++} = $d;
		return $this;
	}

	public function ada (array $da): i_bindx
	{
		$this->px->{$this->index++} = $da;
		return $this;
	}

	public function as (string $s): i_bindx
	{
		$this->px->{$this->index++} = $s;
		return $this;
	}

	public function asa (array $sa): i_bindx
	{
		$this->px->{$this->index++} = $sa;
		return $this;
	}

	private function on_error (string $err)
	{
		gf()->fun->debug->echo_with_nl($err)->echo_with_nl($this->get_sql_debug())->die;
	}

	private function get_sql_debug (): string
	{
		return /* < */gf()->fun->debug->cc->yellow('DEBUG.')->red("SQL : ")->cyan('RAW : '	)->anl()->as( $this->sql_raw)
										  ->anl()->yellow('DEBUG.')->red("SQL : ")->pink('TRI : '	)->anl()->as( $this->sql)
										  ->anl()->yellow('DEBUG.')->red("SQL : ")->green('EXT : '	)->anl()->as( $this->sqlx)
										  ->get()
																						;/* > */
	}

	private function create_parameter_place_holder (): c_bind_parameter
	{
		$this->px->for_each(function ($k , $v)
		{
			if (is_int($v))
			{
				$pb = [ 'i' => $v];
			}
			elseif (is_float($v))
			{
				$pb = [ 'd' => $v];
			}
			elseif (is_string($v))
			{
				$pb = [ 's' => $v];
			}
			elseif (is_array($v))
			{
				$pbk = '';
				$pbv = [ ];
				foreach ($v as $vv)
				{
					if (is_int($vv))
					{
						$pbk .= 'i';
					}
					elseif (is_float($vv))
					{
						$pbk .= 'd';
					}
					elseif (is_string($vv))
					{
						$pbk .= 's';
					}
					$pbv[] = $vv;
				}
				$pb = [ $pbk => $pbv];
			}

			$this->pb->$k = $pb;
		});

		/* < map bind parameter to placeholder */$ar_sql = explode('?',$this->sql);$ar_count = count($ar_sql);foreach ($ar_sql as $k => $v)
		{
			if($this->px->to_array()->count()!== $ar_count-1)
			{
				$this->on_error(gf()->fun->cc->new()->yellow('The number of MySQL bound parameter variables does not match the target placeholders')->get());
			}
			
			if ($k !== ($ar_count - 1))
			{
				$this->sqlx .= $v . rtrim(str_repeat('?,',strlen(key($this->pb->$k))),',');
			}
			else
			{
				$this->sqlx .= $v;
			}
		}
		/* > */
		return $this;
	}

	private function create_parameter_binding (): c_bind_parameter
	{
		try
		{
			/* < Create mysqli parameter binding */$this->bind_parameter->type = '';$this->bind_parameter->value = [];$this->pb->for_each(function ($k , $v)
			{
				$this->bind_parameter->type .= key($v);
				$vv							 = current($v);if(is_array($vv))
				{
					$this->bind_parameter->value = array_merge($this->bind_parameter->value,$vv/* array */);
				}
				else 
				{
					$this->bind_parameter->value[] = current($v/* single value */);
				}
			});
		
			if(count($this->bind_parameter->value) > 0)
			{
				$ok = $this->stmt->bind_param($this->bind_parameter->type, ...$this->bind_parameter->value);
			}
		}
		catch (\ArgumentCountError $e)
		{
			$this->on_error(gf()->fun->cc->as($e->getMessage())->anl()
										 ->yellow('SQL.BIND.T : ')->as(gf()->fun->debug->print_r_to_string($this->bind_parameter->type))
										 ->yellow('SQL.BIND.V : ')->as(gf()->fun->debug->print_r_to_string($this->bind_parameter->value))
										 ->get());
		}
		
		/* > */
		return $this;
	}

	private function trim_sql_comment_as_empty (): c_bind_parameter
	{
		# $this->sql = preg_replace('/\/\*.*?\*\//','',$this->sql);
		#
		#
		$this->sql = preg_replace('/\/\*.*?\*\/|--.*?(?:\n|$)|#.*?(?:\n|$)/s','',$this->sql);

		return $this;
	}

	public function go (): i_query
	{
		try
		{
			/* < prepare to bind parameter */$this->trim_sql_comment_as_empty()->create_parameter_place_holder();$this->stmt = $this->mysqli->prepare($this->sqlx);$this->create_parameter_binding();/* > */
		}
		catch (\Exception $e)
		{
			$this->on_error($e->getMessage());
		}

		return new c_query($this->make_weak_reference());
	}

	public function for_each (callable $on_for_each): i_query
	{
		return $this->go()->for_each($on_for_each);
	}
}

class c_query extends c_base_class implements i_query
{
	private c_bind_parameter $bp;
	private c_stdclass $data;

	/**
	 * 
	 * @var \mysqli_result
	 */
	private ?\mysqli_result $mr = null;

	public function __construct (\WeakReference $pb)
	{
		$this->bp = $pb->get();
		$this->data = gf()->fun->stdclass->new();
		$this->execute();
	}

	public function __destruct ()
	{
		unset($this->data);
	}

	private function execute (): c_query
	{
		/* < execute SQL operations including but not limited to, SELECT, UPDATE, and DELETE
		 * 
		 *
		 */ 
		$this->bp->stmt->execute();$mr = $this->bp->stmt->get_result();if ($mr !== false)
		{
			$this->mr = $mr;
			$this->fetch_assoc();
		}
		/* > */
		return $this;
	}

	private function fetch_assoc (): c_query
	{
		/* < get data */$i = 0;for (;;)
		{
			$row = $this->mr->fetch_assoc();if (is_array($row) === FALSE)
			{
				break;
			}
			$this->data->add($i++, gf()->fun->stdclass->new_with_array($row));
		}
		/* > */
		$this->mr->close();
		$this->bp->stmt->close();
		return $this;
	}

	/**
	 *
	 * @param callable(string $k, c_stdclass $v): bool $on_for_each
	 *
	 * @return i_query
	 * 
	 * 
	 */
	public function for_each (callable $on_for_each): i_query
	{
		$this->data->for_each($on_for_each);
		return $this;
	}
}
