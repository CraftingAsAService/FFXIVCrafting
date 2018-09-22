<?php
// A copy of laravel's env() helper
function env($key, $default = null)
{
    $value = getenv($key);

    if ($value === false) {
        return value($default);
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;

        case 'empty':
        case '(empty)':
            return '';

        case 'null':
        case '(null)':
            return;
    }

    if (Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
        return substr($value, 1, -1);
    }

    return $value;
}

// Use laravel's autoloader
require __DIR__.'/../../vendor/autoload.php';

// Laravel's ENV loader
$dotenv = new Dotenv\Dotenv(__DIR__ . '/../../');
$dotenv->load();

// Connect to the database
$pdo = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_DATABASE'], $_ENV['DB_USERNAME'] ?? '', $_ENV['DB_PASSWORD'] ?? '');

// Test the database with a query
$result = $pdo->query('SELECT DISTINCT `id` FROM `item` LIMIT 1')->fetch();

// Output "OK" if things are good
if (isset($result['id']) && is_numeric($result['id']))
	echo 'OK';
