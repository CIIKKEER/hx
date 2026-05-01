<?php
/* Copyright 2026 BREEZZEER
 * SPDX-License-Identifier: Apache-2.0
 *
 *
 *
 */
/* < auto load vendor
 * 
 */
function auto_load_vendor () :string
{
	static $autoload_path = null;if($autoload_path===NULL)
	{
		for ($dir = __DIR__ ; $dir !== dirname($dir) ; $dir = dirname($dir))
		{
			$va = $dir . '/vendor/autoload.php';if (file_exists($va))
			{
				$autoload_path = $va;
				break;
			}
		}
	}
	return $autoload_path;
}
/* > */

require_once (auto_load_vendor());