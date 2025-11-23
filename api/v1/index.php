<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key');

$routesFile = __DIR__ . '/routes.php';
if (!file_exists($routesFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Файл путей не найден']);
    exit;
}

require $routesFile;

if (!isset($routes) || !is_array($routes)) {
    http_response_code(500);
    echo json_encode(['error' => 'Файл путей не заполнен']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

foreach ($routes as $pattern => $handler) {
    if (preg_match('#' . $pattern . '#', $uri, $matches)) {
        array_shift($matches);
        $handlerPath = __DIR__ . '/' . $handler;

        if (!file_exists($handlerPath)) {
            http_response_code(500);
            echo json_encode(['error' => 'Обработчик не найден: ' . $handler]);
            exit;
        }

        $requestParams = $matches;
        $_REQUEST['requestParams'] = $requestParams;
        echo $_REQUEST['requestParams'];

        require $handlerPath;
        exit;
    }
}

http_response_code(404);
echo json_encode([
    'error' => 'Маршрут не найден',
    'requested_uri' => $uri,
]);
