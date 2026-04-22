<?php
namespace hx\reflection;

use hx\c_base_class;

interface i_reflection_property
{

	public function get (): mixed;
}

class c_reflection extends c_base_class
{

	/**
	 * 
	 * @param 	object $o
	 * @param 	string $property
	 * @return 	i_reflection_property
	 */
	public function property (object $o , string $property): i_reflection_property
	{
		return new c_reflection_property($o,$property);
	}

	public function class (object $o): \ReflectionClass
	{
		return (new \ReflectionClass($o));
	}
}

class c_reflection_property extends c_base_class implements i_reflection_property
{
	private object $o;
	private mixed $p;

	public function __construct (object $o , string $property)
	{
		$this->o = $o;
		$prop = new \ReflectionProperty($o::class,$property);
		$prop->setAccessible(TRUE);
		$this->p = $prop->getValue(...);
	}

	public function __destruct ()
	{
		unset($this->p);
	}

	public function get (): mixed
	{
		return ($this->p)($this->o);
	}
}