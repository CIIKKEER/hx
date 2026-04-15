<?php

/* <
 * 
 */
declare(strict_types = 1);require_once __DIR__ . '/../vendor/autoload.php';

/* 
 * 
 * 
 * > */
use hx\c_base_class;
use hx\cli\c_cli;

class c_dev_web_svr extends c_base_class
{
	public const version = '1.0.0';
	public const server = 'PHP built-in web server';
	private c_cli $cli;

	public function __construct ()
	{
		$this->cli = gf()->cli->new();
	}

	public function about (): string
	{
		return self::version;
	}

	/**
	 * 
	 * @param 	int 	$argc
	 * @param 	array 	$argv
	 * @return 	int
	 * 
	 * 
	 */
	public function main (int $argc , array $argv): int
	{
		$this->cli->parse($argv);
		$this->parse_option();

		return 0;
	}

	/* < 
	 * 
	 */

	private function parse_option_and_set_default_value (): self
	{
		$this->cli->get_options()->is_empty('dev-web-srv-default-startup-file'	, gf()->fun->file->realpath(__DIR__ . '/dev.web.svr.bootstrap.php'));
		$this->cli->get_options()->is_empty('ip'								, '0.0.0.0');
		$this->cli->get_options()->is_empty('port'								, 80);
		$this->cli->get_options()->is_empty('t'									, fn() => gf()->fun->file->realpath('./'))->is_ok('t',fn ($v) => gf()->fun->file->realpath($v));

		return $this;
	}
	private function on_help() : self
	{
		gf()->fun->cc->green('usage : ')->as($this->cli->get_cmd_line_raw_path())
										->cyan(' --t')
										->as(' /a/b/c/c/d')
										->cyan(' --ip')
										->as(' 0.0.0.0')
										->cyan(' --port')
										->as(' 80')
										->cyan(' --dev-web-srv-default-startup-file')
										->as('')
										->cyan(' --help')
										->as('')
										->echo();
		return $this;
	}
	private function parse_option (): self
	{
		/* on help
		 * 
		 */
		if ($this->cli->get_options()->exist('help') || $this->cli->get_arg()->to_array()->search('help')->ok())
		{
			return $this->on_help();
		}
		
		/* set the option to default value
		 * 
		 */
		$this->parse_option_and_set_default_value();
				
		/* start
		 * 
		 */
		return	$this->start();
	}
	
	private function start ():self 
	{
		$cmd="D:/tools.x/cygwin.x64/cygwin.min.x/usr/local/php/8.1.32.x64.nts/php.exe -S ".$this->cli->get_options()->ip.":".$this->cli->get_options()->port." -t ".$this->cli->get_options()->get('t').' '.$this->cli->get_options()->get("dev-web-srv-default-startup-file");
		gf()->os->process->system($cmd);
		
		return $this;
	}
}
/* > */
function main (int $argc , array $argv): int
{
	return c_dev_web_svr::new()->main($argc,$argv);
}

/* go ...
 * 
 * 
 * 
 */
main($argc,$argv);