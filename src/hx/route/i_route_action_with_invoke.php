<?php
namespace hx\route;

/** 
 * @author Administrator
 * 
 */
interface i_route_action_with_invoke
{

	public function on_invoke (i_request $r , i_response $s);
}

