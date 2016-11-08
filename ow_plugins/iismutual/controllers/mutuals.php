<?php

class IISMUTUAL_CTRL_Mutuals extends OW_ActionController
{

    public function index($params)
    {
        if(!isset($params['userId'])){
            OW::getApplication()->redirect(OW_URL_HOME);
        }
        $profileOwnerId = (int) $params['userId'];
        $currentUserId = OW::getUser()->getId();

        if($currentUserId == $profileOwnerId){
            OW::getApplication()->redirect(OW_URL_HOME);
        }

        $profileOwnerFriendsId = OW::getEventManager()->call('plugin.friends.get_friend_list', array('userId' => $profileOwnerId));
        $currentUserFriendsId = OW::getEventManager()->call('plugin.friends.get_friend_list', array('userId' => $currentUserId));

        $mutualFriensdId = array();
        foreach($profileOwnerFriendsId as $profileOwnerFriendId){
            if(in_array($profileOwnerFriendId,$currentUserFriendsId)){
                $mutualFriensdId[] = $profileOwnerFriendId;
            }
        }

        if(sizeof($mutualFriensdId)==0){
            $this->assign('empty_list',true);
        }else{
            $this->addComponent('userList', new BASE_CMP_AvatarUserList($mutualFriensdId));
        }
    }

}