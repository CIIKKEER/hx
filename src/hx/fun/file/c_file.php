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

	/**
	 * 
	 * @param 	string $path
	 * @return 	string|bool
	 * @throws	\Exception
	 * 
	 */
	public function realpath ($path): string
	{
		/* < */$r = realpath($path);if ($r === FALSE)/* > */
		{
			throw gf()->exception->throw(90000000,'the file does not exist : ' . $path);
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

	public function operate (): i_file_operate
	{
		return new c_file_operate($this->make_weak_reference());
	}
}

class c_file_operate extends c_base_class implements i_file_operate
{
	private bool $enable_file_lock = false;
	private c_file $c_file;
	private $fp = null;
	private int $total_bytes_written = 0;

	public function __construct (\WeakReference $w)
	{
		$this->c_file = $w->get();
	}

	public function __destruct ()
	{
		$this->close();
	}

	public function close (): self
	{
		if (is_resource($this->fp) === false || null === $this->fp)
		{
			return $this;
		}

		fclose($this->fp);
		$this->fp = null;

		return $this;
	}

	/**
	 * 
	 * {@inheritDoc}
	 * @see 			\hx\fun\file\i_file_operate::open($file_path)
	 * @throws			\Exception : if the file path is not found , then throw a standard exception
	 * 
	 */
	public function open (string $file_path , e_file_operate_mode $e_file_operate_mode): self
	{
		$file_path = $this->c_file->realpath($file_path);

		/* reset total bytes written
		 * 
		 */
		$this->total_bytes_written = 0;

		/* open file with standard an operation mode
		 * 
		 */
		$this->fp = fopen($file_path,$e_file_operate_mode->value);
		if ($this->fp === FALSE)
		{
			$this->fp = null;
			gf()->exception->throw(2000002,'an error occurs when process try to open the file path : ' . $file_path);
		}

		return $this;
	}

	/**
	 * 
	 * {@inheritDoc}
	 * @see 			\hx\fun\file\i_file_operate::write()
	 * @throws 			\Exception : the write method throws a standard exception to the caller when an error occurs while writing data to a file
	 * 
	 */
	public function write (mixed $data): self
	{
		if (is_resource($this->fp) === false)
		{
			gf()->exception->throw(2000003,'the file pointer is null');
		}

		/* file lock
		 * 
		 */
		$ok = false;
		$r = false;
		try
		{
			if ($this->enable_file_lock === true && flock($this->fp,LOCK_EX) === false)
			{
				gf()->exception->throw(2000004,'an error occurs when the process requests a file lock');
			}

			$ok = true;
			$r = fwrite($this->fp,$data);
		}
		finally 
		{
			$this->enable_file_lock === true && $ok === true ? flock($this->fp,LOCK_UN) : null;
		}

		if ($r === FALSE)
		{
			gf()->exception->throw(2000001,'an error occurred when the data was written to the file.');
		}

		$this->total_bytes_written += $r;

		return $this;
	}

	/**
	 * 
	 * {@inheritDoc}
	 * @see \hx\fun\file\i_file_operate::enable_file_lock()
	 * 
	 */
	public function enable_file_lock (bool $on = true): self
	{
		$this->enable_file_lock = $on;
		return $this;
	}
}

