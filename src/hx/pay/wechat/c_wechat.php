<?php

/* Copyright 2026 BREEZZEER
 * SPDX-License-Identifier: Apache-2.0
 *
 *
 *
 */
namespace hx\pay\wechat;
use hx\c_base_class;
use hx\pay\i_pay;
use hx\pay\self;


class c_wechat extends c_base_class implements i_pay
{
	public function make_an_order (c_base_class $product): self
	{
	}

	public function load_pay_vendor_sdk (): self
	{
	}

	public function notify_callback (): self
	{
	}

}