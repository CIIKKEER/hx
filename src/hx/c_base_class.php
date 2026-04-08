<?php
namespace hx;

abstract class c_base_class extends \stdclass
{

	public static function new ()
	{
		return new static();
	}

	public function make_weak_refernce (): \WeakReference
	{
		return \WeakReference::create($this);
	}

	/**
	 *
	 * @author	BREEZZEER
	 * @desc 	Attach the deferred object as a public property of the current object instance
	 * @return 	c_base_class
	 * @param 	string 				$k
	 * @param 	string |callable 	$v
	 *  
	 *  
	 *  
	 *  
	 */
	public function ado (string $k , string |callable|object $v): c_base_class
	{
		if (property_exists($this,$k) === false)
		{
			$this->$k = is_callable($v) ? $v()/* callable */ : (is_object($v) ? $v : new $v())/* class name with absolute namespace */;
		}
		return $this;
	}
}
 