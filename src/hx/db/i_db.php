<?php
namespace hx\db;

use hx\db\mysqli\c_mysql_connection_info;

interface i_bindx
{

	public function ai (int $i): i_bindx;

	public function aia (array $ia): i_bindx;

	public function as (string $s): i_bindx;

	public function asa (array $sa): i_bindx;

	/**
	 * 
	 * @param float $d
	 * @return i_bindx
	 */
	public function ad (float $d): i_bindx;

	/**
	 * 
	 * @param array $da
	 * @return i_bindx
	 */
	public function ada (array $da): i_bindx;

	public function go (): i_query;
}

interface i_query
{

	public function for_each (callable $on_for_each): i_query;
}

interface i_db
{

	public function open_with_env_json (string $env_file_path): i_db;

	public function open_with_mysql_connection_info (c_mysql_connection_info $conn): i_db;

	public function connect (string $connection_key = 'default'): i_db;

	public function get_db_information (): string;

	public function close (): i_db;

	public function query (string $sql): i_bindx;
}