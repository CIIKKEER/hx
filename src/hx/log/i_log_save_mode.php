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
namespace hx\log;

interface i_log_save_mode
{

	public function save (e_log_level $log_level , mixed $data): self;
}