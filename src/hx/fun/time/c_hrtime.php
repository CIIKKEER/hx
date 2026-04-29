<?php
namespace hx\fun\time;

use hx\c_base_class;

class c_hrtime extends c_base_class
{
	private $hrtime;

	public function create (bool $as_number = false): self
	{
		$this->hrtime = hrtime($as_number);
		return $this;
	}

	public function get (): array|int|float|false
	{
		return $this->hrtime;
	}

	public function diff_with_millisecond ()
	{
		return new class($this->make_weak_reference()) extends c_base_class
		{
			private c_hrtime $c_hrtime;
			private $start;
			private $end;
			private $elapse;

			public function __construct (\WeakReference $w)
			{
				$this->c_hrtime = $w->get();
			}

			public function do (callable $on_do): self
			{
				$this->start = $this->c_hrtime->create(true)->get();
				$on_do();
				$this->end = $this->c_hrtime->create(true)->get();
				return $this;
			}

			public function get ()
			{
				$this->elapse = ($this->end - $this->start);
				return $this->elapse / 1e6;
			}

			public function echo (): void
			{
				gf()->fun->cc->green('elapsed time : ')
					->yellow($this->get())
					->as(' milliseconds')
					->echo();
			}
		};
	}
}

