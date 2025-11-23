<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Json;
use App\Cars;


header('Content-Type: application/json');

$request = Context::getCurrent()->getRequest();
$response = [
    'success' => false,
    'data' => null,
    'error' => null,
    'http_code' => 200
];

try {
    if ($request->getRequestMethod() !== 'POST') {
        $response['error'] = 'Метод не поддерживается. Используйте POST.';
        $response['http_code'] = 405;
        http_response_code(405);
        echo Json::encode($response);
        die();
    }

    $rawBody = $request->getJsonList();
    if(count($rawBody) === 0){
        $response['error'] = 'Пустое JSON-тело запроса. Отправьте корректные данные в формате JSON.';
        $response['http_code'] = 400;
        http_response_code(400);
        echo Json::encode($response);
        die();
    }
    $inputData = $rawBody;
    $car = new Cars($_REQUEST['requestParams'][0]);
    $result = $car->updateCar($inputData);

    if (!$result) {
        $response['error'] = 'Не удалось создать автомобиль. Проверьте данные.';
        $response['http_code'] = 500;
        http_response_code(500);
        echo Json::encode($response);
        die();
    }

    $response = [
        'success' => true,
        'data' => [
            'ID' => $car->carId,
        ],
        'http_code' => 201 // Created
    ];
    http_response_code(201);

} catch (\Exception $e) {
    $response['error'] = $e->getMessage();
    $response['http_code'] = 500;
    http_response_code(500);
} catch (\Error $e) {
    $response['error'] = 'Внутренняя ошибка сервера. ' . $e->getMessage();
    $response['http_code'] = 500;
    http_response_code(500);
}

http_response_code($response['http_code']);
echo Json::encode($response);
die();
