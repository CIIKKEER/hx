<?php
namespace appx\route;

use hx\c_base_class;
use appx\controller\test\test;

/* <
 * 
 */
class route extends c_base_class
{

	public function get (): array
	{
		return [ 
					'/user/del' 		=> test::new()->user()->del(...)		,
					'/user/add' 		=> test::new()->user()->add()			,
					'/user/login'		=> [test::new()->user()::class,'login']	,
					'/user/register' 	=> test::new()->user()->register(...)	,
					'/user/info'		=> test::new()->user()->info()			,
				];
	}
}
/* > */