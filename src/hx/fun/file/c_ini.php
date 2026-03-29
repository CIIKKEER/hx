<?php
namespace hx\fun\file;

use hx\c_base_class;

class c_ini extends c_base_class
{
	private string $m_ini_file_path;
	private array $m_ar_file_content;

	public function open ($ini_file_path): bool
	{
		/* < */ $this->m_ini_file_path = realpath($ini_file_path);if (file_exists($this->m_ini_file_path) === FALSE) /* > */
		{
			throw new \Exception("ini file not exist at : " . $this->m_ini_file_path);
		}
		
		$this->m_ar_file_content = parse_ini_file($this->m_ini_file_path,true);

		gf()->fun->debug->print_r($this->m_ini_file_path,$this->m_ar_file_content);

		return true;
	}
	
	public function open_with_local_php_code_file ($file) 
	{
		$this->m_ar_file_content = include_once ($file);
		
		gf()->fun->debug->print_r($this->m_ar_file_content)->die('dddddddddddddddddddddddddddd');
	}
	
	public function open_with_json ($file) 
	{
		gf()->fun->debug->print_r( gf()->fun->json->decoder(gf()->fun->file->get_contents($file)))->die('bbbbbbbbbbbbbbbbbbbbbbbbbb');	
	}
}