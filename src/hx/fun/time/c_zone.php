<?php
namespace hx\fun\time;

use hx\c_base_class;

class c_zone extends c_base_class
{

	public function asia ()
	{
		return new class() extends c_base_class
		{
			const hong_kong = 'Asia/Hong_Kong';
			const macau = 'Asia/Macau';
			const shanghai = 'Asia/Shanghai';
		};
	}
}