<?php
/* < auto load vendor
 * 
 */
$autoload_path = null;for ($dir = __DIR__ ; $dir !== dirname($dir) ; $dir = dirname($dir))
{
	$va = $dir . '/vendor/autoload.php';if (file_exists($va))
	{
		$autoload_path = $va;
		break;
	}
}

require_once $autoload_path;
/* > */