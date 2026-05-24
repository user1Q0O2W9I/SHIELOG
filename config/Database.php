<?php

class Database
{
    private const HOST = 'localhost';
    private const DB_NAME = 'shieldlog';
    private const USER = 'root';
    private const PASSWORD = '';

    public static function connect(): PDO
    {
        $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4';

        // PDO permite trabajar con consultas preparadas y controlar errores de forma limpia.
        return new PDO($dsn, self::USER, self::PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}
