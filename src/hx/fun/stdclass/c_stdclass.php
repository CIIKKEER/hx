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

	/**
	 * 
	 * @param callable (function (string $k,$v) : bool $on_for_each
	 * @return c_stdclass
	 * 
	 * 
	 */
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

	/**
	 * @desc 	I will create a class instance from a standard stdClass object
	 * @param 	\stdClass $stdclass
	 * @return 	c_stdclass
	 * @throws	\Exception
	 * 
	 */
	public function new_with_stdclass (object $stdclass): c_stdclass
	{
		if (is_object($stdclass) === FALSE)
		{
			gf()->exception->throw(770000,'parameter type mismatch');
		}

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

	public function push (...$v): c_stdclass
	{
		foreach ($v as $vv)
		{
			$this->add($this->count(),$vv);
		}
		return $this;
	}

	/**
	 * @des 	The property $k exists in the current object
	 * @param 	string $k
	 * @return 	bool
	 */
	public function exist (string $k): bool
	{
		return property_exists($this,$k);
	}

	public function set ($k , $v): self
	{
		$this->{$k} = $v;
		return $this;
	}

	public function get ($k)
	{
		return $this->{$k};
	}

	public function is_ok ($k , mixed $v = NULL): mixed
	{
		$ok = $this->is_empty($k);
		if ($ok === FALSE/* the value of current property is not empty  */ && $v != NULL)
		{
			$this->set($k,is_callable($v) ? $v($this->get($k)) : $v);
		}

		return is_callable($v) ? $this : !$ok;
	}

	public function free (): self
	{
		$this->for_each(function ($k , $v)
		{
			unset($this->$k);
		});

		return $this;
	}

	/** 	
	 * @desc	Check whether the value of the current property $k is empty
	 * @param 	string 			$k
	 * @param 	mixed 			$v　: if the value of property $k is empty and the value of $v is not null then I will update the value of property $k with $v
	 * @return 	mixed 
	 * 
	 * 
	 */
	/* < */
	public function is_empty (string $k , mixed $v = null): mixed
	{
		$vx = $this->get($k);if (is_object($vx))
		{
			$ok = count(get_object_vars($vx)) === 0 ? true : false;
		}
		else
		{
			$ok = empty($vx) ? true : false;
		}

		if ($ok === TRUE && $v !== NULL)
		{
			$this->set($k,is_callable($v) ? $v() : $v);
		}

		return is_callable($v) ? $this : $ok;
	}
	/* > */
}