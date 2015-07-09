## Latest Change ##

[r163](https://code.google.com/p/php-plurk-api/source/detail?r=163) | roga.lin@gmail.com | 2011-11-05 03:02:16 CST

realtime\_get\_commet\_channel should use HTTP GET Method, and return raw response for realtime\_get\_commet\_channel.

[r157](https://code.google.com/p/php-plurk-api/source/detail?r=157) | appleboy.tw | 2011-10-26 11:07:02 CST

Add PlurkTop/getCollections, PlurkTop/getDefaultCollection, PlurkTop/getPlurks function"


---


## Example ##

```

/* for one user. */
   require('plurk_api.php');
   $plurk = new plurk_api();
   $plurk->login($api_key, $username, $password);
   print_r($plurk->get_plurks(time(), 20));

/* for multi users. */
   require('plurk_api.php');   
   $plurk = new plurk_api();
   $plurk->set_cookie_path(dirname(__FILE__) . DIRECTORY_SEPARATOR . $username . 'cookie');
   $plurk->set_log_path(dirname(__FILE__) . DIRECTORY_SEPARATOR . $username . 'log');
   $plurk->login($api_key, $username, $password);
   print_r($plurk->get_plurks(time(), 20));
```

## Summary ##

php-plurk-api is a Plurk API Client implementation with PHP.

  * the API Official Website: http://www.plurk.com/API

  * Code Repository: https://code.google.com/p/php-plurk-api/

  * php-plurk-api online documents: http://plurk-doc.roga.tw/docs/

very strongly suggested running the API in CLI (Commandline Interface) Mode.

your environment should have at least two extensions, including php5-json and php5-curl.


**roga, appleboy, whatup, chrisliu, limit** thanks for their contribution.