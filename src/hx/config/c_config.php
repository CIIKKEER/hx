<?php
namespace hx\config;

use function hx\gf_hx;
use hx\fun\stdclass\c_stdclass;
use hx\db\mysqli\c_mysql_connection_info;
use hx\c_base_class;

/**
 * @author Administrator
 * @property c_config_mysql $mysql
 * @property c_config_clickhouse $clickhouse
 *
 */
class c_config extends c_base_class
{

	public function __get ($k)
	{
		return $this->ado('mysql',c_config_mysql::class)->ado('clickhouse',c_config_clickhouse::class)->$k;
	}
}

class c_config_mysql
{

	// $ar =>	[
	//			'mysql' => 	[
	//							'default' => ['hostname' => '','port' => 3306,'username' => '','password'=>''], # default mysql connection string 
	//							'aaaaaaa' => ['hostname' => '','port' => 3306,'username' => '','password'=>''], # another ...
	//							.
	//							.
	//							.
	//							.
	//							.
	//							.
	//							.
	//							.
	//							'nnnnnnn' => ['hostname' => '','port' => 3306,'username' => '','password'=>''], # another ...
	//						]
	//			]
	/**
	 * @desc	obtain MySQL connection configuration information => you can use an array format in your code or save this configuration information to a local environment file.
	 * @param 	array $ar
	 * @return \hx\db\mysqli\c_mysql_connection_info
	 * 
	 * 
	 * 
	 */
	public function get_with_array (array $ar): c_mysql_connection_info
	{
		return (new c_mysql_connection_info())->set_mysql_connection_info(gf()->fun->stdclass->new_with_array($ar));
	}

	public function get_with_env_json ($file): c_mysql_connection_info
	{
		$data = gf()->fun->stdclass->new(); /* < */gf()->fun->file->ini->open_with_json($file)->to_array()->on_empty( function()
		{
			return gf()->exception->throw('1000000','parse enviorment json configuration file failed');
		})
		->on_ok ( function ($ar) use($data)		
		{
			$data->mysql_connection_info = $ar;
		});/* > */

		return $this->get_with_array($data->mysql_connection_info);
	}
}

class c_config_clickhouse
{

	/* get ClickHouse database configuration information
	 * 
	 * 
	 * 
	 * 
	 * 
	 */
	public function get_with_env_json (string $file): string
	{
		return $file;
	}
}