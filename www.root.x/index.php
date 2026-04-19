<?php
require_once __DIR__ . '/../bin/auto.load.php';

/* HX => go ...
 * 
 */
gf()->route->add_with_array(\appx\route\route::new()->get())
	->go();




			
			
