<?php
/* Copyright 2026 BREEZZEER
 * SPDX-License-Identifier: Apache-2.0
 *
 *
 *
 */
namespace hx\pay;

use hx\c_base_class;

interface i_pay
{

	public function load_pay_vendor_sdk (): self;

	public function make_an_order (c_base_class $product): self;

	public function notify_callback (): self;
}