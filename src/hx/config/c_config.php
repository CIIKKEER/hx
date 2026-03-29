<?php
namespace hx\config;

use function hx\gf_hx;
use hx\fun\stdclass\c_stdclass;

class c_config
{
	
	public function get (): c_stdclass
	{
// 		gf()->fun->file->ini->open_with_json(__DIR__.'/../../../env/env.json');
// 		gf()->fun->file->ini->open_with_local_php_code_file(__DIR__.'/../../../env/env.config');
		gf()->fun->file->ini->open(__DIR__.'/../../../env/env.ini');
		
		return gf()->fun->stdclass->new_with_array([ 
			'mysql' => [ 
				'default' => [ 
					'hostname' => 'x.x.x.x',
					'port' => 1,
					'username' => '',
					'password' => ''
				],
				'aaa' => [ 
					'hostname' => 'a.b.x.x',
					'port' => 2,
					'username' => '',
					'password' => ''
				],
				'bbb' => [ 
					'hostname' => 'c.d.x.x',
					'port' => 3,
					'username' => '',
					'password' => ''
				]
			]
		]);
	}
	
}