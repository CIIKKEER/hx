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
namespace hx\fun\regx;

use hx\c_base_class;

class c_regx extends c_base_class
{

	public function preg_grep (string $pattern , string $subject): bool
	{
		return boolval(preg_grep($pattern,$array));
	}

	/**
	 * 
	 * @desc 	string|array|null preg_replace returns an array if the subject parameter is an array, or a string otherwise.If matches are found, the new subject will be returned, otherwise subject will be returned unchanged or null if an error occurred.
	 * @param 	string|array $pattern
	 * @param 	string|array $replacement
	 * @param 	string|array $subject
	 * @param 	int $limit
	 * @param 	int $count
	 * @return 	string|array|null
	 */
	public function preg_replace (string|array $pattern , string|array $replacement , string|array $subject , int $limit = -1 , int &$count = null): string|array|null
	{
		return preg_replace($pattern,$replacement,$subject);
	}
}