<?php
namespace Lib16\Calendar\Tests;

use Lib16\Calendar\DateTime;
use Lib16\Calendar\Calendar;

require_once 'src/DateTime.php';
require_once 'src/Calendar.php';

class CalendarTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider yearProvider
	 */
	public function testYear(int $year = null, Calendar $expected)
	{
		$actual = Calendar::year($year);
		$this->assertEquals($expected, $actual);
	}

	public function yearProvider()
	{
		return array(
			[null, Calendar::span(DateTime::create(1, 1), DateTime::create(31, 12))],
			[2016, Calendar::span('2016-01-01', '2016-12-31')]
		);
	}

	/**
	 * @dataProvider monthProvider
	 */
	public function testMonth(int $month = null, int $year = null, Calendar $expected)
	{
		$actual = Calendar::month($month, $year);
		$this->assertEquals($expected, $actual);
	}

	public function monthProvider()
	{
		$year = date('Y');
		return array(
			[null, null, Calendar::span(
					DateTime::create(1),
					DateTime::create(1)->modify('last day of this month'))],
			[4, null, Calendar::span("$year-04-01", "$year-04-30")],
			[2, 2016, Calendar::span("2016-02-01", "2016-02-29")]
		);
	}

	/**
	 * @dataProvider monthsProvider
	 */
	public function testMonths(
			int $delta = null, int $month = null, int $year = null, Calendar $expected)
	{
		$actual = Calendar::months($delta, $month, $year);
		$this->assertEquals($expected, $actual);
	}

	public function monthsProvider()
	{
		return array(
			[0, null, null, Calendar::span(
					DateTime::create(1),
					DateTime::create(1)->modify('last day of this month'))],
			[-1, null, null, Calendar::span(
					DateTime::create(1),
					DateTime::create(1)->modify('last day of this month'))],
			[+1, null, null, Calendar::span(
					DateTime::create(1),
					DateTime::create(1)->modify('last day of this month'))],
			[-3, null, null, Calendar::span(
					DateTime::create(1)->addMonths(-2),
					DateTime::create(1)->modify('last day of this month'))],
			[+2, null, null, Calendar::span(
					DateTime::create(1),
					DateTime::create(1)->addMonths(1)->modify('last day of this month'))]
		);
	}

	public function testBuildArray()
	{
		$actual = Calendar::span('2015-12-29', '2016-02-01')
				->addEntry('2016-01-30', 'International PHP Day')
				->buildArray();
		$expected = array(
			'weekdays' => array(
				'mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu',
				'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun'
			),
			'years' => array(
				array(
					'time' => '2015',
					'label' => '2015',
					'months' => array(
						array(
							'time' => '2015-12',
							'label' => 'December',
							'month' => '12',
							'weeks' => array(
								array(
									'time' => '2015-W53',
									'label' => '53',
									'leading' => 1,
									'following' => 3,
									'days' => array(
										array(
											'time' => '2015-12-29',
											'label' => '29',
											'weekday' => 'tue'
										),
										array(
											'time' => '2015-12-30',
											'label' => '30',
											'weekday' => 'wed'
										),
										array(
											'time' => '2015-12-31',
											'label' => '31',
											'weekday' => 'thu'
										),
									)
								)
							)
						)
					)
				),
				array(
					'time' => '2016',
					'label' => '2016',
					'months' => array(
						array(
							'time' => '2016-01',
							'label' => 'January',
							'month' => '01',
							'weeks' => array(
								array(
									'time' => '2015-W53',
									'label' => '53',
									'leading' => 4,
									'days' => array(
										array(
											'time' => '2016-01-01',
											'label' => '1',
											'weekday' => 'fri'
										),
										array(
											'time' => '2016-01-02',
											'label' => '2',
											'weekday' => 'sat'
										),
										array(
											'time' => '2016-01-03',
											'label' => '3',
											'weekday' => 'sun'
										),
									)
								),
								array(
									'time' => '2016-W01',
									'label' => '01',
									'days' => array(
										array(
											'time' => '2016-01-04',
											'label' => '4',
											'weekday' => 'mon'
										),
										array(
											'time' => '2016-01-05',
											'label' => '5',
											'weekday' => 'tue'
										),
										array(
											'time' => '2016-01-06',
											'label' => '6',
											'weekday' => 'wed'
										),
										array(
											'time' => '2016-01-07',
											'label' => '7',
											'weekday' => 'thu'
										),
										array(
											'time' => '2016-01-08',
											'label' => '8',
											'weekday' => 'fri'
										),
										array(
											'time' => '2016-01-09',
											'label' => '9',
											'weekday' => 'sat'
										),
										array(
											'time' => '2016-01-10',
											'label' => '10',
											'weekday' => 'sun'
										),
									)
								),
								array(
									'time' => '2016-W02',
									'label' => '02',
									'days' => array(
										array(
											'time' => '2016-01-11',
											'label' => '11',
											'weekday' => 'mon'
										),
										array(
											'time' => '2016-01-12',
											'label' => '12',
											'weekday' => 'tue'
										),
										array(
											'time' => '2016-01-13',
											'label' => '13',
											'weekday' => 'wed'
										),
										array(
											'time' => '2016-01-14',
											'label' => '14',
											'weekday' => 'thu'
										),
										array(
											'time' => '2016-01-15',
											'label' => '15',
											'weekday' => 'fri'
										),
										array(
											'time' => '2016-01-16',
											'label' => '16',
											'weekday' => 'sat'
										),
										array(
											'time' => '2016-01-17',
											'label' => '17',
											'weekday' => 'sun'
										),
									)
								),
								array(
									'time' => '2016-W03',
									'label' => '03',
									'days' => array(
										array(
											'time' => '2016-01-18',
											'label' => '18',
											'weekday' => 'mon'
										),
										array(
											'time' => '2016-01-19',
											'label' => '19',
											'weekday' => 'tue'
										),
										array(
											'time' => '2016-01-20',
											'label' => '20',
											'weekday' => 'wed'
										),
										array(
											'time' => '2016-01-21',
											'label' => '21',
											'weekday' => 'thu'
										),
										array(
											'time' => '2016-01-22',
											'label' => '22',
											'weekday' => 'fri'
										),
										array(
											'time' => '2016-01-23',
											'label' => '23',
											'weekday' => 'sat'
										),
										array(
											'time' => '2016-01-24',
											'label' => '24',
											'weekday' => 'sun'
										),
									)
								),
								array(
									'time' => '2016-W04',
									'label' => '04',
									'days' => array(
										array(
											'time' => '2016-01-25',
											'label' => '25',
											'weekday' => 'mon'
										),
										array(
											'time' => '2016-01-26',
											'label' => '26',
											'weekday' => 'tue'
										),
										array(
											'time' => '2016-01-27',
											'label' => '27',
											'weekday' => 'wed'
										),
										array(
											'time' => '2016-01-28',
											'label' => '28',
											'weekday' => 'thu'
										),
										array(
											'time' => '2016-01-29',
											'label' => '29',
											'weekday' => 'fri'
										),
										array(
											'time' => '2016-01-30',
											'label' => '30',
											'weekday' => 'sat',
											'entries' => array(
												array(
													'class' => 'holiday',
													'title' => 'International PHP Day'
												)
											)
										),
										array(
											'time' => '2016-01-31',
											'label' => '31',
											'weekday' => 'sun'
										),
									)
								)
							)
						),
						array(
							'time' => '2016-02',
							'label' => 'February',
							'month' => '02',
							'weeks' => array(
								array(
									'time' => '2016-W05',
									'label' => '05',
									'following' => 6,
									'days' => array(
										array(
											'time' => '2016-02-01',
											'label' => '1',
											'weekday' => 'mon'
										)
									)
								)
							)
						)
					)
				)
			)
		);
		$this->assertSame($expected, $actual);
	}
}
