<?php

    /**
     *  constants for url setting
     *  @package php-plurk-api
     *  @see     http://www.plurk.com/API
     *
     */

	/**
	 *  Users
	 *  /API/Users/register
	 *  /API/Users/login
	 *  /API/Users/logout
	 *  /API/Users/update requires login
	 *  /API/Users/updatePicture requires login
	 *
	 */

    define('PLURK_REGISTER', 'http://www.plurk.com/API/Users/register');
    define('PLURK_LOGIN', 'http://www.plurk.com/API/Users/login');
    define('PLURK_LOGOUT', 'http://www.plurk.com/API/Users/logout');
    define('PLURK_UPDATE_PICTURE', 'http://www.plurk.com/API/Users/updatePicture');
    define('PLURK_UPDATE', 'http://www.plurk.com/API/Users/update');

	/**
	 *  Real time notifications
	 *  /API/Realtime/getUserChannel requires login
	 *
	 */

	define('PLURK_REALTIME_GET_USER_CHANNEL', 'http://www.plurk.com/API/Realtime/getUserChannel');

	/**
	 *  Polling
	 *  /API/Polling/getPlurks requires login
	 *  /API/Polling/getUnreadCount requires login
	 *
	 */

    define('PLURK_POLLING_GET_PLURK', 'http://www.plurk.com/API/Polling/getPlurks');
    define('PLURK_POLLING_GET_UNREAD_COUNT', 'http://www.plurk.com/API/Polling/getUnreadCount');

    /**
     *  Timeline
     *  /API/Timeline/getPlurk requires login
     *  /API/Timeline/getPlurks requires login
     *  /API/Timeline/getUnreadPlurks requires login
     *  /API/Timeline/plurkAdd requires login
     *  /API/Timeline/plurkDelete requires login
     *  /API/Timeline/plurkEdit requires login
     *  /API/Timeline/mutePlurks requires login
     *  /API/Timeline/unmutePlurks requires login
     *  /API/Timeline/markAsRead requires login
     *  /API/Timeline/uploadPicture requires login
     *
     */

    define('PLURK_TIMELINE_GET_PLURK', 'http://www.plurk.com/API/Timeline/getPlurk');
    define('PLURK_TIMELINE_GET_PLURKS', 'http://www.plurk.com/API/Timeline/getPlurks');
    define('PLURK_TIMELINE_GET_UNREAD_PLURKS', 'http://www.plurk.com/API/Timeline/getUnreadPlurks');
    define('PLURK_TIMELINE_MUTE_PLURKS', 'http://www.plurk.com/API/Timeline/mutePlurks');
    define('PLURK_TIMELINE_UNMUTE_PLURKS', 'http://www.plurk.com/API/Timeline/unmutePlurks');
    define('PLURK_TIMELINE_MARK_AS_READ', 'http://www.plurk.com/API/Timeline/markAsRead');
    define('PLURK_TIMELINE_PLURK_ADD', 'http://www.plurk.com/API/Timeline/plurkAdd');
    define('PLURK_TIMELINE_UPLOAD_PICTURE', 'http://www.plurk.com/API/Timeline/uploadPicture');
    define('PLURK_TIMELINE_PLURK_DELETE', 'http://www.plurk.com/API/Timeline/plurkDelete');
    define('PLURK_TIMELINE_PLURK_EDIT', 'http://www.plurk.com/API/Timeline/plurkEdit');

    /**
     *  Responses
     *  /API/Responses/get
     *  /API/Responses/responseAdd requires login
     *  /API/Responses/responseDelete requires login
     *
     */

    define('PLURK_GET_RESPONSE','http://www.plurk.com/API/Responses/get');
    define('PLURK_ADD_RESPONSE','http://www.plurk.com/API/Responses/responseAdd');
    define('PLURK_DELERE_RESPONSE','http://www.plurk.com/API/Responses/responseDelete');

    /**
     *  Profile
     *  /API/Profile/getOwnProfile requires login
     *  /API/Profile/getPublicProfile
     *
     */

    define('PLURK_GET_OWN_PROFILE','http://www.plurk.com/API/Profile/getOwnProfile');
    define('PLURK_GET_PUBLIC_PROFILE','http://www.plurk.com/API/Profile/getPublicProfile');

    /**
     *  Friends and fans
     *  /API/FriendsFans/getFriendsByOffset
     *  /API/FriendsFans/getFansByOffset
     *  /API/FriendsFans/getFollowingByOffset requires login
     *  /API/FriendsFans/becomeFriend requires login
     *  /API/FriendsFans/removeAsFriend requires login
     *  /API/FriendsFans/becomeFan requires login
     *  /API/FriendsFans/setFollowing requires login
     *  /API/FriendsFans/getCompletion requires login
     *
     */

    define('PLURK_GET_FRIENDS','http://www.plurk.com/API/FriendsFans/getFriendsByOffset');
    define('PLURK_GET_FANS','http://www.plurk.com/API/FriendsFans/getFansByOffset');
    define('PLURK_GET_FOLLOWING','http://www.plurk.com/API/FriendsFans/getFollowingByOffset');
    define('PLURK_BECOME_FRIEND','http://www.plurk.com/API/FriendsFans/becomeFriend');
    define('PLURK_REMOVE_FRIEND','http://www.plurk.com/API/FriendsFans/removeAsFriend');
    define('PLURK_BECOME_FAN','http://www.plurk.com/API/FriendsFans/becomeFan');
    define('PLURK_SET_FOLLOWING','http://www.plurk.com/API/FriendsFans/setFollowing');
    define('PLURK_GET_COMPLETION','http://www.plurk.com/API/FriendsFans/getCompletion');

	/**
	 *  Alerts
	 *  General data structures
	 *  /API/Alerts/getActive requires login
	 *  /API/Alerts/getHistory requires login
	 *  /API/Alerts/addAsFan requires login
	 *  /API/Alerts/addAllAsFan requires login
	 *  /API/Alerts/addAllAsFriends requires login
	 *  /API/Alerts/addAsFriend requires login
	 *  /API/Alerts/denyFriendship requires login
	 *  /API/Alerts/removeNotification requires login
	 *
	 */

    define('PLURK_GET_ACTIVE','http://www.plurk.com/API/Alerts/getActive');
    define('PLURK_GET_HISTORY','http://www.plurk.com/API/Alerts/getHistory');
    define('PLURK_ADD_AS_FAN','http://www.plurk.com/API/Alerts/addAsFan');
    define('PLURK_ADD_AS_FRIEND','http://www.plurk.com/API/Alerts/addAsFriend');
    define('PLURK_ADD_ALL_AS_FAN','http://www.plurk.com/API/Alerts/addAllAsFan');
    define('PLURK_ADD_ALL_AS_FRIEND','http://www.plurk.com/API/Alerts/addAllAsFriends');
    define('PLURK_DENY_FRIEND','http://www.plurk.com/API/Alerts/denyFriendship');
    define('PLURK_REMOVE_NOTIFY','http://www.plurk.com/API/Alerts/removeNotification');

    /**
     * Search
     * /API/PlurkSearch/search
     * /API/UserSearch/search
     *
     */

    define('PLURK_SEARCH','http://www.plurk.com/API/PlurkSearch/search');
    define('PLURK_USER_SEARCH','http://www.plurk.com/API/UserSearch/search');

    /**
     *  Emoticons
     *  /API/Emoticons/get
     *
     */

    define('PLURK_GET_EMOTIONS','http://www.plurk.com/API/Emoticons/get');

    /**
     *  Blocks
     *  /API/Blocks/get requires login
     *  /API/Blocks/block requires login
     *  /API/Blocks/unblock requires login
     *
     */

    define('PLURK_GET_BLOCKS','http://www.plurk.com/API/Blocks/get');
    define('PLURK_BLOCK','http://www.plurk.com/API/Blocks/block');
    define('PLURK_UNBLOCK','http://www.plurk.com/API/Blocks/unblock');

    /**
     *  Cliques
     *  /API/Cliques/getCliques requires login
     *  /API/Cliques/getClique requires login
     *  /API/Cliques/createClique requires login
     *  /API/Cliques/renameClique requires login
     *  /API/Cliques/add requires login
     *  /API/Cliques/remove
     *
     */

    define('PLURK_GET_CLIQUES','http://www.plurk.com/API/Cliques/get_cliques');
    define('PLURK_GET_CLIQUE','http://www.plurk.com/API/Cliques/get_clique');
    define('PLURK_CREATE_CLIQUE','http://www.plurk.com/API/Cliques/create_clique');
    define('PLURK_RENAME_CLIQUE', 'http://www.plurk.com/API/Cliques/rename_clique');
    define('PLURK_DELETE_CLIQUE', 'http://www.plurk.com/API/Cliques/delete_clique');
    define('PLURK_ADD_TO_CLIQUE', 'http://www.plurk.com/API/Cliques/add');
    define('PLURK_REMOVE_FROM_CLIQUE', 'http://www.plurk.com/API/Cliques/remove');
?>