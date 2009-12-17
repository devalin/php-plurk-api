<?php

/**
 * load dependencies.
 */
require('config.php');
require('constant.php');
require('common.php');

/**
 * This is a PHP Plurk API.
 *
 * @category  API
 * @version   php-plurk-api 1.2b
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link      http://code.google.com/p/php-plurk-api
 *
 */
Class plurk_api Extends common {

    /**
     * User name
     * @var string $username
     */
    protected $username;

    /**
     * Password
     * @var string $password
     */
    protected $password;

    /**
     * API KEY
     * @var $api_key
     */
    protected $api_key;

    /**
     * Login status
     * @var bool $is_login
     */
    protected $is_login = FALSE;

    /**
     * Current HTTP Status Code
     * @var int $http_status
     */
    protected $http_status;

    /**
     * Current HTTP Server Response
     * @var JSON object $http_response
     */
    protected $http_response;

    /**
     * User infomation
     * @var JSON object $user_info
     */
    protected $user_info;

    /**
     * The unique user id.
     * @var int $uid
     */
    protected $uid;

    /**
     * The unique nick_name of the user, for example amix.
     * @var string $nick_name
     */
    protected $nick_name;
        
    /**
     * The non-unique display name of the user, for example Amir S. Only set if it's non empty.
     * @var string $display_name
     */    
    protected $display_name;
    /**
     * If 1 then the user has a profile picture, otherwise the user should use the default.
     * @var int $has_profile_image
     */
    protected $has_profile_image;

    /**
     * Specifies what the latest avatar (profile picture) version is.
     * @var string $avatar
     */
    protected $avatar;
    
    /**
     * The user's location, a text string, for example Aarhus Denmark.
     * @var string $location
     */
    protected $location;
    
    /**
     * date_of_birth: The user's birthday.
     * @var string $date_of_birth
     */
    protected $date_of_birth;
    
    /**
     * The user's full name, like Amir Salihefendic.
     * @var string $full_name 
     */
    protected $full_name;
    
    /**
     * 1 is male, 0 is female.
     * @var int $gender;
     */
    protected $gender;
    
    /**
     * The profile title of the user.
     * @var string $page_title
     */
    protected $page_title;
    
    /**
     * User's karma value.
     * @var int $karma
     */
    protected $karma;
    
    /**
     * How many friends has the user recruited.
     * @var int $recruited;
     */
    protected $recruited;
    
    /**
     * Can be not_saying, single, married, divorced, engaged, in_relationship, complicated, widowed, open_relationship
     * @var string $relationship
     */
    protected $relationship;
    
    /**
     * fans count
     * @var int $fans_count
     */
    protected $fans_count;

    /**
     * alert count
     * @var int $alerts_count
     */
    protected $alerts_count;

    /**
     * friends count
     * @var int $friends_count
     */
    protected $friends_count;

    /**
     * Plurk Privacy
     * @var boolean $privacy
     */
    protected $privacy;

      

    function __construct() {}

    /**
     * function plurk
     * Connect to Plurk
     *
     * @param $url
     * @param $array
     * @return JSON object
     */
    function plurk($url, $array)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($array));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, "php-plurk-api 1.2b");

        curl_setopt($ch, CURLOPT_COOKIEFILE, PLURK_COOKIE_PATH);
        curl_setopt($ch, CURLOPT_COOKIEJAR, PLURK_COOKIE_PATH);

        $response = curl_exec($ch);
        $this->http_response = $response;
        $this->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return json_decode($response);
    }

    /**
     * function register 
     * Register a new Plurk account. Should be HTTPS
     * 
     * @param string $nick_name The user's nick name. Should be longer than 3 characters. Should be ASCII. Nick name can only contain letters, numbers and _.
     * @param string $full_name Can't be empty.
     * @param string $password Should be longer than 3 characters.
     * @param string $gender Should be male or female.
     * @param string $date_of_birth Should be YYYY-MM-DD, example 1985-05-13.
     * $param string $email (Optional) Must be a valid email.
     * @return JSON object
     * @see /API/Users/register
     */
    function register($nick_name = '', $full_name = '', $password = '', $gender = 'male', $date_of_birth = '0000-00-00', $email = NULL)
    {

    	if(strlen($nick_name) < 4)
            $this->log('nick name should be longer than 3 characters.');

        if ( ! preg_match('/^[\w_]+$/', $str))
            $this->log('nick name should be ASCII, numbers and _.');

        if($full_name == "")
            $this->log('full name can not be empty.');

        if(strlen($password) < 4)
            $this->log('password should be longer than 3 characters.');

        $gender = strtolower($gender);

        if($gender != 'male' && $gender != 'female')
            $this->log('should be male or female.');

        if ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email))
            $this->log('must be a valid email.');

        $array = array(
            'api_key'       => $this->api_key,
            'nick_name'     => $nick_name,
            'full_name'     => $full_name,
            'password'      => $password,
            'gender'        => $gender,
            'date_of_birth' => $date_of_birth
        );

        if(isset($email)) $array['email'] = $email;

        return $this->plurk(PLURK_REGISTER, $array);
    }

    /**
     * function login
     * Login an already created user. Login creates a session cookie, which can be used to access the other methods.
     * 
     * @param $username The user's nick name or email.
     * @param $password The user's password.
     * @param $api_key Your Plurk API key.
     * @return boolean
     * @see /API/Users/login
     */
    function login($api_key = '', $username = '', $password = '')
    {

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
           
            $this->username = $username;
            $this->password = $password;
            $this->api_key = $api_key;            
            $this->user_info = $result;
            $this->fans_count = $result->fans_count;
            $this->alerts_count = $result->alerts_count;
            $this->friends_count = $result->friends_count;
            $this->privacy = $result->privacy;
        }
        else
        {
            $this->log('Login Failed!');
            
            exit('Please Login Again');
        }

        return $this->is_login;

    }

    /**
     * function update_picture
     * pdate a user's profile picture. You can read more about how to render an avatar via user data.
     * 
     * @param string $profile_image The new profile image.
     * @return boolean
     * @see /API/Users/updatePicture
     */
    function update_picture($profile_image = '')
    {
    	//  RFC 1867

        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $boundary = uniqid('------------------');
        $MPboundary = '--' . $boundary;
        $endMPboundary = $MPboundary. '--';

        $file = file_get_contents($profile_image);
        $file_name = basename($profile_image);

        $multipartbody .= $MPboundary . "\r\n";
        $multipartbody .= 'Content-Disposition: form-data; name="filename"; filename="' . $file_name . '"' . '"\r\n"';
        $multipartbody .= 'Content-Type: text/csv'. "\r\n\r\n";
        $multipartbody .= $file;

        $multipartbody .= $MPboundary . "\r\n";
        $multipartbody.= "content-disposition: form-data; name=api_key\r\n\r\n";
        $multipartbody.= $this->api_key. "\r\n\r\n" . $endMPboundary;

        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, PLURK_UPDATE_PICTURE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $multipartbody );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data; boundary=$boundary"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $result = curl_exec($ch);

        $this->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->http_response = $response;

        return ($this->http_status == '200') ? TRUE : FALSE;

    }

    /**
     * function update
     * Update a user's information (such as email, password or privacy). Should be HTTPS
     * 
     * @param string $current_password User's current password.
     * @param string $full_name Change full name.
     * @param string $new_password Change password.
     * @param string $email Change email.
     * @param string $display_name User's display name, can be empty and full unicode. Must be shorter than 15 characters.
     * @param string $privacy User's privacy settings. The option can be world (whole world can view the profile), only_friends (only friends can view the profile) or only_me (only the user can view own plurks).
     * @param string $date_of_birth Should be YYYY-MM-DD, example 1985-05-13.
     * @return boolean
     * @see /API/Users/update
     */
    function update($current_password = NULL, $full_name = NULL, $new_password = NULL, $email = NULL, $display_name = NULL, $privacy = NULL, $date_of_birth = NULL)
    {
    	if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        if($full_name == "")
            $this->log('full name can not be empty.');

        if(strlen($current_password) < 4)
            $this->log('password should be longer than 3 characters.');

         if($full_name == "")
            $this->log('full name can not be empty.');

        if ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email))
            $this->log('must be a valid email.');

        if(strlen($display_name) < 16)
            $this->log('Display name must be shorter than 15 characters.');

        $array = array(
            'api_key'          => $this->api_key,
            'current_password' => $current_password,
        );

        if(isset($full_name)) $array['full_name'] = $full_name;
        if(isset($new_password)) $array['new_password'] = $new_password;
        if(isset($display_name)) $array['display_name'] = $display_name;
        if(isset($email)) $array['email'] = $email;
        if(isset($privacy)) $array['prvacy'] = $privacy;
        if(isset($date_of_birth)) $array['date_of_birth'] = $date_of_birth;

        $this->plurk(PLURK_UPDATE, $array);

        return ($this->http_status == '200') ? TRUE : FALSE;

    }

    /**
     * function get_plurks_polling
     * 
     * @param time $offset Return plurks newer than offset, use timestamp.
     * @return JSON object
     * @see /API/Polling/getPlurks
     */
    function get_plurks_polling($offset = NULL)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $offset = (isset($offset)) ? $offset : array_shift(explode("+",date("c",$offset)));

        $array = array(
            'api_key' => $this->api_key,
            'offset'  => $offset,
        );

        $result = $this->plurk(PLURK_POLLING_GET_PLURK, $array);

    }

    /**
     * function get_plurks
     * 
     * @param int $plurk_id The unique id of the plurk. Should be passed as a number, and not base 36 encoded.
     * @return JSON object
     * @see /API/Timeline/getPlurk
     */
    function get_plurk($plurk_id = 0)
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
     *
     * @param time $offset Return plurks older than offset, use timestamp.
     * @param int $limit How many plurks should be returned? Default is 20.
     * @param int $only_user The numeric ID of the user who's plurks should be returned.
     * @param boolean $only_responded Setting it to true will only return responded plurks.
     * @param boolean $only_private Setting it to true will only return private plurks.
     * @return JSON object
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
     * function get_unread_plurks
     * 
     * @param time $offset Return plurks older than offset, use timestamp
     * @param int $limit Limit the number of plurks that is retunred.
     * @return JSON object
     * @see /API/Timeline/getUnreadPlurks
     */
    function get_unread_plurks($offset = null ,$limit = 10)
    {
        // $offset seens it's not working now. by whatup.tw
        if( ! isset($offset)) $offset = time();
        
        $date = array_shift(explode("+", date("c", $offset)));
        
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
     * function mute_plurks
     *  
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
        
        $this->plurk(PLURK_TIMELINE_MUTE_PLURKS, $array);
        
        return ($this->http_status == '200') ? TRUE : FALSE;

    }

    /**
     * function unmute_plurks
     * 
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
     * function mark_plurk_as_read
     * 
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
        
        $this->plurk(PLURK_TIMELINE_MARK_AS_READ, $array);
        
        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * function add_Plurk
     *
     * @param string $lang The plurk's language.
     * @param string $qualifier The Plurk's qualifier, must be in English. please see documents/README
     * @param string $content The Plurk's text.
     * @param $limited_to Limit the plurk only to some users (also known as private plurking). limited_to should be a Array list of friend ids, e.g. limited_to = array(3,4,66,34) will only be plurked to these user ids.
     * @param string $lang The plurk's language.
     * @param int $no_commetns If set to 1, then responses are disabled for this plurk. If set to 2, then only friends can respond to this plurk.
     * @return JSON object
     * @see /API/Timeline/plurkAdd
     */
    function add_plurk($lang = 'en', $qualifier = 'says', $content = 'test from roga-plurk-api', $limited_to = NULL, $no_comments = 0)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        if (mb_strlen($content) > 140)
        {
            $this->log('this message should shorter than 140 characters.');
        }

        $array = array(
            'api_key'     => $this->api_key,
            'qualifier'   => 'likes',
            'content'     => urlencode($content),
            'lang'        => $lang,
            'no_comments' => $no_comments
        );

        // roga.2009-12-14: need to confirm. 
        if (isset($limited_to)) $array['limited_to'] = json_encode($limited_to);
        
        $this->plurk(PLURK_TIMELINE_PLURK_ADD, $array);
        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * function upload_picture
     * to upload a picture to Plurk, you should do a multipart/form-data POST request
     * to /API/Timeline/uploadPicture. This will add the picture to Plurk's CDN network
     * and return a image link that you can add to /API/Timeline/plurkAdd
     *
     * @param string $upload_image
     * @return JSON object
     * @see /API/Timeline/uploadPicture
     */
    function upload_picture($upload_image = '')
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $boundary = uniqid('------------------');
        $MPboundary = '--' . $boundary;
        $endMPboundary = $MPboundary. '--';

        $file = file_get_contents($upload_image);
        $file_name = basename($upload_image);

        $multipartbody .= $MPboundary . "\r\n";
        $multipartbody .= 'Content-Disposition: form-data; name="filename"; filename="' . $file_name . '"' . '"\r\n"';
        $multipartbody .= 'Content-Type: text/csv'. "\r\n\r\n";
        $multipartbody .= $file;

        $multipartbody .= $MPboundary . "\r\n";
        $multipartbody.= "content-disposition: form-data; name=api_key\r\n\r\n";
        $multipartbody.= $this->api_key. "\r\n\r\n" . $endMPboundary;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, PLURK_UPDATE_PICTURE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $multipartbody );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data; boundary=$boundary"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $result = curl_exec($ch);

        $this->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->http_response = $response;

        return $result;
    }

    /**
     * function delete_plurk
     *
     * @param int $plurk_id: The id of the plurk.
     * @return boolean
     * @see /API/Timeline/plurkDelete
     */
    function delete_plurk($plurk_id = 0)
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
     * function edit_plurk
     * 
     * @param int $plurk_id The id of the plurk.
     * @param string $content The content of plurk.
     * @return boolean
     * @see /API/Timeline/plurkEdit
     */
    function edit_plurk($plurk_id = 0, $content = '')
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        if (mb_strlen($content) > 140)
        {
            $this->log('this message should shorter than 140 characters.');
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
     * function get_responses
     * 
     * @param int $plurk_id: The plurk that the responses should be added to.
     * @param int $offset: Only fetch responses from an offset, should be 5, 10 or 15.
     * @return JSON object
     * @see /API/Responses/get
     */
    function get_responses($plurk_id = 0, $offset = 0)
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
     * function add_response
     * 
     * @param int $plurk_id The plurk that the responses should be added to.
     * @param string $content The response's text.
     * @param string $qualifier The Plurk's qualifier, please see documents/README
     * @return JSON object
     * @see /API/Responses/responseAdd
     */
    function add_response($plurk_id = 0, $content = '', $qualifier = 'says')
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        if (mb_strlen($content) > 140)
        {
            $this->log('this message should shorter than 140 characters.');
        }

        $array = array(
            'api_key'   => $this->api_key,
            'plurk_id'  => $plurk_id,
            'content'   => urlencode($content),
            'qualifier' => $qualifier
        );
        
        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * function delete_response
     * 
     * @param int $response_id The plurk that the responses should be added to.
     * @param int $plurk_id The plurk that the response belongs to.
     * @return boolean
     * @see /API/Responses/responseDelete
     */
    function delete_response($plurk_id = 0, $response_id = 0)
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
     * function get_own_profile
     * 
     * @return JSON object
     * @see /API/Profile/getOwnProfile
     */
    function get_own_profile()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array('api_key' => $this->api_key);

        return $this->plurk(PLURK_GET_OWN_PROFILE, $array);
    }

    /**
     * function get_public_profile
     * 
     * @param int $user_id The user_id of the public profile. Can be integer (like 34) or nick name (like amix).
     * @return JSON object
     * @see /API/Profile/getPublicProfile
     */
    function get_public_profile($user_id = 0)
    {
        
        $array = array(
            'api_key' => $this->api_key,
            'user_id' => $user_id
        );
        
        return $this->plurk(PLURK_GET_PUBLIC_PROFILE, $array);
    }

    /**
     * function get_friends
     * 
     * @param int|string $user_id The user_id of the public profile. Can be integer (like 34) or nick name (like amix).
     * @param int $offset The offset, can be 10, 20, 30 etc.
     * @return JSON objects
     * @see /API/FriendsFans/getFriendsByOffset
     */
    function get_friends($user_id = 0, $offset = 0)
    {
        
        $array = array(
            'api_key' => $this->api_key,
            'user_id' => $user_id,
            'offset'  => $offset
        );
        
        return $this->plurk(PLURK_GET_FRIENDS, $array);
    }

    /**
     * function get_fans
     * 
     * @param int|string $user_id The user_id of the public profile. Can be integer (like 34) or nick name (like amix).
     * @param int $offset The offset, can be 10, 20, 30 etc.
     * @return JSON object
     * @see /API/FriendsFans/getFansByOffset
     */
    function get_fans($user_id = 0, $offset = 0)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key' => $this->api_key,
            'user_id' => $user_id,
            'offset'  => $offset
        );

        return $this->plurk(PLURK_GET_FANS, $array);
    }

    /**
     * function get_following
     * 
     * @param int $offset The offset, can be 10, 20, 30 etc.
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
     * function become_friend
     * 
     * @param int $friend_id The ID of the user you want to befriend.
     * @return boolean
     * @see /API/FriendsFans/becomeFriend
     */
    function become_friend($friend_id = 0)
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
     * function remove_Friend
     * 
     * @param int $friend_id The ID of the user you want to befriend.
     * @return boolean
     * @see /API/FriendsFans/removeAsFriend
     */
    function remove_Friend($friend_id = 0)
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
     * function become_fan
     *  
     * @param int $fan_id Become fan of fan_id. To stop being a fan of someone, user /API/FriendsFans/setFollowing?fan_id=FAN_ID&follow=false.
     * @return boolean
     * @see /API/FriendsFans/becomeFan
     */
    function become_fan($fan_id = 0)
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
     * function set_following
     * Update following of user_id. A user can befriend someone, but can unfollow them. This request is also used to stop following someone as a fan.
     * 
     * @param int $user_id The ID of the user you want to follow/unfollow
     * @param boolean $follow true if the user should be followed, and false if the user should be unfollowed.
     * @return boolean
     * @see /API/FriendsFans/setFollowing
     */
    function set_following($user_id = 0, $follow = false)
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
     * function get_completion
     * Returns a JSON object of the logged in users friends (nick name and full name).
     * 
     * @return JSON object
     * @see /API/FriendsFans/getCompletion
     */
    function get_completion()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array('api_key' => $this->api_key);

        return $this->plurk(PLURK_GET_COMPLETION, $array);
    }

    /**
     * function get_active
     * Return a JSON list of current active alert
     * 
     * @return JSON object
     * @see /API/Alerts/getActive
     */
    function get_active()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        
        $array = array('api_key' => $this->api_key);
        
        return $this->plurk(PLURK_GET_ACTIVE, $array);
    }

    /**
     * function get_history
     * Return a JSON list of past 30 alerts.
     * 
     * @param
     * @return JSON object
     * @see /API/Alerts/getHistory
     */
    function get_history()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        
        $array = array('api_key' => $this->api_key);
        
        return $this->plurk(PLURK_GET_HISTORY, $array);
    }

    /**
     * function add_as_fan
     * Accept user_id as fan.
     * 
     * @param int $user_id The user_id that has asked for friendship.
     * @return Boolean
     * @see /API/Alerts/addAsFan
     */
    function add_as_fan($user_id = 0)
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
     * function add_as_friend
     * Accept user_id as friend.
     * 
     * @param int $user_id The user_id that has asked for friendship.
     * @return Boolean
     * @see /API/Alerts/addAsFriend
     */
    function add_as_friend($user_id = 0)
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
     * function add_all_as_fan
     * Accept all friendship requests as fans.
     * 
     * @return Boolean
     * @see /API/Alerts/addAllAsFan
     */
    function add_all_as_fan()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        
        $array = array('api_key' => $this->api_key);
        
        $result = $this->plurk(PLURK_ADD_ALL_AS_FAN, $array);
        
        return ($this->http_status == '200') ? TRUE : FALSE;
    }


    /**
     * function add_all_as_friends
     * Accept all friendship requests as friends.
     * 
     * @return boolean
     * @see /API/Alerts/addAllAsFriends
     */
    function add_all_as_friends()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        
        $array = array('api_key' => $this->api_key);
        
        $result = $this->plurk(PLURK_ADD_ALL_AS_FRIEND, $array);
        
        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * function deny_friendship
     * Deny friendship to user_id.
     *
     * @param int $user_id The user_id that has asked for friendship.
     * @return Boolean
     * @see /API/Alerts/denyFriendship
     */
    function deny_friendship($user_id = 0)
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
     * function remove_notification
     * Remove notification to user with id user_id.
     * 
     * @param int $user_id The user_id that the current user has requested friendship for.
     * @return Boolean
     * @see /API/Alerts/removeNotification
     */
    function remove_notification($user_id = 0)
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
     * function search_plurk
     * Returns the latest 20 plurks on a search term.
     * 
     * @param string $query The query after Plurks.
     * @param int $offset A plurk_id of the oldest Plurk in the last search result.          
     * @return JSON object
     * @see /API/PlurkSearch/search
     */
    function search_plurk($query = '', $offset = 0)
    {

    	/* offset: A plurk_id of the oldest Plurk in the last search result.  */

        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key' => $this->api_key,
            'query'   => $query,
            'offset'  => $offset
        ) ;

        return $this->plurk(PLURK_SEARCH, $array);
    }

    /**
     * function search_user
     * Returns 10 users that match query, users are sorted by karma.
     * 
     * @param string $query The query after users.
     * @param int $offset Page offset, like 10, 20, 30 etc.          
     * @return JSON object
     * @see /API/UserSearch/search
     */
    function search_user($query = '', $offset = 0)
    {
    	/* offset: Page offset, like 10, 20, 30 etc. */

        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key' => $this->api_key,
            'query'   => $query,
            'offset'  => $offset
        ) ;

        return $this->plurk(PLURK_USER_SEARCH, $array);
    }

    /**
     * function get_emoticons
     * Emoticons are a big part of Plurk since they make it easy to express feelings. 
     * <a href="http://www.plurk.com/Help/extraSmilies">Check out current Plurk emoticons.</a> This call returns a JSON object that looks like:
     * $link http://www.plurk.com/Help/extraSmilies Check out current Plurk emoticons.
     *
     * @return JSON object
     * @see /API/Emoticons/get
     */
    function get_emoticons()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        
        $array = array('api_key' => $this->api_key);
        
        $result = $this->plurk(PLURK_GET_EMOTIONS, $array);
        
        return $result;
    }

    /**
     * function get_blocks
     * 
     * @param int $offset What page should be shown, e.g. 0, 10, 20.     
     * @return JSON object
     * @see /API/Blocks/get
     */
    function get_blocks($offset = 0)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
          'api_key' => $this->api_key,
          'offset'  => $offset,
        );

        return $this->plurk(PLURK_GET_BLOCKS, $array);

    }

    /**
     * funciton block_user
     *
     * @param int $user_id The id of the user that should be blocked.
     * @return boolean
     * @see /API/Blocks/block
     */
    function block_user($user_id = 0)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key' => $this->api_key,
            'user_id' => $user_id,
        );

        $this->plurk(PLURK_BLOCK, $array);
        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * function unblock_user
     * 
     * @param user_id: The id of the user that should be unblocked.     
     * @return boolean
     * @see /API/Blocks/unblock
     */
    function unblock_user($user_id = 0)
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);
        
        $array = array(
            'api_key' => $this->api_key,
            'user_id' => $user_id,
        );
        
        $this->plurk(PLURK_UNBLOCK, $array);
        
        return ($this->http_status == '200') ? TRUE : FALSE;
    }

    /**
     * function get_cliques
     * 
     * @return JSON object
     * @see /API/Cliques/get_cliques
     */
    function get_cliques()
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array('api_key' => $this->api_key);

        return $this->plurk(PLURK_GET_CLIQUES, $array);
    }

    /**
     * function get_clique
     * get users from clique
     *
     * @param string $clique_name The name of the new clique
     * @return array
     * @see /API/Cliques/get_clique
     */
    function get_clique($clique_name = '')
    {
        if( ! $this->is_login) exit(PLURK_NOT_LOGIN);

        $array = array(
            'api_key'     => $this->api_key,
            'clique_name' => $clique_name
        );

        return $this->plurk(PLURK_GET_CLIQUE, $array);
    }


    /**
     * function create_clique
     * create clique
     *
     * @param string $clique_name The name of the new clique
     * @return boolean
     * @see /API/Cliques/create_clique
     */
    function create_clique($clique_name = '')
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
     * function delete_clique
     * delete clique
     *
     * @param string $clique_name The name of the new clique
     * @return boolean
     * @see
     */
    function delete_clique($clique_name = '')
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
     * function rename_clique
     * rename clique
     *
     * @param string $clique_name The name of the current clique.
     * @param string $new_name The name of the new clique.
     * @return boolean
     * @see /API/Cliques/rename_clique
     */
    function rename_clique($clique_name = '', $new_name = '')
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
     * function add_to_clique
     * add friend to clique
     *
     * @param string $clique_name The name of the clique to add.
     * @param int $user_id The user to add to the clique
     * @return boolean
     * @see /API/Cliques/add
     */
    function add_to_clique($clique_name = '', $user_id = 0)
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
     * @param string $clique_name The name of the clique to delete
     * @param int $user_id The user to remove from the clique
     * @return boolean
     * @see /API/Cliques/remove
     */
    function remove_from_clique($clique_name = '', $user_id = 0)
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
     * Get login status
     * 
     * @return boolean
     */
    function get_login_status()
    {
        return ($this->is_login) ? TRUE : FALSE;
    }

    /**
     * function get_http_status
     * Get HTTP Status Code
     * 
     * @return int
     */
    function get_http_status()
    {
        return $this->http_status;
    }

    /**
     * function get_http_response
     * Get HTTP Server Response
     * 
     * @return int
     */
    function get_http_response()
    {
        return $this->http_response;
    }

    /**
     * function get_user_info
     * Get user information
     * 
     * @return JSON object
     */
    function get_user_info()
    {
        return $this->user_info;
    }
}
