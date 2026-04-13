<?php
namespace hx\exception;

class c_exception
{
	private static $m_on_set_exception_handler;

	public function throw_with_string ($s = '')
	{
		throw new \Exception($s);
	}

	public function throw_with_exception (\Exception $e)
	{
		throw $e;
	}

	public function throw_with_wrap (int $error_code , \Throwable $e)
	{
		throw new \Exception('[' . $error_code . ']->' . $e->getMessage());
	}

	public function throw (int $error_code , string $s)
	{
		$this->throw_with_string('[' . $error_code . '] ' . $s);
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
}