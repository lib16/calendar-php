<?php

namespace Lib16\Calendar;

use Lib16\Calendar\DateTime;

class Calendar
{
	protected $from, $till;
	protected $firstWeekday;
	protected $dayFormat;
	protected $monthFormat;
	protected $yearFormat;
	protected $weekdayFormat;

	protected $entries;
	protected $weekdays;
	protected $buildWeekdays;

	/**
	 * You may use the static <code>span</code> method instead.
	 *
	 * @param  DateTime|string  $from  First day which should be shown on the calendar.<br>
	 *                                 A string of the format <code>Y-m-d</code> or a
	 *                                 <code>DateTime</code> object.
	 * @param  DateTime|string  $till  Last day which should be shown on the calendar.
	 */
	public function __construct($from, $till)
	{
		$this->from = DateTime::create($from);
		$this->till = DateTime::create($till);
		$this->entries = [];
		$this->setDayFormat()->setMonthFormat()->setYearFormat()->setWeekdayFormat();
		$this->setFirstWeekday();
	}

	/**
	 * @param  DateTime|string  $from  First day which should be shown on the calendar.<br>
	 *                                 A string of the format <code>Y-m-d</code> or a
	 *                                 <code>DateTime</code> object.
	 * @param  DateTime|string  $till  Last day which should be shown on the calendar.
	 */
	public static function span($from, $till): self
	{
		return new Calendar($from, $till);
	}

	/**
	 * Returns a <code>Calendar</code> object for the current or a given year.
	 *
	 * @param  int  $year  The current year if omitted.
	 */
	public static function year(int $year = null): self
	{
		$from = DateTime::create(1, 1, $year);
		$till = $from->copy()->addYears(1)->addDays(-1);
		return new Calendar($from, $till);
	}

	/**
	 * Returns a <code>Calendar</code> object for the current or a given month.
	 *
	 * @param  int  $month  The current month if omitted.
	 * @param  int  $year   The current year if omitted.
	 */
	public static function month(int $month = null, int $year = null): self
	{
		return self::months(1, $month, $year);
	}

	/**
	 * Returns a <code>Calendar</code> object for multiple months.
	 *
	 * @param  int  $count  <= -2: The previous <code>$count</code> months
	 *                      (including the current or given month). <br>
	 *                      >= +2: The next <code>$count</code> months
	 *                      (including the current or given month).
	 * @param  int  $month  The current month if omitted.
	 * @param  int  $year   The current year if omitted.
	 */
	public static function months(int $count = 1, int $month = null, int $year = null): self
	{
		$delta = (abs($count) - 1) * (($count > 0) - ($count < 0));
		$from = DateTime::create(1, $month, $year);
		$till = $from->copy()->addMonths(1);
		if ($delta > 0) {
			$till->addMonths($delta);
		}
		else if ($delta < 0) {
			$from->addMonths($delta);
		}
		$till->addDays(-1);
		return new Calendar($from, $till);
	}

	/**
	 * @param  string  $format  <code>%d</code> or <code>%#d</code>
	 *                          (<code>%e</code> doesn't work on Windows).
	 */
	public function setDayFormat(string $format = '%#d'): self
	{
		$this->dayFormat = $format;
		return $this;
	}

	/**
	 * @param  string  $format  <code>%b</code>, <code>%B</code> or <code>%m</code>,
	 *                          also in combination with <code>%Y</code> or <code>%y</code>.
	 */
	public function setMonthFormat(string $format = '%B'): self
	{
		$this->monthFormat = $format;
		return $this;
	}

	/**
	 * @param  string  $format  <code>%Y</code>, <code>%y</code> or an empty string.
	 */
	public function setYearFormat(string $format = '%Y'): self
	{
		$this->yearFormat = $format;
		return $this;
	}

	/**
	 * @param  string|array  $format  Either a format string containing <code>%a</code> or
	 *                                <code>%A</code> or an array starting with Monday, for example:
	 *                                <code>['M', 'T', 'W', 'T', 'F', 'S', 'S']</code>.
	 */
	public function setWeekdayFormat($format = '%a'): self
	{
		$this->weekdayFormat = $format;
		$this->buildWeekdays = true;
		return $this;
	}

	/**
	 * Sets the first day of the week in a calendar page view.
	 *
	 * @link http://unicode.org/repos/cldr/trunk/common/supplemental/supplementalData.xml
	 *
	 * @param  int|string  $firstWeekday  0 (for Monday) through 6 (for Sunday)
	 *                                    or an ISO 3166 country code for example
	 *                                    <code>BR</code> for Brazil, <code>SE</code> for Sweden.
	 * @return Calendar
	 */
	public function setFirstWeekday($firstWeekday = 0): self
	{
		$this->firstWeekday = is_string($firstWeekday)
				? self::firstWeekday($firstWeekday)
				: $firstWeekday;
		$this->buildWeekdays = true;
		return $this;
	}

	/**
	 * Adds  a entry (for example a holiday).
	 *
	 * @param  \DateTime  $date
	 * @param  string     $title
	 * @param  string     $link
	 * @param  string     $class
	 * @return Calendar
	 */
	public function addEntry($date, $title, $link = null, $class = 'holiday')
	{
		if (\is_string($date)) {
			$date = DateTime::create($date);
		}
		$this->entries[$date->format('Y-m-d')][] = array_filter([
				'class' => $class,
				'title' => $title,
				'link' => $link]);
		return $this;
	}

	/**
	 * The basis to generate your output.
	 *
	 * Keys:
	 * <ul>
	 *   <li><code>weekdays</code>
	 *   <li><code>years</code>
	 *   <ul>
	 *     <li><code>time</code>
	 *     <li><code>label</code>
	 *     <li><code>months</code>
	 *     <ul>
	 *       <li><code>time</code>
	 *       <li><code>label</code>
	 *       <li><code>month</code>
	 *       <li><code>weeks</code>
	 *       <ul>
	 *         <li><code>time</code>
	 *         <li><code>label</code>
	 *         <li><code>leading</code>
	 *         <li><code>following</code>
	 *         <li><code>days</code>
	 *         <ul>
	 *           <li><code>time</code>
	 *           <li><code>label</code>
	 *           <li><code>weekday</code>
	 *           <li><code>entries</code>
	 *           <ul>
	 *             <li><code>class</code>
	 *             <li><code>title</code>
	 *             <li><code>link</code>
	 *           </ul>
	 *         </ul>
	 *       </ul>
	 *     </ul>
	 *   </ul>
	 * </ul>
	 *
	 * @return array
	 */
	public function buildArray()
	{
		$this->buildWeekdays();
		$array = array(
			'weekdays' => $this->weekdays,
			'years' => []
		);
		$day = $this->from->copy();
		$first = true;
		$last = false;
		while (!$last) {
			$iso = $day->format('Y-m-d');
			$wd = ((int) $day->format('N') - $this->firstWeekday + 6) % 7;
			$d = (int) $day->format('d');
			$m = $day->format('m');
			$t = $day->format('t'); // last day in month: 28, 29, 30, 31
			$y = $day->format('Y');
			$w = $day->format('o-\WW'); // 2015-W53, 2016-W01 etc.
			$last = $day == $this->till;

			// first day in year
			if ($first || ($d == 1 && $m == 1)) {
				$year = array(
					'time' => $y,
					'label' => $day->formatLocalized($this->yearFormat),
					'months' => []
				);
			}
			// first day in month
			if ($first || $d == 1) {
				$month = array(
					'time' => "$y-$m",
					'label' => $day->formatLocalized($this->monthFormat),
					'month' => $m,
					'weeks' => []
				);
			}
			// first day in week
			if ($first || $wd == 0 || $d == 1) {
				$week = [];
				if ($this->firstWeekday == 0) {
					$week['time'] = $w;
					$week['label'] = $day->format('W');
				}
				$week['leading'] = $wd;
				$week['following'] = null;
				$week['days'] = [];
			}
			// day
			$week['days'][] = array_filter(array(
				'time' => $iso,
				'label' => $day->formatLocalized($this->dayFormat),
				'weekday' => \strtolower($day->format('D')),
				'entries' => \array_key_exists($iso, $this->entries) ? $this->entries[$iso] : null
			));
			// last day in week
			if ($last || $wd == 6 || $d == $t) {
				if ($wd < 6) {
					$week['following'] = 6 - $wd;
				}
				$month['weeks'][] = array_filter($week);
			}
			// last day in month
			if ($last || $d == $t) {
				$year['months'][] = array_filter($month);
			}
			// last day in year
			if ($last || ($m == 12 && $d == $t)) {
				$array['years'][] = array_filter($year);
			}
			$day->addDays(1);
			$first = false;
		}
		return $array;
	}

	private static function firstWeekday($countryCode)
	{
		$countryCode = strtoupper($countryCode);
		$territories = array(
			array(
				'AD', 'AI', 'AL', 'AM', 'AN', 'AT', 'AX', 'AZ', 'BA', 'BE', 'BG', 'BM',
				'BN', 'BY', 'CH', 'CL', 'CM', 'CR', 'CY', 'CZ', 'DE', 'DK', 'EC', 'EE',
				'ES', 'FI', 'FJ', 'FO', 'FR', 'GB', 'GE', 'GF', 'GP', 'GR', 'HR', 'HU',
				'IS', 'IT', 'KG', 'KZ', 'LB', 'LI', 'LK', 'LT', 'LU', 'LV', 'MC', 'MD',
				'ME', 'MK', 'MN', 'MQ', 'MY', 'NL', 'NO', 'PL', 'PT', 'RE', 'RO', 'RS',
				'RU', 'SE', 'SI', 'SK', 'SM', 'TJ', 'TM', 'TR', 'UA', 'UY', 'UZ', 'VA',
				'VN', 'XK'
			),
			array(), array(), array(), array('BD', 'MV'),
			array(
				'AE', 'AF', 'BH', 'DJ', 'DZ', 'EG', 'IQ', 'IR', 'JO', 'KW', 'LY', 'MA',
				'OM', 'QA', 'SD', 'SY'
			),
			array(
				'AG', 'AR', 'AS', 'AU', 'BR', 'BS', 'BT', 'BW', 'BZ', 'CA', 'CN', 'CO',
				'DM', 'DO', 'ET', 'GT', 'GU', 'HK', 'HN', 'ID', 'IE', 'IL', 'IN', 'JM',
				'JP', 'KE', 'KH', 'KR', 'LA', 'MH', 'MM', 'MO', 'MT', 'MX', 'MZ', 'NI',
				'NP', 'NZ', 'PA', 'PE', 'PH', 'PK', 'PR', 'PY', 'SA', 'SG', 'SV', 'TH',
				'TN', 'TT', 'TW', 'UM', 'US', 'VE', 'VI', 'WS', 'YE', 'ZA', 'ZW'
			)
		);
		foreach ($territories as $i => $territory) {
			if (in_array($countryCode, $territory)) return $i;
		}
		return 0;
	}

	private function buildWeekdays()
	{
		if ($this->buildWeekdays) {
			$this->weekdays = [];
			$day = (new DateTime('2014-01-06'))->addDays($this->firstWeekday);
			for ($i = 0; $i < 7; $i++) {
				$index = \strtolower($day->format('D'));
				if (\is_array($this->weekdayFormat)) {
					$this->weekdays[$index] = $this->weekdayFormat[($i + $this->firstWeekday) % 7];
				}
				else {
					$this->weekdays[$index] = $day
							->formatLocalized($this->weekdayFormat);
				}
				$day->addDays(1);
			}
			$this->buildWeekdays = false;
		}
		return $this;
	}
}