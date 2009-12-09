#!/usr/bin/php5
<?php

	require('plurk_api.php');

	$plurk = new plurk_api();

	$api_key = 'vNHmWbxiEac28PrGnwwBgnVou3wxF7Mt';
	$user_name = 'whatup1981';
	$password = '1234qwer';

	$plurk->login($user_name, $password, $api_key);

  print_r($plurk->create_clique("test"));
  print_r($plurk->get_cliques());

  print_r($plurk->rename_clique("test","test1"));
  print_r($plurk->get_cliques());

  print_r($plurk->delete_clique("test1"));
  print_r($plurk->get_cliques());

		
?>
