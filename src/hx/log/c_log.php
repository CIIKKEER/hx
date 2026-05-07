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
use hx\db\orm\c_orm;

/* this is the content of a standard log system configuration file, which directly returns a PHP array when included in the execution context of the parent file.
 <
 *
 * return [
 *              'log' => [
 *                              # log file path
 *                              #
 *                              #
 *                              'file' => __DIR__.'/../bin/test/log.data/test.log',
 *
 *                              # the JSON field log_data_table of this array is a standard mapping relation, in which each item is mapped to an actual table name, a log level, and a log data field in your database.
 *                              #
 *                              #
 *                              #
 *                              'db' => [
 *                                              'mysql' => [
 *                                                              'default' => [
 *                                                                              'hostname'                      => "x.x.x.x"                      		,       # required
 *                                                                              'port'                          => 3306                                	,       # required
 *                                                                              'username'                      => "xxxx"                              	,       # required
 *                                                                              'password'                      => "xxxxxxxxxxxxxxxxxxxxxx"             ,       # required
 *                                                                              'database'                      => "bbb"                                ,       # required
 *                                                                              
 *                                                                              # mapping log table field name
 *                                                                              #
 *                                                                              #
 *                                                                              'log_data_table'                => [
 *                                                                                                                      'table'         => 'log'        ,       # required
 *                                                                                                                      'log_level'     => 'log_level'  ,       # required
 *                                                                                                                      'log_data'      => 'log_data'   ,       # required
 *                                                                                                                 ],
 *                                                                           ],
 *                                                         ],
 *                                      ]
 *                      ]
 *       ];
 *
 >
 */
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
	private ?c_stdclass $save_with_multi_mode = null;

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
		 <
		 */
		$i_log_save_mode = match ($e_log_save_mode) 
		{
			e_log_save_mode::file => 
			( 
				function ()
				{
					return (new c_log_driver_with_file())->set_file_local_path($this->log_env_json->log->file);
				}
			)()
			,
			e_log_save_mode::db => 
			( 
				function ()
				{
					$db = $this->log_env_json->log->db;$log_table = $db->mysql->default->log_data_table;gf()->exception->try 
					( 
						fn() => (new c_log_driver_with_db())->set_db_connection_information(c_mysql_connection_info::new()->set_mysql_connection_info($db))->set_log_table($log_table->table)->set_log_level_field ($log_table->log_level)->set_log_data_field ($log_table->log_data)
					)
					->catch
					(
						fn ($error_code,\Throwable $e) => gf()->exception->throw($error_code, 'You must configure the database log table storage information mapping before using the database log saving mode in the log environment configuration file. '.$e->getMessage()),120005
					)
					->ok($r);
					
					return $r;
				}
			)() 
		};
		/*
		 >
		 */

		return $this->set_log_driver($i_log_save_mode);
	}

	public function append_log_save_mode (c_log $log): self
	{
		$this->save_with_multi_mode ??= gf()->fun->stdclass->new();
		$this->save_with_multi_mode->push($log);

		return $this;
	}

	/**
	 * 
	 * @param 	e_log_level $log_level
	 * @param 	mixed 		$data
	 * @return 	self
	 * 
	 */
	public function save_with_multi_mode (e_log_level $log_level , mixed $data): self
	{
		/* is the log mode for validation?
		 * 
		 */
		$this->save_with_multi_mode->for_each(function ($k , c_log $v)
		{
			$v->is_config_file_ok();
		});

		/* save logs in multiple modes
		 * 
		 */
		$this->save_with_multi_mode->for_each(function ($k , c_log $v) use ( &$log_level , &$data)
		{
			$v->save($log_level,$data);
		});
		return $this;
	}

	/**
	 * 
	 * @return 	self
	 * @throws	\Exception : if the log configuration file status is incorrect , then throw a standard exception
	 * 
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
	private ?c_stdclass $log_table = null;

	/**
	 * 
	 * {@inheritDoc}
	 * @see 		\hx\log\i_log_save_mode::save()
	 * @throws		\Exception : if an error occurs while validating the storage log table information, then throw a standard exception
	 *  
	 */
	public function save (e_log_level $log_level , mixed $data): self
	{
		$this->ini_log_table();

		if (empty($this->log_table->table) || empty($this->log_table->log_level) || empty($this->log_table->log_data))
		{
			gf()->exception->throw(120004,'an error occurred while validating the log storage table information');
		}

		/* save log ...
		 * 
		 */
		$this->i_trans->query("insert into " . $this->log_table->table . " (" . $this->log_table->log_level . "," . $this->log_table->log_data . ") values (?,?)")
			->as($log_level->name)
			->as(gf()->fun->json->encoder($data))
			->go();

		return $this;
	}

	public function set_db_connection_information (c_mysql_connection_info $c_mysql_connection_info): self
	{
		$this->i_trans = gf()->db->mysqli->open_with_mysql_connection_info($c_mysql_connection_info)->connect();

		return $this;
	}

	public function set_log_table (string $table): self
	{
		$this->ini_log_table()->log_table->table = $table;
		return $this;
	}

	public function set_log_level_field (string $log_level): self
	{
		$this->ini_log_table()->log_table->log_level = $log_level;
		return $this;
	}

	public function set_log_data_field (string $log_data): self
	{
		$this->ini_log_table()->log_table->log_data = $log_data;
		return $this;
	}

	private function ini_log_table (): self
	{
		$this->log_table ??= gf()->fun->stdclass->new();

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
		return $this;
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