<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
}

use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Bitrix\Highloadblock\HighloadBlockTable;
use App\Cars;
GLOBAL $APPLICATION;
$APPLICATION->ShowHeadStrings();
$APPLICATION->ShowHeadScripts();
$APPLICATION->ShowPanel();
$data = [
    'UF_MODEL' => 'Quadra Sport R-7 "Vigilante"',
    'UF_YEAR' => 2055,
    'UF_VIN' => 'WBAAB8C5XFG234567',
    'UF_PRICE_PER_DAY' => 2300
];
//Cars::createCar($data);

$dataError = [];
$moreData = [
    [
        'UF_MODEL' => 'Thorton Merrimac "Warlock"',
        'UF_YEAR' => 2035,
        'UF_VIN' => 'WBAAB8C5XFG234444',
        'UF_PRICE_PER_DAY' => 2400,
        'UF_STATUS' => 1
    ],
    [
        'UF_MODEL' => 'Villefort Deleon V410-S Coupé',
        'UF_YEAR' => 2045,
        'UF_VIN' => 'ZXCCB8C5XFG234567',
        'UF_PRICE_PER_DAY' => 3300,
        'UF_STATUS' => 1
    ],
    [
        'UF_MODEL' => 'Villefort Deleon V333',
        'UF_YEAR' => 2070,
        'UF_VIN' => 'QWECB8C5XFG234666',
        'UF_PRICE_PER_DAY' => 7300,
        'UF_STATUS' => 2
    ],
];
//Cars::createBunchCars($moreData);
$car = new Cars(1);
//$car->updateCar(6666, 3);

//$anotherCar = new Cars(22);
//$anotherCar->delete();
//Cars::getCars();

$statusData = Cars::getCars();
//dump($statusData);

$dataForBooking = [
    'UF_CAR' => 6,                   // Идентификатор автомобиля
    'UF_DATE_START' => "24.11.2025 12:00:00",  // Начало бронирования (пример московского времени)
    'UF_DATE_END' => "27.11.2025 14:00:00",    // Окончание бронирования (пример московского времени)
];

$APPLICATION->IncludeComponent(
    'my:showTestDrives',
    ''
);

//\App\TestDrives::createTestDrive($dataForBooking);


require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');

?>