<?php
namespace hx\exception;

use hx\t_base_magic_method;

class c_exception
{
	private static $m_on_set_exception_handler;

	public function throw_with_string ($s = '')
	{
		throw new \Exception(rtrim($s) . "\n");
	}

	public function throw_with_exception (\Exception $e)
	{
		throw $e;
	}

	public function throw_with_wrap (int $error_code , \Throwable $e)
	{
		throw new \Exception('[' . gf()->fun->cc->yellow(strval($error_code))->get() . ']->' . rtrim($e->getMessage()) . "\n");
	}

	public function throw (int $error_code , string $s)
	{
		$this->throw_with_string('[' . gf()->fun->cc->yellow(strval($error_code))
			->get() . '] ' . rtrim($s) . "\n");
	}

	/**
	 * @desc 	set custome exception process callable handler
	 * 
	 * @param 	callable $on_set_exception_handler
	 * 
	 * @return 	c_exception
	 * 
	 **/
	public function set_exception_handler (callable $on_set_exception_handler = NULL): c_exception
	{
		if ($on_set_exception_handler === null)
		{
			/* < error and exception default handler */set_exception_handler(fn ($e) => print_r($e));set_error_handler(function ($errno , $errstr , $errfile , $errline)
			{
				$err = new \stdClass();
				$err->file = $errfile;
				$err->line = $errline;
				$err->desc = $errstr;
				$err->erno = $errno;
				print_r($err);
			});
			/* > */
		}
		else
		{
			/* < */self::$m_on_set_exception_handler = $on_set_exception_handler;set_exception_handler(self::$m_on_set_exception_handler)/* > */;
		}

		return $this;
	}

	public function try (callable $on_try): mixed
	{
		try
		{
			return new class($on_try())
			{
				use t_base_magic_method;
			};
		}
		catch (\Throwable $e)
		{
			return new class(\WeakReference::create($e))
			{
				private ?\Throwable $e = null;

				public function __construct (\WeakReference $w)
				{
					$this->e = $w->get();
				}

				public function catch (int $error_code = 9999999999 , callable $on_catch = null)
				{
					if ($this->e !== NULL)
					{
						
						$on_catch === null ? gf()->exception->throw_with_wrap($error_code,$this->e) : $on_catch($this->e);
					}
				}

				public function die ($error_code = 1111111111)
				{
					die('[' . gf()->fun->cc->yellow($error_code)->get() . '] ' . $this->e->getMessage() . "\n");
				}

				public function __get ($k)
				{
					match ($k) {
						'die' => $this->die() ,
						'catch' => $this->catch()
					};
				}
			};
		}
	}
}