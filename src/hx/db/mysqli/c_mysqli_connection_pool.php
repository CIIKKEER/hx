<?php
namespace hx\db\mysqli;

use hx\c_base_class;
use hx\fun\array\c_array;
use Ds\Set;

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

	public function Set (string $k , \mysqli $mysqli): self
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
		/**
		 * 
		 * @var \mysqli $o
		 */
		$o = $this->get_pool()->value_with_index($k);

		if ($o === FALSE)
		{
			return false;
		}

		if ($o->ping() === FALSE)
		{
			$this->get_pool()->del($k);
			return false;
		}

		return $o;
	}
}