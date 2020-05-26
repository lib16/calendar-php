<?php
namespace Lib16\Calendar;

class DateTime extends \DateTime
{

    const MON = 1;

    const TUE = 2;

    const WED = 3;

    const THU = 4;

    const FRI = 5;

    const SAT = 6;

    const SUN = 7;

    const WEEKDAY = 'l';

    const WEEKDAY_SHORT = 'D';

    const WEEKDAY_ISO8601 = 'N';

    public static $encodeUtf8 = true;

    public static function create(
        $day = null,
        int $month = null,
        int $year = null
    ): self {
        if (\is_string($day)) {
            $datetime = new static($day);
        } elseif ($day instanceof \DateTime) {
            $datetime = new static($day->format('Y-m-d'));
        } else {
            if ($day > 31) {
                $swap = $day;
                $day = $year;
                $year = $swap;
            }
            $year = $year ?? date('Y');
            $month = $month ?? date('m');
            $day = $day ?? date('d');
            $datetime = new static("$year-$month-$day");
        }
        return $datetime;
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

    public function copy(): self
    {
        return clone $this;
    }

    /**
     * @param int $years
     * @return self
     */
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

    public function lastDayOfMonth(): self
    {
        return $this->modify('last day of this month');
    }

    /**
     * Sets the current date to the nearest or next workday.
     *
     * @param bool $sunBecomesTue
     *            if <code>true</code> sunday becomes tuesday,
     *            otherwise monday.
     * @param bool $satBecomesFri
     *            if <code>true</code> saturday becomes friday,
     *            otherwise monday.
     */
    public function forceWorkday(
        bool $sunBecomesTue = false,
        bool $satBecomesFri = false
    ): self {
        if ($this->checkWeekday(self::SAT)) {
            $this->addDays($satBecomesFri ? -1 : 2);
        } elseif ($this->checkWeekday(self::SUN)) {
            $this->addDays($sunBecomesTue ? 2 : 1);
        }
        return $this;
    }

    public function checkWeekday(int ...$weekdays): bool
    {
        return in_array($this->weekdayIso8601(), $weekdays);
    }

    public function weekday(): string
    {
        return $this->format(self::WEEKDAY_SHORT);
    }

    public function weekdayIso8601(): int
    {
        return (int) $this->format(self::WEEKDAY_ISO8601);
    }

    public function mysqlDate(): string
    {
        return $this->format('Y-m-d');
    }

    public function mysqlDateTime(): string
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     * Returns a string representation according to locale settings.
     *
     * @link http://php.net/manual/en/function.strftime.php
     * @link http://php.net/manual/en/class.datetime.php
     *
     * @param string $format
     *            A format string containing specifiers like <code>%a</code>,
     *            <code>%B</code> etc.
     */
    public function formatLocalized(string $format): string
    {
        $string = strftime($format, $this->getTimestamp());
        if (self::$encodeUtf8) {
            $string = utf8_encode($string);
        }
        return $string;
    }

    public function __toString(): string
    {
        return $this->format('r');
    }

    /**
     * @deprecated
     */
    public static function setCharacterEncoding(string $encoding)
    {
        self::$encodeUtf8 = $encoding == 'UTF-8';
    }
}