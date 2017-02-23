# `lib16/calendar-php`

## Installation with Composer

This package is available on [packagist](https://packagist.org/packages/lib16/calendar),
so you can use [Composer](https://getcomposer.org) to install it:
```
composer require lib16/calendar
```


## The `Calendar` class

```php
&lt;?php
require_once 'vendor/autoload.php';

use Lib16\Calendar\Calendar;
use Lib16\Calendar\DateTime;

setlocale(LC_TIME, 'de');
$easter = DateTime::easter(2016);
$calendar = Calendar::month(5, 2016)
        -&gt;setMonthFormat('%b %Y')
        -&gt;setFirstWeekday('DE')
        -&gt;addEntry('2016-05-01', 'Tag der Arbeit')
        -&gt;addEntry($easter-&gt;copy()-&gt;addDays(39), 'Christi Himmelfahrt')
        -&gt;addEntry($easter-&gt;copy()-&gt;addDays(50), 'Pfingstmontag')
        -&gt;addEntry($easter-&gt;copy()-&gt;addDays(60), 'Fronleichnam');
print json_encode($calendar-&gt;buildArray(), JSON_PRETTY_PRINT);
```

The generated output:

```json
{
    &quot;weekdays&quot;: {
        &quot;mon&quot;: &quot;Mo&quot;,
        &quot;tue&quot;: &quot;Di&quot;,
        &quot;wed&quot;: &quot;Mi&quot;,
        &quot;thu&quot;: &quot;Do&quot;,
        &quot;fri&quot;: &quot;Fr&quot;,
        &quot;sat&quot;: &quot;Sa&quot;,
        &quot;sun&quot;: &quot;So&quot;
    },
    &quot;years&quot;: [
        {
            &quot;time&quot;: &quot;2016&quot;,
            &quot;label&quot;: &quot;2016&quot;,
            &quot;months&quot;: [
                {
                    &quot;time&quot;: &quot;2016-05&quot;,
                    &quot;label&quot;: &quot;Mai 2016&quot;,
                    &quot;month&quot;: &quot;05&quot;,
                    &quot;weeks&quot;: [
                        {
                            &quot;time&quot;: &quot;2016-W17&quot;,
                            &quot;label&quot;: &quot;17&quot;,
                            &quot;leading&quot;: 6,
                            &quot;days&quot;: [
                                {
                                    &quot;time&quot;: &quot;2016-05-01&quot;,
                                    &quot;label&quot;: &quot;1&quot;,
                                    &quot;weekday&quot;: &quot;sun&quot;,
                                    &quot;entries&quot;: [
                                        {
                                            &quot;class&quot;: &quot;holiday&quot;,
                                            &quot;title&quot;: &quot;Tag der Arbeit&quot;
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            &quot;time&quot;: &quot;2016-W18&quot;,
                            &quot;label&quot;: &quot;18&quot;,
                            &quot;days&quot;: [
                                {
                                    &quot;time&quot;: &quot;2016-05-02&quot;,
                                    &quot;label&quot;: &quot;2&quot;,
                                    &quot;weekday&quot;: &quot;mon&quot;
                                },
                                {
                                    &quot;time&quot;: &quot;2016-05-03&quot;,
                                    &quot;label&quot;: &quot;3&quot;,
                                    &quot;weekday&quot;: &quot;tue&quot;
                                },
                                {
                                    &quot;time&quot;: &quot;2016-05-04&quot;,
                                    &quot;label&quot;: &quot;4&quot;,
                                    &quot;weekday&quot;: &quot;wed&quot;
                                },
                                {
                                    &quot;time&quot;: &quot;2016-05-05&quot;,
                                    &quot;label&quot;: &quot;5&quot;,
                                    &quot;weekday&quot;: &quot;thu&quot;,
                                    &quot;entries&quot;: [
                                        {
                                            &quot;class&quot;: &quot;holiday&quot;,
                                            &quot;title&quot;: &quot;Christi Himmelfahrt&quot;
                                        }
                                    ]
                                },
                                {
                                    &quot;time&quot;: &quot;2016-05-06&quot;,
                                    &quot;label&quot;: &quot;6&quot;,
                                    &quot;weekday&quot;: &quot;fri&quot;
                                },
                                {
                                    &quot;time&quot;: &quot;2016-05-07&quot;,
                                    &quot;label&quot;: &quot;7&quot;,
                                    &quot;weekday&quot;: &quot;sat&quot;
                                },
                                {
                                    &quot;time&quot;: &quot;2016-05-08&quot;,

…

```


## The `DateTime` class

`DateTime` extends the standard [PHP class](http://php.net/manual/en/class.datetime.php)
with the same name.

Assuming that current date is
<var><time>Thu, 23 Feb 2017 20:59:49 +0100</time></var>:
```php
&lt;?php
require_once 'vendor/autoload.php';

use Lib16\Calendar\DateTime;

const LN = &quot;\n&quot;;

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
&lt;?php
require_once 'vendor/autoload.php';

use Lib16\Calendar\DateTime;

const LN = &quot;\n&quot;;

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
&lt;?php
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
&lt;?php
require_once 'vendor/autoload.php';

use Lib16\Calendar\DateTime;

const LN = &quot;\n&quot;;

print LN . DateTime::create('2016-03-29')-&gt;addYears(2);
print LN . DateTime::create('2016-03-29')-&gt;addMonths(-2);
print LN . DateTime::create('2016-03-29')-&gt;addDays(3);
print LN . DateTime::create('2016-04-01')-&gt;forceWorkday();
print LN . DateTime::create('2016-04-02')-&gt;forceWorkday();
print LN . DateTime::create('2016-04-03')-&gt;forceWorkday();
print LN . DateTime::create('2016-04-04')-&gt;forceWorkday();

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
&lt;?php
require_once 'vendor/autoload.php';

use Lib16\Calendar\DateTime;

$easter = DateTime::easter(2016);
$pentecost = $easter-&gt;copy()-&gt;addDays(49);

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
&lt;?php
require_once 'vendor/autoload.php';

use Lib16\Calendar\DateTime;

setlocale(LC_TIME, 'de');

$date = DateTime::create('2016-06-05');
print $date-&gt;formatLocalized('%A, %#d. %B %Y');

```

The generated output:

```
Sonntag, 5. Juni 2016
```

