<?php
declare(strict_types = 1)
	;
namespace hx\fun\array;

use hx\c_base_class;
use hx\c_ok_error;
use hx\i_ok_error;
use hx\fun\regx\c_regx;

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

	public function keys (): c_array
	{
		return $this->new()->new_with_array(array_keys($this->get()));
	}

	public function column (int|string|null $column_key , int|string|null $index_key = null): c_array
	{
		return $this->new()->new_with_array(array_column($this->get(),$column_key,$index_key));
	}

	public function search (mixed $v): i_ok_error
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

	public function unshift (...$v): self
	{
		array_unshift($this->get(),...$v);
		return $this;
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

	public function map (callable $on_map): self
	{
		$this->m_ar = array_map($on_map,$this->get());
		return $this;
	}

	public function free (): self
	{
		unset($this->m_ar);
		$this->m_ar = [ ];
		return $this;
	}

	public function merge_x_row_2_single (): self
	{
		/**
		 * @var c_array $ar
		 */
		$ar = gf()->fun->array->new();
		$this->for_each(function ($k , $v) use ( $ar)
		{
			$ar->push(...$v);
		});
		return $ar;
	}

	public function value_with_index (int $index): mixed
	{
		return $this->get()[$index];
	}

	public function to_string_without_newlines (): string
	{
		return gf()->fun->regx->preg_replace('/\s+/',' ',str_replace([ "\t","\n","\r"],'',$this->to_string()));
	}

	public function to_string (): string
	{
		return gf()->fun->debug->print_r_to_string($this->get());
		;
	}
}