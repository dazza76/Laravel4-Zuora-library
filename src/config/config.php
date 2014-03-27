<?php
/**
 * config.php
 *
 * @package default
 */


return array(

	/*
	|--------------------------------------------------------------------------
	| Zuora Config
	|--------------------------------------------------------------------------
	|
	*/
	'default' => 'connection1',
	'connections' => array(
		'connection1' => array(
			'wsdl' => '',
			'username' => '',
			'password' => '',
			'endpoint' => 'https://www.zuora.com/apps/services/a/54.0'
			// 'endpoint' => 'https://apisandbox.zuora.com/apps/services/a/54.0'
		),
	),

);
