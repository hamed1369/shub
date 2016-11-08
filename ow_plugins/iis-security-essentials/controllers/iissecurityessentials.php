<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iissecurityessentials.controllers
 * @since 1.0
 */
class IISSECURITYESSENTIALS_CTRL_Iissecurityessentials extends OW_ActionController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param null $params
     */
    public function editPrivacy($params = NULL)
    {
        if (!OW::getUser()->isAuthenticated()) {
            exit(json_encode(array('result' => false)));
        }

        $privacy = $_REQUEST['privacy'];
        $privacy = IISSECURITYESSENTIALS_BOL_Service::getInstance()->validatePrivacy($privacy);
        $objectId = $_REQUEST['objectId'];
        $actionType = $_REQUEST['actionType'];
        $actionId = null;
        $feedId = $_REQUEST['feedId'];
        $objectUserId = null;

        if($actionType == 'user_status' || $actionType == 'group_status') {
            $actionId = $objectId;
            $action = NEWSFEED_BOL_Service::getInstance()->findActionById($actionId);
            $entityType = $action->entityType;
            if($entityType == 'video_comments'){
                $objectUserId = IISSECURITYESSENTIALS_BOL_Service::getInstance()->updatePrivacyOfVideo($action->entityId, $privacy,$feedId);
            }else if($entityType == 'multiple_photo_upload'){
                $objectUserId =IISSECURITYESSENTIALS_BOL_Service::getInstance()->updatePrivacyOfMultiplePhoto(json_decode($action->data)->photoIdList, $privacy,$feedId);
            }else if($entityType == 'photo_comments'){
                if(IISSECURITYESSENTIALS_BOL_Service::getInstance()->getActionOwner($actionId) == IISSECURITYESSENTIALS_BOL_Service::getInstance()->getPhotoOwner($action->entityId)){
                    $albumId = PHOTO_BOL_PhotoService::getInstance()->findPhotoById($action->entityId)->albumId;
                    IISSECURITYESSENTIALS_BOL_Service::getInstance()->updatePrivacyOfPhotosByAlbumId($albumId, $privacy, $feedId);
                    $objectUserId = IISSECURITYESSENTIALS_BOL_Service::getInstance()->updatePrivacyOfPhoto($action->entityId, $privacy,$feedId);

                }
            }
        }else if($actionType == 'video_comments'){
            $objectUserId = IISSECURITYESSENTIALS_BOL_Service::getInstance()->updatePrivacyOfVideo($objectId, $privacy,$feedId);
            $actionId = NEWSFEED_BOL_Service::getInstance()->findAction($actionType, $objectId)->id;
        }else if($actionType == 'album'){
            $result = IISSECURITYESSENTIALS_BOL_Service::getInstance()->updatePrivacyOfPhotosByAlbumId($objectId, $privacy,$feedId);
            $objectUserId = $result['userId'];
            $actionId = $result['actionId'];
        }else if($actionType == 'questionsPrivacy'){
            IISSECURITYESSENTIALS_BOL_Service::getInstance()->setQuestionPrivacy($feedId, $objectId, $privacy);
            $actionId = null;
        }

        if($objectUserId!=null && ($feedId==null || $feedId=='')){
            $feedId = $objectUserId;
        }

        if($actionId!=null && $feedId!=null) {
            IISSECURITYESSENTIALS_BOL_Service::getInstance()->updateNewsFeedActivitiesByActionIds($actionId, $privacy, $feedId);
        }
        exit(json_encode(array('result' => true,'title' =>IISSECURITYESSENTIALS_BOL_Service::getInstance()->getPrivacyLabelByFeedId($privacy, $feedId), 'id' => '#sec-'.$objectId.'-'.$feedId, 'src' => OW::getPluginManager()->getPlugin('iissecurityessentials')->getStaticUrl() . 'images/' . $privacy . '.png')));
    }
}