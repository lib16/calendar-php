<?php
namespace Lib16\Calendar\Tests;

use Lib16\Calendar\DateTime as D;
use PHPUnit\Framework\TestCase;

class DExtended extends D
{
    public function __toString(): string
    {
        return $this->mysqlDateTime();
    }
}

class DateTimeTest extends TestCase
{

    public function testAddDays()
    {
        $actual = D::create(1, 1, 2016)
            ->addDays(- 1)
            ->addDays(2)
            ->addDays(0)
            ->mysqlDateTime();
        $this->assertEquals('2016-01-02 00:00:00', $actual);
    }

    public function testAddMonths()
    {
        $actual = D::create(31, 1, 2016)
            ->addMonths(1)
            ->addMonths(- 2)
            ->addMonths(0)
            ->mysqlDateTime();
        $this->assertEquals('2016-01-02 00:00:00', $actual);
    }

    public function testAddYears()
    {
        $actual = D::create(29, 2, 2016)
            ->addYears(1)
            ->addYears(- 2)
            ->addYears(0)
            ->mysqlDateTime();
        $this->assertEquals('2015-03-01 00:00:00', $actual);
    }

    public function testAddHours()
    {
        $actual = D::create('2016-03-01 04:23:24')
            ->addHours(1)
            ->addHours(- 7)
            ->addHours(0)
            ->mysqlDateTime();
        $this->assertEquals('2016-02-29 22:23:24', $actual);
    }

    public function testAddMinutes()
    {
        $actual = D::create('2016-03-01 00:23:10')
            ->addMinutes(7)
            ->addMinutes(- 40)
            ->addMinutes(0)
            ->mysqlDateTime();
        $this->assertEquals('2016-02-29 23:50:10', $actual);
    }

    public function testAddSeconds()
    {
        $actual = D::create('2016-03-01 00:00:23')
            ->addSeconds(7)
            ->addSeconds(- 40)
            ->addSeconds(0)
            ->mysqlDateTime();
        $this->assertEquals('2016-02-29 23:59:50', $actual);
    }

    /**
     * @dataProvider lastDayOfMonthProvider
     */
    public function testLastDayOfMonth(
        string $expected,
        int $month = null,
        int $year = null
    ) {
        $actual = D::create(1, $month, $year)->lastDayOfMonth();
        $this->assertEquals($expected, $actual->mysqlDate());
    }

    public function lastDayOfMonthProvider(): array
    {
        return [
            ['2016-02-29', 2, 2016],
            ['2019-02-28', 2, 2019]
        ];
    }

    /**
     * @dataProvider forceWorkdayProvider
     */
    public function testForceWorkday(
        string $expectedDate,
        string $date,
        bool $satBecomesFri = false,
        bool $sunBecomesTue = false
    ) {
        $date = D::create($date)
            ->forceWorkday($satBecomesFri, $sunBecomesTue)
            ->mysqlDate();
        $this->assertEquals($expectedDate, $date);
    }

    public function forceWorkdayProvider(): array
    {
        return [
            ['2019-09-23', '2019-09-21'],
            ['2019-09-23', '2019-09-22'],
            ['2019-09-23', '2019-09-21', true],
            ['2019-09-24', '2019-09-22', true],
            ['2019-09-20', '2019-09-21', false, true],
            ['2019-09-23', '2019-09-22', false, true],
            ['2019-09-20', '2019-09-21', true, true],
            ['2019-09-24', '2019-09-22', true, true]
        ];
    }

    /**
     * @dataProvider formatLocalizedProvider
     */
    public function testFormatLocalized(
        string $format,
        string $expected,
        string $locale = 'en'
    ) {
        \setlocale(LC_TIME, $locale);
        $actual = D::create(1, 3, 2016)->formatLocalized($format);
        $this->assertSame($expected, $actual);
    }

    public function formatLocalizedProvider(): array
    {
        return [
            ['%a', 'Tue'],
            ['%A', 'Tuesday'],
            ['%d', '01'],
            ['%#d', '1'],
            ['%b', 'Mar'],
            ['%B', 'March'],
            ['%m', '03'],
            ['%#m', '3'],
            ['%Y', '2016'],
            ['%y', '16'],
//             ['%a', 'Di', 'de'],
//             ['%A', 'Dienstag', 'de'],
//             ['%d', '01', 'de'],
//             ['%#d', '1', 'de'],
//             ['%b', 'Mrz', 'de'],
//             ['%B', 'März', 'de'],
//             ['%m', '03', 'de'],
//             ['%#m', '3', 'de'],
//             ['%Y', '2016', 'de'],
//             ['%y', '16', 'de']
        ];
    }

    public function testCopy()
    {
        $src = new D('2016-03-29 09:15:55');
        $dst = $src
            ->copy()
            ->addHours(14)
            ->addMinutes(44)
            ->addSeconds(5);
        $this->assertEquals('2016-03-29 09:15:55', $src->mysqlDateTime());
        $this->assertEquals('2016-03-30 00:00:00', $dst->mysqlDateTime());
    }

    /**
     * @dataProvider createProvider
     */
    public function testCreate(
        string $expected,
        $day = null,
        int $month = null,
        int $year = null
    ) {
        $actual = D::create($day, $month, $year)->mysqlDateTime();
        $this->assertEquals($expected, $actual);
    }

    public function createProvider(): array
    {
        return [
            [date('Y-m-d 00:00:00')],
            [date('Y-m-01 00:00:00'), 1],
            [date('Y-02-01 00:00:00'), 1, 2],
            ['2016-03-02 00:00:00', 2, 3, 2016],
            [date('2016-m-d 00:00:00'), 2016],
            [date('2016-03-d 00:00:00'), 2016, 3],
            ['2016-03-02 00:00:00', 2016, 3, 2],
            [date('Y-03-d 00:00:00'), null, 3],
            ['2016-01-02 06:30:10', '2016-01-02 06:30:10'],
            ['2016-01-02 00:00:00', new \DateTime('2016-01-02 06:30:10')],
            ['2016-01-02 00:00:00', new D('2016-01-02 06:30:10')]
        ];
    }

    /**
     * @dataProvider easterProvider
     */
    public function testEaster(string $expected, int $year)
    {
        $actual = D::easter($year)->mysqlDateTime();
        $this->assertEquals($expected, $actual);
    }

    public function easterProvider(): array
    {
        return [
            [
                '2014-04-20 00:00:00',
                2014
            ],
            [
                '2015-04-05 00:00:00',
                2015
            ],
            [
                '2016-03-27 00:00:00',
                2016
            ],
            [
                '2017-04-16 00:00:00',
                2017
            ]
        ];
    }

    public function testInheritance()
    {
        $dt = DExtended::create(1, 1, 2020);
        $this->assertEquals('2020-01-01 00:00:00', $dt);

        $this->assertTrue(DExtended::$encodeUtf8);
        $this->assertTrue(D::$encodeUtf8);
        D::setCharacterEncoding('ISO-8859-15');
        $this->assertFalse(DExtended::$encodeUtf8);
        $this->assertFalse(D::$encodeUtf8);
    }

    public function testOutput() {
        $tz = \date_default_timezone_get();
        \date_default_timezone_set('Europe/London');

        $dt = D::create(30, 6, 2019)->setTime(8, 15);
        $this->assertEquals('Sun', $dt->weekday());
        $this->assertEquals(7, $dt->weekdayIso8601());
        $this->assertEquals('2019-06-30', $dt->mysqlDate());
        $this->assertEquals('2019-06-30 08:15:00', $dt->mysqlDateTime());
        $this->assertEquals('Sun, 30 Jun 2019 08:15:00 +0100', $dt);

        \date_default_timezone_set($tz);
    }
}