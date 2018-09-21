<?php
	$envs = [];
	foreach (explode("\n", file_get_contents('../../.env')) as $row)
	{
		list($key, $value) = explode('=', $row);
		$envs[$key] = $value;
	}
	unset($row);

	$pdo = new PDO('mysql:host=' . $envs['DB_HOST'] . ';dbname=' . $envs['DB_DATABASE'], $envs['DB_USERNAME'] ?? '', $envs['DB_PASSWORD'] ?? '');
	$result = $pdo->query('SELECT DISTINCT `id` FROM `item` LIMIT 1')->fetch();

	if (isset($result['id']) && is_numeric($result['id']))
		echo 'OK';