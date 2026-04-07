<?php
use hx\hx;

function gf (): hx
{
	static $i = 0; /* < */static $gf = null;if ($gf === NULL)/* > */
	{
		$gf = hx::new();
	}
	return $gf;
}

