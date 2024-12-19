<?php
declare(strict_types=1);

setlocale(LC_TIME, 'ru_RU.UTF-8');

generateShedule('12', '24');


function generateShedule(string $month, string $year, int $countMonths = 1): void
{
    try{
        $dateString = "$year-$month-01";
        $sourceDate = new DateTime($dateString);
        calculateShedule($sourceDate);
    }catch (DateMalformedStringException $ex){
        echo 'Не верно указана дата для расчета!' . PHP_EOL;
        echo 'Расчет будет сделан по текущей дате!' . PHP_EOL;
        $sourceDate = new DateTime();
        calculateShedule($sourceDate);
    }
}

function calculateShedule(DateTime $sourceDate): void
{
    define("WORKED_DAY_STEP", 3);
    $firstDay = new DateTime($sourceDate->format('Y-m-01'));
    $lastDay = new DateTime($sourceDate->format('Y-m-t'));
    $daysInMonth = [];

    $monthFormatter = new IntlDateFormatter(
        'ru-RU',
        IntlDateFormatter::SHORT,
        IntlDateFormatter::NONE,
        'Europe/Moscow',
        IntlDateFormatter::GREGORIAN,
        'MMM y'
    );
    $dayFormatter = new IntlDateFormatter(
        'ru-RU',
        IntlDateFormatter::SHORT,
        IntlDateFormatter::NONE,
        'Europe/Moscow',
        IntlDateFormatter::GREGORIAN,
        'd eeee'
    );
    $workedIndex = 1;
    While($firstDay <= $lastDay){
        $dayName = $dayFormatter->format($firstDay);
        if(isWeekend($dayName) && count($daysInMonth) !== 0) {
            $daysInMonth[] = "\033[31m" . $dayName . "\033[0m";
            $workedIndex = 3;
        }
        else if($workedIndex === WORKED_DAY_STEP){
            $daysInMonth[] = "\033[32m" . $dayName . "\033[0m";
            $workedIndex = 1;
        }
        else if(count($daysInMonth) === 0){
            $daysInMonth[] = "\033[32m" . $dayName . "\033[0m";
            $workedIndex++;
        }else{
            $daysInMonth[] = "\033[31m" . $dayName . "\033[0m";
            $workedIndex++;
        }
        $firstDay->modify('+1 day');

    }
    echo 'Расчет для: ' . $monthFormatter->format($sourceDate). PHP_EOL;
    showShedule($daysInMonth);

}
function isWeekend(string $dayName): bool
{
    $workingDays = ['суббота', 'воскресенье'];
    foreach ($workingDays as $day) {
        if(stripos($dayName, $day) !== false){
            return true;
        }
    }
    return false;

}
function showShedule(array $daysInMonth): void
{
    foreach ($daysInMonth as $day){
        echo "\t" . $day . PHP_EOL;
    }
}