<?php
declare(strict_types = 1)
	;
namespace hx\db\mysqli;

use hx\db\i_db;
use hx\db\i_query;
use hx\db\mysqli;
use hx\c_base_class;
use hx\db\mysqli\c_mysql_connection_info;
use function hx\gf_hx;
use hx\fun\stdclass\c_stdclass;
use hx\db\i_bindx;
use hx\db\i_trans;
use hx\fun\array\c_array;
use hx\db\i_query_status;

/**
 *
 * @desc	read mysql database data by mysql native driver mode
 * @author 	Administrator
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
		catch (\Throwable $e)
		{
			gf()->exception->throw_with_wrap(1000001,$e);
		}
		return $this;
	}

	/**
	 * @return c_mysqli
	 * 
	 */
	public function open_with_mysql_connection_info (i_mysql_connection_info $conn): c_mysqli
	{
		$this->m_mysql_connection_info = $conn;
		return $this;
	}

	/**
	 * 
	 * @param 	c_mysql_connection_info $conn
	 * @return 	c_mysqli
	 * @throws	\Exception
	 */
	private function open (c_mysql_connection_info $conn): c_mysqli
	{
		$this->m_mysqli = new \mysqli();

		# mysqli::connect(?string $hostname=null, ?string $username=null, ?string $password=null, ?string $database=null, ?int $port=null, ?string $socket=null) : bool
		#
		#
		try
		{
			$this->m_mysqli->connect($conn->ip(),$conn->user(),$conn->password(),$conn->database(),$conn->port());
			$this->m_mysqli->set_charset('utf8mb4');
		}
		catch (\Throwable $e)
		{
			$this->m_mysqli = null;
			gf()->exception->throw_with_wrap(1000002,$e);
		}
		return $this;
	}

	/* < create database transaction 
	 * 
	 */
	public function connect (string $connection_key = 'default'): i_trans
	{
		$db = $this->new()->open($this->m_mysql_connection_info->$connection_key);return new c_trans($db->make_weak_reference());
	}
	
	public function get_db_information (string $connection_key = 'default'): c_stdclass
	{
		$db = $this->new()->open($this->m_mysql_connection_info->$connection_key);
		$db->dc()->db->server_info = $db->m_mysqli->get_server_info();
		$db->dc()->db->host_info = $db->m_mysqli->host_info;
		
		return $db->dc()->db;
	}
	/* > */
	public function get_connection_info (): c_mysql_connection_info
	{
		return $this->m_mysql_connection_info;
	}
}

class c_trans extends c_base_class implements i_trans
{
	private c_mysqli $c_mysqli;

	public function __construct (\WeakReference $c_mysqli)
	{
		$this->c_mysqli = $c_mysqli->get();
	}

	/* < begin transaction
	 * 
	 */
	public function auto (callable $on_transaction): i_trans
	{
		$ex = null;
		try 
		{
			$this->begin();$ok = $on_transaction($this);
		}
		catch (\Throwable $e)
		{
			$ok = false;
			$ex = $e;
		}
		
		in_array ($ok,[null,true],TRUE) ? $this->commit() : $this->rollback();if($ex !== null)
		{
			gf()->exception->throw_with_wrap(1000007, $ex);
		}
		
		return $this;
	}
	/* > */
	public function query (string $sql): i_bindx
	{
		return new c_bind_parameter($this->c_mysqli->make_weak_reference(),$sql);
	}

	public function begin (): i_trans
	{
		$this->c_mysqli->m_mysqli->begin_transaction();
		return $this;
	}

	public function commit (): i_trans
	{
		$this->c_mysqli->m_mysqli->commit();
		return $this;
	}

	public function rollback (): i_trans
	{
		$this->c_mysqli->m_mysqli->rollback();
		return $this;
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
	private c_mysqli		$c_mysqli;
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
	
	
	public function __construct (\WeakReference $c_mysqli , string $sql)
	{
		$this->c_mysqli				= $c_mysqli->get();
		$this->mysqli 				= $this->c_mysqli->m_mysqli;
		$this->sql_raw				= $sql;
		$this->sql 					= $sql;
		$this->sqlx					= '';
		$this->px 					= gf()->fun->stdclass->new();
		$this->pb					= gf()->fun->stdclass->new();
		$this->bind_parameter		= gf()->fun->stdclass->new();
		$this->index 				= 0;
	}
	/* > */

	/**
	 * 
	 * {@inheritDoc}
	 * @see 			\hx\db\i_bindx::go()
	 *  
	 * 
	 * 
	 */
	public function go (callable $on_go = null): i_query
	{
		try
		{
			/* < prepare to bind parameter */$this->trim_sql_comment_as_empty()->create_parameter_place_holder();$this->stmt = $this->mysqli->prepare($this->sqlx);$this->create_parameter_binding();/* > */
			if ($on_go !== null)
			{
				$on_go("\n" . $this->get_sql_debug());
			}

			return new c_query($this->make_weak_reference());
		}
		catch (\Throwable $e)
		{
			gf()->exception->throw(1000006,$e->getMessage() . "\n" . $this->get_sql_debug() . "\n");
		}
	}

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
		gf()->fun->debug->echo_with_nl($err)->echo_with_nl($this->get_sql_debug());
	}

	public function get_sql_debug (): string
	{
		$ext = is_array($this->bind_parameter->value) ? vsprintf(str_replace('?','%s',$this->sqlx),$this->bind_parameter->value) : $this->sqlx;

		/* < */
		return gf()->fun->debug->cc->new()->yellow('DEBUG.')->red("SQL   : ")->cyan('RAW \ '	)->anl()->as('                   ')->as( $this->sql_raw)
										  ->anl()->yellow('DEBUG.')->red("SQL   : ")->pink('TRI \ '	)->anl()->as('                   ')->as( $this->sql)
										  ->anl()->yellow('DEBUG.')->red("SQL   : ")->green('EXT \ '	)->anl()->as('                   ')->as( $ext)
										  ->get();
		/* > */
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

		/* < map bind parameter to placeholder */$ar_sql = $this->convert_place_holder_2_array($this->sql);$ar_count = count($ar_sql);foreach ($ar_sql as $k => $v)
		{
			if ($this->px->to_array()->count() !== $ar_count - 1)
			{
				$this->on_error(gf()->fun->cc->new()->blue('SQL.PREPARE : ')->as('The number of MySQL bound parameter variables does not match the target placeholders')->get());
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

	private function convert_place_holder_2_array (string $str , string $delimiter = '?'): array
	{
		/* < eliminate the effect of the original ? character
		 * 
		 */
		$placeholder = "\x00";$protected = [ ];$str = preg_replace_callback
		(
			'/\\\\.|"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'|`[^`\\\\]*(?:\\\\.[^`\\\\]*)*`/'
			,
			function ($m) use ( &$protected , $placeholder)
			{
				$key 			 = $placeholder . count($protected);
				$protected[$key] = $m[0];

				return $key;
			}
			,
			$str
		);

		$parts = explode($delimiter,$str);foreach ($parts as &$part)
		{
			$part = str_replace(array_keys($protected),array_values($protected),$part);
		}

		return $parts;
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
		$this->sql = preg_replace('/\/\*.*?\*\/|--.*?(?:\n|$)|#.*?(?:\n|$)/s','',$this->sql);

		return $this;
	}

	public function for_each (callable $on_for_each): i_query
	{
		return $this->go()->for_each($on_for_each);
	}

	public function ax (mixed $ax): self
	{
		match (gettype($ax)) {
			"integer" , "bool" , "boolean" => $this->ai($ax) ,
			"double" => $this->ad($ax) ,
			"string" => $this->as($ax) ,
			default => gf()->exception->throw(1000008,"the type of the bound variable is incorrect.\n")
		};
		return $this;
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

	/* < execute SQL operations including but not limited to, SELECT, UPDATE, and DELETE
	 * 
	 *
	 */ 
	/**
	 * 
	 * @return c_query
	 * @throws \Exception
	 * 
	 */
	private function execute (): c_query
	{
		try 
		{
			$r = $this->bp->stmt->execute();
			$mr = $this->bp->stmt->get_result();
			if ($mr !== false)
			{
				$this->mr = $mr;
				$this->fetch_assoc();
			}
		}
		catch (\Throwable $e)
		{
			gf()->exception->throw_with_wrap(1000004, $e);
		}
		
		return $this;
	}
	/* > */
	private function fetch_assoc (): c_query
	{
		/* < get data */$i = 0;for (;;)
		{
			$row = $this->mr->fetch_assoc();if (is_array($row) === FALSE)
			{
				break;
			}
			$this->data->add(strval($i++), gf()->fun->stdclass->new_with_array($row));
		}
		/* > */
		$this->mr->close();
		$this->bp->stmt->close();
		return $this;
	}

	/**
	 * 
	 * {@inheritDoc}
	 * @see \hx\db\i_query::for_each()
	 * 
	 */
	public function for_each (callable $on_for_each): i_query
	{
		$this->data->for_each($on_for_each);
		return $this;
	}

	/**
	 * 
	 * {@inheritDoc}
	 * @see \hx\db\i_query::get_single_row()
	 * 
	 */
	public function get_single_row (): c_stdclass
	{
		/** <
		 * @var c_stdclass $data
		 */
		$data = gf()->fun->stdclass->new();$this->for_each(function ($k , $v) use (&$data)
		{
			$data = $v;
			return true;
		});
		/* > */
		return $data;
	}

	public function get_single_value (): mixed
	{
		/* < */return $this->get_single_row()->to_array()->shift();/* > */
	}

	public function get_affected_rows (): int
	{
		return $this->bp->stmt->affected_rows;
	}

	public function get_insert_id (): int
	{
		return $this->bp->stmt->insert_id;
	}
}
