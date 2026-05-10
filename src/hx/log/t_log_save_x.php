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

trait t_log_save_x
{

	public function warning (mixed $data): self
	{
		return $this->save(e_log_level::warning,$data);
	}

	public function error (mixed $data): self
	{
		return $this->save(e_log_level::error,$data);
	}

	public function tips (mixed $data): self
	{
		return $this->save(e_log_level::tips,$data);
	}

	public function info (mixed $data): self
	{
		return $this->save(e_log_level::info,$data);
	}
}