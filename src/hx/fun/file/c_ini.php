<?php
namespace hx\fun\file;

use hx\c_base_class;
use hx\fun\stdclass\c_stdclass;

class c_ini extends c_file
{
	private string $m_ini_file_path;

	public function open_with_ini_file ($ini_file_path): array
	{
		/* < */
		$this->ok
		(
			$ini_file_path
			,
			on_ok: function ($file_path)
			{
				$this->m_ini_file_path = $file_path;$this->return = parse_ini_file($this->m_ini_file_path,true);
			}
			,
			on_error: fn ($file_path) => $this->return = []
		);
		/* > */

		return $this->return;
	}

	public function open_with_local_php_code_file ($file): array
	{
		/* < */
		$this->ok
		(
			$file
			, 
			function ($file) { $this->m_ini_file_path;$this->return = include_once ($file);}
			,
			function ($file) { $this->return = [];}
		);
		/* > */
		
		return $this->return;
	}

	public function open_with_json ($file): c_stdclass
	{
		return gf()->fun->json->decoder_with_local_file($file)->ok();
	}
}