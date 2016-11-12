<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iismobilesupport.bol
 * @since 1.0
 */
class IISMOBILESUPPORT_MCLASS_EventHandler
{
    /**
     * @var IISMOBILESUPPORT_MCLASS_EventHandler
     */
    private static $classInstance;

    /**
     * @return IISMOBILESUPPORT_MCLASS_EventHandler
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct() { }

    public function init()
    {
        $service = IISMOBILESUPPORT_BOL_Service::getInstance();
        $eventManager = OW::getEventManager();
        $eventManager->bind(OW_EventManager::ON_AFTER_ROUTE, array($service, 'saveDeviceToken'));
        $eventManager->bind(OW_EventManager::ON_USER_LOGOUT, array($service, 'userLogout'));
        $eventManager->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($service, 'addMobileCss'));
        $eventManager->bind('notifications.add', array($service, 'addNotification'));
        $eventManager->bind('iismobilesupport.browser.information', array($service, 'getBrowserInformation'));
    }
}