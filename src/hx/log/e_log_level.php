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

enum e_log_level
{
	case info;
	case error;
	case warning;
	case tips;
}