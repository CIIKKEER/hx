<?php
namespace hx\cli;

use hx\c_base_class;
use hx\fun\stdclass\c_stdclass;

class c_cli extends c_base_class
{
	private string $argv_raw;
	private array $argv_raw_to_array;
	private c_stdclass $arg;
	private c_stdclass $option;
	private string $cmd_line_raw_path;
	private string $os;

	public function __construct ()
	{
		$this->arg = gf()->fun->stdclass->new();
		$this->option = gf()->fun->stdclass->new();
	}

	public function get_cmd_line_raw_path (): string
	{
		return gf()->fun->file->realpath($this->cmd_line_raw_path);
	}

	public function get_arg (): c_stdclass
	{
		return $this->arg;
	}

	public function get_options (): c_stdclass
	{
		return $this->option;
	}

	private function trime_argv (): self
	{
		$str = $this->argv_raw;
		preg_match_all('/\'(?:\\\\.|[^\'])*\'|"(?:\\\\.|[^"])*"|\S+/',$str,$matches);
		$tokens = $matches[0];
		$result = [ ];
		$buffer = [ ];
		$currentOption = null;

		foreach ($tokens as $token)
		{
			$isOption = (strpos($token,'-') === 0 && $token !== '-');
			if ($currentOption !== null)
			{
				if ($isOption)
				{
					$result[] = $currentOption;
					$currentOption = $token;
				}
				else
				{
					$currentOption .= ' ' . $token;
				}
			}
			else
			{
				if ($isOption)
				{
					if (!empty($buffer))
					{
						$result[] = implode(' ',$buffer);
						$buffer = [ ];
					}
					$currentOption = $token;
				}
				else
				{
					$buffer[] = $token;
				}
			}
		}

		if ($currentOption !== null)
		{
			$result[] = $currentOption;
		}
		elseif (!empty($buffer))
		{
			$result[] = implode(' ',$buffer);
		}

		$this->argv_raw_to_array = $result;

		/* < command line raw path and position argcument value
		 * 
		 */
		$str 					 = $this->argv_raw_to_array[0];preg_match_all("/'[^']*'|\S+/", $str, $matches);
		$ar_cmd_and_arg 		 = $matches[0];
		$this->cmd_line_raw_path = array_shift($ar_cmd_and_arg);
		$this->arg 				 = gf()->fun->stdclass->new_with_array($ar_cmd_and_arg);

		/* option parameter format as with -a xxx --b xxx ---c xxx ....
		 * 
		 */
		array_shift($this->argv_raw_to_array);for ($i = 0 ; $i < count($this->argv_raw_to_array) ; $i++)
		{
			$option_ar 	= explode (' ',$this->argv_raw_to_array[$i]);
			$k 			= trim ($option_ar[0],'-');
			$v 			= trim (ltrim(implode(' ',$option_ar),$option_ar[0]));if ($k !== '')
			{
				$this->option->$k = $v;
			}
		}
		/* > */
		return $this;
	}

	public function parse (array $argv): self
	{
		$this->argv_raw = implode(' ',$argv);
		$this->trime_argv();

		return $this;
	}
}