<?php
namespace hx\route;

use hx\c_base_class;
use hx\fun\stdclass\c_stdclass;

interface i_route_action
{

	public function add (string $route_url , mixed $action): self;
}

interface i_route_url
{

	public function add (string $k , mixed $action): self;
}

interface i_response
{

	public function success (mixed $content , string $message = '' , int $code = 0): array;

	public function error (mixed $content , string $message = '' , int $code = 0): array;
}

interface i_json_to_object
{

	public function to_string (): string;

	public function to_object (): c_stdclass;
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

	public function raw_body_content (): i_json_to_object;

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

	private function normalize_path (string $path): string
	{
		$path = str_replace('\\','/',$path);
		$path = preg_replace('#/+#','/',$path);
		$parts = explode('/',trim($path,'/'));
		$resolved = [ ];
		foreach ($parts as $part)
		{
			if ($part === '' || $part === '.')
			{
				continue;
			}
			if ($part === '..')
			{
				if (!empty($resolved))
				{
					array_pop($resolved);
				}
				continue;
			}
			$resolved[] = $part;
		}
		return '/' . implode('/',$resolved);
	}

	public function url_route (): string
	{
		$uriRaw = explode('?',$this->request_uri())[0];
		$scriptRaw = $this->script_name();
		$uri = rawurldecode($uriRaw);
		$script = rawurldecode($scriptRaw);
		$uri = $this->normalize_path($uri);
		$script = $this->normalize_path($script);
		$scriptDir = $this->normalize_path(dirname($script));
		$path = str_starts_with($uri,$script) ? substr($uri,strlen($script)) : $uri;
		if ($scriptDir !== '/' && $scriptDir !== '')
		{
			$len = strlen($scriptDir);
			if (str_starts_with($path,$scriptDir) && (strlen($path) === $len || $path[$len] === '/'))
			{
				$path = substr($path,$len);
			}
		}

		$path = '/' . ltrim($path,'/');
		$path = rtrim($path,'/');

		return $path === '' ? '/' : $path;
	}

	public function get ($k): mixed
	{
		return array_key_exists($k,$_REQUEST) ? $_REQUEST[$k] : '';
	}

	public function raw_body_content (): i_json_to_object
	{
		return new class() extends c_base_class implements i_json_to_object
		{

			public function __construct ()
			{
				$r = file_get_contents('php://input');
				if ($r === FALSE)
				{
					gf()->exception->throw(880003,'get raw php input stream error');
				}

				$this->input = trim($r);
			}

			public function to_string (): string
			{
				return $this->input;
			}

			public function to_object (): c_stdclass
			{
				return gf()->fun->json->decoder($this->to_string())
					->ok();
			}
		};
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

	public function go ()
	{
		$this->c_request = new c_request();
		(new c_response($this))->do_action();
	}

	public function get_action_by_route_url (string $k): mixed
	{
		/** <
		 * 
		 * @var c_stdclass $au
		 * 
		 */
		$au = $this->get_route()->get($k);if ($au===null||$au->count() === 0)
		{
			return false;
		}
		else
		{
			return $au->action;
		}
		/* > */
	}

	/**
	 * 
	 * {@inheritDoc}
	 * @see \hx\route\i_route_action::add()
	 * @throws \Exception
	 * 
	 */
	public function add (string $route_url , mixed $action): self
	{
		if (is_array($action) && count($action) === 2)
		{
			$class = $action[0];
			$method = $action[1];
			if (class_exists($class) === FALSE)
			{
				return gf()->exception->throw(880002,$class . '->' . $method . ' not found');
			}
			$action = function (i_request $r , i_response $s) use ( $class , $method)
			{
				$o = (new $class());
				if (is_callable([ $o,$method]) === FALSE)
				{
					return gf()->exception->throw(880004,$class . '->' . $method . ' cannot be called');
				}

				return $o->$method($r,$s);
			};
		}
		else
		{
			if (is_callable($action) === FALSE)
			{
				gf()->exception->throw(880007,$route_url . ' : the route action must be callable');
			}
		}

		# add a route url
		#
		#
		(new c_route_url($this))->add($route_url,$action);
		return $this;
	}
}

class c_route_url extends c_base_class implements i_route_url
{
	private c_route $c_route;

	public function __construct ($w)
	{
		$this->c_route = $w;
	}

	public function add (string $k , mixed $action): self
	{
		$this->c_route->get_route()->{$k}->route_url = $k;
		$this->c_route->get_route()->{$k}->action = $action;
		return $this;
	}
}

class c_response extends c_base_class implements i_response
{
	private c_route $c_route;
	private array $response_data;

	public function __construct ($w)
	{
		$this->c_route = $w;
	}

	public function do_action ()
	{
		$action = $this->c_route->get_action_by_route_url($this->c_route->c_request->url_route());
		if ($action === false)
		{
			$this->response_data = $this->error($this->c_route->c_request->url_route(),' could not find any action',880000);
		}
		else
		{
			$r = null;
			try
			{
				$r = $action($this->c_route->c_request,$this);
			}
			catch (\Throwable $e)
			{
				print_r($e);
				$r = $this->error($this->c_route->c_request->url_route(),'an error occurred during the internal call of the URL route action : ' . $e->getMessage(),880006);
			}
			finally 
			{
				$this->response_data = $this->is_standard_response($r);
			}
		}
		$this->echo();
	}

	private function is_standard_response (mixed $v): array
	{
		$ok = false;
		if (is_array($v) && array_key_exists('status',$v) && array_key_exists('code',$v) && array_key_exists('content',$v) && array_key_exists('message',$v))
		{
			if (in_array($v['status'],[ 0,1],TRUE))
			{
				$ok = true;
			}
		}
		return $ok === true ? $v : $this->error($v,'route.url : [' . $this->c_route->c_request->url_route() . '] the return value of the route URL action is not a standard response',880001);
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

	private function echo (): void
	{
		if (headers_sent() === FALSE)
		{
			header('Content-Type: application/json; charset=utf-8');
			http_response_code(200);
		}

		echo gf()->fun->cc->as(gf()->fun->json->encoder($this->response_data))
			->get();
	}
}

