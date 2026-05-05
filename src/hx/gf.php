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
function gf (): \hx\hx
{
	/* < */static $gf = null;if ($gf === NULL)/* > */
	{
		$gf = \hx\hx::new();

		/* enable the default exception handler
		 * 
		 */
		$gf->exception->set_exception_handler();
	}

	return $gf;
}

function dp (mixed ...$v): \hx\fun\debug\c_debug
{
	return gf()->fun->debug->print_r(...$v);
}
 