<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 * 
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iissecurityessentials.bol
 * @since 1.0
 */
class IISSECURITYESSENTIALS_CLASS_EventHandler
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
    
    public function init()
    {
        $service = IISSECURITYESSENTIALS_BOL_Service::getInstance();
        $eventManager = OW::getEventManager();
        $eventManager->bind('feed.collect_privacy', array($service, 'onFeedCollectPrivacy'));
        $eventManager->bind('feed.on_item_render', array($service, 'onFeedItemRender'));
        $eventManager->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($service, 'onBeforeDocumentRenderer'));
        $eventManager->bind('video.collect_video_toolbar_items', array($service, 'onCollectVideoToolbarItems'));
//        $eventManager->bind('photo.collect_photo_context_actions', array($service, 'onCollectPhotoContextActions'));
        $eventManager->bind(IISEventManager::ON_BEFORE_OBJECT_RENDERER, array($service, 'onBeforeObjectRenderer'));
        $eventManager->bind(IISEventManager::ON_BEFORE_UPDATE_STATUS_FORM_RENDERER, array($service, 'onBeforeUpdateStatusFormRenderer'));
        $eventManager->bind(IISEventManager::ON_AFTER_UPDATE_STATUS_FORM_RENDERER, array($service, 'onAfterUpdateStatusFormRenderer'));
        $eventManager->bind('feed.after_activity', array($service, 'onAfterActivity'));
        $eventManager->bind(IISEventManager::ON_BEFORE_UPDATE_STATUS_FORM_CREATE, array($service, 'onBeforeUpdateStatusFormCreate'));
        $eventManager->bind(IISEventManager::ON_QUERY_FEED_CREATE, array($service, 'onQueryFeedCreate'));
        $eventManager->bind('plugin.privacy.get_action_list', array($service, 'privacyAddAction'));
        $eventManager->bind('plugin.privacy.on_change_action_privacy', array($service, 'privacyOnChangeActionPrivacy'));
        $eventManager->bind(IISEventManager::ON_BEFORE_UPDATE_STATUS_FORM_CREATE_IN_PROFILE, array($service, 'onBeforeUpdateStatusFormCreateInProfile'));
        $eventManager->bind(IISEventManager::ON_BEFORE_PHOTO_UPLOAD_FORM_RENDERER, array($service, 'onBeforePhotoUploadFormRenderer'));
        $eventManager->bind(IISEventManager::ON_BEFORE_VIDEO_UPLOAD_FORM_RENDERER, array($service, 'onBeforeVideoUploadFormRenderer'));
        $eventManager->bind(IISEventManager::ON_BEFORE_VIDEO_UPLOAD_COMPONENT_RENDERER, array($service, 'onBeforeVideoUploadComponentRenderer'));
        $eventManager->bind(IISEventManager::ON_BEFORE_PRIVACY_CHECK, array($service, 'getActionPrivacy'));
        $eventManager->bind(IISEventManager::ON_BEFORE_FEED_ITEM_RENDERER, array($service, 'onBeforeFeedItemRenderer'));
        $eventManager->bind(IISEventManager::ON_BEFORE_FEED_ACTIVITY_CREATE, array($service, 'onBeforeFeedActivity'));
        $eventManager->bind(IISEventManager::ON_FEED_ITEM_RENDERER, array($service, 'onFeedItemRenderer'));
        $eventManager->bind('photo.onReadyResponse', array($service, 'onReadyResponseOfPhoto'));
        $eventManager->bind(IISEventManager::ON_BEFORE_ALBUMS_RENDERER, array($service, 'onBeforeAlbumsRenderer'));
        $eventManager->bind(IISEventManager::ON_BEFORE_ALBUM_INFO_RENDERER, array($service, 'onBeforeAlbumInfoRenderer'));
        $eventManager->bind('plugin.privacy.check_permission', array($service, 'check_permission'));
        $eventManager->bind('photo.onAfterPhotoMove', array($service, 'eventAfterPhotoMove'));
        $eventManager->bind(IISEventManager::ON_AFTER_LAST_PHOTO_FEED_REMOVED, array($service, 'onAfterLastPhotoRemoved'));
        $eventManager->bind(IISEventManager::ON_BEFORE_ALBUM_CREATE_FOR_STATUS_UPDATE, array($service, 'onBeforeAlbumCreateForStatusUpdate'));
        $eventManager->bind(IISEventManager::ON_BEFORE_QUESTIONS_DATA_PROFILE_RENDER, array($service, 'onBeforeQuestionsDataProfileRender'));
        $eventManager->bind(IISEventManager::ON_BEFORE_EMAIL_VERIFY_FORM_RENDER, array($service, 'onBeforeEmailVerifyFormRender'));
        $eventManager->bind('base.members_only_exceptions', array($service, 'catchAllRequestsExceptions'));
        $eventManager->bind(IISEventManager::ON_BEFORE_PRIVACY_ITEM_ADD, array($service, 'onBeforePrivacyItemAdd'));
        $eventManager->bind(IISEventManager::ON_BEFORE_USER_INFORMATION_RENDER, array($service, 'onBeforeUsersInformationRender'));
        $eventManager->bind(IISEventManager::ON_BEFORE_INDEX_STATUS_ENABLED, array($service, 'onBeforeIndexStatusEnabled'));
        $eventManager->bind(IISEventManager::ON_BEFORE_UPDATE_ACTIVITY_TIMESTAMP,array($service,'logoutIfIdle'));
        $eventManager->bind(IISEventManager::ON_BEFORE_FEED_RENDERED, array($service, 'onBeforeFeedRendered'));
        $eventManager->bind(OW_EventManager::ON_BEFORE_USER_LOGIN,array($service,'regenerateSessionID'));
        $eventManager->bind(IISEventManager::ON_BEFORE_CREATE_FORM_USING_FIELD_PRIVACY,array($service,'onBeforeCreateFormUsingFieldPrivacy'));
    }

}