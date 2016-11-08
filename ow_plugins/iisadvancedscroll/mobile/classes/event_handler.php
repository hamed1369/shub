<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisadvancedscroll.bol
 * @since 1.0
 */
class IISADVANCEDSCROLL_MCLASS_EventHandler
{
    /**
     * @var IISADVANCEDSCROLL_MCLASS_EventHandler
     */
    private static $classInstance;

    /**
     * @return IISADVANCEDSCROLL_MCLASS_EventHandler
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
        $service = IISADVANCEDSCROLL_BOL_Service::getInstance();
        $eventManager = OW::getEventManager();
        $eventManager->bind(OW_EventManager::ON_FINALIZE, array($service, 'onFinalize'));
        $eventManager->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($service, 'onBeforeDocumentRenderer'));
    }

}