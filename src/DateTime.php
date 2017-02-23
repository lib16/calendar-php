<?php

namespace Lib16\Calendar;

class DateTime extends \DateTime
{
	private static $encoding = 'UTF-8';

	public static function setCharacterEncoding(string $encoding)
	{
		self::$encoding = $encoding;
	}

	public function addYears(int $years): self
	{
		return $this->modify(\sprintf('%+d years', $years));
	}

	public function addMonths(int $months): self
	{
		return $this->modify(\sprintf('%+d months', $months));
	}

	public function addDays(int $days): self
	{
		return $this->modify(\sprintf('%+d days', $days));
	}

	public function addHours(int $hours): self
	{
		return $this->modify(\sprintf('%+d hours', $hours));
	}

	public function addMinutes(int $minutes): self
	{
		return $this->modify(\sprintf('%+d minutes', $minutes));
	}

	public function addSeconds(int $seconds): self
	{
		return $this->modify(\sprintf('%+d seconds', $seconds));
	}

	/**
	 * Sets the current date to the nearest or next workday.
	 *
	 * Sunday always becomes monday.
	 *
	 * @param  string  $next  if <code>true</code> saturday becomes monday, otherwise friday.
	 */
	public function forceWorkday(bool $next = false): self
	{
		$weekday = $this->format('N');
		if ($weekday == 7) {
			$this->addDays(1);
		}
		else if ($weekday == 6) {
			$next ? $this->addDays(2) : $this->addDays(-1);
		}
		return $this;
	}

	/**
	 * Returns a string representation according to locale settings.
	 *
	 * @link http://php.net/manual/en/function.strftime.php
	 * @link http://php.net/manual/en/class.datetime.php
	 *
	 * @param  string  $format    A format string containing specifiers like <code>%a</code>,
	 *                            <code>%B</code> etc.
	 */
	public function formatLocalized(string $format): string
	{
		$string = strftime($format, $this->getTimestamp());
		if (self::$encoding == 'UTF-8') {
			$string = utf8_encode($string);
		}
		return $string;
	}

	public function copy(): self
	{
		return clone $this;
	}

	public static function create($day = null, int $month = null, int $year = null): self
	{
		if (\is_string($day)) {
			return new DateTime($day);
		}
		if ($day instanceof \DateTime) {
			return new DateTime($day->format('Y-m-d'));
		}
		if ($day > 31) {
			$swap = $day; $day = $year; $year = $swap;
		}
		$year = $year ?? date('Y');
		$month = $month ?? date('m');
		$day = $day ?? date('d');
		return new static("$year-$month-$day");
	}

	/**
	 * Creates a <code>DateTime</code> object for the easter date.
	 *
	 * @link http://php.net/manual/en/function.easter-days.php
	 */
	public static function easter(int $year): self
	{
		return self::create(21, 3, $year)->addDays(easter_days($year));
	}

	public function __toString()
	{
		return $this->format('r');
	}
}