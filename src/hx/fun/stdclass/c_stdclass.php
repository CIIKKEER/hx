<?php
namespace hx\fun\stdclass;

use hx\c_base_class;
use hx\fun\array\c_array;

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
		/* < */$obj = $this->new();foreach ($ar as $key => $value)/* > */
		{
			if (is_array($value))
			{
				$obj->{$key} = $this->array_2_object($value);
			}
			else
			{
				/* < convert the variable to the correct data type */$var_type = gettype($value);switch ($var_type)/* > */
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

	private function object_2_array (&$root = null): array
	{
		/* < root object is myself */$root = $root === null ? $this : $root;$ar = [];if (is_object($root))/* > */
		{
			$ar = get_object_vars($root);
		}

		foreach ($ar as $k => $v)
		{
			if (is_object($v))
			{
				$ar[$k] = $this->object_2_array($v);
			}
		}

		return $ar;
	}

	public function for_each ($on_for_each): c_stdclass
	{
		foreach ($this as $k => $v)
		{
			if ($on_for_each($k,$v) === true)
			{
				break;
			}
		}
		return $this;
	}

	public function new_with_stdclass ($stdclass): c_stdclass
	{
		/* < */$o = $this->new();foreach ($stdclass as $k => $v)/* > */
		{
			$o->{$k} = $v;
		}
		return $o;
	}

	public function to_array (): c_array
	{
		return gf()->fun->array->new_with_array($this->object_2_array());
	}

	public function __get ($k): c_stdclass
	{
		$this->$k = $this->new();
		return $this->$k;
	}

	public function count (): int
	{
		return count(get_object_vars($this));
	}

	public function to_string (): string
	{
		return gf()->fun->debug->print_r_to_string($this);
	}

	public function add (string $k , $v): c_stdclass
	{
		$this->$k = $v;
		return $this;
	}

	public function del (string $k): c_stdclass
	{
		if (property_exists($this,$k))
		{
			unset($this->$k);
		}
		return $this;
	}

	public function push ($v): c_stdclass
	{
		return $this->add($this->count(),$v);
	}
}