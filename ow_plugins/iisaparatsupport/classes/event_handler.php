<?php

/**
 * User: Hamed Tahmooresi
 * Date: 4/27/2016
 * Time: 4:26 PM
 */
class IISAPARATSUPPORT_CLASS_EventHandler
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
        $service = IISAPARATSUPPORT_BOL_Service::getInstance();
        $eventManager = OW::getEventManager();
        $eventManager->bind(IISEventManager::ON_AFTER_VIDEO_PROVIDERS_DEFINED, array($service, 'onAfterVideoProvidersDefined'));
        $eventManager->bind(IISEventManager::ON_BEFORE_VIDEO_ADD, array($service, 'onBeforeVideoAdded'));
        $eventManager->bind(IISEventManager::ON_VIDEO_URL_VALIDATION, array($service, 'onVideoUrlValidation'));

    }
}