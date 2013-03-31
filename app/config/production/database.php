<?php

return array(
	'connections' => array(
		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => $_SERVER['MysqlDb'],
			'username'  => $_SERVER['MysqlUser'],
			'password'  => $_SERVER['MysqlPass'],
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		)
	)
);