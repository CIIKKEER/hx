<?php
namespace hx\fun\array;

use hx\c_base_class;

class c_array extends c_base_class
{
	private array $m_ar;

	public function __destruct ()
	{
		unset($this->m_ar);
	}

	public function empty (): bool
	{
		return count($this->m_ar) === 0 ? TRUE : FALSE;
	}

	public function count (): int

	{
		return count($this->m_ar);
	}

	public function get (): array
	{
		return $this->m_ar;
	}

	public function on_empty (callable $on_empty): c_array
	{
		/* < */$this->empty() ? $on_empty() : null;return $this;/* > */
	}

	public function for_each (callable $on_for_each): c_array
	{
		foreach ($this->m_ar as $k => $v)
		{
			$on_for_each($k,$v);
		}
		return $this;
	}

	public function new_with_array ($ar): c_array
	{
		$o = $this->new();
		$o->m_ar = $ar;
		return $o;
	}

	public function on_ok (callable $on_ok): c_array
	{
		$this->empty() === FALSE ? $on_ok($this->m_ar) : null;
		return $this;
	}
}