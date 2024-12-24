<?php
declare(strict_types=1);

setlocale(LC_TIME, 'ru_RU.UTF-8');

$red = "\033[48;5;1m";
$lightGray = "\033[48;5;145m";
$reset = "\033[0m";

echo
'Программа для расчета рабочих дней.
График работы - сутки через двое.
Во входных параметрах можно указать данные для расчета: Год(число), Месяц(номер), Кол-во месяцев к расчету'.PHP_EOL;

echo $red . str_repeat("  ", 1)  . $reset . "-Выходные дни" . PHP_EOL;
echo $lightGray . str_repeat("  ", 1)  . $reset . "-Рабочие дни" . PHP_EOL;

if($argc < 4) {
    echo 'Аргументы для расчета не указаны или указаны не полностью, расчет будет сделан для тек. Даты!' . PHP_EOL;
    $month = date('m');
    $year = date('Y');
    $countMonths = '1';
}else{
    $year = $argv[1];
    $month = $argv[2];
    $countMonths = $argv[3];
}

generateShedule($month, $year, $countMonths);

//Функция с которой начинается выполнение задачи
function generateShedule(string $month, string $year, string $countMonths): void
{
    try{
        $dateString = "$year-$month-01";
        $sourceDate = new DateTime($dateString);
        setMonthsDays($sourceDate, $countMonths);
    }catch (DateMalformedStringException $ex){
        echo 'Не верно указана дата для расчета!' . PHP_EOL;
        echo 'Расчет будет сделан для тек. Даты!' . PHP_EOL;
        $sourceDate = new DateTime();
        setMonthsDays($sourceDate, $countMonths);
    }
}
//Сбор, инициализация данных по месяцам и дням.
function setMonthsDays(DateTime $sourceDate, string $countMonths): void
{
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
    $currentMonth = 1;
    $months = [];
    do {
        $firstDay = new DateTime($sourceDate->format('Y-m-01'));
        $lastDay = new DateTime($sourceDate->format('Y-m-t'));
        $daysInMonth = [];
        While($firstDay <= $lastDay){
            $dayName = $dayFormatter->format($firstDay);
            $daysInMonth[] = "\033[31m" . mb_convert_case( $dayName, MB_CASE_UPPER) . "\033[0m";
            $firstDay->modify('+1 day');
        }
        $months[$monthFormatter->format($sourceDate)] = $daysInMonth;
        $sourceDate->modify('+1 month');
        $currentMonth++;
    }while ($currentMonth <= (int)$countMonths);
    setWorkingDays($months);
    showShedule($months);
}

//Расчет и установка рабочих дней
function setWorkingDays(array &$months): void
{
    define("WORKED_DAY_STEP", 3);
        foreach ($months as &$days) {
            $isLastDay = false;
            for($i = 0, $countDays = count($days); $i < $countDays; $i += WORKED_DAY_STEP){
                if(isWeekend($days[$i])) {
                    while (isWeekend($days[$i])) {
                        $i++;
                        if($i >= $countDays){
                            $isLastDay = true;
                            break;
                        }
                    }
                }
                if(!$isLastDay){
                    $days[$i] = preg_replace('/\033\[[0-9;]*m/', '', $days[$i]);
                }
            }
        }
}

//Проверка на выходной дней
function isWeekend(string $dayName): bool
{
    $workingDays = ['сб', 'вс'];
    foreach ($workingDays as $day) {
        if(mb_stripos($dayName, $day) !== false){
            return true;
        }
    }
    return false;
}

//Вывод календаря в консоль
function showShedule(array $months): void
{
    foreach ($months as $month=>$days) {
        echo "\033[32m" . mb_convert_case($month, MB_CASE_TITLE) . "\033[0m" . PHP_EOL;
        $j = 0;
        foreach ($days as $day) {
            if($j === 6){
                $j = 0;
                echo PHP_EOL;
            }
            echo "\t" . $day;
            $j++;
        }
        echo PHP_EOL;
    }
}