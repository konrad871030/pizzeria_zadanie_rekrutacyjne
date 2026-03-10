<?php

namespace App\Service;

use PDO;
use RuntimeException;

final class DbConnectionFactory
{
    private ?PDO $connection = null;

    public function getConnection(): PDO
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        $databaseUrl = (string) ($_ENV['DATABASE_URL'] ?? $_SERVER['DATABASE_URL'] ?? '');
        if ($databaseUrl === '') {
            throw new RuntimeException('DATABASE_URL is not configured.');
        }

        $parts = parse_url($databaseUrl);
        if (!is_array($parts)) {
            throw new RuntimeException('Invalid DATABASE_URL.');
        }

        $host = $parts['host'] ?? '127.0.0.1';
        $port = $parts['port'] ?? 3306;
        $user = $parts['user'] ?? '';
        $pass = $parts['pass'] ?? '';
        $dbName = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
        $query = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }
        $charset = (string) ($query['charset'] ?? 'utf8mb4');

        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $dbName, $charset);
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->connection = $pdo;

        return $this->connection;
    }
}
