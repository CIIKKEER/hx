<?php
declare(strict_types = 1)
	;
namespace hx;

/**
 * 
 * @author Administrator
 *
 */
abstract class c_base_class extends \stdclass
{

	public static function new ()
	{
		return new static();
	}

	public function make_weak_reference (): \WeakReference
	{
		return \WeakReference::create($this);
	}

	/**
	 * @author	BREEZZEER
	 * @desc 	Attach the deferred object as a public property of the current object instance
	 * 
	 * @param 	string 						$k
	 * @param 	string |callable|object 	$v
	 * @return 	c_base_class
	 *  
	 *  
	 *  
	 *  
	 */
	public function ado (string $k , string |callable|object $v): c_base_class
	{
		if (property_exists($this,$k) === false)
		{
			$this->$k = is_callable($v) ? $v()/* callable function */ : (is_object($v)/* object */ ? $v : (class_exists($v)/* class */ ? new $v() : die("[50000000] fatal error : $v class not found\n")))/* class name with absolute namespace */;
		}
		return $this;
	}

	 
}
 