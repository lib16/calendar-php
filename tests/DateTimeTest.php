<?php

namespace Lib16\Calendar\Tests;

require_once 'src/DateTime.php';

use Lib16\Calendar\DateTime;

class DateTimeTest extends \PHPUnit_Framework_TestCase
{
	public function testAddDays()
	{
		$actual = DateTime::create(1, 1, 2016)->addDays(-1)->addDays(2)->addDays(0);
		$expected = DateTime::create(2, 1, 2016);
		$this->assertEquals($expected, $actual);
	}

	public function testAddMonths()
	{
		$actual = DateTime::create(31, 1, 2016)
				->addMonths(1)->addMonths(-2)->addMonths(0);
		$expected = DateTime::create(2, 1, 2016);
		$this->assertEquals($expected, $actual);
	}

	public function testAddYears()
	{
		$actual = DateTime::create(29, 2, 2016)
				->addYears(1)->addYears(-2)->addYears(0);
		$expected = DateTime::create(1, 3, 2015);
		$this->assertEquals($expected, $actual);
	}

	public function testAddHours()
	{
		$actual = DateTime::create('2016-03-01 04:23:24')
				->addHours(1)->addHours(-7)->addHours(0);
		$expected = DateTime::create('2016-02-29 22:23:24');
		$this->assertEquals($expected, $actual);
	}

	public function testAddMinutes()
	{
		$actual = DateTime::create('2016-03-01 00:23:10')
				->addMinutes(7)->addMinutes(-40)->addMinutes(0);
		$expected = DateTime::create('2016-2-29 23:50:10');
		$this->assertEquals($expected, $actual);
	}

	public function testAddSeconds()
	{
		$actual = DateTime::create('2016-03-01 00:00:23')
				->addSeconds(7)->addSeconds(-40)->addSeconds(0);
		$expected = DateTime::create('2016-2-29 23:59:50');
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @dataProvider forceWorkdayProvider
	 */
	public function testForceWorkday(string $date, bool $next, string $expected)
	{
		$actual = DateTime::create($date)->forceWorkday($next);
		$expected = DateTime::create($expected);
		$this->assertEquals($expected, $actual);
	}

	public function forceWorkdayProvider(): array
	{
		return [['2016-03-04', false, '2016-03-04'],
			['2016-03-05', false, '2016-03-04'],
			['2016-03-06', false, '2016-03-07'],
			['2016-03-07', false, '2016-03-07'],
			['2016-03-04', true, '2016-03-04'],
			['2016-03-05', true, '2016-03-07'],
			['2016-03-06', true, '2016-03-07'],
			['2016-03-07', true, '2016-03-07']];
	}

	/**
	 * @dataProvider formatLocalizedProvider
	 */
	public function testFormatLocalized(string $format, string $expected)
	{
		$actual = DateTime::create(1, 4, 2016)->formatLocalized($format);
		$this->assertSame($expected, $actual);
	}

	public function formatLocalizedProvider(): array
	{
		return [['%a', 'Fri'],
			['%A', 'Friday'],
			['%d', '01'],
			['%#d', '1'],
			['%b', 'Apr'],
			['%B', 'April'],
			['%m', '04'],
			['%#m', '4'],
			['%Y', '2016'],
			['%y', '16']];
	}

	public function testCopy()
	{
		$this->assertEquals(DateTime::create(29, 3, 2016)->copy(), new DateTime('2016-03-29'));
	}

	/**
	 * @dataProvider createProvider
	 */
	public function testCreate($day, int $month = null, int $year = null, DateTime $expected)
	{
		$actual = DateTime::create($day, $month, $year);
		$this->assertEquals($expected, $actual);
	}

	public function createProvider(): array
	{
		return [[null, null, null, new DateTime(date('Y-m-d'))],
			[1, null, null, new DateTime(date('Y-m-01'))],
			[1, 2, null, new DateTime(date('Y-02-01'))],
			[2, 3, 2016, new DateTime('2016-03-02')],
			[2016, null, null, new DateTime(date('2016-m-d'))],
			[2016, 2, null, new DateTime(date('2016-02-d'))],
			[2016, 3, 2, new DateTime('2016-03-02')],
			[null, 3, null, new DateTime(date('Y-03-d'))],
			['2016-01-02', null, null, new DateTime('2016-01-02')],
			[new \DateTime('2016-01-02'), null, null, new DateTime('2016-01-02')],
			[new DateTime('2016-01-02'), null, null, new DateTime('2016-01-02')]
		];
	}

	/**
	 * @dataProvider easterProvider
	 */
	public function testEaster(int $year, DateTime $expected)
	{
		$actual = DateTime::easter($year);
		$this->assertEquals($expected, $actual);
	}

	public function easterProvider(): array
	{
		return [[2014, DateTime::create(20, 4, 2014)],
			[2015, DateTime::create(05, 4, 2015)],
			[2016, DateTime::create(27, 3, 2016)],
			[2017, DateTime::create(16, 4, 2017)]];
	}
}