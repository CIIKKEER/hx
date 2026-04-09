<?php
namespace hx\test;

use hx\c_base_class;

class c_test extends c_base_class
{
	/**
	 * @var \mysqli
	 */
	private \mysqli $mysqli;

	public function go (): c_test
	{
		$this->on_test_cc()->
		// 			->on_test_c_stdclass_to_string()
		on_test_db();

		return $this;
	}

	private function on_test_cc (): c_test
	{
		gf()->fun->cc->new()
			->as('The test process is loading')
			->green(' please wait ...')
			->echo();
		return $this;
	}

	private function on_test_c_stdclass_to_string (): c_test
	{
		gf()->fun->cc->pink(gf()->fun->stdclass->new_with_array([ 1,2,3])
			->to_string())
			->echo();

		return $this;
	}

	private function on_test_db (): c_test
	{
			
		/* < */
		gf()->db->mysqli->open_with_env_json(__DIR__ . '/../../../env/env.json')->connect()
			->query("select version(),? as '100',? as '200',? as aaa,? as bbb,? as '1.234'  where 1 in (?) and 'ccc' in(?) union all select version(),? as '100',? as '200',? as aaa,? as bbb,? as '1.234'  where 1 in (?) and 'ccc' in(?)  ;")
			->ai(100)->ai(200)->as('aaa')->as('bbb')->ad(1.234)->aia([0,1,2,3])->asa(['ccc','ddd'])
			->ai(200)->ai(300)->as('eee')->as('fff')->ad(1.234)->aia([0,1,2,3])->asa(['ccc','ggg'])
			->go()->for_each(function($k,$v)
			{
				gf()->fun->debug->print_r($k,' => ',$v);
			
			});
		
		gf()->fun->debug->print_r
		(
			gf()->fun->debug->cc->red('rrrrrrrrrrrrrrrrrrrrr')->green('gggggggggggggggggg')->blue('bbbbbbbbbbbbbbbbbb')->pink('pppppppppppppp')->yellow('yyyyyyyyyyy')->anl()->get()
			,
			gf()->version::author.'@'.gf()->version::description
			,
			
			// 	1
			//gf()->db->mysqli->open_with_mysql_connection_info(gf()->config->mysql->get_with_env_json(__DIR__ . '/../env/env.json'))->connect('aaa')->get_db_information()
		// 	,
		// 	2
		// 	,
		// 	3
		// 	,
		// 	gf()->config->clickhouse->get_with_env_json('click.house.configuration.by.environment.file')
		// 	,

		// 	,

		// 	gf()->fun->file->ini->open_with_ini_file(__DIR__ . '/../env/env.ini')

		// 	,
		// 	gf()->fun->file->ini->open_with_local_php_code_file(__DIR__ . '/../env/env.config')
		);
		/* > */
		return $this;
	}
}