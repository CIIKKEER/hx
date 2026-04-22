<?php
namespace hx\config;

use function hx\gf_hx;
use hx\fun\stdclass\c_stdclass;
use hx\db\mysqli\c_mysql_connection_info;
use hx\c_base_class;
use hx\db\mysqli\i_mysql_connection_info;

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
		return $this->ado('mysql',c_config_mysql::class,$k)->ado('clickhouse',c_config_clickhouse::class,$k)->$k;
	}
}

class c_config_mysql
{
	private static ?string $mysql_config_env_file_path = null;

	public function get_with_array (array $ar): c_mysql_connection_info
	{
		return (new c_mysql_connection_info())->set_mysql_connection_info(gf()->fun->stdclass->new_with_array($ar));
	}

	public function get_with_env_json ($file): c_mysql_connection_info
	{
		/* < 
		 * 
		 */
		self::$mysql_config_env_file_path = $file;$data = gf()->fun->stdclass->new();gf()->fun->file->ini->open_with_json($file)->to_array()->on_empty( function()
		{
			return gf()->exception->throw('1000000','i failed to parse the environment JSON configuration file');
		})
		->on_ok ( function ($ar) use ($data)		
		{
			$data->mysql_connection_info = $ar;
		});
		/* > */

		return $this->get_with_array($data->mysql_connection_info);
	}

	/**
	 * 
	 * @return 	string
	 * @throws	\Exception
	 */
	public function get_mysql_config_env_file_path (): string
	{
		if (self::$mysql_config_env_file_path === NULL)
		{
			gf()->exception->throw(1000003,'the MySQL configuration environment JSON file path does not exist and must be set before use');
		}
		
		return self::$mysql_config_env_file_path;
	}

	/**
	 * 
	 * @param 	string $file_path
	 * @return self
	 */
	public function set_mysql_config_env_file_path (string $file_path): self
	{
		self::$mysql_config_env_file_path = $file_path;
		return $this;
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