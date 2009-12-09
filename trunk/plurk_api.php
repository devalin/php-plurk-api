<?php

/**
 * 
 * Dependencies on DBI, Constants, Misc.
 */
require('config.php');
require('constant.php');
require('common_dbi.php');
	
/**
 * This is an PHP Plurk API.
 *
 * @author roga <roga@roga.tw>
 * @category  API
 * @package   roga-plurk-api
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link      http://code.google.com/p/roga-plurk-api
 *
 */
Class plurk_api Extends common_dbi {
	
	/**
	 * 帳號
	 * @var string $username
	 */
	protected $username;
	
	/**
	 * 密碼
	 * @var string $password
	 */
	protected $password;
	
	/**
	 * API KEY
	 * @var $api_key
	 */
	protected $api_key;
	
	/**
	 * 判斷是否登入
	 * @var bool $is_login
	 */
	protected $is_login;
	
	/**
	 * Current HTTP Status Code
	 * @var int $http_status
	 */
	protected $http_status;
	
	/**
	 * 使用者的資料
	 * @var object $user_info
	 */	
	protected $user_info;
	
	/**
	 * fans 數目
	 * @var int $fans_count
	 */
	protected $fans_count;
	
	/**
	 * 通知數目
	 * @var int $alerts_count
	 */
	protected $alerts_count;
	
	/**
	 * 好友數目
	 * @var int $friends_count
	 */	
	protected $friends_count;
	
	/**
	 * 是否公開河道
	 * @var boolean $privacy 
	 */
	protected $privacy;

	function __construct()
	{
		/* nothing here*/
	}	

	/**
	 * function plurk
	 * 每次連接到 Plurk Server 都透過這個 method
	 * 
	 * @param $url
	 * @param $array
	 * @return object
	 */
	function plurk($url, $array)
	{		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($array));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "roga-plurk-api v0.1");

		curl_setopt($ch, CURLOPT_COOKIEFILE, PLURK_COOKIE_PATH);
		curl_setopt($ch, CURLOPT_COOKIEJAR, PLURK_COOKIE_PATH);
								
		$response = curl_exec($ch);
		
		$this->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		  
		curl_close($ch);
					
		return json_decode($response);
	}

	/**
	 * function login
	 * 登入 Plurk 用 method
	 *  
	 * @param $username
	 * @param $password
	 * @param $api_key
	 * @return boolean
	 */
	function login($username, $password, $api_key)
	{

		$this->username = $username;
		$this->password = $password;
		$this->api_key = $api_key;

		$array = array(
			'username'	  => $username,
			'password'	  => $password,
			'api_key'	   => $api_key,
		);

		$result = $this->plurk(PLURK_LOGIN, $array);
						 
		($this->http_status == '200') ? $this->is_login = TRUE : $this->is_login = FALSE;

		if($this->is_login)
		{
			$this->log('Login Success');
			$this->user_info = $result;
			$this->fans_count = $result->fans_count;
			$this->alerts_count = $result->alerts_count;
			$this->friends_count = $result->friends_count;
			$this->privacy = $result->privacy;
		}
		else
		{
			$this->log('Login Failed!');
		}
		
		return $this->is_login;
		
	}

	/**
	 * function add_Plurk
	 * 
	 * no_comments:
	 * 如果是 0, 允許回應  
	 * 如果是 1, 不允許回應
	 * 如果是 2, 只有好友能夠回應	 
	 * @return object
	 * 
	 */
	function add_Plurk($lang = 'en', $qualifier = 'says', $content = 'test from roga-plurk-api', $limited_to = NULL, $no_comments = 0)
	{
		if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
				
		if (mb_strlen($content) > 140)
		{
			$this->log('這個噗訊息太長了');	
		}
   					
		$array = array(
			'api_key'	   => $this->api_key,
			'qualifier'	 => 'likes',
			'content'	   => urlencode($content),
			'lang'			=> $lang 
		);
		
		if ($limited_to != NULL)
		{						
			// need to comfirm
			$array['limited_to'] = json_encode($limited_to);		
		}
		
		if ($no_comments != 0)
		{
			$array['no_comments'] = $no_comments;
		}									
		
		return $this->plurk(PLURK_TIMELINE_PLURK_ADD, $array);
	}
	
	/**
	 * function get_plurks 
	 * 取回某一個特定的噗
	 * 
	 * @param $plurk_id
	 * @return object
	 */	
	function get_plurk($plurk_id = '')
	{
		if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

		$array = array(
			'api_key'	   => $this->api_key,
			'plurk_id'	  => $plurk_id, 
		);
		
		return $this->plurk(PLURK_TIMELINE_GET_PLURK, $array);
	}
	
	/**
	 * function get_plurks 
	 * 取回自己河道上所有的噗
	 * 
	 * @param $offset
	 * @param $limit
	 * @param $only_user
	 * @param $only_responded
	 * @param $only_private
	 * @return object
	 */
	function get_plurks($offset = 0, $limit = 20, $only_user = '', $only_responded = FALSE, $only_private = FALSE)		 
	{
		if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

		$array = array(
			'api_key'	   => $this->api_key,
			'offset'		=> $offset, 
			'limit'		 => $limit,
			'only_user'	 => $only_user,
			'only_responded'=> $only_responded,
			'only_private'  => $only_private
		);

		return $this->plurk(PLURK_TIMELINE_GET_PLURKS, $array);		
	}

	/**
	 * function get_fans
	 * 取回粉絲列表 
	 * 
	 * @param $offset
	 * @return object
	 */
	function get_fans($offset = 0)
	{
		if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

		$array = array(
			'api_key'	   => $this->api_key,
			'user_id'		=> $this->user_info->uid,
			'offset'		=> $offset
		);

		return $this->plurk(PLURK_GET_FANS, $array);		
	}

	/**
	 * funciton block_user
	 * 封鎖特定使用者
	 * 
	 * @param $uid
	 * @return object
	 */
	function block_user($uid)
	{
		if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

		$array = array(
			'api_key'	   => $this->api_key,
			'user_id'		=> $uid,
		);

		return $this->plurk(PLURK_BLOCK, $array);		 
	}
	
	/**
	 * function get_login_status
	 * 取得登入狀態
	 * @return boolean
	 */
	function get_login_status()
	{
		return ($this->is_login) ? TRUE : FALSE;
	}

	/**
	 * function get_http_status
	 * 取得 HTTP Status Code
	 * @return int
	 */
	function get_http_status()
	{
		return $this->http_status;
	}
	
	/**
	 * function get_user_info
	 * 取得使用者資料
	 * @return object
	 */
	function get_user_info()
	{
		return $this->user_info;	
	}
	
	/**
	 * function get_permalink
	 * 把 plurk_id 轉換為 permalink
	 *
	 * @param $plurk_id
	 * @return string.
	 */
	function get_permalink($plurk_id)
	{
		return "http://www.plurk.com/p/" . base_convert($plurk_id, 10, 36);
	}

	/**
	 * function get_permalink
	 * 把 permalink 轉換為 plurk_id 
	 *
	 * @param $permalink
	 * @return int.
	 */
	function permalinkToPlurkID($permalink)
	{
		return base_convert(str_replace('http://www.plurk.com/p/', '', $permalink), 36, 10);
	}
	
	/**
	 * funciton log
	 * 紀錄操作歷史訊息
	 * 
	 * @param $message
	 *  
	 */
	function log($message = '')
	{
		$source = file_get_contents(PLURK_LOG_PATH);
		$source .= date("Y-m-d H:i:s - ") . $message . "\n";
		file_put_contents(PLURK_LOG_PATH, $source);
	}
}