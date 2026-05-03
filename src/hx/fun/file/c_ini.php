<?php
/*
 <
 */
declare(strict_types = 1);

/* Copyright 2026 BREEZZEER
 * SPDX-License-Identifier: Apache-2.0
 *
 *
 */
namespace hx\fun\file;

use hx\c_base_class;
use hx\fun\stdclass\c_stdclass;
use http\Encoding\Stream;

class c_ini extends c_file
{
	private string $m_ini_file_path;

	public function open_with_ini_file (string $ini_file_path): array
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

	public function open_with_local_php_code_file (String $file): array
	{
		/* < */
		$this->ok
		(
			$file
			, 
			function ($file) { $this->m_ini_file_path=$file;$this->return = ( function ($file) { return include ($file);} ) ($file);}
			,
			function ($file) { $this->return = [];}
		);
		/* > */

		return $this->return;
	}

	public function open_with_json (string $file): c_stdclass
	{
		return gf()->fun->json->decoder_with_local_file($file)->ok();
	}
}