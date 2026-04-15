<?php
namespace hx\os;

use hx\c_base_class;
use hx\os\process\c_process;

/**
 * 
 * @author 		Administrator
 * @property	c_process		$process
 *
 */
class c_os extends c_base_class
{
	public function __get ($k)
	{
		/* < */
		return $this->ado('process', c_process::class, $k)
					->$k;
		/* > */
	}
	public function platform ()
	{
		return new class() extends c_base_class
		{
			private string $pf;

			public function __construct ()
			{
				$this->pf = strtolower(PHP_OS_FAMILY);
			}

			public function is_windows (): bool
			{
				return 'windows' === $this->pf;
			}

			public function is_linux (): bool
			{
				return 'linux' === $this->pf;
			}

			public function is_mac (): bool
			{
				return 'darwin' === $this->pf;
			}
		};
	}
}