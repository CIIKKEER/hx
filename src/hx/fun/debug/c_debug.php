<?php
namespace hx\fun\debug;

use hx\c_base_class;
use function hx\gf_hx;

class c_console_color extends c_base_class
{
	private string $s;
	private \WeakReference $debug;

	public function __construct (\WeakReference $debug)
	{
		$this->s = '';
		$this->debug = $debug;
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

	public function echo (): c_console_color
	{
		$this->debug->get()->echo_with_nl($this->s);
		return $this;
	}

	public function get (): string
	{
		return $this->s;
	}
}

/**
 * 
 * @author 		Administrator
 * @property	c_console_color $cc
 *
 */
class c_debug extends c_base_class
{

	public function print_r (...$v): c_debug
	{
		/* < */
		foreach ($v as $kk => $vv)
		{
			print_r($vv);if (array_key_last($v) !== $kk && (is_object($vv) === FALSE || is_array($vv) === FALSE))
			{
				$this->echo_with_nl();
			}
		}
		return $this->echo_with_nl();
		/* > */
	}

	public function var_dump　 (...$v): c_debug
	{
		var_dump(...$v);
		return $this->echo_with_nl();
	}

	public function print_r_to_string (...$v): string
	{
		$data = gf_hx()->fun->stdclass->new();
		$data->v = $v;

		/* < */
		$this->ob(on_ob_echo: function () use ( $data)
		{
			$this->print_r(...$data->v);
		}
		,
		on_ob_end: function ($s) use ( $data)
		{
			$data->s = $s;
		});
		/* > */

		return $data->s;
	}

	public function echo_with_nl ($s = ''): c_debug
	{
		echo $s . "\n";
		return $this;
	}

	private function ob (callable $on_ob_echo , callable $on_ob_end , callable $on_ob_start = null)
	{
		ob_start();

		echo $on_ob_start === null ? '' : $on_ob_start();
		$on_ob_echo();
		$on_ob_end(ob_get_contents());

		ob_clean();
		return $this;
	}

	public function __get ($k)
	{
		/* < */
		if($k==='die')
		{
			die;
		}
		
		return $this->ado('cc'	,new c_console_color($this->make_weak_refernce()))->$k;
		/* > */
	}

	public function die ($v = '')
	{
		return $this->print_r($v)->die;
	}
}