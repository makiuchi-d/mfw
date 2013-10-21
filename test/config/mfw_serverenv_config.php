<?php

$serverenv_config = array(
	'application_identifier' => 'unittest-env',

	'unittest' => array(
		'http_proxy' => array(
			'host' => '127.0.0.1',
			'port' => 10080,
			),
		'database' => array(
			'authfile' => dirname(__FILE__).'/dbauthfile',
			'default_master' => 'mysql:dbname=unittest;host=localhost',
			),
		'memcache' => array(
			'host' => 'localhost',
			'port' => 11211,
			),
		'swfmill' => '/usr/local/bin/swfmill',
		),
	'illegalenv' => array(
		'http_proxy' => array(
			'port' => 1,
			),
		'database' => array(
			'authfile' => dirname(__FILE__).'/no-file',
			'default_master' => 'dummy',
			),
		'memcache' => array(
			'host' => 'localhost',
			),
		'swfmill' => 'notfound',
		),
	'unconnectable' => array(
		'memcache' => array(
			'host' => 'localhost',
			'port' => 1,
			),
		'database' => array(
			'authfile' => dirname(__FILE__).'/dbauthfile',
			'default_master' => 'mysql:dbname=dummy;host=localhost',
			),
		),
	'noserver' => array(
		),
	);

