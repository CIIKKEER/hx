<?php
namespace hx\db;

interface i_db
{
	public function open (): i_db;
	public function close ();
}