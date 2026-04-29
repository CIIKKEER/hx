<?php
namespace hx\test;

use hx\c_base_class;
use hx\fun\stdclass\c_stdclass;
use hx\db\i_db;
use hx\db\i_trans;
use hx\cache\redis\i_redis_connect;
use hx\cache\redis\i_redis_type;
use hx\cache\redis\c_redis;
use hx\db\orm\c_orm;
use hx\db\i_bindx;

class aaa extends c_orm
{

	protected function on_set_connection_key (): string
	{
		return 'bbb';
	}

	protected function on_set_open_with_env_json (): string
	{
		return (__DIR__ . '/../../../env/env.json');
	}

	protected function on_db_driver (): i_db
	{
		return gf()->db->mysqli->new();
	}

	public function get_aaa_name_by_id (int $id): string
	{
		return $this->field('name')
			->where()
			->and('id',1)
			->order()
			->desc('id')
			->asc('name')
			->by()
			->select()
			->go()
			->get_single_value();
	}
}
class bbb extends c_orm
{
	protected function on_set_connection_key (): string
	{
		return 'bbb';
	}

	
}

class c_test extends c_base_class
{
	/**
	 * @var \mysqli
	 */
	private \mysqli $mysqli;

	public function go (): c_test
	{
		$this->on_test_cc();

		gf()->fun->debug->print_r(gf()->fun->test()->elapse()->diff_with_millisecond()->do( fn() =>	$this->on_test_db_orm())->echo());
		
		
		
		
		die();
		$this->on_test_db();
		die();
		$this->on_test_route();
		die();
		$this->on_test_time();
		die();
		$this->on_test_redis();
		return $this;
	}

	private function on_test_db_orm (): self
	{
		$aaa = aaa::new();
		$bbb = bbb::new();

		/**
		 *
		 * @var i_db $db
		 *
		 */
		
		
		

		/* <
		 * 
		 */
		gf()->exception->try 
		(
			fn () => gf()->fun->debug->print_r
			(
				
 				 $aaa->insert()->done(['name' => 'jack','age' => 21])->go()->get_insert_id()
				, $aaa->where()->and('id', '=', 1)->update()->done(['name'=>'jack.update','age'=>21])->go()->get_information()
				
				, $aaa->query->sql("select * from ".$aaa->get_table_name()." where name like ?")->as('jack%')->go()->get_single_row()
 				, $aaa->where()->and('name', 'like', 'jack%')->select()->go()->get_single_row()
				, $aaa->where()->like('name', 'jack')->select()->go()->get_single_row()
				, $aaa->where()->like_right('name', 'jack')->select()->go( )->get_single_row()
				, $aaa->field('id')->where()->like_left('name', 'jack')->is_not_null('id')->between('id', 1, 1000000)->in('id', [963,1,2,3])->column('id','<','id+1')->raw('id>?')->select()->ai( 11)->go(  )->get_single_row()
				, $aaa->from($aaa->get_table_name())->join()->inner("bbb")->on("aaa.id","bbb.id")->field('aaa.age','aaa.name','max(bbb.id) nx')->where()->and('aaa.id', '>', 1)->between('aaa.id', 1, 99999)->group()->by('aaa.id','aaa.name')->order()->asc('aaa.id')->by()->limit()->offset(0, 11)->select()->go()->get_single_row()
				
				
				,$bbb::new()->where()->and('id', '=', 36)->select()->go()->get_single_row()
				,$bbb::new()->insert()->done(['user_id'=>1,'user_address'=>gf()->fun->cipher->rand->uuid()->v4()])->go()->get_insert_id()
				
				
			)
		)->die;
		
		/* > */
 
		return $this;
	}

	private function on_test_route (): self
	{
		gf()->route->get('/user/login',function ()
		{
		});

		gf()->fun->debug->print_r(gf()->route->get_route());
		return $this;
	}

	private function on_test_time (): self
	{
		/* <
		 * 
		 */
		
		$this->dc()->time->push
		(
			gf()->fun->time->now()->format()->ymdhis()
			,
			gf()->fun->time->now()->format()->ymd()
		 
			,
			gf()->fun->time->new_with_string_date('Mon, Apr 13, 2026  5:45:26 PM')->to_datetime()->year()->add(100)->get()->format()->ymdhis()
		
			);
			
			
		gf()->fun->debug->print_r($this->dc()->time,gf()->fun->time->datetime->now()->day()->add(11111)->get()->diff(gf()->fun->time->datetime->now()->to_timestamp())->get()->days);

		/* > */
		return $this;
	}

	private function on_test_redis (): self
	{
		/* < redis test ...
		 * 
		 */
		gf()->cache->redis->new()->open_with_json_file(__DIR__ . '/../../../env/env.json')->connect_ex ( function(c_redis $r,i_redis_type $rt)
		{
			$this->dc()->redis->s->push($r->s->get('aaa'));
			$this->dc()->redis->s->push($rt->s()->get('aaa'));
		});
		
		/**
		 * 
		 * @var c_redis $redis
		 * 
		 */
		$redis =gf()->cache->redis->new()->open_with_json_file(__DIR__ . '/../../../env/env.json')->connect_ex();
		$redis->list->with_key('list.bbb')->push('bbb.0')->push('bbb.1')->push('bbb.2');
		$this->dc()->redis->list->bbb->count = $redis->list->with_key('list.bbb')->pop();
		
		
		$this->dc()->redis->list->elapse =gf()->fun->test()->elapse()->diff_with_millisecond()->do ( function() use($redis)
		{
			for($i=0;$i<1000;$i++)
			{
				$redis->list->with_key('test')->push($i);
			}
		})->get();
		
		

		/**
		 * @var i_redis_type $rt
		 */
		$rt = gf()->cache->redis->open_with_json_file(__DIR__ . '/../../../env/env.json')->connect();
		$this->dc()->redis->s->push($rt->s()
			->set('aaa',gf()->fun->cipher->rand->create())
			->get('aaa'));

		$rt->list('list.aaa')
			->push(0)
			->push(1)
			->push(2)
			->push(3)
			->for_each(function ($k , $v , $timeout)
		{
			gf()->fun->debug->print_r($k,$v,$timeout);
			if ($timeout)
			{
				return true;
			}
		});

		gf()->fun->debug->print_r($this->dc()->redis);
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

		/** <
		 *  
		 * @var i_db $db
		 * 
		 */
		$db = gf()->db->mysqli->open_with_env_json(__DIR__ . '/../../../env/env.json');	
		
		$db->connect()->auto(function(i_trans $t)
		{
			gf()->fun->debug->print_r($t->query("insert into bbb.bbb(user_id,user_address)values(?,?);")->ai(888)->as(gf()->fun->cipher->rand->uuid()->v4())->go()->get_insert_id());
			
		});
		
		$this->ado_inject('ccc', new class{public function about(){gf()->fun->debug->print_r('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.ado.inject');}});
		$this->ccc->about();
		
		die('ok');
		
		$db->connect()->auto
		(
			function (i_trans $i) 
			{
				$i->query("select version(),? as '100',? as '200',? as aaa,? as bbb,? as '1.234'  where 1 in (?) and 'ccc' in(?) union all select version(),? as '100',? as '200',? as aaa,? as bbb,? as '1.234'  where 1 in (?) and 'ccc' in(?)  ;")
					->ai(100)->ai(200)->as('aaa')->as('bbb')->ad(1.234)->aia([ 0,1,2,3])->asa([ 'ccc','ddd'])				
					->ai(200)->ai(300)->as('eee')->as('fff')->ad(1.234)->aia([ 0,1,2,3])->asa([ 'ccc','ggg'])
					->go()
					->for_each
					(
						function (string $k , c_stdclass $v): bool
						{
							if ($v->aaa === 'aaa')
							{
									$v->add('xxxxxxxxxxx',[ 12222,333,44]);
									$v->del('bbb');
							}
							$this->dc()->on_test_db->push($v);
							
							
							return FALSE;
						})
						->for_each(function ($k , $v)
						{
							$this->dc()->on_test_db->push($v);
						});
			}
		);
		
		
		/* test db no transcation
		 *
		 */
		$db->connect()->query("select version(),now();")->go()->for_each(function($k,$v)
		{
			$this->dc()->on_test_db->push($v);
		});
		
		/* test database transactions manually
		 * 
		 */
		$i = $db->connect('aaa')->begin();
		$i->query("insert into bbb.bbb(user_id,user_address)values(?,?);")->ai(888)->as(gf()->fun->cipher->rand->uuid()->v4())->go();
		$i->rollback();
		
		/* 
		 * 
		 */
		$db->connect()->query("select * from bbb.bbb order by id desc;")->go()->get_single_row()->for_each(function($k,$v)
		{
			$this->dc()->get_single_row->push($v);
		});
		gf()->fun->debug->print_r("get_single_value => ",$db->connect()->query("select * from bbb.bbb order by id desc;")->go()->get_single_value());
		
		
		/* test db transcation
		 * 
		 */
		$db->connect()->auto(function (i_trans $db)
		{
			$db->query("insert into bbb.bbb(user_id,user_address)values(?,?);")->ai(gf()->fun->cipher->rand->create())->as(gf()->fun->cipher->rand->uuid()->v4())->go();
			$db->query("select now()")->go()->for_each(function($k,$v)
			{
				$this->dc()->on_test_db->push($v);
			});
			
			$db->query("select version() , ? as '100' ")->ai(100)->go()->for_each(function($k,$v)
			{
				$this->dc()->on_test_db->push($v);
			});
			
			$db->query("select * from bbb.bbb order by id desc limit 3;")->for_each(function($k,$v)
			{
				$this->dc()->on_test_db->push($v);
			});
					 
			
			$db->query
			(
				"select
							1 as aaa
							,
							? as ccc

							/*
						 	2 as bbb
							
							*/
 				# where 1=0
				"
			)
			->as(gf()->fun->cipher->rand->uuid()->create())->go()->for_each (function($k,$v)
			{
				$this->dc()->on_test_db->push($v);
			});
			
			
			$db->query("select *,'?\"?\"`????\\\\\\\\\\\\//////////`'  as xxxxxxxx from bbb.bbb order by id desc limit 3;")->for_each(function($k,$v)
			{
				$this->dc()->on_test_db->push($v);
			});
			
			
		});
		
 
		gf()->fun->debug->print_r($this->dc());
		

		gf()->fun->debug->print_r(gf()->fun->debug->cc->red('ddddddddddddddddddddddddddddddddddddddddddddddddddddddd')
			->green('gggggggggggggggggg')
			->blue('bbbbbbbbbbbbbbbbbb')
			->pink('pppppppppppppp')
			->yellow('yyyyyyyyyyy')
			->anl()
			->get(),gf()->version::author . '@' . gf()->version::description
		,
			
			
		gf()->db->mysqli->open_with_env_json(__DIR__ . '/../../../env/env.json')->get_db_information('aaa')
			

		);
		/* > */
		return $this;
	}
}