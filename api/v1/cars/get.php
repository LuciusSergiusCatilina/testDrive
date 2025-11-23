<?php
// Подключаем ядро Bitrix
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Json;
use App\Cars;

header('Content-Type: application/json');

// Получаем запрос
$request = Context::getCurrent()->getRequest();
$response = [
    'success' => false,
    'data' => null,
    'error' => null,
    'http_code' => 200
];

try {
    if ($request->getRequestMethod() !== 'GET') {
        $response['error'] = 'Метод не поддерживается';
        $response['http_code'] = 405;
        http_response_code(405);
        echo Json::encode($response);
        die();
    }

    $status = $request->get('status');
    $cars = Cars::getCars($status);

    $response = [
        'success' => true,
        'data' => $cars,
        'count' => count($cars),
        'http_code' => 200
    ];

} catch (\Exception $e) {
    $response['error'] = $e->getMessage();
    $response['http_code'] = 500;
    http_response_code(500);

} catch (\Error $e) {
    $response['error'] = 'Внутренняя ошибка сервера';
    $response['http_code'] = 500;
    http_response_code(500);
}

http_response_code($response['http_code']);
echo Json::encode($response);
die();
