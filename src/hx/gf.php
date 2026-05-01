<?php
/* Copyright 2026 BREEZZEER
 * SPDX-License-Identifier: Apache-2.0
 *
 *
 *
 */
use hx\hx;

function gf (): hx
{
	static $i = 0; /* < */static $gf = null;if ($gf === NULL)/* > */
	{
		$gf = hx::new();

		/* enable the default exception handler
		 * 
		 */
		$gf->exception->set_exception_handler();
	}
	return $gf;
}

 