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

    $data = [
        'UF_MODEL' => (string)$inputData['UF_MODEL'],
        'UF_VIN' => (string)$inputData['UF_VIN'],
        'UF_YEAR' => isset($inputData['UF_YEAR']) ? (int)$inputData['UF_YEAR'] : null,
        'UF_STATUS' => isset($inputData['UF_STATUS']) ? (string)$inputData['UF_STATUS'] : 'available',
        'UF_PRICE_PER_DAY' => isset($inputData['UF_PRICE_PER_DAY']) ? (int)$inputData['UF_PRICE_PER_DAY'] : 0
    ];
    $carId = Cars::createCar($data);

    if (!$carId) {
        $response['error'] = 'Не удалось создать автомобиль. Проверьте данные.';
        $response['http_code'] = 500;
        http_response_code(500);
        echo Json::encode($response);
        die();
    }

    $response = [
        'success' => true,
        'data' => [
            'ID' => $carId,
            'UF_MODEL' => $data['UF_MODEL'],
            'UF_VIN' => $data['UF_VIN']
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
