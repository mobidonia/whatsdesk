<?php

namespace Modules\Setup\Services;

use Exception;

class EnvWriter
{
    /**
     * The path to the .env file.
     */
    protected string $envPath;

    public function __construct()
    {
        $this->envPath = base_path('.env');
    }

    /**
     * Check if the .env file is writable.
     */
    public function isWritable(): bool
    {
        if (!file_exists($this->envPath)) {
            return is_writable(dirname($this->envPath));
        }

        return is_writable($this->envPath);
    }

    /**
     * Update multiple environment values.
     *
     * @param array<string, string> $values
     * @throws Exception
     */
    public function update(array $values): void
    {
        if (!$this->isWritable()) {
            throw new Exception('The .env file is not writable. Please check file permissions.');
        }

        $envContent = file_exists($this->envPath) ? file_get_contents($this->envPath) : '';

        if ($envContent === false) {
            throw new Exception('Unable to read the .env file.');
        }

        foreach ($values as $key => $value) {
            $envContent = $this->setEnvValue($envContent, $key, $value);
        }

        $result = file_put_contents($this->envPath, $envContent);

        if ($result === false) {
            throw new Exception('Unable to write to the .env file.');
        }
    }

    /**
     * Set a single environment value in the content.
     */
    protected function setEnvValue(string $envContent, string $key, string $value): string
    {
        $key = strtoupper($key);
        
        // Escape the value if it contains spaces or special characters
        $escapedValue = $this->escapeValue($value);

        // Check if the key exists
        $pattern = "/^{$key}=.*/m";
        
        if (preg_match($pattern, $envContent)) {
            // Replace existing value
            $envContent = preg_replace($pattern, "{$key}={$escapedValue}", $envContent);
        } else {
            // Add new key-value pair at the end
            $envContent = rtrim($envContent, "\n") . "\n{$key}={$escapedValue}\n";
        }

        return $envContent;
    }

    /**
     * Escape value for .env file.
     */
    protected function escapeValue(string $value): string
    {
        // If value contains spaces, newlines, or special characters, wrap in quotes
        if (preg_match('/[\s"\'\\\\#]/', $value) || $value === '') {
            // Escape existing quotes and backslashes
            $value = str_replace(['\\', '"'], ['\\\\', '\\"'], $value);
            return '"' . $value . '"';
        }

        return $value;
    }

    /**
     * Get a value from the .env file.
     */
    public function get(string $key): ?string
    {
        if (!file_exists($this->envPath)) {
            return null;
        }

        $envContent = file_get_contents($this->envPath);
        
        if ($envContent === false) {
            return null;
        }

        $key = strtoupper($key);
        $pattern = "/^{$key}=(.*)$/m";
        
        if (preg_match($pattern, $envContent, $matches)) {
            $value = trim($matches[1]);
            
            // Remove surrounding quotes if present
            if (preg_match('/^"(.*)"$/', $value, $quotedMatches)) {
                return stripcslashes($quotedMatches[1]);
            }
            
            if (preg_match("/^'(.*)'$/", $value, $quotedMatches)) {
                return $quotedMatches[1];
            }
            
            return $value;
        }

        return null;
    }
}
