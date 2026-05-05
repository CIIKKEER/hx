<?php
/*
 <
 */
declare(strict_types = 1);
/*
 >
 <
 */

/* Copyright 2026 BREEZZEER
 * SPDX-License-Identifier: Apache-2.0
 *
 *
 */
namespace hx\route;

 
interface i_route_action_with_invoke
{

	public function on_invoke (i_request $r , i_response $s);
}

