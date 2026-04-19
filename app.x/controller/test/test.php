<?php
namespace appx\controller\test;

use hx\route\i_request;
use hx\route\i_response;
use hx\c_base_class;
use hx\route\c_route;
use hx\route\i_route_action_with_invoke;

class test extends c_base_class
{

	public function user ()
	{
		return new class() extends c_base_class
		{

			public function detail (i_request $r , i_response $s)
			{
				return $s->success(__METHOD__);
			}

			public function info ()
			{
				return new class() implements i_route_action_with_invoke
				{

					public function on_invoke (i_request $r , i_response $s)
					{
						return $s->success([ __METHOD__]);
					}
				};
			}

			public function register (i_request $r , i_response $s)
			{
				return $s->success(gf()->fun->jwt->set_key('xxxxxxxxxx11111111116666666666666xxxxxx')
					->encoder_with_key([ 'aa' => new \stdClass(),1111111111111]));
			}

			public function login (i_request $r , i_response $s)
			{
				return $s->success(gf()->fun->jwt->set_key('xxxxxxxxxx11111111116666666666666xxxxxx')
					->decoder($r->get('jwt')));
			}

			public function del (i_request $r , i_response $s)
			{
				return $s->success(__METHOD__);
			}

			public function add ()
			{
				return new class() implements i_route_action_with_invoke
				{

					public function on_invoke (i_request $r , i_response $s)
					{
						return $s->error(__METHOD__);
					}
				};
			}
		};
	}
}