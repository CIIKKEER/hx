<?php
/*
 <
 */
declare(strict_types = 1);

/* Copyright 2026 BREEZZEER
 * SPDX-License-Identifier: Apache-2.0
 *
 >
 */
namespace hx\exception;

use hx\t_base_magic_method;
use hx\fun\stdclass\c_stdclass;
use hx\fun\debug\c_console_color;

class c_exception
{
	private static $m_on_set_exception_handler;

	public function throw_with_string (string $s = '')
	{
		throw new \Exception(rtrim($s) . "");
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
			->get() . '] ' . rtrim($s) . "");
	}

	/**
	 * 
	 * @desc 	set custome exception process callable handler
	 * @param 	callable $on_set_exception_handler
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

	/**
	 * 
	 * @param 	callable    $on_try  : closure callable 
	 * @param 	mixed       ...$args : these parameters will be unpacked and passed to the callable function
	 * @return 	mixed
	 * 
	 */
	public function try (callable $on_try , mixed ...$args): mixed
	{
		return new class(\WeakReference::create($on_try),...$args)
		{
			private mixed $r;
			private bool $ok;

			public function __construct (\WeakReference $w , ...$args)
			{
				try
				{
					$this->r = ($w->get())(...$args);
					$this->ok = true;
				}
				catch (\Throwable $e)
				{
					$this->ok = FALSE;
					$this->r = new class($e)
					{
						private ?\Throwable $e = null;

						public function __construct (\Throwable $e)
						{
							$this->e = $e;
						}

						public function __destruct ()
						{
							unset($this->e);
						}

						public function catch ($on_catch , $error_code)
						{
							if ($this->e !== NULL)
							{
								$on_catch === null ? gf()->exception->throw($error_code,$this->get_exception_info()) : $on_catch($error_code,$this->e);
							}
						}

						private function get_exception_info ($error_code = null): string
						{
							return gf()->fun->cc->new()
								->yellow($error_code ?? '')
								->get() . "-> \n               message    : " . $this->e->getMessage() . " " . "\n	       file.name  : " . $this->e->getFile() . "\n	       error.line : " . gf()->fun->cc->pink(strval($this->e->getLine()))
								->get();
						}

						public function die ($error_code)
						{
							die($this->get_exception_info('[' . $error_code . ']') . "\n");
						}
					};
				}
			}

			public function __get (string $k): mixed
			{
				return $this->get_correct_result_or_exception($k);
			}

			public function __call (string $k , array $v): mixed
			{
				return $this->get_correct_result_or_exception($k,$v);
			}

			private function get_correct_result_or_exception (string $k , array $v = null)
			{
				if ($this->ok)
				{
					return $this->r;
				}
				if ($k === 'die')
				{
					return $this->die();
				}
			}

			public function die ($error_code = 1111111111)
			{
				return $this->r->die($error_code);
			}

			public function catch (callable $on_catch = null , int $error_code = 9999999999)
			{
				return $this->r->catch($on_catch,$error_code);
			}

			public function ok (&$r = null): bool
			{
				$r = $this->r;
				return $this->ok;
			}
		};
	}
}