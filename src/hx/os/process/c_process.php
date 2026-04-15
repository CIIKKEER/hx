<?php
namespace hx\os\process;

use hx\c_base_class;
use hx\i_ok_error;
use hx\t_ok_error;
use hx\c_ok_error;

class c_process extends c_base_class
{

	public function system (string $command)
	{
		return new class($command) extends c_ok_error
		{

			public function __construct ($command)
			{
				$this->result_code = null;
				system($command . " 2>&1",$this->result_code);
				$this->set_ok($this->get_result_code() === 0 ? true : false);
			}

			public function get_result_code ()
			{
				return $this->result_code;
			}
		};
	}
}