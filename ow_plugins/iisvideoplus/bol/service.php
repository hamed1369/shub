<?php

/**
 * Copyright (c) 2016, Mohammad Aghaabbasloo
 * All rights reserved.
 */

/**
 * 
 *
 * @author Mohammad Aghaabbasloo
 * @package ow_plugins.iisvideoplus
 * @since 1.0
 */
class IISVIDEOPLUS_BOL_Service
{
    private static $LATEST_FRIENDS = 'latest_friends';
    private static $LATEST_MYVIDEO = 'latest_myvideo';
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

    public function setTtileHeaderListItemVideo( OW_Event $event )
    {
        $params = $event->getParams();
        if (isset($params['listType']) && $params['listType'] == IISVIDEOPLUS_BOL_Service::$LATEST_FRIENDS) {
            OW::getDocument()->setTitle(OW::getLanguage()->text('iisvideoplus', 'meta_title_video_add_latest_friends'));
            OW::getDocument()->setDescription(OW::getLanguage()->text('iisvideoplus', 'meta_description_video_latest_friends'));
        }
        if (isset($params['listType']) && $params['listType'] == IISVIDEOPLUS_BOL_Service::$LATEST_MYVIDEO) {
            OW::getDocument()->setTitle(OW::getLanguage()->text('iisvideoplus', 'meta_title_video_add_latest_myvideo'));
            OW::getDocument()->setDescription(OW::getLanguage()->text('iisvideoplus', 'meta_description_video_latest_myvideo'));
        }
    }

    public function addListTypeToVideo( OW_Event $event )
    {
        $params = $event->getParams();
        if(isset($params['validLists'])){
            $validLists = $params['validLists'];
            if(OW::getUser()->isAuthenticated()) {
                $validLists[] = IISVIDEOPLUS_BOL_Service::$LATEST_FRIENDS;
                $validLists[] = IISVIDEOPLUS_BOL_Service::$LATEST_MYVIDEO;
            }
            $event->setData(array('validLists' => $validLists));
        }
        if(isset($params['menuItems']) && OW::getUser()->isAuthenticated()){
            $menuItems = $params['menuItems'];

            //its for my friends videos
            $item = new BASE_MenuItem();
            $item->setLabel(OW::getLanguage()->text('iisvideoplus', IISVIDEOPLUS_BOL_Service::$LATEST_FRIENDS));
            $item->setUrl(OW::getRouter()->urlForRoute('view_list', array('listType' => IISVIDEOPLUS_BOL_Service::$LATEST_FRIENDS)));
            $item->setKey(IISVIDEOPLUS_BOL_Service::$LATEST_FRIENDS);
            $item->setIconClass('ow_ic_clock');
            $item->setOrder(sizeof($params['menuItems']));
            array_push($menuItems, $item);

            //its for my videos
            $item = new BASE_MenuItem();
            $item->setLabel(OW::getLanguage()->text('iisvideoplus', IISVIDEOPLUS_BOL_Service::$LATEST_MYVIDEO));
            $item->setUrl(OW::getRouter()->urlForRoute('view_list', array('listType' => IISVIDEOPLUS_BOL_Service::$LATEST_MYVIDEO)));
            $item->setKey(IISVIDEOPLUS_BOL_Service::$LATEST_MYVIDEO);
            $item->setIconClass('ow_ic_video');
            $item->setOrder(sizeof($params['menuItems'])+1);
            array_push($menuItems, $item);
            $event->setData(array('menuItems' => $menuItems));
        }
    }

    public function getResultForListItemVideo( OW_Event $event )
    {
        $params = $event->getParams();
        if(isset($params['this']) &&
            isset($params['listtype']) &&
            isset($params['cacheLifeTime']) &&
            isset($params['cacheTags']) &&
            isset($params['first']) &&
            isset($params['limit']) &&
            $params['listtype'] == IISVIDEOPLUS_BOL_Service::$LATEST_FRIENDS){

            $friendsOfCurrentUser = array();
            if(OW::getUser()->isAuthenticated()){
                $friendsOfCurrentUser = OW::getEventManager()->call('plugin.friends.get_friend_list', array('userId' => OW::getUser()->getId()));
            }
            if(!empty($friendsOfCurrentUser)) {

                $example = new OW_Example();

                $example->andFieldEqual('status', 'approved');
                $example->andFieldInArray('userId', $friendsOfCurrentUser);
                $example->andFieldNotEqual('privacy', 'only_for_me');
                $example->setOrder('`addDatetime` DESC');
                $example->setLimitClause($params['first'], $params['limit']);

                $result = $params['this']->findListByExample($example, $params['cacheLifeTime'], $params['cacheTags']);
                $event->setData(array('result' => $result));
            }
        }
        /*
         * add my list video result
         */
        if(isset($params['this']) &&
            isset($params['listtype']) &&
            isset($params['cacheLifeTime']) &&
            isset($params['cacheTags']) &&
            isset($params['first']) &&
            isset($params['limit']) &&

            $params['listtype'] == IISVIDEOPLUS_BOL_Service::$LATEST_MYVIDEO){

            if(OW::getUser()->isAuthenticated()) {
                $example = new OW_Example();
                $example->andFieldEqual('status', 'approved');
                $example->andFieldEqual('userId', OW::getUser()->getId());
                $example->setOrder('`addDatetime` DESC');
                $example->setLimitClause($params['first'], $params['limit']);
                $result = $params['this']->findListByExample($example, $params['cacheLifeTime'], $params['cacheTags']);
                $event->setData(array('result' => $result));
            }
        }
    }
    /*
 * show video thumb image after video rendered in main page
 * @param OW_Event $event
 */
    public static function onAfterVideoRendered(OW_Event $event)
    {
        $js = UTIL_JsGenerator::newInstance();
        $params = $event->getParams();
        if(isset($params['uniqId'])) {
            $js->addScript('$(".ow_oembed_video_cover", "#" + {$uniqId}).trigger( "click" );', array(
                "uniqId" => $params['uniqId']
            ));
        }
        OW::getDocument()->addOnloadScript($js);
    }
}
