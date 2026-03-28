<?php
require_once __DIR__ . '/../vendor/autoload.php';

use hx\hx;
use function hx\gf_hx;

$hx = new hx();

print_r($hx);

$ar = [ 
	11,
	22,
	33,
	'ssssssssssssss' => [ 
		44,
		55,
		66
	],
	'5555555555555555',
	666,
	'ffffffffffff' => 1.23
];
 

gf_hx()->fun->debug->print_r(123)->print_r(gf_hx()->fun->debug->print_r_to_string($ar))->die();