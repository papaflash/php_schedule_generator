<?php
declare(strict_types=1);

setlocale(LC_TIME, 'ru_RU.UTF-8');

generateShedule('10', '24');


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
        'd eee'
    );
    While($firstDay <= $lastDay){
        $daysInMonth[] = "\033[31m" . $dayFormatter->format($firstDay) . "\033[0m";
        $firstDay->modify('+1 day');
    }
    echo 'Расчет для: ' . "\033[32m" . $monthFormatter->format($sourceDate) . "\033[0m" . PHP_EOL;
    setWorkingDays($daysInMonth);
    showShedule($daysInMonth);
}
function setWorkingDays(array &$days): void
{
    define("WORKED_DAY_STEP", 3);
    for($i = 0, $countDays = count($days); $i < $countDays; $i += WORKED_DAY_STEP){
        if(!isWeekend($days[$i])){
            $days[$i] = preg_replace('/\033\[[0-9;]*m/', '', $days[$i]);
        }else if($i === 0){
            $days[$i] = preg_replace('/\033\[[0-9;]*m/', '', $days[$i]);
        }else {
            while (isWeekend($days[$i])){
                $i++;
            }
            $days[$i] = preg_replace('/\033\[[0-9;]*m/', '', $days[$i]);
        }
    }
}
function isWeekend(string $dayName): bool
{
    $workingDays = ['ст', 'вс'];
    foreach ($workingDays as $day) {
        if(stripos($dayName, $day) !== false){
            return true;
        }
    }
    return false;
}
function showShedule(array $daysInMonth): void
{
    $j = 0;
    foreach ($daysInMonth as $day) {
        if($j === 6){
            $j = 0;
            echo PHP_EOL;
        }
        echo "\t" . $day;
        $j++;
    }
}