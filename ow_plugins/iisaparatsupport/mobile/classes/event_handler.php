<?php

/**
 * Copyright (c) 2016,
 * All rights reserved.
 */

/**
 *
 *
 * @author
 * @package
 * @since 1.0
 */
class IISAPARATSUPPORT_MCLASS_EventHandler
{
    /**
     * @var IISAPARATSUPPORT_MCLASS_EventHandler
     */
    private static $classInstance;

    /**
     * @return IISAPARATSUPPORT_MCLASS_EventHandler
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
        $service = IISAPARATSUPPORT_BOL_Service::getInstance();
        $eventManager = OW::getEventManager();
        $eventManager->bind(IISEventManager::ON_AFTER_VIDEO_PROVIDERS_DEFINED, array($service, 'onAfterVideoProvidersDefined'));
        $eventManager->bind(IISEventManager::ON_BEFORE_VIDEO_ADD, array($service, 'onBeforeVideoAdded'));
        $eventManager->bind(IISEventManager::ON_VIDEO_URL_VALIDATION, array($service, 'onVideoUrlValidation'));

    }

}