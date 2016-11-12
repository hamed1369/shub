<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2011, Oxwall Foundation
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and
 *  the following disclaimer.
 *
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *  the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  - Neither the name of the Oxwall Foundation nor the names of its contributors may be used to endorse or promote products
 *  derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Video action controller
 * @package ow_plugins.video.controllers
 * @since 1.0
 */
class VIDEO_MCTRL_Video extends OW_MobileActionController
{
    /**
     * @var OW_Plugin
     */
    private $plugin;
    /**
     * @var string
     */
    private $pluginJsUrl;
    /**
     * @var string
     */
    private $ajaxResponder;
    /**
     * @var VIDEO_BOL_ClipService
     */
    private $clipService;
    /**
     * @var BASE_MCMP_ContentMenu
     */
    private $menu;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->plugin = OW::getPluginManager()->getPlugin('video');
        $this->pluginJsUrl = $this->plugin->getStaticJsUrl();
        $this->ajaxResponder = OW::getRouter()->urlFor('VIDEO_MCTRL_Video', 'ajaxResponder');

        $this->clipService = VIDEO_BOL_ClipService::getInstance();
    }

    /**
     * Returns menu component
     *
     * @return BASE_MCMP_ContentMenu
     */
    private function getMenu()
    {
        $validLists = array('featured', 'latest');
        $classes = array('ow_ic_push_pin', 'ow_ic_clock', 'ow_ic_star', 'ow_ic_tag');

        if ( !VIDEO_BOL_ClipService::getInstance()->findClipsCount('featured') )
        {
            array_shift($validLists);
            array_shift($classes);
        }

        $language = OW::getLanguage();

        $menuItems = array();

        $order = 0;
        foreach ( $validLists as $type )
        {
            $item = new BASE_MenuItem();
            $item->setLabel($language->text('video', 'menu_' . $type));
            $item->setUrl(OW::getRouter()->urlForRoute('view_list', array('listType' => $type)));
            $item->setKey($type);
            $item->setIconClass($classes[$order]);
            $item->setOrder($order);

            array_push($menuItems, $item);

            $order++;
        }

        $validListsEvent = OW::getEventManager()->trigger(new OW_Event(IISEventManager::ADD_LIST_TYPE_TO_VIDEO,array('menuItems' => $menuItems)));
        if(isset($validListsEvent->getData()['menuItems'])){
            $menuItems = $validListsEvent->getData()['menuItems'];
        }

        $menu = new BASE_MCMP_ContentMenu($menuItems);

        return $menu;
    }

    /**
     * Video view action
     *
     * @param array $params
     * @throws Redirect404Exception
     */
    public function view( array $params )
    {
        if ( !isset($params['id']) || !($id = (int) $params['id']) )
        {
            throw new Redirect404Exception();
        }

        $clip = $this->clipService->findClipById($id);

        if ( !$clip )
        {
            throw new Redirect404Exception();
        }
        
        
        OW::getEventManager()->trigger(new OW_Event(IISEventManager::ON_BEFORE_OBJECT_RENDERER, array('privacy' => $clip->privacy, 'ownerId' => $clip->userId)));
        $userId = OW::getUser()->getId();
        $contentOwner = (int) $this->clipService->findClipOwner($id);
        $ownerMode = $contentOwner == $userId;
        
        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('video');
        
        if ( $clip->status != VIDEO_BOL_ClipDao::STATUS_APPROVED && !( $modPermissions || $ownerMode ) )
        {
            throw new Redirect403Exception;
        }

        $language = OW_Language::getInstance();

        $description = $clip->description;
        $clip->description = UTIL_HtmlTag::autoLink($clip->description);
        $this->assign('clip', $clip);
        $is_featured = VIDEO_BOL_ClipFeaturedService::getInstance()->isFeatured($clip->id);
        $this->assign('featured', $is_featured);
        $this->assign('moderatorMode', $modPermissions);
        $this->assign('ownerMode', $ownerMode);

        if ( !$ownerMode && !OW::getUser()->isAuthorized('video', 'view') && !$modPermissions )
        {
            $error = BOL_AuthorizationService::getInstance()->getActionStatus('video', 'view');
            throw new AuthorizationException($error['msg']);
        }

        // permissions check
        if ( !$ownerMode && !$modPermissions )
        {
            $privacyParams = array('action' => 'video_view_video', 'ownerId' => $contentOwner, 'viewerId' => $userId);
            $event = new OW_Event('privacy_check_permission', $privacyParams);
            OW::getEventManager()->trigger($event);
        }

        $cmtParams = new BASE_CommentsParams('video', 'video_comments');
        $cmtParams->setEntityId($id);
        $cmtParams->setOwnerId($contentOwner);
        $cmtParams->setDisplayType(BASE_CommentsParams::DISPLAY_TYPE_BOTTOM_FORM_WITH_FULL_LIST);
        
        $cmtParams->setAddComment($clip->status == VIDEO_BOL_ClipDao::STATUS_APPROVED);

        $videoCmts = new BASE_MCMP_Comments($cmtParams);
        $this->addComponent('comments', $videoCmts);


        $videoTags = new BASE_CMP_EntityTagCloud('video');
        $videoTags->setEntityId($id);
        $videoTags->setRouteName('view_tagged_list');
        $this->addComponent('tags', $videoTags);

        $username = BOL_UserService::getInstance()->getUserName($clip->userId);
        $this->assign('username', $username);

        $displayName = BOL_UserService::getInstance()->getDisplayName($clip->userId);
        $this->assign('displayName', $displayName);

        OW::getDocument()->addScript($this->pluginJsUrl . 'video.js');

        $objParams = array(
            'ajaxResponder' => $this->ajaxResponder,
            'clipId' => $id,
            'txtApprove' => OW::getLanguage()->text('base', 'approve'),
            'txtDisapprove' => OW::getLanguage()->text('base', 'disapprove')
        );

        $script =
            "$(document).ready(function(){
                var clip = new videoClip( " . json_encode($objParams) . ");
            }); ";

        OW::getDocument()->addOnloadScript($script);

        $pendingApprovalString = "";
        if ( $clip->status != VIDEO_BOL_ClipDao::STATUS_APPROVED )
        {
            $pendingApprovalString = '<span class="ow_remark ow_small">(' 
                    . OW::getLanguage()->text("base", "pending_approval") . ')</span>';
        }
        
        OW::getDocument()->setHeading($clip->title . " " . $pendingApprovalString);
        OW::getDocument()->setHeadingIconClass('ow_ic_video');

       //        OW::getDocument()->setTitle($language->text('video', 'meta_title_video_view', array('title' => $clip->title)));
        $tagsArr = BOL_TagService::getInstance()->findEntityTags($clip->id, 'video');

        $labels = array();
        foreach ( $tagsArr as $t )
        {
            $labels[] = $t->label;
        }
        $tagStr = $tagsArr ? implode(', ', $labels) : '';
//        OW::getDocument()->setDescription($language->text('video', 'meta_description_video_view', array('title' => $clip->title, 'tags' => $tagStr)));

        $clipThumbUrl = $this->clipService->getClipThumbUrl($id);
        $this->assign('clipThumbUrl', $clipThumbUrl);

        $params = array(
            "sectionKey" => "video",
            "entityKey" => "viewClip",
            "title" => "video+meta_title_view_clip",
            "description" => "video+meta_desc_view_clip",
            "keywords" => "video+meta_keywords_view_clip",
            "vars" => array("video_title" => $clip->title, "user_name" => $displayName),
            "image" => $clipThumbUrl
        );

        OW::getEventManager()->trigger(new OW_Event("base.provide_page_meta_info", $params));

        OW::getEventManager()->trigger(new OW_Event(IISEventManager::ON_BEFORE_VIDEO_RENDER, array('this' => $this, 'objectId' => $clip->id,'userId' => $clip->userId, 'privacy' => $clip->privacy)));
    }

    /**
     * Video list view action
     *
     * @param array $params
     * @throws AuthorizationException
     */
    public function viewList( array $params )
    {
        $listType = isset($params['listType']) ? trim($params['listType']) : 'latest';

        $validLists = array('featured', 'latest', 'toprated', 'tagged');

        $validListsEvent = OW::getEventManager()->trigger(new OW_Event(IISEventManager::ADD_LIST_TYPE_TO_VIDEO,array('validLists' => $validLists)));
        if(isset($validListsEvent->getData()['validLists'])){
            $validLists = $validListsEvent->getData()['validLists'];
        }
        if ( !in_array($listType, $validLists) )
        {
            $this->redirect(OW::getRouter()->urlForRoute('view_list', array('listType' => 'latest')));
        }

        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('video');

        if ( !OW::getUser()->isAuthorized('video', 'view') && !$modPermissions )
        {
            $error = BOL_AuthorizationService::getInstance()->getActionStatus('video', 'view');
            throw new AuthorizationException($error['msg']);
        }


        $menu = $this->getMenu();
        $el = $menu->getElement($listType);

        $el->setActive(true);
       // $this->assign('menu', $menu);
        $this->addComponent('menu', $menu);




       // $this->addComponent('videoMenu', $this->menu);

        //$el = $this->menu->getElement($listType);
/*        if ( $el )
        {
            $el->setActive(true);
        }*/

        $this->assign('listType', $listType);

        $showAddButton = false;

        $this->assign('showAddButton', $showAddButton);

        OW::getDocument()->setHeading(OW::getLanguage()->text('video', 'page_title_browse_video'));
        OW::getDocument()->setHeadingIconClass('ow_ic_video');
        //OW::getDocument()->setTitle(OW::getLanguage()->text('video', 'meta_title_video_'.$listType));
        //OW::getDocument()->setDescription(OW::getLanguage()->text('video', 'meta_description_video_'.$listType));
        OW::getEventManager()->trigger(new OW_Event(IISEventManager::SET_TILE_HEADER_LIST_ITEM_VIDEO, array('listType' => $listType)));
        $params = array(
            "sectionKey" => "video",
            "entityKey" => "viewList",
            "title" => "video+meta_title_view_list",
            "description" => "video+meta_desc_view_list",
            "keywords" => "video+meta_keywords_view_list",
            "vars" => array("video_list" => OW::getLanguage()->text("video", "{$listType}_list_label"))
        );

        OW::getEventManager()->trigger(new OW_Event("base.provide_page_meta_info", $params));
    }

    /**
     * User video list view action
     *
     * @param array $params
     * @throws AuthorizationException
     * @throws Redirect404Exception
     */
    public function viewUserVideoList( array $params )
    {
        if ( !isset($params['user']) || !strlen($userName = trim($params['user'])) )
        {
            throw new Redirect404Exception();
        }

        $user = BOL_UserService::getInstance()->findByUsername($userName);
        if ( !$user )
        {
            throw new Redirect404Exception();
        }

        $ownerMode = $user->id == OW::getUser()->getId();

        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('video');

        if ( !OW::getUser()->isAuthorized('video', 'view') && !$modPermissions && !$ownerMode )
        {
            $error = BOL_AuthorizationService::getInstance()->getActionStatus('video', 'view');
            throw new AuthorizationException($error['msg']);
        }

        // permissions check
        if ( !$ownerMode && !$modPermissions )
        {
            $privacyParams = array('action' => 'video_view_video', 'ownerId' => $user->id, 'viewerId' => OW::getUser()->getId());
            $event = new OW_Event('privacy_check_permission', $privacyParams);
            OW::getEventManager()->trigger($event);
        }

        $this->assign('permissionError', null);
        $this->assign('userId', $user->id);

        $clipCount = VIDEO_BOL_ClipService::getInstance()->findUserClipsCount($user->id);
        $this->assign('total', $clipCount);

        $displayName = BOL_UserService::getInstance()->getDisplayName($user->id);
        $this->assign('userName', $displayName);

        $lang = OW::getLanguage();
        $heading = $lang->text('video', 'page_title_video_by', array('user' => $displayName));

        OW::getDocument()->setHeading($heading);
        OW::getDocument()->setHeadingIconClass('ow_ic_video');
//        OW::getDocument()->setTitle($lang->text('video', 'meta_title_user_video', array('displayName' => $displayName)));
//        OW::getDocument()->setDescription($lang->text('video', 'meta_description_user_video', array('displayName' => $displayName)));

        $vars = BOL_SeoService::getInstance()->getUserMetaInfo($user);

        $params = array(
            "sectionKey" => "video",
            "entityKey" => "userVideoList",
            "title" => "video+meta_title_user_video_list",
            "description" => "video+meta_desc_user_video_list",
            "keywords" => "video+meta_keywords_user_video_list",
            "vars" => $vars,
            "image" => BOL_AvatarService::getInstance()->getAvatarUrl($user->getId(), 2)
        );

        OW::getEventManager()->trigger(new OW_Event("base.provide_page_meta_info", $params));
    }


    /**
     * Method acts as ajax responder. Calls methods using ajax
     *
     * @throws Redirect404Exception
     * @return string
     */
    public function ajaxResponder()
    {
        if ( isset($_POST['ajaxFunc']) && OW::getRequest()->isAjax() )
        {
            $callFunc = (string) $_POST['ajaxFunc'];

            $result = call_user_func(array($this, $callFunc), $_POST);
        }
        else
        {
            throw new Redirect404Exception();
        }

        exit(json_encode($result));
    }

    /**
     * Set video clip approval status (approved | blocked)
     *
     * @param array $params
     * @throws Redirect404Exception
     * @return array
     */
    public function ajaxSetApprovalStatus( $params )
    {
        $clipId = $params['clipId'];
        $status = $params['status'];

        $isModerator = OW::getUser()->isAuthorized('video');

        if ( !$isModerator )
        {
            throw new Redirect404Exception();
        }

        $setStatus = $this->clipService->updateClipStatus($clipId, $status);

        if ( $setStatus )
        {
            $return = array('result' => true, 'msg' => OW::getLanguage()->text('video', 'status_changed'));
        }
        else
        {
            $return = array('result' => false, 'error' => OW::getLanguage()->text('video', 'status_not_changed'));
        }

        return $return;
    }

    /**
     * Deletes video clip
     *
     * @param array $params
     * @throws Redirect404Exception
     * @return array
     */
    public function ajaxDeleteClip( $params )
    {
        $clipId = $params['clipId'];

        $ownerId = $this->clipService->findClipOwner($clipId);
        $isOwner = OW::getUser()->getId() == $ownerId;
        $isModerator = OW::getUser()->isAuthorized('video');

        if ( !$isOwner && !$isModerator )
        {
            throw new Redirect404Exception();
        }

        $delResult = $this->clipService->deleteClip($clipId);

        if ( $delResult )
        {
            OW::getFeedback()->info(OW::getLanguage()->text('video', 'clip_deleted'));

            $return = array(
                'result' => true,
                'url' => OW_Router::getInstance()->urlForRoute('video_view_list')
            );
        }
        else
        {
            $return = array(
                'result' => false,
                'error' => OW::getLanguage()->text('video', 'clip_not_deleted')
            );
        }

        return $return;
    }

    /**
     * Set 'is featured' status to video clip
     *
     * @param array $params
     * @throws Redirect404Exception
     * @return array
     */
    public function ajaxSetFeaturedStatus( $params )
    {
        $clipId = $params['clipId'];
        $status = $params['status'];

        $isModerator = OW::getUser()->isAuthorized('video');

        if ( !$isModerator )
        {
            throw new Redirect404Exception();
        }

        $setResult = $this->clipService->updateClipFeaturedStatus($clipId, $status);

        if ( $setResult )
        {
            $return = array('result' => true, 'msg' => OW::getLanguage()->text('video', 'status_changed'));
        }
        else
        {
            $return = array('result' => false, 'error' => OW::getLanguage()->text('video', 'status_not_changed'));
        }

        return $return;
    }
    
    public function approve( $params )
    {
        $entityId = $params["clipId"];
        $entityType = VIDEO_CLASS_ContentProvider::ENTITY_TYPE;
        
        $backUrl = OW::getRouter()->urlForRoute("view_clip", array(
            "id" => $entityId
        ));
        
        $event = new OW_Event("moderation.approve", array(
            "entityType" => $entityType,
            "entityId" => $entityId
        ));
        
        OW::getEventManager()->trigger($event);
        
        $data = $event->getData();
        if ( empty($data) )
        {
            $this->redirect($backUrl);
        }
        
        if ( $data["message"] )
        {
            OW::getFeedback()->info($data["message"]);
        }
        else
        {
            OW::getFeedback()->error($data["error"]);
        }
        
        $this->redirect($backUrl);
    }
}
