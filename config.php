<?php

$envFile = __DIR__ . '/.env';
$env = file_exists($envFile) ? parse_ini_file($envFile) : [];

function env_value($key, $env)
{
    $value = getenv($key);

    if ($value !== false) {
        return $value;
    }

    return $env[$key] ?? null;
}

define('DB_HOST', env_value('DB_HOST', $env));
define('DB_PORT', env_value('DB_PORT', $env) ?: 3306);
define('DB_NAME', env_value('DB_NAME', $env));
define('DB_USER', env_value('DB_USER', $env));
define('DB_PASS', env_value('DB_PASS', $env));

?>