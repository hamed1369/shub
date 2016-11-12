<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 * 
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisvideoplus.bol
 * @since 1.0
 */
class IISVIDEOPLUS_CLASS_EventHandler
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
        $service = IISVIDEOPLUS_BOL_Service::getInstance();
        $eventManager = OW::getEventManager();
        $eventManager->bind(IISEventManager::ADD_LIST_TYPE_TO_VIDEO, array($service, 'addListTypeToVideo'));
        $eventManager->bind(IISEventManager::GET_RESULT_FOR_LIST_ITEM_VIDEO, array($service, 'getResultForListItemVideo'));
        $eventManager->bind(IISEventManager::SET_TILE_HEADER_LIST_ITEM_VIDEO, array($service, 'setTtileHeaderListItemVideo'));
    }

}