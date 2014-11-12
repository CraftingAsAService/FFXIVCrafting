<?php

return array(
	'default' => 'mysql',

	'connections' => array(

		// 'sqlite' => array(
		// 	'driver'   => 'sqlite',
		// 	'database' => getenv('wardrobe.location') ?: app_path().'/database/wardrobe.sqlite',
		// 	'prefix'   => '',
		// ),
		
		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => getenv('db.host'),
			'database'  => getenv('db.schema'),
			'username'  => getenv('db.username'),
			'password'  => getenv('db.password'),
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => 'wardrobe_',
		),

		// 'pgsql' => array(
		// 	'driver'   => 'pgsql',
		// 	'host'     => 'localhost',
		// 	'database' => 'database',
		// 	'username' => 'root',
		// 	'password' => '',
		// 	'charset'  => 'utf8',
		// 	'prefix'   => '',
		// 	'schema'   => 'public',
		// ),

		// 'sqlsrv' => array(
		// 	'driver'   => 'sqlsrv',
		// 	'host'     => 'localhost',
		// 	'database' => 'database',
		// 	'username' => 'root',
		// 	'password' => '',
		// 	'prefix'   => '',
		// ),

	),
);