<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 * 
 *
 * @author Mohammad Agha Abbasloo <a.mohammad85@gmail.com>
 * @package ow_plugins.iisphotoplus.bol
 * @since 1.0
 */
class IISPHOTOPLUS_CLASS_EventHandler
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
        $service = IISPHOTOPLUS_BOL_Service::getInstance();
        $eventManager = OW::getEventManager();
        $eventManager->bind(IISEventManager::ADD_LIST_TYPE_TO_PHOTO, array($service, 'addListTypeToPhoto'));
        $eventManager->bind(IISEventManager::GET_RESULT_FOR_LIST_ITEM_PHOTO, array($service, 'getResultForListItemPhoto'));
        $eventManager->bind(IISEventManager::SET_TILE_HEADER_LIST_ITEM_PHOTO, array($service, 'setTtileHeaderListItemPhoto'));
        $eventManager->bind(IISEventManager::GET_VALID_LIST_FOR_PHOTO, array($service, 'getValidListForPhoto'));
    }
}