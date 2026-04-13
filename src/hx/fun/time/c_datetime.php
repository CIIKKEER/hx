<?php
namespace hx\fun\time;

use hx\c_base_class;

/** 
 * @author 		Administrator
 * 
 */
class c_datetime extends c_base_class
{
	/**
	 * 
	 * @var \DateTime $datetime
	 */
	public ?\DateTime $datetime = NULL;

	private function create_datetime (): \DateTime
	{
		$o = new \DateTime();
		$o->setTimezone(new \DateTimeZone('Asia/Shanghai'));
		return $o;
	}

	public function now (): self
	{
		return $this->new_with_timestamp(time());
	}

	public function format ()
	{
		return new c_datetime_format($this->make_weak_reference());
	}

	public function new_with_timestamp (int $timestamp): self
	{
		$o = $this->new();
		$o->datetime = $this->create_datetime();
		$o->datetime->setTimestamp($timestamp);

		return $o;
	}

	public function day ()
	{
		return new c_datetime_day($this->make_weak_reference());
	}

	public function week ()
	{
		return new c_datetime_week($this->make_weak_reference());
	}

	public function month ()
	{
		return new c_datetime_month($this->make_weak_reference());
	}

	public function hour ()
	{
		return new c_datetime_hour($this->make_weak_reference());
	}

	public function minute ()
	{
		return new c_datetime_minute($this->make_weak_reference());
	}

	public function year ()
	{
		return new c_datetime_year($this->make_weak_reference());
	}

	public function to_timestamp (): int
	{
		return $this->datetime->getTimestamp();
	}

	public function diff (int $timestamp)
	{
		return new c_datetime_diff($this->make_weak_reference(),$timestamp);
	}
}

class c_datetime_diff extends c_base_class
{
	private c_datetime $c_datetime_src;
	private c_datetime $c_datetime_des;

	/**
	 * 
	 * @var \DateInterval $di
	 */
	private \DateInterval $di;

	public function __construct (\WeakReference $w , int $timestamp)
	{
		$this->c_datetime_des = c_datetime::new()->new_with_timestamp($timestamp);
		$this->c_datetime_src = $w->get();
		$this->di = $this->c_datetime_src->datetime->diff($this->c_datetime_des->datetime);
	}

	public function get (): \DateInterval
	{
		return $this->di;
	}
}

class c_datetime_year extends c_base_class
{
	private c_datetime $c_datetime;

	public function __construct (\WeakReference $w)
	{
		$this->c_datetime = $w->get();
	}

	public function add (int $year): self
	{
		$this->c_datetime->datetime->add(new \DateInterval("P" . $year . "Y"));
		return $this;
	}

	public function sub (int $year): self
	{
		$this->c_datetime->datetime->sub(new \DateInterval("P" . $year . "Y"));
		return $this;
	}

	public function get (): c_datetime
	{
		return $this->c_datetime;
	}
}

class c_datetime_minute extends c_base_class
{
	private c_datetime $c_datetime;

	public function __construct (\WeakReference $w)
	{
		$this->c_datetime = $w->get();
	}

	public function add (int $minute): self
	{
		$this->c_datetime->datetime->add(new \DateInterval("PT" . $minute . "M"));
		return $this;
	}

	public function sub (int $minute): self
	{
		$this->c_datetime->datetime->sub(new \DateInterval("PT" . $minute . "M"));
		return $this;
	}

	public function get (): c_datetime
	{
		return $this->c_datetime;
	}
}

class c_datetime_hour extends c_base_class
{
	private c_datetime $c_datetime;

	public function __construct (\WeakReference $w)
	{
		$this->c_datetime = $w->get();
	}

	public function add (int $hour): self
	{
		$this->c_datetime->datetime->add(new \DateInterval("PT" . $hour . "H"));
		return $this;
	}

	public function sub (int $hour): self
	{
		$this->c_datetime->datetime->sub(new \DateInterval("PT" . $hour . "H"));
		return $this;
	}

	public function get (): c_datetime
	{
		return $this->c_datetime;
	}
}

class c_datetime_month extends c_base_class
{
	private c_datetime $c_datetime;

	public function __construct (\WeakReference $w)
	{
		$this->c_datetime = $w->get();
	}

	public function add (int $month): self
	{
		$this->c_datetime->datetime->add(new \DateInterval("PT" . $month . "M"));
		return $this;
	}

	public function sub (int $month): self
	{
		$this->c_datetime->datetime->sub(new \DateInterval("PT" . $month . "M"));
		return $this;
	}

	public function get (): c_datetime
	{
		return $this->c_datetime;
	}
}

class c_datetime_week extends c_base_class
{
	private c_datetime $c_datetime;

	public function __construct (\WeakReference $w)
	{
		$this->c_datetime = $w->get();
	}

	public function add (int $week): self
	{
		$this->c_datetime->datetime->add(new \DateInterval("P" . $week . "W"));
		return $this;
	}

	public function sub (int $week): self
	{
		$this->c_datetime->datetime->sub(new \DateInterval("P" . $week . "W"));
		return $this;
	}

	public function get (): c_datetime
	{
		return $this->c_datetime;
	}
}

class c_datetime_day extends c_base_class
{
	private ?c_datetime $c_datetime = NULL;

	public function __construct (\WeakReference $w)
	{
		$this->c_datetime = $w->get();
	}

	public function add (int $day): self
	{
		$this->c_datetime->datetime->add(new \DateInterval("P" . $day . "D"));
		return $this;
	}

	public function sub (int $day): self
	{
		$this->c_datetime->datetime->sub(new \DateInterval("P" . $day . "D"));
		return $this;
	}

	public function get (): c_datetime
	{
		return $this->c_datetime;
	}
}

class c_datetime_format extends c_base_class
{
	private c_datetime $c_datetime;

	public function __construct (\WeakReference $w)
	{
		$this->c_datetime = $w->get();
	}

	public function ymdyis (): string
	{
		return $this->c_datetime->datetime->format('Y-m-d H:i:s');
	}
}

