<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 * 
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iismutual.bol
 * @since 1.0
 */
class IISMUTUAL_CLASS_Mutual
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

    /***
     * @param $profileOwnerId
     * @param $currentUserId
     * @return array
     */
    public function getMutualFriends($profileOwnerId, $currentUserId){
        $profileOwnerFriendsId = OW::getEventManager()->call('plugin.friends.get_friend_list', array('userId' => $profileOwnerId));
        $currentUserFriendsId = OW::getEventManager()->call('plugin.friends.get_friend_list', array('userId' => $currentUserId));

        $mutualFriensdId = array();
        foreach ($profileOwnerFriendsId as $profileOwnerFriendId) {
            if (in_array($profileOwnerFriendId, $currentUserFriendsId)) {
                $mutualFriensdId[] = $profileOwnerFriendId;
            }
        }

        return $mutualFriensdId;
    }
}