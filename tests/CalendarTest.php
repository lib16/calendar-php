<?php
namespace Lib16\Calendar\Tests;

use Lib16\Calendar\DateTime;
use Lib16\Calendar\Calendar;
use PHPUnit\Framework\TestCase;

class CalendarTest extends TestCase
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
        return [
            [null, Calendar::span(DateTime::create(1, 1), DateTime::create(31, 12))],
            [2016, Calendar::span('2016-01-01', '2016-12-31')]
        ];
    }

    /**
     * @dataProvider monthProvider
     */
    public function testMonth(
        int $month = null,
        int $year = null,
        Calendar $expected
    ) {
        $actual = Calendar::month($month, $year);
        $this->assertEquals($expected, $actual);
    }

    public function monthProvider()
    {
        $year = date('Y');
        return [
            [null, null, Calendar::span(
                DateTime::create(1),
                DateTime::create(1)->modify('last day of this month')
            )],
            [4, null, Calendar::span("$year-04-01", "$year-04-30")],
            [2, 2016, Calendar::span("2016-02-01", "2016-02-29")]
        ];
    }

    /**
     * @dataProvider monthsProvider
     */
    public function testMonths(
        int $delta = null,
        int $month = null,
        int $year = null,
        Calendar $expected
    ) {
        $actual = Calendar::months($delta, $month, $year);
        $this->assertEquals($expected, $actual);
    }

    public function monthsProvider()
    {
        return [
            [0, null, null, Calendar::span(
                DateTime::create(1),
                DateTime::create(1)->modify('last day of this month')
            )],
            [-1, null, null, Calendar::span(
                DateTime::create(1),
                DateTime::create(1)->modify('last day of this month')
            )],
            [+1, null, null, Calendar::span(
                DateTime::create(1),
                DateTime::create(1)->modify('last day of this month')
            )],
            [-3, null, null, Calendar::span(
                DateTime::create(1)->addMonths(-2),
                DateTime::create(1)->modify('last day of this month')
            )],
            [+2, null, null, Calendar::span(
                DateTime::create(1),
                DateTime::create(1)->addMonths(1)->modify('last day of this month')
            )]
        ];
    }

    /**
     * @dataProvider setFirstWeekdayProvider
     */
    public function testSetFirstWeekday(
        $firstWeekday,
        string $expectedWeekday
    ) {
        $cal = Calendar::month(1, 2020);
        $cal->setFirstWeekday($firstWeekday);
        $data = $cal->buildArray();
        $data = $data['years'][0]['months'][0]['weeks'][1]['days'][0]['weekday'];
        $this->assertEquals($expectedWeekday, $data);
    }

    public function setFirstWeekdayProvider()
    {
        return [
            ['DE', 'mon'],
            ['BD', 'fri'],
            ['IQ', 'sat'],
            ['US', 'sun'],
            ['XX', 'mon']
        ];
    }

    /**
     * @dataProvider setWeekdayFormatProvider
     */
    public function testSetWeekdayFormat(
        $weekdayFormat,
        string $expectedWeekday
    ) {
        $cal = Calendar::month(1, 2020);
        $cal->setWeekdayFormat($weekdayFormat);
        $data = $cal->buildArray();
        $data = $data['weekdays']['mon'];
        $this->assertEquals($expectedWeekday, $data);
    }

    public function setWeekdayFormatProvider()
    {
        return [
            ['%a', 'Mon'],
            ['%A', 'Monday'],
            [['M', 'T', 'W', 'T', 'F', 'S', 'S'], 'M'],
        ];
    }

    public function testBuildArray()
    {
        $actual = Calendar::span('2015-12-29', '2016-02-01')
            ->addEntry('2016-01-30', 'International PHP Day')
            ->buildArray();
        $expected = [
            'weekdays' => [
                'mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu',
                'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun'
            ],
            'years' => [
                [
                    'time' => '2015',
                    'label' => '2015',
                    'months' => [
                        [
                            'time' => '2015-12',
                            'label' => 'December',
                            'month' => '12',
                            'weeks' => [
                                [
                                    'time' => '2015-W53',
                                    'label' => '53',
                                    'leading' => 1,
                                    'following' => 3,
                                    'days' => [
                                        [
                                            'time' => '2015-12-29',
                                            'label' => '29',
                                            'weekday' => 'tue'
                                        ],
                                        [
                                            'time' => '2015-12-30',
                                            'label' => '30',
                                            'weekday' => 'wed'
                                        ],
                                        [
                                            'time' => '2015-12-31',
                                            'label' => '31',
                                            'weekday' => 'thu'
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'time' => '2016',
                    'label' => '2016',
                    'months' => [
                        [
                            'time' => '2016-01',
                            'label' => 'January',
                            'month' => '01',
                            'weeks' => [
                                [
                                    'time' => '2015-W53',
                                    'label' => '53',
                                    'leading' => 4,
                                    'days' => [
                                        [
                                            'time' => '2016-01-01',
                                            'label' => '1',
                                            'weekday' => 'fri'
                                        ],
                                        [
                                            'time' => '2016-01-02',
                                            'label' => '2',
                                            'weekday' => 'sat'
                                        ],
                                        [
                                            'time' => '2016-01-03',
                                            'label' => '3',
                                            'weekday' => 'sun'
                                        ],
                                    ]
                                ],
                                [
                                    'time' => '2016-W01',
                                    'label' => '01',
                                    'days' => [
                                        [
                                            'time' => '2016-01-04',
                                            'label' => '4',
                                            'weekday' => 'mon'
                                        ],
                                        [
                                            'time' => '2016-01-05',
                                            'label' => '5',
                                            'weekday' => 'tue'
                                        ],
                                        [
                                            'time' => '2016-01-06',
                                            'label' => '6',
                                            'weekday' => 'wed'
                                        ],
                                        [
                                            'time' => '2016-01-07',
                                            'label' => '7',
                                            'weekday' => 'thu'
                                        ],
                                        [
                                            'time' => '2016-01-08',
                                            'label' => '8',
                                            'weekday' => 'fri'
                                        ],
                                        [
                                            'time' => '2016-01-09',
                                            'label' => '9',
                                            'weekday' => 'sat'
                                        ],
                                        [
                                            'time' => '2016-01-10',
                                            'label' => '10',
                                            'weekday' => 'sun'
                                        ],
                                    ]
                                ],
                                [
                                    'time' => '2016-W02',
                                    'label' => '02',
                                    'days' => [
                                        [
                                            'time' => '2016-01-11',
                                            'label' => '11',
                                            'weekday' => 'mon'
                                        ],
                                        [
                                            'time' => '2016-01-12',
                                            'label' => '12',
                                            'weekday' => 'tue'
                                        ],
                                        [
                                            'time' => '2016-01-13',
                                            'label' => '13',
                                            'weekday' => 'wed'
                                        ],
                                        [
                                            'time' => '2016-01-14',
                                            'label' => '14',
                                            'weekday' => 'thu'
                                        ],
                                        [
                                            'time' => '2016-01-15',
                                            'label' => '15',
                                            'weekday' => 'fri'
                                        ],
                                        [
                                            'time' => '2016-01-16',
                                            'label' => '16',
                                            'weekday' => 'sat'
                                        ],
                                        [
                                            'time' => '2016-01-17',
                                            'label' => '17',
                                            'weekday' => 'sun'
                                        ],
                                    ]
                                ],
                                [
                                    'time' => '2016-W03',
                                    'label' => '03',
                                    'days' => [
                                        [
                                            'time' => '2016-01-18',
                                            'label' => '18',
                                            'weekday' => 'mon'
                                        ],
                                        [
                                            'time' => '2016-01-19',
                                            'label' => '19',
                                            'weekday' => 'tue'
                                        ],
                                        [
                                            'time' => '2016-01-20',
                                            'label' => '20',
                                            'weekday' => 'wed'
                                        ],
                                        [
                                            'time' => '2016-01-21',
                                            'label' => '21',
                                            'weekday' => 'thu'
                                        ],
                                        [
                                            'time' => '2016-01-22',
                                            'label' => '22',
                                            'weekday' => 'fri'
                                        ],
                                        [
                                            'time' => '2016-01-23',
                                            'label' => '23',
                                            'weekday' => 'sat'
                                        ],
                                        [
                                            'time' => '2016-01-24',
                                            'label' => '24',
                                            'weekday' => 'sun'
                                        ],
                                    ]
                                ],
                                [
                                    'time' => '2016-W04',
                                    'label' => '04',
                                    'days' => [
                                        [
                                            'time' => '2016-01-25',
                                            'label' => '25',
                                            'weekday' => 'mon'
                                        ],
                                        [
                                            'time' => '2016-01-26',
                                            'label' => '26',
                                            'weekday' => 'tue'
                                        ],
                                        [
                                            'time' => '2016-01-27',
                                            'label' => '27',
                                            'weekday' => 'wed'
                                        ],
                                        [
                                            'time' => '2016-01-28',
                                            'label' => '28',
                                            'weekday' => 'thu'
                                        ],
                                        [
                                            'time' => '2016-01-29',
                                            'label' => '29',
                                            'weekday' => 'fri'
                                        ],
                                        [
                                            'time' => '2016-01-30',
                                            'label' => '30',
                                            'weekday' => 'sat',
                                            'entries' => [
                                                [
                                                    'class' => 'holiday',
                                                    'title' => 'International PHP Day'
                                                ]
                                            ]
                                        ],
                                        [
                                            'time' => '2016-01-31',
                                            'label' => '31',
                                            'weekday' => 'sun'
                                        ],
                                    ]
                                ]
                            ]
                        ],
                        [
                            'time' => '2016-02',
                            'label' => 'February',
                            'month' => '02',
                            'weeks' => [
                                [
                                    'time' => '2016-W05',
                                    'label' => '05',
                                    'following' => 6,
                                    'days' => [
                                        [
                                            'time' => '2016-02-01',
                                            'label' => '1',
                                            'weekday' => 'mon'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $this->assertSame(serialize($expected), serialize($actual));
    }
}
