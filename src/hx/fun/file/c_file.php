<?php
namespace hx\fun\file;

use hx\c_base_class;

/**
 * @desc		local disk file content read and write
 * @author 		Administrator
 * @property 	c_ini $ini
 *
 */
class c_file extends c_base_class
{

	public function __get ($k)
	{
		return $this->ado('ini',c_ini::class,$k)->$k;
	}

	public function get_contents ($file)
	{
		if ($this->file_exists($file) === FALSE)
		{
			return gf()->exception->throw(30000000,'file or directory specified by filename not exists');
		}

		return file_get_contents($file);
	}

	public function file_exists ($filename): bool
	{
		return file_exists($filename);
	}

	public function realpath ($path): string|bool
	{
		/* < */$r = realpath($path);if ($r === FALSE)/* > */
		{
			return $r;
		}
		else
		{
			return str_replace('\\','/',$r);
		}
	}

	public function ok ($file , callable $on_ok , callable $on_error = NULL): c_file
	{
		/* < */$r = $this->realpath($file);if ($r === FALSE)/* > */
		{
			$on_error === null ? gf()->exception->throw(2000000,'returns canonicalized absolute path name failed : ' . $file) : $on_error($file);
		}
		else
		{
			$on_ok($r);
		}

		return $this;
	}
}