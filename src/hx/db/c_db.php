<?php
declare(strict_types = 1);

/* Copyright 2026 BREEZZEER
 * SPDX-License-Identifier: Apache-2.0
 *
 *
 */
namespace hx\db;

use hx\db\mysqli\c_mysqli;
use hx\db\mysqli\c_mysql_connection_info;
use hx\db\pdo\c_pdo;
use hx\c_base_class;

/**
 * @desc 		database factory
 * @author 		Administrator
 * @property 	c_mysqli	$mysqli
 * @property 	c_pdo		$pdo
 *
 *
 *
 */
class c_db extends c_base_class
{

	public function __get ($k)
	{
		return $this->ado('mysqli',c_mysqli::class,$k)->ado('pdo',c_pdo::class,$k)->$k;
	}
}