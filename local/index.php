<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Bitrix\Highloadblock\HighloadBlockTable;



Loader::includeModule('highloadblock');

// Получаем запись о Highload-блоке
$hlblock = HighloadBlockTable::getById(1)->fetch();

// "Компилируем" сущность на основе HL-блока
$entity = HighloadBlockTable::compileEntity($hlblock);

// Получаем класс для работы с данными
$entityClass = $entity->getDataClass();

$data = [
    'UF_MODEL' => 'Porshe 991',
    'UF_YEAR' => 2022,
    'UF_VIN' => 'WBAKE2C5XPW123456',
    'UF_STATUS' => "В ремонте",
    'UF_PRICE_PER_DAY' => 1500

];

$rsData = $entityClass::getList([
    'select' => ['*'],
    'order'  => ['ID' => 'ASC'],
    'limit'  => 10
]);
Car::sayHello();

while($arData = $rsData->fetch()) {
    print($arData['UF_MODEL']);
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');

?>