<?php
namespace hx\db;

use hx\db\mysqli\c_mysql_connection_info;
use hx\fun\stdclass\c_stdclass;
use hx\db\mysqli\i_mysql_connection_info;

interface i_query_status
{

	public function get_affected_rows (): int;

	public function get_insert_id (): int;
}

interface i_bindx
{

	public function ai (int $i): self;

	public function aia (array $ia): self;

	public function as (string $s): self;

	public function asa (array $sa): self;

	public function ad (float $d): self;

	public function ada (array $da): self;

	/**
	 * 
	 * @param 	callable $on_go
	 * @return 	i_query
	 */
	public function go (callable $on_go = null): i_query;
}

interface i_query extends i_query_status
{

	/**
	 * @desc 	get all data
	 * @param 	callable(string $k, c_stdclass $v): bool $on_for_each
	 * 
	 * @return 	i_query
	 * 
	 */
	public function for_each (callable $on_for_each): i_query;

	public function get_single_row (): c_stdclass;

	public function get_single_value (): mixed;
}

interface i_trans
{

	public function query (string $sql): i_bindx;

	public function begin (): i_trans;

	public function rollback (): i_trans;

	public function commit (): i_trans;

	/**
	 * 
	 * @param callable(i_trans $i) : bool $on_transaction
	 * 
	 * @return i_trans
	 * 
	 */
	public function auto (callable $on_transaction): i_trans;
}

interface i_db
{

	public function open_with_env_json (string $env_file_path): i_db;

	public function open_with_mysql_connection_info (i_mysql_connection_info $conn): i_db;

	public function connect (string $connection_key = 'default'): i_trans;

	public function get_db_information (string $connection_key = 'default'): c_stdclass;
}

 