<?php
require_once __DIR__ . '/../auto.load.php';

use hx\c_base_class;
use hx\cli\c_cli;

class c_dev_web_svr extends c_base_class
{
	public const version = '1.0.0';
	public const server = 'PHP built-in web server';
	private ?c_cli $cli = null;

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
		$this->parse_option($argc);

		return 0;
	}

	/* < 
	 * 
	 */
	private function parse_option_and_set_default_value (): self
	{
		$this->cli->get_options()->is_ok	('dev-web-srv-default-startup-file'	, fn ($v) => gf()->fun->file->realpath($v))->is_empty('dev-web-srv-default-startup-file',fn () => gf()->fun->file->realpath(__DIR__ . '/dev.web.svr.bootstrap.php'));
		$this->cli->get_options()->is_empty	('ip'								, '0.0.0.0');
		$this->cli->get_options()->is_empty	('port'								, 80);
		$this->cli->get_options()->is_empty	('t'								, fn () => gf()->fun->file->realpath('./'))->is_ok('t',fn ($v) => gf()->fun->file->realpath($v));
		$this->cli->get_options()->is_ok	('php'								, fn ($v) => gf()->fun->file->realpath($v))->is_empty('php','php');
		return $this;
	}
	private function on_help() : self
	{
		gf()->fun->cc->new()->green('usage : ')->as($this->cli->get_cmd_line_raw_path())->anl()->as('       ')
										->cyan(' --t'			)->as(' 					/a/b/c/c/d')->as(' document root direcotory')->anl()->as('       ')
										->cyan(' --ip'			)->as(' 					0.0.0.0')->anl()->as('       ')
										->cyan(' --port'		)->as(' 					80')->anl()->as('       ')
										->cyan(' --php'			)->as(' 					')->as('use the system\'s default PHP interpreter or specify a PHP version yourself')->anl()->as('       ')
										->cyan(' --dev-web-srv-default-startup-file')->as('	')->as($this->cli->get_options()->get('dev-web-srv-default-startup-file'))->anl()->as('       ')
										->cyan(' --help|-h'		)->as('')
										->echo();
		return $this;
	}
	private function parse_option (int$argc): self
	{
		
		/* set the option to default value
		 * 
		 */
		$this->parse_option_and_set_default_value();
		
		
		/* on help
		 * 
		 */
		if 	(1 == $argc || $this->cli->get_options()->exist('help') || $this->cli->get_options()->exist('h') || $this->cli->get_arg()->to_array()->search('help')->ok() || $this->cli->get_arg()->to_array()->search('h')->ok())
		{
			return $this->on_help();
		}
		
				
		/* start
		 * 
		 */
		return	$this->start();
	}
	
	private function start ():self 
	{
		$cmd=$this->cli->get_options()->php." -S ".$this->cli->get_options()->ip.":".$this->cli->get_options()->port." -t ".$this->cli->get_options()->get('t').' '.$this->cli->get_options()->get("dev-web-srv-default-startup-file");
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