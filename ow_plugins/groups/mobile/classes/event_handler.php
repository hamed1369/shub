<?php

/**
 * EXHIBIT A. Common Public Attribution License Version 1.0
 * The contents of this file are subject to the Common Public Attribution License Version 1.0 (the “License”);
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.oxwall.org/license. The License is based on the Mozilla Public License Version 1.1
 * but Sections 14 and 15 have been added to cover use of software over a computer network and provide for
 * limited attribution for the Original Developer. In addition, Exhibit A has been modified to be consistent
 * with Exhibit B. Software distributed under the License is distributed on an “AS IS” basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for the specific language
 * governing rights and limitations under the License. The Original Code is Oxwall software.
 * The Initial Developer of the Original Code is Oxwall Foundation (http://www.oxwall.org/foundation).
 * All portions of the code written by Oxwall Foundation are Copyright (c) 2011. All Rights Reserved.

 * EXHIBIT B. Attribution Information
 * Attribution Copyright Notice: Copyright 2011 Oxwall Foundation. All rights reserved.
 * Attribution Phrase (not exceeding 10 words): Powered by Oxwall community software
 * Attribution URL: http://www.oxwall.org/
 * Graphic Image as provided in the Covered Code.
 * Display of Attribution Information is required in Larger Works which are defined in the CPAL as a work
 * which combines Covered Code or portions thereof with code not governed by the terms of the CPAL.
 */

/**
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.ow_plugins.event.mobile.classes
 * @since 1.6.0
 */
class GROUPS_MCLASS_EventHandler
{
    /**
     * Class instance
     *
     * @var GROUPS_MCLASS_EventHandler
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return GROUPS_MCLASS_EventHandler
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function genericInit()
    {
        $eventHandler = $this;
        OW::getEventManager()->bind('groups.on_toolbar_collect', array($eventHandler, "onGroupToolbarCollect"));
        OW::getEventManager()->bind('base.mobile_top_menu_add_options', array($this, 'onMobileTopMenuAddLink'));
    }
    public function onInvitationCommand( OW_Event $event )
    {
        $params = $event->getParams();

        $result = array('result' => false);
        if ( !in_array($params['command'], array('groups.accept', 'groups.ignore')) )
        {
            return;
        }

        $groupId = $params['data'];
        $userId = OW::getUser()->getId();

        if ( $params['command'] == 'groups.accept' )
        {
            GROUPS_BOL_Service::getInstance()->addUser($groupId, $userId);
            $result = array('result' => true, 'msg' => OW::getLanguage()->text('groups', 'join_complete_message'));
        }
        else if ( $params['command'] == 'groups.ignore' )
        {
            GROUPS_BOL_Service::getInstance()->deleteInvite($groupId, $userId);
        }

        $event->setData($result);
    }

    public function onInvitationsItemRender( OW_Event $event )
    {
        $params = $event->getParams();

        if ( $params['entityType'] == 'group-join' )
        {
            $data = $params['data'];
            $data['string']['vars']['group'] = strip_tags($data['string']['vars']['group']);
            $data['acceptCommand'] = 'groups.accept';
            $data['declineCommand'] = 'groups.ignore';
            $event->setData($data);
        }
    }
    
    public function onFeedItemRenderDisableActions( OW_Event $event )
    {
        $params = $event->getParams();
        
        if ( !in_array($params["action"]["entityType"], array( GROUPS_BOL_Service::FEED_ENTITY_TYPE, "groups-join", "groups-status" )) )
        {
            return;
        }
        
        $data = $event->getData();
        
        $data["disabled"] = false;
        
        $event->setData($data);
    }

    public function onFeedWidgetConstruct( OW_Event $e )
    {
        $params = $e->getParams();

        if ( $params['feedType'] != 'groups' )
        {
            return;
        }

        $data = $e->getData();

        if ( !OW::getUser()->isAuthorized('groups', 'add_comment') )
        {
            $data['statusForm'] = false;
            $actionStatus = BOL_AuthorizationService::getInstance()->getActionStatus('groups', 'add_comment');

            if ( $actionStatus["status"] == BOL_AuthorizationService::STATUS_PROMOTED )
            {
                $data["statusMessage"] = $actionStatus["msg"];
            }

            $e->setData($data);

            return;
        }

        $groupId = (int) $params['feedId'];
        $userId = OW::getUser()->getId();

        $group = GROUPS_BOL_Service::getInstance()->findGroupById($groupId);
        $userDto = GROUPS_BOL_Service::getInstance()->findUser($groupId, $userId);

        $data['statusForm'] = $userDto !== null && $group->status == GROUPS_BOL_Group::STATUS_ACTIVE;

        $e->setData($data);
    }

    public function onFeedItemRender( OW_Event $event )
    {
        $params = $event->getParams();
        $data = $event->getData();

        $actionUserId = $userId = (int) $data['action']['userId'];
        if ( OW::getUser()->isAuthenticated() && in_array($params['feedType'], array('groups')) )
        {
            $groupDto = GROUPS_BOL_Service::getInstance()->findGroupById($params['feedId']);
            $isGroupOwner = $groupDto->userId == OW::getUser()->getId();
            $isGroupModerator = OW::getUser()->isAuthorized('groups');

            if ( $actionUserId != OW::getUser()->getId() && ($isGroupOwner || $isGroupModerator) )
            {
                $groupUserDto = GROUPS_BOL_Service::getInstance()->findUser($groupDto->id, $actionUserId);
                if ( $groupUserDto !== null )
                {
                    $data['contextMenu'] = empty($data['contextMenu']) ? array() : $data['contextMenu'];


                    if ( $groupDto->userId == $userId )
                    {
                        array_unshift($data['contextMenu'], array(
                            'label' => OW::getLanguage()->text('groups', 'delete_group_user_label'),
                            'url' => 'javascript://',
                            'attributes' => array(
                                'data-message' => OW::getLanguage()->text('groups', 'group_owner_delete_error'),
                                'onclick' => 'OW.error($(this).data().message); return false;'
                            )
                        ));
                    }
                    else
                    {
                        $callbackUri = OW::getRequest()->getRequestUri();
                        $deleteUrl = OW::getRequest()->buildUrlQueryString(OW::getRouter()->urlFor('GROUPS_CTRL_Groups', 'deleteUser', array(
                            'groupId' => $groupDto->id,
                            'userId' => $userId
                        )), array(
                            'redirectUri' => urlencode($callbackUri)
                        ));

                        array_unshift($data['contextMenu'], array(
                            'label' => OW::getLanguage()->text('groups', 'delete_group_user_label'),
                            'url' => $deleteUrl,
                            'attributes' => array(
                                'data-message' => OW::getLanguage()->text('groups', 'delete_group_user_confirmation'),
                                'onclick' => 'return confirm($(this).data().message);'
                            )
                        ));
                    }
                }
            }

            $canRemove = $isGroupOwner || $params['action']['userId'] == OW::getUser()->getId() || $isGroupModerator;

            if ( $canRemove )
            {
                $callbackUrl = OW_URL_HOME . OW::getRequest()->getRequestUri();
                array_unshift($data['contextMenu'], array(
                    'label' => OW::getLanguage()->text('newsfeed', 'delete_feed_item_user_label'),
                    'attributes' => array(
                        'onclick' => UTIL_JsGenerator::composeJsString('if ( confirm($(this).data(\'confirm-msg\')) ) OW.Users.deleteUser({$userId}, \'' . $callbackUrl . '\', true);', array(
                            'userId' => $actionUserId
                        )),
                        "data-confirm-msg" => OW::getLanguage()->text('base', 'are_you_sure')
                    ),
                    "class" => "owm_red_btn"
                ));
            }
        }

        $event->setData($data);
    }

    public function onFeedItemRenderContext( OW_Event $event )
    {
        $params = $event->getParams();
        $data = $event->getData();

        $groupActions = array(
            'groups-status'
        );

        if ( in_array($params['action']['entityType'], $groupActions) && $params['feedType'] == 'groups' )
        {
            $data['context'] = null;
        }

        if ( $params['action']['entityType'] == 'forum-topic' && isset($data['contextFeedType'])
            && $data['contextFeedType'] == 'groups' && $data['contextFeedType'] != $params['feedType'] )
        {
            $service = GROUPS_BOL_Service::getInstance();
            $group = $service->findGroupById($data['contextFeedId']);
            $url = $service->getGroupUrl($group);
            $title = UTIL_String::truncate(strip_tags($group->title), 100, '...');

            $data['context'] = array(
                'label' => $title,
                'url' => $url
            );
        }

        $event->setData($data);
    }

    /*public function onFeedItemRenderContext( OW_Event $event )
    {
        $params = $event->getParams();
        $data = $event->getData();

        if ( empty($data['contextFeedType']) )
        {
            return;
        }

        if ( $data['contextFeedType'] != "groups" )
        {
            return;
        }

        if ( $params['feedType'] == "groups" )
        {
            $data["context"] = null;
            $event->setData($data);

            return;
        }

        $service = GROUPS_BOL_Service::getInstance();
        $group = $service->findGroupById($data['contextFeedId']);
        $url = $service->getGroupUrl($group);
        $title = UTIL_String::truncate(strip_tags($group->title), 100, '...');

        $data['context'] = array(
            'label' => $title,
            'url' => $url
        );

        $event->setData($data);
    }*/

    public function onFeedItemRenderActivity( OW_Event $event )
    {
        $params = $event->getParams();
        $data = $event->getData();

        if ( $params['action']['entityType'] != GROUPS_BOL_Service::FEED_ENTITY_TYPE || $params['feedType'] == 'groups')
        {
            return;
        }

        $groupId = $params['action']['entityId'];
        $usersCount = GROUPS_BOL_Service::getInstance()->findUserListCount($groupId);

        if ( $usersCount == 1 )
        {
            return;
        }

        $users = GROUPS_BOL_Service::getInstance()->findGroupUserIdList($groupId, GROUPS_BOL_Service::PRIVACY_EVERYBODY);
        $activityUserIds = array();

        foreach ( $params['activity'] as $activity )
        {
            if ( $activity['activityType'] == 'groups-join')
            {
                $activityUserIds[] = $activity['data']['userId'];
            }
        }

        $lastUserId = reset($activityUserIds);
        $follows = array_intersect($activityUserIds, $users);
        $notFollows = array_diff($users, $activityUserIds);
        $idlist = array_merge($follows, $notFollows);

        $viewMoreUrl = null;

        if ( count($idlist) > 5 )
        {
            $viewMoreUrl = array("routeName" => "groups-user-list", "vars" => array(
                "groupId" => $groupId
            ));
        }

        if ( is_array($data["content"])  )
        {
            $data["content"]["vars"]["userList"] = array(
                "label" => array(
                    "key" => "groups+feed_activity_users",
                    "vars" => array(
                        "usersCount" => $usersCount
                    )
                ),
                "viewAllUrl" => $viewMoreUrl,
                "ids" => array_slice($idlist, 0, 5)
            );
        }
        else // Backward compatibility
        {
            $avatarList = new BASE_CMP_MiniAvatarUserList( array_slice($idlist, 0, 5) );
            $avatarList->setEmptyListNoRender(true);

            if ( count($idlist) > 5 )
            {
                $avatarList->setViewMoreUrl(OW::getRouter()->urlForRoute($viewMoreUrl["routeName"], $viewMoreUrl["vars"]));
            }

            $language = OW::getLanguage();
            $content = $avatarList->render();

            if ( $lastUserId )
            {
                $userName = BOL_UserService::getInstance()->getDisplayName($lastUserId);
                $userUrl = BOL_UserService::getInstance()->getUserUrl($lastUserId);
                $content .= $language->text('groups', 'feed_activity_joined', array('user' => '<a href="' . $userUrl . '">' . $userName . '</a>'));
            }

            $data['assign']['activity'] = array('template' => 'activity', 'vars' => array(
                'title' => $language->text('groups', 'feed_activity_users', array('usersCount' => $usersCount)),
                'content' => $content
            ));
        }

        $event->setData($data);
    }

    public function onGroupToolbarCollect( BASE_CLASS_EventCollector $e )
    {
        if ( !OW::getUser()->isAuthenticated() )
        {
            return;
        }

        $params = $e->getParams();
        $backUri = OW::getRequest()->getRequestUri();

        if ( OW::getEventManager()->call('feed.is_inited') )
        {
            $url = OW::getRouter()->urlFor('GROUPS_MCTRL_Groups', 'follow');

            $eventParams = array(
                'userId' => OW::getUser()->getId(),
                'feedType' => GROUPS_BOL_Service::ENTITY_TYPE_GROUP,
                'feedId' => $params['groupId']
            );

            if ( !OW::getEventManager()->call('feed.is_follow', $eventParams) )
            {
                $e->add(array(
                    'label' => OW::getLanguage()->text('groups', 'feed_group_follow'),
                    'href' => OW::getRequest()->buildUrlQueryString($url, array(
                        'backUri' => $backUri,
                        'groupId' => $params['groupId'],
                        'command' => 'follow'))
                ));
            }
            else
            {
                $e->add(array(
                    'label' => OW::getLanguage()->text('groups', 'feed_group_unfollow'),
                    'href' => OW::getRequest()->buildUrlQueryString($url, array(
                        'backUri' => $backUri,
                        'groupId' => $params['groupId'],
                        'command' => 'unfollow'))
                ));
            }
        }
    }

    public function onMobileTopMenuAddLink( BASE_CLASS_EventCollector $event )
    {
        if ( OW::getUser()->isAuthenticated() && (OW::getUser()->isAuthorized('groups', 'create'))) {
            $id = uniqid('group_add');
            $status = BOL_AuthorizationService::getInstance()->getActionStatus('groups', 'create');
            OW::getDocument()->addScriptDeclaration(
                UTIL_JsGenerator::composeJsString(
                    ';$("#" + {$btn}).on("click", function()
                    {
                        OWM.showContent();
                        OWM.authorizationLimitedFloatbox({$msg});
                    });',
                    array(
                        'btn' => $id,
                        'msg' => $status['msg'],
                    )
                )
            );
            $event->add(array(
                'prefix' => 'groups',
                'key' => 'mobile_main_menu_list',
                'id' => $id,
                'url' => OW::getRequest()->buildUrlQueryString(OW::getRouter()->urlForRoute('groups-create'))
            ));
        }
    }
}