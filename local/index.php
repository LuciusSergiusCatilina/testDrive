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

$APPLICATION->IncludeComponent(
    'my:showTestDrives',
    ''
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');

?>