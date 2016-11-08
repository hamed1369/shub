<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 * 
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iissuggestfriend.bol
 * @since 1.0
 */
class IISSUGGESTFRIEND_CLASS_Suggest
{
    private static $classInstance;
    
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    
    private function __construct()
    {
    }
    
    public function getSuggestedFriends($currentUserId){
        $secondLevelFriendsOfFriendsId = array();
        if(!class_exists('FRIENDS_BOL_Service')){
            return $secondLevelFriendsOfFriendsId;
        }
        $currentUserFriendsId = OW::getEventManager()->call('plugin.friends.get_friend_list', array('userId' => $currentUserId));
        $friendsOfFriendsOfUser = FRIENDS_BOL_Service::getInstance()->findFriendsIdOfUsersList($currentUserFriendsId, 0, 100000);

        $sizeOfSuggestFriend = 9;
        foreach ($currentUserFriendsId as $currentUserFriendId) {
            $secondLevelFriendsId = $friendsOfFriendsOfUser[$currentUserFriendId]; //OW::getEventManager()->call('plugin.friends.get_friend_list', array('userId' => $currentUserFriendId));
            foreach ($secondLevelFriendsId as $secondLevelFriendId) {
                if (sizeof($secondLevelFriendsOfFriendsId) < $sizeOfSuggestFriend && $secondLevelFriendId != $currentUserId && !in_array($secondLevelFriendId, $secondLevelFriendsOfFriendsId) && !in_array($secondLevelFriendId, $currentUserFriendsId)) {
                    $secondLevelFriendsOfFriendsId[] = $secondLevelFriendId;
                }
            }
        }

        return $secondLevelFriendsOfFriendsId;
    }
}