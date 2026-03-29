<?php
namespace hx\fun\json;
use hx\c_base_class;

class c_json extends c_base_class
{
	public  function decoder ($s,$b_array=false) 
	{
		return json_decode($s,$b_array);
	}
	
}