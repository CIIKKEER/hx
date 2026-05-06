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
namespace hx\log;

use hx\c_base_class;
use hx\fun\file\e_file_operate_mode;
use hx\fun\stdclass\c_stdclass;
use hx\fun\file\i_file_operate;
use hx\db\mysqli\c_mysql_connection_info;
use hx\db\i_trans;

enum e_log_save_mode
{
	case file;
	case db;
}

interface i_log_save_mode
{

	public function save (e_log_level $log_level , mixed $data): self;
}

enum e_log_level
{
	case info;
	case error;
	case warning;
	case tips;
}

class c_log extends c_base_class
{
	private ?i_log_save_mode $i_log_save_mode = null;
	private ?c_stdclass $log_env_json = null;

	private function set_log_driver (i_log_save_mode $i_log_save_mode): self
	{
		$this->i_log_save_mode = $i_log_save_mode;
		return $this;
	}

	public function set_log_save_mode (e_log_save_mode $e_log_save_mode): self
	{
		/* is the logger configuration file status ok ?
		 * 
		 */
		if (null === $this->log_env_json)
		{
			return gf()->exception->throw(120003,'the logger environment configuration file is not specified');
		}

		/* match log mode
		 * 
		 */
		$i_log_save_mode = match ($e_log_save_mode) {
			e_log_save_mode::file => (function ()
			{
				return (new c_log_driver_with_file())->set_file_local_path($this->log_env_json->log->file);
			})() ,
			e_log_save_mode::db => (function ()
			{
				return (new c_log_driver_with_db())->set_db_connection_information(c_mysql_connection_info::new()->set_mysql_connection_info($this->log_env_json->log->db));
			})() ,
		};

		return $this->set_log_driver($i_log_save_mode);
	}

	/**
	 * 
	 * @return 	self
	 * @throws	\Exception : if the log configuration file status is incorrect , then throw a standard exception
	 */
	private function is_config_file_ok (): self
	{
		if (null === $this->log_env_json)
		{
			return gf()->exception->throw(120002,'the logger environment configuration file is not specified');
		}
		if (null === $this->i_log_save_mode)
		{
			return gf()->exception->throw(120000,'the log driver provider is not yet set');
		}

		return $this;
	}

	/**
	 * 
	 * @param 	e_log_level $log_level
	 * @param 	mixed $data
	 * @return 	self
	 * @throws	\Exception : if the log configuration file status is incorrect , then throw a standard exception
	 * 
	 */
	public function save (e_log_level $log_level , mixed $data): self
	{
		$this->is_config_file_ok()->i_log_save_mode->save($log_level,$data);

		return $this;
	}

	public function set_log_env_json_file (string $log_env_json_file): self
	{
		$this->log_env_json = gf()->fun->stdclass->new_with_array(gf()->fun->file->ini->open_with_local_php_code_file(gf()->fun->file->realpath($log_env_json_file)));

		return $this;
	}
}

class c_log_driver_with_db implements i_log_save_mode
{
	private ?i_trans $i_trans = null;

	public function save (e_log_level $log_level , mixed $data): self
	{
		return $this;
	}

	public function set_db_connection_information (c_mysql_connection_info $c_mysql_connection_info): self
	{
		$this->i_trans = gf()->db->mysqli->open_with_mysql_connection_info($c_mysql_connection_info)->connect();

		return $this;
	}
}

class c_log_driver_with_file implements i_log_save_mode
{
	private ?string $file_path = null;
	private ?i_file_operate $i_file_operate = null;

	public function __destruct ()
	{
		$this->close();
	}

	private function close (): self
	{
		if ($this->i_file_operate !== NULL)
		{
			$this->i_file_operate->close();
			$this->i_file_operate = null;
		}
	}

	/**
	 * 
	 * @param 	string $file_path
	 * @return 	self
	 * @throws	\Exception : if the log configuration file status is incorrect , then throw a standard exception 
	 * 
	 */
	public function set_file_local_path (string $file_path): self
	{
		$file_path = gf()->fun->file->realpath($file_path);
		$this->close();
		$this->i_file_operate = gf()->fun->file->operate()->open($file_path,e_file_operate_mode::append);
		$this->file_path = $file_path;

		return $this;
	}

	/* save log data
	 <
	 */
	public function save (e_log_level $log_level , mixed $data): self
	{
		 
		$r = gf()->fun->json->encoder($data);
		$this->i_file_operate->write(gf()->fun->cc->new()->as('[')->as($log_level->name)->as(']')->as('[')->as(gf()->fun->time->now()->format()->ymdhis())->as(']')->as(' ')->as($r)->anl()->get());
		
		return $this;
	}
	/* 
	 >
	 */
}