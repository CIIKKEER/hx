<?php
namespace appx\route;

use hx\c_base_class;
use appx\api\v100\controller\test\n_test;

/* <
 * 
 */
class route extends c_base_class
{

	public function get (): array
	{
		return [ 
					'/user/del' 		=> n_test::new()->user()->del(...)			,
					'/user/add' 		=> n_test::new()->user()->add()				,
					'/user/login'		=> [n_test::new()->user()::class,'login']	,
					'/user/register' 	=> n_test::new()->user()->register(...)		,
					'/user/info'		=> n_test::new()->user()->info()			,
					'/user/modify'		=> n_test::new()->user()->modify()			,
					'/hx/about'			=> n_test::new()->hx()->about(...)			,
				];
	}
}
/* > */