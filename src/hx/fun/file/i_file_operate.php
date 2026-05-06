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
namespace hx\fun\file;
interface i_file_operate
{
	
	public function write (mixed $data): self;
	
	public function open (string $file_path , e_file_operate_mode $e_file_operate_mode): self;
	
	public function close (): self;
}