<?php
namespace hx\config;

use hx\c_base_class;

class c_config
{
	public function get (): c_stdclass
	{
		return [ 
			'mysql' => [ 
				'default' => [ 
					'ip' => 'sony.vaio.x.x',
					'port' => '53306',
					'user' => 'rootx',
					'password' => '!QAZ2wsx3edc.pl,.mysql'
				],
				'aaa' => [ ]
			]
		];
	}
}