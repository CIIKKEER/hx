<?php

/* Copyright 2026 BREEZZEER
 * SPDX-License-Identifier: Apache-2.0
 *
 *
 *
 */
namespace hx\pay;

use hx\c_base_class;
use hx\pay\wechat\c_wechat;

/**
 * 
 * @desc		The current payment vendor supports purchasing services via WeChat and Alipay
 * @property	c_wechat $wechat
 */
class c_pay extends c_base_class
{

	public function __get ($k)
	{
		return $this->ado('wechat',c_wechat::class,$k)->$k;
	}
}


 