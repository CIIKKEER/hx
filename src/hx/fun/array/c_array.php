<?php
namespace hx\fun\array;

use hx\c_base_class;
use hx\c_ok_error;
use hx\i_ok_error;

class c_array extends c_base_class
{
	private ?array $m_ar = null;

	public function __destruct ()
	{
		unset($this->m_ar);
	}

	public function empty (): bool
	{
		return count($this->get()) === 0 ? TRUE : FALSE;
	}

	public function count (): int

	{
		return count($this->get());
	}

	public function &get (): array
	{
		$this->m_ar ??= [ ];
		return $this->m_ar;
	}

	public function on_empty (callable $on_empty): c_array
	{
		/* < */$this->empty() ? $on_empty() : null;return $this;/* > */
	}

	public function for_each (callable $on_for_each): c_array
	{
		foreach ($this->get() as $k => $v)
		{
			$on_for_each($k,$v);
		}
		return $this;
	}

	/**
	 * 
	 * @param 	array $ar
	 * @return 	c_array
	 */
	public function new_with_array (array $ar): c_array
	{
		$o = $this->new();
		$o->m_ar = $ar;
		return $o;
	}

	public function append_with_array (array $ar): self
	{
		$this->m_ar = array_merge($this->get(),$ar);
		return $this;
	}

	public function on_ok (callable $on_ok): c_array
	{
		$this->empty() === FALSE ? $on_ok($this->get()) : null;
		return $this;
	}

	public function search (mixed $v)
	{
		return new class($this->make_weak_reference(),$v) extends c_ok_error
		{

			/** <
			 * 
			 * @param \WeakReference 	$w
			 * @param mixed				$v : search value
			 */
			public function __construct (\WeakReference $w , mixed $v)
			{
				$this->set_ok(array_search($v,$w->get()->get(),true) !== false ? true : false);
			}
			/* > */
		};
	}

	public function shift ()
	{
		$ar = array_shift($this->get());
		return $ar;
	}

	public function push (...$v): self
	{
		$this->m_ar = array_merge($this->get(),is_array($v) ? $v : [ $v]);
		return $this;
	}

	public function implode ($separator = '')
	{
		return implode($separator,$this->get());
	}

	public function free (): self
	{
		unset($this->m_ar);
		$this->m_ar = [ ];
		return $this;
	}
}