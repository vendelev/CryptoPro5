<?php

declare(strict_types=1);

final readonly class CryptoPro
{
    /**
     * @throws RuntimeException
     */
    public function verify(string $filePath, string $signPath): bool
    {
        if (!is_file($filePath) || !is_file($signPath)) {
            return false;
        }

        $result = exec(sprintf(
            '/opt/cprocsp/bin/amd64/cryptcp -verify -verall -nochain -norev -detached %s %s',
            escapeshellarg($filePath),
            escapeshellarg($signPath)
        ));

        if ($result !== '[ErrorCode: 0x00000000]') {
            throw new RuntimeException($result);
        }

        return true;
    }

    public function sign(string $filePath): ?string
    {
        if (!is_file($filePath)) {
            return null;
        }

        $signPath = $filePath . '.sig';

        exec(sprintf(
            '/opt/cprocsp/bin/amd64/cryptcp -sign -nochain -norev -detach %s %s',
            escapeshellarg($filePath),
            escapeshellarg($signPath)
        ));

        if (!file_exists($signPath)) {
            return null;
        }

        $result = file_get_contents($signPath);

        return !empty($result) ? $result : null;
    }
}
