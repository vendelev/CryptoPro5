<?php

declare(strict_types=1);

require_once 'JsonResponse.php';

$response = new JsonResponse;
$serverMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($serverMethod !== 'POST') {
    $response->send('Поддерживается только POST', 405);
    exit;
}

const ROUTE_SIGN = 'sign';
const ROUTE_VERIFY = 'verify';

$route = trim($_SERVER['REQUEST_URI'] ?? '', '/');
$allowRoutes = [ROUTE_SIGN, ROUTE_VERIFY];

if (!in_array($route, $allowRoutes)) {
    $response->send(sprintf('Поддерживаются методы %s и %s', ROUTE_SIGN, ROUTE_VERIFY), 405);
    exit;
}

require_once 'CryptoPro.php';
$cryptopro = new CryptoPro();

if ($route === ROUTE_SIGN) {
    $filePath = $_FILES['file']['tmp_name'] ?? '';

    if (empty($filePath)) {
        $response->send('Нет информации для подписания', 400);
        return;
    }

    $result = $cryptopro->sign($filePath);

    if (empty($result)) {
        $response->send('Не удалось подписать', 500);
    } else {
        $response->send($result);
    }

    exit;
}

if ($route === ROUTE_VERIFY) {
    $filePath = $_FILES['file']['tmp_name'] ?? '';
    $signPath = $_FILES['sign']['tmp_name'] ?? '';

    if (empty($filePath) || empty($signPath)) {
        $response->send('Нет информации для проверки подписи', 400);
        return;
    }

    try {
        $cryptopro->verify($filePath, $signPath);
        $response->send('');
    } catch (Exception $exception) {
        $response->send($exception->getMessage(), 400);
    }

    exit;
}
