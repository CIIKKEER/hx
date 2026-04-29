<?php
namespace hx\fun\string;

use hx\c_base_class;
use hx\fun\array\c_array;
use hx\fun\debug\c_console_color;

class c_string extends c_base_class
{
	private ?c_array $s = null;

	public function __construct ()
	{
		$this->s = gf()->fun->array->new();
	}

	public function as (string ...$s): self
	{
		$this->s->push(...$s);
		return $this;
	}

	public function cc (): c_cc
	{
		return new c_cc($this->make_weak_reference());
	}

	public function get (): string
	{
		$s = $this->s->implode('');
		$this->s->free();
		return $s;
	}

	public function an (int|float ...$n): self
	{
		foreach ($n as $v)
		{
			$this->as(strval($v));
		}
		return $this;
	}

	public function ao (object|array $o): self
	{
		$this->as(gf()->fun->debug->print_r_to_string($o));

		return $this;
	}

	public function aa (array $a): self
	{
		foreach ($a as $v)
		{
			if (is_int($v) || is_float($v))
			{
				$this->an($v);
			}
			elseif (is_string($v))
			{
				$this->as($v);
			}
			else
			{
				$this->ao($v);
			}
		}
		return $this;
	}
	public function echo ():self 
	{
		echo $this->get()."\n";
		return $this;
	}
}

class c_cc extends c_console_color
{
	private c_string $c_string;

	public function __construct (\WeakReference $w)
	{
		$this->s = '';
		$this->c_string = $w->get();
	}

	public function done (): c_string
	{
		$this->c_string->as($this->get());
		return $this->c_string;
	}
}