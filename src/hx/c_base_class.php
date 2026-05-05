<?php
/* 
 < 
 */
declare(strict_types = 1);

/* Copyright 2026 BREEZZEER
 * SPDX-License-Identifier: Apache-2.0
 * 
 >
 */

/*
 <
 */
namespace hx;

use hx\fun\stdclass\c_stdclass;

abstract class c_base_class extends \stdClass
{
	/**
	 * @var \hx\fun\stdclass\c_stdclass $dc
	 */
	private ?c_stdclass $dc = null;

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
	 * @param 	string 						$k	: type
	 * @param 	string |callable|object 	$v	: value
	 * @param	string 						$c 	: current request type
	 * @return 	c_base_class
	 * @throws	\Exception  When a class string is given but the class does not exist.
	 *  
	 *  
	 *  
	 */
	public function ado (string $k , string |callable|object $v , string $c): self
	{
		if (property_exists($this,$k) === false)
		{
			if ($k === $c)
			{
				$this->$k = is_callable($v) ? $v()/* callable function */ : (is_object($v)/* object */ ? $v : (class_exists($v) ? new $v() : throw new \Exception('[50000000] class : ' . $v . ' missing ')))/* class name */;
			}
		}
		return $this;
	}

	/** 
	 * @desc	data container
	 * @return 	c_stdclass
	 */
	public function dc (): c_stdclass
	{
		return $this->dc === null ? (function () {$this->dc = gf()->fun->stdclass->new();return $this->dc;})() : $this->dc;
	}

	/** 
	 * @author	BREEZZEER
	 * @desc 	at runtime add new functionality to an object instance by dynamically injecting new properties or replacing existing ones
	 * @param 	string 						$k	: type
	 * @param 	string |callable|object 	$v	: value
	 * @return 	c_base_class
	 * @throws	\Exception  When a class string is given but the class does not exist.
	 *
	 >
	 */
	public function ado_inject (string $k , string |callable|object $v): self
	{
		if (property_exists($this,$k) === FALSE)
		{
			return $this->ado($k,$v,$k);
		}
		else
		{
			$this->$k = $this->new()->ado($k,$v,$k)->$k;
		}
		return $this;
	}
}
 