<?php
namespace hx;

interface i_ok_error
{

	public function ok (mixed $v = null): mixed;

	public function error (mixed $v = null): mixed;
}

class c_ok_error extends c_base_class implements i_ok_error
{
	protected ?bool $ok = null;

	protected function set_ok (bool $ok = TRUE): self
	{
		$this->ok = $ok;
		return $this;
	}

	public function ok (mixed $v = null): mixed
	{
		if (is_callable($v))
		{
			if ($this->ok === true)
			{
				$v();
			}
			return $this;
		}
		else
		{
			return $this->ok === true ? true : false;
		}
	}

	public function error (mixed $v = null): mixed
	{
		if (is_callable($v))
		{
			if ($this->ok === FALSE)
			{
				$v();
			}
			return $this;
		}
		else
		{
			return $this->ok === false ? true : false;
		}
	}
}