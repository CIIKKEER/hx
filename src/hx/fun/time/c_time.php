<?php
namespace hx\fun\time;

use hx\c_base_class;
use hx\fun\time\c_zone;
use hx\fun\time\c_datetime;

/**
 * 
 * @author 		Administrator
 * @property	c_zone			$zone
 * @property	c_datetime		$datetime
 *
 */
class c_time extends c_base_class
{
	private int $timestamp;

	public function __construct ()
	{
		date_default_timezone_set(c_zone::new()->asia()::shanghai);
	}

	public function __get ($k)
	{
		return $this->ado('zone',c_zone::class,$k)->ado('datetime',c_datetime::class,$k)->$k;
	}

	public function to_datetime (): c_datetime
	{
		try
		{
			return c_datetime::new()->new_with_timestamp($this->get());
		}
		catch (\Throwable $e)
		{
			gf()->exception->throw_with_wrap(70000000,$e);
		}
	}

	public function now (): self
	{
		return $this->new_with_now();
	}

	private function new_with_now (): self
	{
		return $this->new_with_timestamp(time());
	}

	public function new_with_timestamp (int $timestamp): self
	{
		$o = $this->new();
		$o->timestamp = $timestamp;
		return $o;
	}

	public function get (): int
	{
		return $this->timestamp;
	}

	public function new_with_string_date (string $datetime): self
	{
		return $this->new_with_timestamp(strtotime($datetime));
	}

	public function format ()
	{
		return new class($this->make_weak_reference()) extends c_base_class
		{
			/**
			 * 
			 * @var c_time $c_time
			 */
			private c_time $c_time;

			public function __construct (\WeakReference $w)
			{
				$this->c_time = $w->get();
			}

			public function ymdyis (): string
			{
				return date('Y-m-d H:i:s',$this->c_time->timestamp);
			}

			public function ymd (): string
			{
				return date('Y-m-d',$this->c_time->timestamp);
			}

			public function yis (): string
			{
				return date('H:i:s',$this->c_time->timestamp);
			}
		};
	}
}