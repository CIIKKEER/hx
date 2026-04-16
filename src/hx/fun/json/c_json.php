<?php
namespace hx\fun\json;

use hx\c_base_class;
use hx\fun\stdclass\c_stdclass;

class c_json extends c_base_class
{

	public function decoder ($s , $b_associative = false/* false => stdclass object true => array */)
	{
		/* < */$this->m_json_string = $s;$r = json_decode($this->m_json_string,$b_associative);return new class($r) extends c_base_class/* > */
		{

			public function __destruct ()
			{
				unset($this->m_r);
			}

			public function __construct ($r)
			{
				$this->m_r = $r === null ? gf()->fun->stdclass->new() : gf()->fun->stdclass->new_with_stdclass($r);
			}

			public function ok (callable $on_ok = NULL): c_stdclass
			{
				return $on_ok === null ? $this->m_r : $on_ok($this->m_r);
			}
		};
	}

	public function decoder_with_local_file ($file)
	{
		return $this->decoder(gf()->fun->file->get_contents($file));
	}

	public function encoder (mixed $v): string
	{
		$j = json_encode($v,JSON_PRETTY_PRINT);
		if ($j === FALSE)
		{
			return '';
		}
		else
		{
			return $j;
		}
	}
}