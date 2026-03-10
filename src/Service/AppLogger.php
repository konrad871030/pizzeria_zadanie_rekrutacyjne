<?php

namespace App\Service;

final class AppLogger
{
    public function __construct(private readonly string $projectDir)
    {
    }

    public function warning(string $message): void
    {
        $this->write('WARNING', $message);
    }

    public function info(string $message): void
    {
        $this->write('INFO', $message);
    }

    private function write(string $level, string $message): void
    {
        $logPath = $this->projectDir.'/var/log/app.log';
        $line = sprintf("[%s] %s %s\n", date('Y-m-d H:i:s'), $level, $message);
        file_put_contents($logPath, $line, FILE_APPEND);
    }
}
