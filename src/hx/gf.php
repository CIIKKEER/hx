<?php
use hx\hx;

function gf (): hx
{
	static $gf = null;
	if ($gf === NULL)
	{
		$gf = hx::new();
	}
	return $gf;
}

