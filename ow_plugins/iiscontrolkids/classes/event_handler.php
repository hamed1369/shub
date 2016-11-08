<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 * 
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iiscontrolkids.bol
 * @since 1.0
 */
class IISCONTROLKIDS_CLASS_EventHandler
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

    public function init()
    {
        $service = IISCONTROLKIDS_BOL_Service::getInstance();
        $eventManager = OW::getEventManager();
        $eventManager->bind(IISEventManager::ON_BEFORE_JOIN_FORM_RENDER, array($service, 'onBeforeJoinFormRender'));
        $eventManager->bind(OW_EventManager::ON_USER_REGISTER, array($service, 'onUserRegistered'));
        $eventManager->bind(OW_EventManager::ON_BEFORE_USER_REGISTER, array($service, 'onBeforeUserRegistered'));
        $eventManager->bind('base.add_main_console_item', array($service, 'onAddMainConsoleItem'));
    }
}