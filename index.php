<?php
declare(strict_types=1);

setlocale(LC_TIME, 'ru_RU.UTF-8');

showDays();

function showDays(): void
{
    $currentDate = new DateTime();
    $firstDay = new DateTime($currentDate->format('Y-m-01'));
    $lastDay = new DateTime($currentDate->format('Y-m-t'));
    $daysInMonth = [];

    $monthFormatter = new IntlDateFormatter(
      'ru-RU',
      IntlDateFormatter::SHORT,
      IntlDateFormatter::NONE,
      'Europe/Moscow',
      IntlDateFormatter::GREGORIAN,
      'MMM'
    );
    $dayFormatter = new IntlDateFormatter(
       'ru-RU',
       IntlDateFormatter::SHORT,
       IntlDateFormatter::NONE,
       'Europe/Moscow',
       IntlDateFormatter::GREGORIAN,
       'd eeee'
    );
    While($firstDay <= $lastDay){
        $daysInMonth[] = $dayFormatter->format($firstDay);
        $firstDay->modify('+1 day');
    }
    echo 'Текущий месяц: ' . $monthFormatter->format(time()). PHP_EOL;
    foreach ($daysInMonth as $day){
        echo $day . PHP_EOL;
    }
}