<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 * 
 *
 * @author Mohammad Aghaabbasloo
 * @package ow_plugins.iisphotoplus
 * @since 1.0
 */
class IISPHOTOPLUS_BOL_Service
{
    private static $PHOTO_FRIENDS = 'photo_friends';
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

    public function setTtileHeaderListItemPHOTO( OW_Event $event )
    {
        $params = $event->getParams();
        if (isset($params['listType']) && $params['listType'] == IISPHOTOPLUS_BOL_Service::$PHOTO_FRIENDS) {
            OW::getDocument()->setTitle(OW::getLanguage()->text('iisphotoplus', 'meta_title_photo_add_friends'));
            OW::getDocument()->setDescription(OW::getLanguage()->text('iisphotoplus', 'meta_description_photo_friends'));
        }
    }
    public function getValidListForPhoto( OW_Event $event )
    {
        $params = $event->getParams();
        if(isset($params['validLists'])){
            $validLists = $params['validLists'];
            if(OW::getUser()->isAuthenticated()) {
                $validLists[] = IISPHOTOPLUS_BOL_Service::$PHOTO_FRIENDS;
            }
            $event->setData(array('validLists' => $validLists));
        }
    }

    public function addListTypeToPhoto( OW_Event $event )
    {
        $params = $event->getParams();
        if(isset($params['validLists'])){
            $validLists = $params['validLists'];
            if(OW::getUser()->isAuthenticated()) {
                $validLists[] = IISPHOTOPLUS_BOL_Service::$PHOTO_FRIENDS;
            }
            $event->setData(array('validLists' => $validLists));
        }
        if(isset($params['menuItems']) && OW::getUser()->isAuthenticated() && isset($params['isCmp']) && $params['isCmp']==true
        && isset($params['uniqId'])){
            $menuItems = $params['menuItems'];
            $menuItems['photo_friends'] = array(
                    'label' => OW::getLanguage()->text('iisphotoplus', IISPHOTOPLUS_BOL_Service::$PHOTO_FRIENDS),
                    'id' => 'photo-cmp-menu-photo_friends-'.$params['uniqId'],
                    'contId' => 'photo-cmp-photo_friends-'.$params['uniqId'],
                    'active' => false,
                    'visibility' => true
                );
            $event->setData(array('menuItems' => $menuItems));
        }
        else if(isset($params['menuItems']) && OW::getUser()->isAuthenticated()){
            $menuItems = $params['menuItems'];

            $item = new BASE_MenuItem();
            $item->setLabel(OW::getLanguage()->text('iisphotoplus', IISPHOTOPLUS_BOL_Service::$PHOTO_FRIENDS));
            $item->setUrl(OW::getRouter()->urlForRoute('view_photo_list', array('listType' => IISPHOTOPLUS_BOL_Service::$PHOTO_FRIENDS)));
            $item->setKey(IISPHOTOPLUS_BOL_Service::$PHOTO_FRIENDS);
            $item->setIconClass('ow_ic_clock');
            $item->setOrder(sizeof($params['menuItems']));
            array_push($menuItems, $item);
            $event->setData(array('menuItems' => $menuItems));
        }
    }

    public function getResultForListItemPhoto( OW_Event $event )
    {
        $params = $event->getParams();
        if(isset($params['listtype']) &&
            isset($params['page']) &&
            isset($params['photosPerPage']) &&
            $params['listtype'] == IISPHOTOPLUS_BOL_Service::$PHOTO_FRIENDS){

            $friendsOfCurrentUser = array();
            $result = array();
            if(OW::getUser()->isAuthenticated()){
                $friendsOfCurrentUser = OW::getEventManager()->call('plugin.friends.get_friend_list', array('userId' => OW::getUser()->getId()));
            }
            if(!empty($friendsOfCurrentUser)) {
                $photos = PHOTO_BOL_PhotoService::getInstance()->findPhotoListByUserIdList($friendsOfCurrentUser, $params['page'], $params['photosPerPage']);
                foreach($photos as $photo)
                {
                    if(strcmp($photo['privacy'],'only_for_me')!=0)
                    {
                        $result[]=$photo;
                    }
                }
                $event->setData(array('result' => $result));
            }
        }
    }
}
