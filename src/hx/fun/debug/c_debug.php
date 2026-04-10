<?php
namespace hx\fun\debug;

use hx\c_base_class;

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
			if ((is_object($vv) === true || is_array($vv) === true))
			{
				$this->echo_with_nl();
			}
			print_r($vv);
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
		$data = gf()->fun->stdclass->new();
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
		
		return $this->ado('cc'	,new c_console_color($this->make_weak_reference()),$k)->$k;
		/* > */
	}

	public function die ($v = '')
	{
		return $this->print_r($v)->die;
	}
}