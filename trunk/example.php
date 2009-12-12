#!/usr/bin/php5
<?php

	require('plurk_api.php');

	$plurk = new plurk_api();

	$api_key = '';
	$user_name = '';
	$password = '';

	$plurk->login($api_key, $user_name, $password);

	print_r($plurk->get_plurks());
	print_r($plurk->get_user_info());

	print_r($plurk->create_clique("test"));
	print_r($plurk->get_cliques());

	print_r($plurk->rename_clique("test","test1"));
	print_r($plurk->get_cliques());

	print_r($plurk->delete_clique("test1"));
	print_r($plurk->get_cliques());

?>
