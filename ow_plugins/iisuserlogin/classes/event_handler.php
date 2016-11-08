<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 * 
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisuserlogin.bol
 * @since 1.0
 */
class IISUSERLOGIN_CLASS_EventHandler
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
    
    private $service;
    
    private function __construct()
    {
        $this->service = IISUSERLOGIN_BOL_Service::getInstance();
    }
    
    public function init()
    {
        $service = IISUSERLOGIN_BOL_Service::getInstance();
        $eventManager = OW::getEventManager();
        $eventManager->bind(BOL_PreferenceService::PREFERENCE_ADD_FORM_ELEMENT_EVENT, array($service, 'onPreferenceAddFormElement'));
        $eventManager->bind(OW_EventManager::ON_USER_LOGIN, array($service, 'onUserLogin'));
    }
}