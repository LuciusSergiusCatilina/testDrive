<?php
// Подключаем ядро Bitrix
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
    foreach ($inputData as $index=>$item) {
        $data[$index] = [
            'UF_MODEL' => (string)$item['UF_MODEL'],
            'UF_VIN' => (string)$item['UF_VIN'],
            'UF_YEAR' => isset($item['UF_YEAR']) ? (int)$item['UF_YEAR'] : null,
            'UF_STATUS' => isset($item['UF_STATUS']) ? (int)$item['UF_STATUS'] : 1,
            'UF_PRICE_PER_DAY' => isset($item['UF_PRICE_PER_DAY']) ? (int)$item['UF_PRICE_PER_DAY'] : 0
        ];
        $responseData[$index] = [
            'UF_MODEL' => $data[$index]["UF_MODEL"]
        ];
    }
    Cars::createBunchCars($data);

    $response = [
        'success' => true,
        'data' => $responseData,
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
