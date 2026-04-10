<?php
namespace hx\fun\debug;

use hx\c_base_class;

class c_console_color extends c_base_class
{
	private string $s;
	private \WeakReference $debug;

	public function __construct (\WeakReference $debug = NULL)
	{
		$this->s = '';
		$this->debug = $debug === NULL ? gf()->fun->debug->make_weak_reference() : $debug;
	}

	public function red (string $s = ''): c_console_color
	{
		$this->s .= "\e[38;5;196m" . $s . "\e[0m";

		return $this;
	}

	public function green (string $s = ''): c_console_color
	{
		$this->s .= "\e[38;5;41m" . $s . "\e[0m";
		return $this;
	}

	public function blue (string $s = ''): c_console_color
	{
		$this->s .= "\e[38;5;27m" . $s . "\e[0m";
		return $this;
	}

	public function pink (string $s = ''): c_console_color
	{
		$this->s .= "\e[38;5;211m" . $s . "\e[0m";
		return $this;
	}

	public function yellow (string $s = ''): c_console_color
	{
		$this->s .= "\e[38;5;220m" . $s . "\e[0m";
		return $this;
	}

	public function white (string $s = ''): c_console_color
	{
		$this->s .= "\e[38;5;7m" . $s . "\e[0m";
		return $this;
	}

	public function anl (): c_console_color
	{
		$this->s .= "\n";
		return $this;
	}

	public function as (string $s = ''): c_console_color
	{
		$this->s .= $s;
		return $this;
	}

	public function echo (): c_console_color
	{
		$this->debug->get()->echo_with_nl($this->s);
		unset($this->s);
		$this->s = '';
		return $this;
	}

	public function get (): string
	{
		return $this->s;
	}
}