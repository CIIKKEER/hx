<?php
/*
 <
 */
declare(strict_types = 1);

/* Copyright 2026 BREEZZEER
 * SPDX-License-Identifier: Apache-2.0
 *
 >
 */
namespace hx\fun\debug;

use hx\c_base_class;

class c_console_color extends c_base_class
{
	protected string $s;
	private \WeakReference $debug;

	public function __construct (\WeakReference $debug = NULL)
	{
		$this->s = '';
		$this->debug = $debug === NULL ? gf()->fun->debug->make_weak_reference() : $debug;
	}

	public function red (string $s = ''): self
	{
		$this->s .= "\e[38;5;196m" . $s . "\e[0m";

		return $this;
	}

	public function green (string $s = ''): self
	{
		$this->s .= "\e[38;5;41m" . $s . "\e[0m";
		return $this;
	}

	public function blue (string $s = ''): self
	{
		$this->s .= "\e[38;5;27m" . $s . "\e[0m";
		return $this;
	}

	public function cyan (string $s = ''): self
	{
		$this->s .= "\e[38;5;14m" . $s . "\e[0m";
		return $this;
	}

	public function pink (string $s = ''): self
	{
		$this->s .= "\e[38;5;211m" . $s . "\e[0m";
		return $this;
	}

	public function yellow (string $s = ''): self
	{
		$this->s .= "\e[38;5;220m" . $s . "\e[0m";
		return $this;
	}

	public function white (string $s = ''): self
	{
		$this->s .= "\e[38;5;7m" . $s . "\e[0m";
		return $this;
	}

	public function anl (): self
	{
		$this->s .= "\n";
		return $this;
	}

	public function as (string $s = ''): self
	{
		$this->s .= $s;
		return $this;
	}

	public function echo (): self
	{
		$this->debug->get()->echo_with_nl($this->s);
		return $this->free();
	}

	private function free (): self
	{
		unset($this->s);
		$this->s = '';
		return $this;
	}

	public function get (): string
	{
		$s = $this->s;
		$this->free();
		return $s;
	}
}