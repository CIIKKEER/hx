<?php
namespace hx\fun\stdclass;

use hx\c_base_class;

class c_stdclass extends \stdClass
{
	public function new (): c_stdclass
	{
		return new static();
	}
	public function new_with_array ($ar = [ ]): c_stdclass
	{
		return $this->new()->array_2_object($ar);
	}
	private function array_2_object (array $ar): c_stdclass
	{
		$obj = $this->new();
		foreach ($ar as $key => $value)
		{
			if (is_array($value))
			{
				$obj->{$key} = $this->array_2_object($value);
			}
			else
			{
				$var_type = gettype($value);
				switch ($var_type)
				{
					case "boolean":
						$v = boolval($value);
						break;
					case "integer":
						$v = intval($value);
						break;
					case "string":
						$v = strval($value);
						break;
					case "double":
						$v = floatval($value);
						break;
					default:
						$v = $value;
				}
				$obj->{$key} = $v;
			}
		}
		return $obj;
	}
}