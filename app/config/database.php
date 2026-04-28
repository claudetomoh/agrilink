<?php
/**
 * AgriLink – PDO Database Connection (Singleton)
 */

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    public static function connect(): PDO {
        if (self::$instance === null) {
            $port = defined('DB_PORT') ? (';port=' . DB_PORT) : '';
            $dsn = sprintf(
                'mysql:host=%s%s;dbname=%s;charset=%s',
                DB_HOST, $port, DB_NAME, DB_CHARSET
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Never expose real errors to users
                error_log('DB Connection failed: ' . $e->getMessage());
                die(json_encode(['error' => 'Database connection failed. Please try again later.']));
            }
        }
        return self::$instance;
    }
}
