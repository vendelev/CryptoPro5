<?php

declare(strict_types=1);

/**
 * Класс для генерации ответов в формате JSON
 */
final readonly class JsonResponse
{
    /**
     * Отправляет json
     */
    public function send(string $message, int $status = 200): void
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');

        try {
            $result = json_encode(
                ['status' => $status, 'message' => trim($message)],
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
            );
        } catch (JsonException $exception) {
            $result = sprintf('{"status": 500, "message": "%s"}', $exception->getMessage());
        }

        file_put_contents("php://stderr", $result . "\n");
        echo $result;
    }
}
