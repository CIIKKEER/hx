<?php
namespace hx\db\mysqli;

use hx\c_base_class;
use hx\fun\array\c_array;

class c_mysqli_connection_pool extends c_base_class
{
	private static ?c_array $pool = null;

	private function get_pool (): c_array
	{
		self::$pool ??= gf()->fun->array->new();
		return self::$pool;
	}

	private function set_pool (string $k , \mysqli $v): self
	{
		self::$pool->set($k,$v);
		return $this;
	}

	public function set (string|int $k , \mysqli $mysqli): self
	{
		if ($this->get_pool()->key_exists($k) === FALSE)
		{
			gf()->fun->debug->print_r($k);
			$this->set_pool($k,$mysqli);
		}
		return $this;
	}

	public function get (string|int $k): \mysqli|bool
	{
		/** <
		 * @var \mysqli $o
		 */
		$o = $this->get_pool()->value_with_index($k);if ($o === FALSE)
		{
			return false;
		}

		if ($o->ping() === FALSE/* mysql connection is died */)
		{
			$this->close($o)->get_pool()->del($k);
			
			return false;
		}

		return $o;
		/* > */
	}

	private function close (\mysqli $o): self
	{
		gf()->exception->try(fn () => $o->close());
		return $this;
	}

	public function free (): self
	{
		$this->get_pool()->for_each(function ($k , $v)
		{
			$this->close($v);
		});
		return $this;
	}
}