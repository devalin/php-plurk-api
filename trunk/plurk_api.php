<?php

/**
 *
 * load dependencies.
 */
require('config.php');
require('constant.php');
require('common.php');

/**
 * This is an PHP Plurk API.
 *
 * @category  API
 * @package   php-plurk-api
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link      http://code.google.com/p/php-plurk-api
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
	protected $is_login = 'VIVIEN';

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

	function __construct() {}

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
		curl_setopt($ch, CURLOPT_USERAGENT, "php-plurk-api v0.1");

		curl_setopt($ch, CURLOPT_COOKIEFILE, PLURK_COOKIE_PATH);
		curl_setopt($ch, CURLOPT_COOKIEJAR, PLURK_COOKIE_PATH);

		$response = curl_exec($ch);

		$this->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		return json_decode($response);
	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Users/register
	 */
	function register()
	{

	}

	/**
	 * function login
	 * 登入 Plurk 用 method
	 *
	 * @param $username
	 * @param $password
	 * @param $api_key
	 * @return boolean
	 * @see /API/Users/login
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
	 * @param
	 * @return unknown_type
	 * @see /API/Users/updatePicture
	 */
	function update_picture()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Users/update
	 */
	function update()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Polling/getPlurks
	 */
	function get_plurks_polling()
	{

	}

	/**
	 * function get_plurks
	 * 取回某一個特定的噗
	 *
	 * @param $plurk_id
	 * @return object
	 * @see /API/Timeline/getPlurk
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
	 * @param
	 * @return unknown_type
	 * @see /API/Timeline/getUnreadPlurks
	 */
	function get_unread_plurks()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Timeline/mutePlurks
	 */
	function mute_plurks()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Timeline/unmutePlurks
	 */
	function unmute_plurks()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Timeline/markAsRead
	 */
	function mark_plurk_as_read()
	{

	}

	/**
	 * function add_Plurk
	 *
	 * no_comments:
	 * 如果是 0, 允許回應
	 * 如果是 1, 不允許回應
	 * 如果是 2, 只有好友能夠回應
	 * @return object
	 * @see /API/Timeline/plurkAdd
	 */
	function add_plurk($lang = 'en', $qualifier = 'says', $content = 'test from roga-plurk-api', $limited_to = NULL, $no_comments = 0)
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
	 * @param
	 * @return unknown_type
	 * @see /API/Timeline/uploadPicture
	 */
	function upload_picture()
	{

	}

	/**
	 * @param
	 * plurk_id: The id of the plurk.	 
	 * @return object
	 * @see /API/Timeline/plurkDelete	 
	 */
	function delete_plurk($plurk_id = '')
	{
		if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
		$array = array(
			'api_key'	   => $this->api_key,
			'plurk_id'	  => $plurk_id
		);

		return $this->plurk(PLURK_TIMELINE_PLURK_DELETE, $array);
	}

	/**
	 * @param
	 * plurk_id: The id of the plurk.
	 * ontent: The content of plurk.	 
	 * @return object
	 * @see /API/Timeline/plurkEdit
	 */
	function edit_plurk($plurk_id = '', $content = '')
	{
		if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

		if (mb_strlen($content) > 140)
		{
			$this->log('這個噗訊息太長了');
		}

		$array = array(
			'api_key'	   => $this->api_key,
			'plurk_id'	  => $plurk_id,
			'content'	   => urlencode($content)
		);
		return $this->plurk(PLURK_TIMELINE_PLURK_EDIT, $array);
	}

	/**
	 * @param
	 * plurk_id: The plurk that the responses should be added to.
	 * offset: Only fetch responses from an offset, should be 5, 10 or 15.   	 
	 * @return object
	 * @see /API/Responses/get
	 */
	function get_responses($plurk_id = '', $offset = 0)
	{
		$array = array(
			'api_key'	   => $this->api_key,
			'offset'		=> $offset
		);
		return $this->plurk(PLURK_GET_RESPONSE, $array);
	}

	/**
	 * @param
	 * plurk_id: The plurk that the responses should be added to.
	 * content: The response's text.
	 * qualifier: The Plurk's qualifier, must be in English. ex: loves, likes, shares, gives, hates, wants, has, will, asks, wishes, was, feels, thinks, says, is, :, freestyle, hopes, needs, wonders     	 
	 * @return object
	 * @see /API/Responses/responseAdd
	 */
	function add_response($plurk_id = '', $content = '', $qualifier = 'says')
	{
		if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

		if (mb_strlen($content) > 140)
		{
			$this->log('這個噗訊息太長了');
		}

		$array = array(
			'api_key'	   => $this->api_key,
			'plurk_id'	  => $plurk_id,
			'content'	   => urlencode($content),
			'qualifier'  => $qualifier 
		);
		return $this->plurk(PLURK_ADD_RESPONSE, $array);
	}

	/**
	 * @param
	 * response_id: The plurk that the responses should be added to.
	 * plurk_id: The plurk that the response belongs to.   	 
	 * @return object
	 * @see /API/Responses/responseDelete
	 */
	function delete_response($plurk_id = '', $response_id = '')
	{
    if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
		$array = array(
			'api_key' => $this->api_key,
			'plurk_id' => $plurk_id,
			'response_id' => $response_id
		);
		return $this->plurk(PLURK_DELERE_RESPONSE, $array);    
	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Profile/getOwnProfile
	 */
	function get_profile()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Profile/getPublicProfile
	 */
	function get_public_profile()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see getFriendsByOffset
	 */
	function get_friends()
	{

	}

	/**
	 * function get_fans
	 * 取回粉絲列表
	 *
	 * @param $offset
	 * @return object
	 * @see /API/FriendsFans/getFansByOffset
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
	 * @param
	 * @return unknown_type
	 * @see /API/FriendsFans/getFollowingByOffset
	 */
	function get_following()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/FriendsFans/becomeFriend
	 */
	function become_friend()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/FriendsFans/removeAsFriend
	 */
	function remove_Friend()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/FriendsFans/becomeFan
	 */
	function become_fan()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/FriendsFans/setFollowing
	 */
	function set_following()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/FriendsFans/getCompletion
	 */
	function get_completion()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Alerts/getActive
	 */
	function get_active()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Alerts/getHistory
	 */
	function get_history()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Alerts/addAsFan
	 */
	function add_as_fan()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Alerts/addAllAsFan
	 */
	function add_all_as_fan()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Alerts/addAllAsFriends
	 */
	function add_all_as_friends()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Alerts/addAsFriend
	 */
	function add_as_friend()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Alerts/denyFriendship
	 */
	function deny_friendship()
	{
	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Alerts/removeNotification
	 */
	function remove_notification()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/PlurkSearch/search
	 */
	function search_plurk()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/UserSearch/search
	 */
	function search_user()
	{
	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Emoticons/get
	 */
	function get_emoticons()
	{

	}

	/**
	 * @param
	 * @return unknown_type
	 * @see /API/Blocks/get
	 */
	function get_blocks()
	{

	}

	/**
	 * funciton block_user
	 * 封鎖特定使用者
	 *
	 * @param $uid
	 * @return object
	 * @see /API/Blocks/block
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
	 * @param
	 * @return unknown_type
	 * @see /API/Blocks/unblock
	 */
	function unblock_user()
	{

	}

	/**
	 * function get_cliques()
	 * 取得小圈圈
	 * @return array
	 * @see /API/Cliques/get_cliques
	 */
	function get_cliques()
	{
		if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

		$array = array(
			'api_key'	   => $this->api_key,
		);

		return $this->plurk(PLURK_GET_CLIQUES, $array);
	}

	/**
	 * function get_clique()
	 * 取得單一小圈圈的使用者
	 *
	 * @param $clique_name
	 * @return array
	 * @see /API/Cliques/get_clique
	 */
	function get_clique($clique_name)
	{
		if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

		$array = array(
			'api_key'	   => $this->api_key,
			'clique_name' => $clique_name
		);

		return $this->plurk(PLURK_GET_CLIQUE, $array);
	}


	/**
	 * function create_clique()
	 * create clique
	 *
	 * @param $clique_name
	 * @return boolean
	 * @see /API/Cliques/create_clique
	 */
	function create_clique($clique_name)
	{
		if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

		$array = array(
			'api_key'	   => $this->api_key,
			'clique_name' => $clique_name
		);

		$result =  $this->plurk(PLURK_CREATE_CLIQUE, $array);

		return ($this->http_status == '200') ? TRUE : FALSE;

	}

	/**
	 * function delete_clique()
	 * delete clique
	 *
	 * @param $clique_name
	 * @return boolean
	 * @see
	 */
	function delete_clique($clique_name)
	{
		if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

		$array = array(
			'api_key'	   => $this->api_key,
			'clique_name' => $clique_name,
		);

		$result = $this->plurk(PLURK_DELETE_CLIQUE, $array);

		return ($this->http_status == '200') ? TRUE : FALSE;

	}

	/**
	 * function rename_clique()
	 * rename clique
	 *
	 * @param $clique_name
	 * @param $new_name
	 * @return boolean
	 * @see /API/Cliques/rename_clique
	 */
	function rename_clique($clique_name,$new_name)
	{
		if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
		$array = array(
			'api_key'	   => $this->api_key,
			'clique_name' => $clique_name,
			'new_name'   => $new_name
		);

		$result = $this->plurk(PLURK_RENAME_CLIQUE, $array);

		return ($this->http_status == '200') ? TRUE : FALSE;
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
}