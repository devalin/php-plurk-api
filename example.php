#!/usr/bin/php5
<?php
		
	require('plurk_api.php');

	$plurk = new plurk_api();
	
	$api_key = '';
	$user_name = '';
	$password = '';
	
	$plurk->login($user_name, $password, $api_key);
		
	print_r($plurk->user_info);
	print_r($plurk->get_plurks());
		
?>