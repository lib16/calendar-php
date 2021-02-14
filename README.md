# `lib16/calendar-php`

[![Build Status](https://travis-ci.com/lib16/calendar-php.svg?branch=master)](https://travis-ci.com/lib16/calendar-php)

## Installation with Composer

This package is available on [packagist](https://packagist.org/packages/lib16/calendar),
so you can use [Composer](https://getcomposer.org) to install it:

```
composer require lib16/calendar
```


## The `Calendar` class

```php
<?php
require_once 'vendor/autoload.php';

use Lib16\Calendar\Calendar;
use Lib16\Calendar\DateTime;

setlocale(LC_TIME, 'de');
$easter = DateTime::easter(2016);
$calendar = Calendar::month(5, 2016)
    ->setMonthFormat('%b %Y')
    ->setFirstWeekday('DE')
    ->addEntry('2016-05-01', 'Tag der Arbeit')
    ->addEntry($easter->copy()->addDays(39), 'Christi Himmelfahrt')
    ->addEntry($easter->copy()->addDays(50), 'Pfingstmontag')
    ->addEntry($easter->copy()->addDays(60), 'Fronleichnam');
print json_encode($calendar->buildArray(), JSON_PRETTY_PRINT);
```

The generated output:

```json
{
    "weekdays": {
        "mon": "Mo",
        "tue": "Di",
        "wed": "Mi",
        "thu": "Do",
        "fri": "Fr",
        "sat": "Sa",
        "sun": "So"
    },
    "years": [
        {
            "time": "2016",
            "label": "2016",
            "months": [
                {
                    "time": "2016-05",
                    "label": "Mai 2016",
                    "month": "05",
                    "weeks": [
                        {
                            "time": "2016-W17",
                            "label": "17",
                            "leading": 6,
                            "days": [
                                {
                                    "time": "2016-05-01",
                                    "label": "1",
                                    "weekday": "sun",
                                    "entries": [
                                        {
                                            "class": "holiday",
                                            "title": "Tag der Arbeit"
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "time": "2016-W18",
                            "label": "18",
                            "days": [
                                {
                                    "time": "2016-05-02",
                                    "label": "2",
                                    "weekday": "mon"
                                },
                                {
                                    "time": "2016-05-03",
                                    "label": "3",
                                    "weekday": "tue"
                                },
                                {
                                    "time": "2016-05-04",
                                    "label": "4",
                                    "weekday": "wed"
                                },
                                {
                                    "time": "2016-05-05",
                                    "label": "5",
                                    "weekday": "thu",
                                    "entries": [
                                        {
                                            "class": "holiday",
                                            "title": "Christi Himmelfahrt"
                                        }
                                    ]
                                },
                                {
                                    "time": "2016-05-06",
                                    "label": "6",
                                    "weekday": "fri"
                                },
                                {
                                    "time": "2016-05-07",
                                    "label": "7",
                                    "weekday": "sat"
                                },
                                {
                                    "time": "2016-05-08",

…

```


## The `DateTime` class

`DateTime` extends the standard [PHP class](http://php.net/manual/en/class.datetime.php)
with the same name.

Assuming that current date is
<var><time>Thu, 23 Feb 2017 20:59:49 +0100</time></var>:

```php
<?php
require_once 'vendor/autoload.php';

use Lib16\Calendar\DateTime;

const LN = "\n";

print      new DateTime();
print LN . new DateTime('2017-02-22');
print LN . new DateTime('first day of next month');

```

The generated output:

```
Thu, 23 Feb 2017 20:59:49 +0100
Wed, 22 Feb 2017 00:00:00 +0100
Wed, 01 Mar 2017 20:59:49 +0100
```


### The `create` method

```php
<?php
require_once 'vendor/autoload.php';

use Lib16\Calendar\DateTime;

const LN = "\n";

print      DateTime::create();
print LN . DateTime::create(new DateTime());
print LN . DateTime::create(new \DateTime());
print LN;
print LN . DateTime::create('2017-02-22');
print LN . DateTime::create('2017-02');
print LN . DateTime::create('22.02.2017');
print LN . DateTime::create('02/22/2017');
print LN . DateTime::create('last day of previous month');
print LN . DateTime::create('first monday of june 2017');
print LN . DateTime::create('now');
print LN;
print LN . DateTime::create(22, 2, 2017);
print LN . DateTime::create(22, 2);
print LN . DateTime::create(22);
print LN . DateTime::create(2017, 2, 22);
print LN . DateTime::create(2017, 2);
print LN . DateTime::create(2017);

```

The generated output:

```
Thu, 23 Feb 2017 00:00:00 +0100
Thu, 23 Feb 2017 00:00:00 +0100
Thu, 23 Feb 2017 00:00:00 +0100

Wed, 22 Feb 2017 00:00:00 +0100
Wed, 01 Feb 2017 00:00:00 +0100
Wed, 22 Feb 2017 00:00:00 +0100
Wed, 22 Feb 2017 00:00:00 +0100
Tue, 31 Jan 2017 20:59:49 +0100
Mon, 05 Jun 2017 00:00:00 +0200
Thu, 23 Feb 2017 20:59:49 +0100

Wed, 22 Feb 2017 00:00:00 +0100
Wed, 22 Feb 2017 00:00:00 +0100
Wed, 22 Feb 2017 00:00:00 +0100
Wed, 22 Feb 2017 00:00:00 +0100
Thu, 23 Feb 2017 00:00:00 +0100
Thu, 23 Feb 2017 00:00:00 +0100
```


### The `easter` method

```php
<?php
require_once 'vendor/autoload.php';

use Lib16\Calendar\DateTime;

print DateTime::easter(2017);
```

The generated output:

```
Sun, 16 Apr 2017 00:00:00 +0200
```


### Modify Dates

```php
<?php
require_once 'vendor/autoload.php';

use Lib16\Calendar\DateTime;

const LN = "\n";

print LN . DateTime::create('2016-03-29')->addYears(2);
print LN . DateTime::create('2016-03-29')->addMonths(-2);
print LN . DateTime::create('2016-03-29')->addDays(3);
print LN . DateTime::create('2016-04-01')->forceWorkday();
print LN . DateTime::create('2016-04-02')->forceWorkday();
print LN . DateTime::create('2016-04-03')->forceWorkday();
print LN . DateTime::create('2016-04-04')->forceWorkday();

```

The generated output:

```

Thu, 29 Mar 2018 00:00:00 +0200
Fri, 29 Jan 2016 00:00:00 +0100
Fri, 01 Apr 2016 00:00:00 +0200
Fri, 01 Apr 2016 00:00:00 +0200
Fri, 01 Apr 2016 00:00:00 +0200
Mon, 04 Apr 2016 00:00:00 +0200
Mon, 04 Apr 2016 00:00:00 +0200
```


### Clone with the `copy` method

```php
<?php
require_once 'vendor/autoload.php';

use Lib16\Calendar\DateTime;

$easter = DateTime::easter(2016);
$pentecost = $easter->copy()->addDays(49);

print $pentecost;

```

The generated output:

```
Sun, 15 May 2016 00:00:00 +0200
```


### The `formatLocalized` method
This method returns a string representation according to locale settings.
http://php.net/manual/en/function.strftime.php lists the specifiers you can use
in the format string.

```php
<?php
require_once 'vendor/autoload.php';

use Lib16\Calendar\DateTime;

setlocale(LC_TIME, 'de');

$date = DateTime::create('2016-06-05');
print $date->formatLocalized('%A, %#d. %B %Y');

```

The generated output:

```
Sonntag, 5. Juni 2016
```

