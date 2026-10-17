<?php

namespace Modules\Setup\Services;

use Exception;
use PDO;
use PDOException;

class DatabaseTester
{
    /**
     * Test database connection with provided credentials.
     *
     * @param array{host: string, port: string, database: string, username: string, password: string} $credentials
     * @return array{success: bool, message: string}
     */
    public function test(array $credentials): array
    {
        try {
            $this->validateCredentials($credentials);

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $credentials['host'],
                $credentials['port'],
                $credentials['database']
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5,
            ];

            $pdo = new PDO(
                $dsn,
                $credentials['username'],
                $credentials['password'],
                $options
            );

            // Test with a simple query
            $pdo->query('SELECT 1');

            return [
                'success' => true,
                'message' => 'Database connection successful!',
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => $this->parseError($e),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate that all required credentials are provided.
     *
     * @throws Exception
     */
    protected function validateCredentials(array $credentials): void
    {
        $required = ['host', 'port', 'database', 'username', 'password'];

        foreach ($required as $field) {
            if (!isset($credentials[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }

        if (empty($credentials['host'])) {
            throw new Exception('Database host is required.');
        }

        if (empty($credentials['database'])) {
            throw new Exception('Database name is required.');
        }

        if (empty($credentials['username'])) {
            throw new Exception('Database username is required.');
        }
    }

    /**
     * Parse PDO exception to user-friendly message.
     */
    protected function parseError(PDOException $e): string
    {
        $code = $e->getCode();
        $message = $e->getMessage();

        // Common MySQL error codes
        return match (true) {
            str_contains($message, 'Unknown database') => 
                'The specified database does not exist. Please create it first or check the database name.',
            
            str_contains($message, 'Access denied') => 
                'Access denied. Please check your username and password.',
            
            str_contains($message, 'Connection refused') || str_contains($message, 'No such file or directory') => 
                'Could not connect to the database server. Please check the host and port.',
            
            str_contains($message, 'Name or service not known') || str_contains($message, 'getaddrinfo failed') => 
                'Could not resolve the database host. Please check the hostname.',
            
            str_contains($message, 'Connection timed out') => 
                'Connection timed out. The database server might be unreachable.',
            
            str_contains($message, 'driver') => 
                'MySQL PDO driver is not installed or enabled on this server.',
            
            default => "Database error: {$message}",
        };
    }
}
