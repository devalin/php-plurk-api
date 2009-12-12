<?php

/**
 *
 * load dependencies.
 */
require('config.php');
require('constant.php');
require('common.php');

/**
 * This is a PHP Plurk API.
 *
 * @category  API
 * @package   php-plurk-api
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link      http://code.google.com/p/php-plurk-api
 *
 */
Class plurk_api Extends common {

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
     * nick_name: The user's nick name. Should be longer than 3 characters. Should be ASCII. Nick name can only contain letters, numbers and _.
     * full_name: Can't be empty.
     * password: Should be longer than 3 characters.
     * gender: Should be male or female.
     * date_of_birth: Should be YYYY-MM-DD, example 1985-05-13.                         
     * @return JSON object 
     * @see /API/Users/register
     */
    function register($nick_name = '', $full_name = '', $password = '', $gender = 'male', $date_of_birth = '', $email = '')
    {
        if(empty($nick_name) || empty($full_name) || empty($password)) exit(PLURK_FIELD_NOT_EMPTY);
        if (!preg_match('/^[\w_]+$/', $nick_name) || mb_strlen($nick_name) < 3)
        {
            exit('Nick name must be at least 3 characters long or can only contain letters, numbers and _');
        }
        if(mb_strlen($password) < 3)
        {
            exit('Password too small');
        }
        if(!empty($email) && preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])*(\.([a-z0-9])([-a-z0-9_-])([a-z0-9])+)*$/i', $email))
            exit('Email invalid');
        $array = array(
            'api_key'   => $this->api_key,
            'nick_name' => $nick_name,
            'full_name' => $full_name,
            'password' => $password,
            'gender' => $gender,
            'date_of_birth' => $date_of_birth,
            'email' => $email
        );
        return $this->plurk(PLURK_REGISTER, $array);
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
            'username' => $username,
            'password' => $password,
            'api_key'  => $api_key,
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
     * @return JSON object
     * @see /API/Users/updatePicture
     */
    function update_picture()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key'   => $this->api_key  
        );
        $result = $this->plurk(PLURK_UPDATE_PICTURE, $array);
    }

    /**
     * @param
     * full_name: Change full name.
     * new_password: Change password.
     * email: Change email.
     * display_name: User's display name, can be empty and full unicode. Must be shorter than 15 characters.
     * privacy: User's privacy settings. The option can be world (whole world can view the profile), only_friends (only friends can view the profile) or only_me (only the user can view own plurks).
     * date_of_birth: Should be YYYY-MM-DD, example 1985-05-13.                              
     * @return JSON object
     * @see /API/Users/update
     */
    function update($full_name = '', $new_password = '', $email = '', $display_name = '', $privacy = 'world', $date_of_birth = '')
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        
        $array = array(
            'api_key'   => $this->api_key,
            'current_password' => $this->password,
            'privacy' => $privacy            
        );
        
        if(!empty($full_name))
            $array['full_name'] = $full_name;     
        if(!empty($new_password))
            $array['new_password'] = $new_password; 
        if(!empty($display_name))
            $array['display_name'] = $display_name; 
        if(!empty($date_of_birth))
            $array['date_of_birth'] = $date_of_birth;
                                                
        return $this->plurk(PLURK_UPDATE, $array);
    }

    /**
     * @param
     * offset: Return plurks newer than offset, formatted as 2009-6-20T21:55:34.     
     * @return JSON object
     * @see /API/Polling/getPlurks
     */
    function get_plurks_polling($offset = '')
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $offset = (empty($offset)) ? array_shift(explode("+",date("c",$offset))) : $offset;
        $array = array(
            'api_key'   => $this->api_key,
            'offset'   => $offset
        );
        $result = $this->plurk(PLURK_POLLING_GET_PLURK, $array);

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
            'api_key'   => $this->api_key,
            'plurk_id'  => $plurk_id,
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
     * @see /API/Timeline/getPlurks
     */
    function get_plurks($offset = 0, $limit = 20, $only_user = '', $only_responded = FALSE, $only_private = FALSE)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key'       => $this->api_key,
            'offset'        => $offset,
            'limit'         => $limit,
            'only_user'     => $only_user,
            'only_responded'=> $only_responded,
            'only_private'  => $only_private
        );

        return $this->plurk(PLURK_TIMELINE_GET_PLURKS, $array);
    }

    /**
     * @param $offset Return plurks older than offset, use timestamp. 
     * @param $limit Limit the number of plurks that is retunred.
     * @return object
     * @see /API/Timeline/getUnreadPlurks
     */
    function get_unread_plurks($offset = null ,$limit = 10)
    {
        // $offset seens it's not working now. by whatup.tw
        if($offset == null) $offset = time();
        $date = array_shift(explode("+",date("c",$offset)));
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key'   => $this->api_key,
            'offset'    => $date,
            'limit'     => $limit
        );
        $result = $this->plurk(PLURK_TIMELINE_GET_UNREAD_PLURKS, $array);
        return $result;
    }

    /**
     * @param $ids The plurk ids, eg. array(123,456,789)
     * @return boolean
     * @see /API/Timeline/mutePlurks
     */
    function mute_plurks($ids)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key'    => $this->api_key,
            'ids'        => json_encode($ids),
        );
        $result = $this->plurk(PLURK_TIMELINE_MUTE_PLURKS, $array);
        return ($this->http_status == '200') ? TRUE : FALSE;

    }

    /**
     * @param $ids The plurk ids, eg. array(123,456,789)
     * @return boolean
     * @see /API/Timeline/unmutePlurks
     */
    function unmute_plurks($ids)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key'    => $this->api_key,
            'ids'        => json_encode($ids),
        );
        $result = $this->plurk(PLURK_TIMELINE_UNMUTE_PLURKS, $array);
        return ($this->http_status == '200') ? TRUE : FALSE;

    }

    /**
     * @param $ids The plurk ids, eg. array(123,456,789)
     * @return boolean
     * @see /API/Timeline/markAsRead
     */
    function mark_plurk_as_read($ids)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key'    => $this->api_key,
            'ids'        => json_encode($ids),
        );
        $result = $this->plurk(PLURK_TIMELINE_MARK_AS_READ, $array);
        return ($this->http_status == '200') ? TRUE : FALSE;
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
            'api_key'   => $this->api_key,
            'qualifier' => 'likes',
            'content'   => urlencode($content),
            'lang'      => $lang
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
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array();
        $result = $this->plurk(PLURK_TIMELINE_UPLOAD_PICTURE, $array);
    }

    /**
     * @param
     * plurk_id: The id of the plurk.
     * @return boolean
     * @see /API/Timeline/plurkDelete
     */
    function delete_plurk($plurk_id = '')
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key'  => $this->api_key,
            'plurk_id' => $plurk_id
        );

        $result = $this->plurk(PLURK_TIMELINE_PLURK_DELETE, $array);
        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * @param
     * plurk_id: The id of the plurk.
     * ontent: The content of plurk.
     * @return boolean
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
            'api_key'  => $this->api_key,
            'plurk_id' => $plurk_id,
            'content'  => urlencode($content)
        );
        $result = $this->plurk(PLURK_TIMELINE_PLURK_EDIT, $array);
        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * @param
     * plurk_id: The plurk that the responses should be added to.
     * offset: Only fetch responses from an offset, should be 5, 10 or 15.
     * @return JSON object
     * @see /API/Responses/get
     */
    function get_responses($plurk_id = '', $offset = 0)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key' => $this->api_key,
            'plurk_id' => $plurk_id,
            'offset'  => $offset
        );
        return $this->plurk(PLURK_GET_RESPONSE, $array);
    }

    /**
     * @param
     * plurk_id: The plurk that the responses should be added to.
     * content: The response's text.
     * qualifier: The Plurk's qualifier, please see documents/README
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
            'api_key'   => $this->api_key,
            'plurk_id'  => $plurk_id,
            'content'   => urlencode($content),
            'qualifier' => $qualifier
        );
        return $this->plurk(PLURK_ADD_RESPONSE, $array);
    }

    /**
     * @param
     * response_id: The plurk that the responses should be added to.
     * plurk_id: The plurk that the response belongs to.
     * @return boolean
     * @see /API/Responses/responseDelete
     */
    function delete_response($plurk_id = '', $response_id = '')
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key'     => $this->api_key,
            'plurk_id'    => $plurk_id,
            'response_id' => $response_id
        );
        $result = $this->plurk(PLURK_DELERE_RESPONSE, $array);
        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * @param
     * @return object
     * @see /API/Profile/getOwnProfile
     */
    function get_own_profile()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key' => $this->api_key
        );
        return $this->plurk(PLURK_GET_OWN_PROFILE, $array);
    }

    /**
     * @param
     * user_id: The user_id of the public profile. Can be integer (like 34) or nick name (like amix).
     * @return JSON object 
     * @see /API/Profile/getPublicProfile
     */
    function get_public_profile($user_id = '')
    {
        $user_id = (empty($user_id)) ? $this->user_info->uid : $user_id;
        $array = array(
            'api_key' => $this->api_key,
            'user_id' => $user_id
        );
        return $this->plurk(PLURK_GET_PUBLIC_PROFILE, $array);
    }

    /**
     * @param
     * user_id: The user_id of the public profile. Can be integer (like 34) or nick name (like amix).
     * offset: The offset, can be 10, 20, 30 etc.
     * @return JSON objects
     * @see /API/FriendsFans/getFriendsByOffset
     */
    function get_friends($user_id = '', $offset = 0)
    {
        $user_id = (empty($user_id)) ? $this->user_info->uid : $user_id;
        $array = array(
            'api_key' => $this->api_key,
            'user_id' => $user_id,
            'offset'  => $offset
        );
        return $this->plurk(PLURK_GET_FRIENDS, $array);
    }

    /**
     * function get_fans
     * 取回粉絲列表
     *
     * @param
     * user_id: The user_id of the public profile. Can be integer (like 34) or nick name (like amix).
     * offset: The offset, can be 10, 20, 30 etc.
     * @return object
     * @see /API/FriendsFans/getFansByOffset
     */
    function get_fans($user_id = '', $offset = 0)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $user_id = (empty($user_id)) ? $this->user_info->uid : $user_id;

        $array = array(
            'api_key' => $this->api_key,
            'user_id' => $user_id,
            'offset'  => $offset
        );

        return $this->plurk(PLURK_GET_FANS, $array);
    }

    /**
     * @param
     * offset: The offset, can be 10, 20, 30 etc.
     * @return object
     * @see /API/FriendsFans/getFollowingByOffset
     */
    function get_following($offset = 0)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key' => $this->api_key,
            'offset'  => $offset
        );

        return $this->plurk(PLURK_GET_FOLLOWING, $array);
    }

    /**
     * @param
     * friend_id: The ID of the user you want to befriend.
     * @return boolean
     * @see /API/FriendsFans/becomeFriend
     */
    function become_friend($friend_id = '')
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key'   => $this->api_key,
            'friend_id' => $friend_id
        );

        $result =  $this->plurk(PLURK_BECOME_FRIEND, $array);

        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * @param
     * friend_id: The ID of the user you want to befriend.
     * @return boolean
     * @see /API/FriendsFans/removeAsFriend
     */
    function remove_Friend($friend_id = '')
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key'   => $this->api_key,
            'friend_id' => $friend_id
        );

        $result =  $this->plurk(PLURK_REMOVE_FRIEND, $array);

        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * @param
     * fan_id: Become fan of fan_id. To stop being a fan of someone, user /API/FriendsFans/setFollowing?fan_id=FAN_ID&follow=false.
     * @return boolean
     * @see /API/FriendsFans/becomeFan
     */
    function become_fan($fan_id = '')
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key' => $this->api_key,
            'fan_id'  => $fan_id
        );

        $result =  $this->plurk(PLURK_BECOME_FAN, $array);

        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * @param
     * user_id: The ID of the user you want to follow/unfollow
     * follow: true if the user should be followed, and false if the user should be unfollowed.
     * @return boolean
     * @see /API/FriendsFans/setFollowing
     */
    function set_following($user_id = '', $follow = false)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key' => $this->api_key,
            'user_id' => $user_id,
            'follow'  => $follow
        );

        $result =  $this->plurk(PLURK_SET_FOLLOWING, $array);

        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * Returns a JSON object of the logged in users friends (nick name and full name).
     * @param
     * @return object
     * @see /API/FriendsFans/getCompletion
     */
    function get_completion()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key' => $this->api_key
        );

        return $this->plurk(PLURK_GET_COMPLETION, $array);
    }

    /**
     * @param
     * @return JSON object 
     * @see /API/Alerts/getActive
     */
    function get_active()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key' => $this->api_key    
        );
        return $this->plurk(PLURK_GET_ACTIVE, $array);
    }

    /**
     * @param
     * @return JSON object 
     * @see /API/Alerts/getHistory
     */
    function get_history()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key' => $this->api_key    
        );
        return $this->plurk(PLURK_GET_HISTORY, $array);
    }

    /**
     * @param
     * user_id: The user_id that has asked for friendship.     
     * @return Boolean 
     * @see /API/Alerts/addAsFan
     */
    function add_as_fan($user_id = '')
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key' => $this->api_key,
            'user_id' => $user_id    
        );
        $result = $this->plurk(PLURK_ADD_AS_FAN, $array);
        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * @param
     * user_id: The user_id that has asked for friendship.          
     * @return Boolean
     * @see /API/Alerts/addAsFriend
     */
    function add_as_friend()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key' => $this->api_key,
            'user_id' => $user_id    
        );
        $result = $this->plurk(PLURK_ADD_AS_FRIEND, $array);
        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * @param
     * @return Boolean
     * @see /API/Alerts/addAllAsFan
     */
    function add_all_as_fan()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key' => $this->api_key 
        );
        $result = $this->plurk(PLURK_ADD_ALL_AS_FAN, $array);
        return ($this->http_status == '200') ? TRUE : FALSE;
    }


    /**
     * @param
     * @return Boolean
     * @see /API/Alerts/addAllAsFriends
     */
    function add_all_as_friends()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key' => $this->api_key 
        );
        $result = $this->plurk(PLURK_ADD_ALL_AS_FRIEND, $array);
        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * @param
     * The user_id that has asked for friendship.     
     * @return Boolean
     * @see /API/Alerts/denyFriendship
     */
    function deny_friendship($user_id = '')
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key' => $this->api_key,
            'user_id' => $user_id    
        );
        $result = $this->plurk(PLURK_DENY_FRIEND, $array);
        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * @param
     * user_id: The user_id that the current user has requested friendship for.     
     * @return Boolean
     * @see /API/Alerts/removeNotification
     */
    function remove_notification($user_id = '')
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array(
            'api_key' => $this->api_key,
            'user_id' => $user_id    
        );
        $result = $this->plurk(PLURK_REMOVE_NOTIFY, $array);
        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * @param
     * @return unknown_type
     * @see /API/PlurkSearch/search
     */
    function search_plurk()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array();
        $result = $this->plurk(PLURK_SEARCH, $array);
    }

    /**
     * @param
     * @return unknown_type
     * @see /API/UserSearch/search
     */
    function search_user()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array();
        $result = $this->plurk(PLURK_USER_SEARCH, $array);
    }

    /**
     * @param
     * @return unknown_type
     * @see /API/Emoticons/get
     */
    function get_emoticons()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array();
        $result = $this->plurk(PLURK_GET_EMOTIONS, $array);
    }

    /**
     * @param
     * @return unknown_type
     * @see /API/Blocks/get
     */
    function get_blocks()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array();
        $result = $this->plurk(PLURK_GET_BLOCKS, $array);
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
            'api_key' => $this->api_key,
            'user_id' => $uid,
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
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        $array = array();
        $result = $this->plurk(PLURK_UNBLOCK, $array);
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
            'api_key' => $this->api_key,
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
            'api_key'     => $this->api_key,
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
            'api_key'     => $this->api_key,
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
            'api_key'     => $this->api_key,
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
            'api_key'     => $this->api_key,
            'clique_name' => $clique_name,
            'new_name'    => $new_name
        );

        $result = $this->plurk(PLURK_RENAME_CLIQUE, $array);

        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * function add_to_clique()
     * add friend to clique
     *
     * @param $clique_name
     * @param $user_id
     * @return boolean
     * @see /API/Cliques/add
     */
    function add_to_clique($clique_name,$user_id)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key'     => $this->api_key,
            'clique_name' => $clique_name,
            'user_id'     => $user_id
        );

        $result = $this->plurk(PLURK_ADD_TO_CLIQUE, $array);

        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * function remove_from_clique()
     * remove friend from clique
     *
     * @param $clique_name
     * @param $user_id
     * @return boolean
     * @see /API/Cliques/remove
     */
    function remove_from_clique($clique_name,$user_id)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key'     => $this->api_key,
            'clique_name' => $clique_name,
            'user_id'     => $user_id
        );

        $result = $this->plurk(PLURK_REMOVE_FROM_CLIQUE, $array);

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
