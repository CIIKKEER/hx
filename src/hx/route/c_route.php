<?php
namespace hx\route;

use hx\c_base_class;
use hx\fun\stdclass\c_stdclass;

interface i_route_url
{

	public function parse (): self;
}

interface i_response
{
}

class c_route extends c_base_class
{
	public const version = '1.0.0';
	private static ?c_stdclass $route = null;

	public function get_route (): c_stdclass
	{
		if (self::$route === NULL)
		{
			self::$route = new c_stdclass();
		}
		return self::$route;
	}

	public function about (): string
	{
		return self::version;
	}

	public function get (string $route_url , mixed $action): self
	{
		$this->route_url = new c_route_url($this->make_weak_reference(),$route_url,$action);
		return $this;
	}

	public function go (): i_response
	{
		return new c_response($this->make_weak_reference());
	}
}

class c_response extends c_base_class implements i_response
{
	private c_route $c_route;

	public function __construct (\WeakReference $w)
	{
		$this->c_route = $w->get();
		
		gf()->fun->debug->print_r($this->c_route->get_route());
	}
}

class c_route_url extends c_base_class implements i_route_url
{
	private string $route_url;
	private c_route $c_route;

	public function __construct (\WeakReference $w , string $route_url , $action)
	{
		$this->c_route = $w->get();
		$this->c_route->get_route()->$route_url->route_url = $route_url;
		$this->c_route->get_route()->$route_url->action = $action;
	}

	public function parse (): self
	{
		return $this;
	}
}