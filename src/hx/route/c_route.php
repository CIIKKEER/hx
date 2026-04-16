<?php
namespace hx\route;

use hx\c_base_class;
use hx\fun\stdclass\c_stdclass;

interface i_route_action
{

	/**
	 * 
	 * @param 	string $route_url
	 * @param 	callable (i_request $q,i_response $s) : void $action
	 * @return 	i_route_action
	 * 
	 */
	public function add (string $route_url , callable $action): self;
}

interface i_route_url
{

	public function add (string $k , mixed $action): self;
}

interface i_response
{

	public function success (mixed $content , string $message = ''): array;

	public function error (mixed $content , string $message = ''): array;
}

interface i_request
{

	public function ini (): self;

	public function script_name (): string;

	public function request_uri (): string;

	public function path_info (): string;

	public function query_string (): string;

	public function content_type (): string;

	public function request_method (): string;

	public function url_route (): string;

	public function raw_body_content (): string;

	public function get ($k): mixed;
}

class c_request extends c_base_class implements i_request
{
	private c_stdclass $r;

	public function __construct ()
	{
		$this->ini();
	}

	public function script_name (): string
	{
		return $this->r->script_name;
	}

	public function request_uri (): string
	{
		return $this->r->request_uri;
	}

	public function ini (): self
	{
		$this->r = gf()->fun->stdclass->new_with_array(array_change_key_case($_SERVER));

		return $this;
	}

	public function path_info (): string
	{
		return $this->r->path_info;
	}

	public function content_type (): string
	{
		return $this->r->content_type;
	}

	public function request_method (): string
	{
		return $this->r->request_method;
	}

	public function query_string (): string
	{
		return $this->r->query_string;
	}

	public function url_route (): string
	{
		return '/' . ltrim($this->path_info(),$this->script_name());
	}

	public function raw_body_content (): string
	{
		return file_get_contents('php://input');
	}

	public function get ($k): mixed
	{
		return array_key_exists($k,$_REQUEST) ? $_REQUEST[$k] : '';
	}
}

class c_route extends c_base_class implements i_route_action
{
	public const version = '1.0.0';
	private static ?c_stdclass $route = null;
	public ?c_request $c_request = null;

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

	public function go (): i_response
	{
		$this->c_request = new c_request();
		return new c_response($this->make_weak_reference());
	}

	public function get_action_by_route_url (string $k): mixed
	{
		/** <
		 * 
		 * @var c_stdclass $au
		 * 
		 */
		$au = $this->get_route()->get($k);if ($au->count() === 0)
		{
			return false;
		}
		else
		{
			return $au->action;
		}
		/* > */
	}

	public function add (string $route_url , callable $action): self
	{
		(new c_route_url($this->make_weak_reference()))->add($route_url,$action);
		return $this;
	}
}

class c_route_url extends c_base_class implements i_route_url
{
	private string $route_url;
	private c_route $c_route;

	public function __construct (\WeakReference $w)
	{
		$this->c_route = $w->get();
	}

	public function add (string $k , mixed $action): self
	{
		$this->c_route->get_route()->$k->route_url = $k;
		$this->c_route->get_route()->$k->action = $action;
		return $this;
	}
}

class c_response extends c_base_class implements i_response
{
	private c_route $c_route;
	private array $response_data;

	public function __construct (\WeakReference $w)
	{
		$this->c_route = $w->get();
		$this->do_action();
	}

	private function do_action ()
	{
		$action = $this->c_route->get_action_by_route_url($this->c_route->c_request->url_route());
		if ($action === false)
		{
			return $this->error_with_only_message('route.url : ' . $this->c_route->c_request->url_route() . 'ccould not find any action',880000);
		}

		$this->response_data = $this->is_standard_response($action($this->c_route->c_request,$this));
		$this->echo();
	}

	private function is_standard_response (mixed $v): array
	{
		$ok = false;
		if (is_array($v) && array_key_exists('status',$v) && array_key_exists('code',$v) && array_key_exists('content',$v) && array_key_exists('message',$v))
		{
			if (in_array($v['code'],[ 0,1],TRUE))
			{
				$ok = true;
			}
		}
		return $ok === true ? $v : $this->error($v,'the return value of the route URL action is not a standard response',880001);
	}

	public function get_response_data (): array
	{
		return $this->response_data ?? [ ];
	}

	public function success (mixed $content , string $message = '' , int $code = 0): array
	{
		return $this->set_response_data(1,$content,$message,$code);
	}

	public function error (mixed $content , string $message = '' , int $code = 0): array
	{
		return $this->set_response_data(0,$content,$message,$code);
	}

	private function set_response_data (int $status , mixed $content , string $message , int $code): array
	{
		return [ 'status' => $status,'code' => $code,'message' => $message,'content' => $content];
	}

	private function error_with_only_message (string $message , int $code = 0): void
	{
		$this->error('',$message,$code);
	}

	/* < echo the standard JSON content string back to the caller 
	 * 
	 * */
	private function echo (): void
	{
		echo gf()->fun->cc->as(gf()->fun->json->encoder($this->response_data))->get();
	}
	/* > */
}

