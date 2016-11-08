<?php

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisterms.classes
 * @since 1.0
 */
class IISTERMS_CLASS_EventHandler
{
    /**
     * Singleton instance.
     *
     * @var IISTERMS_CLASS_EventHandler
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return IISTERMS_CLASS_EventHandler
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     *
     * @var IISTERMS_BOL_Service
     */
    private $service;

    private function __construct()
    {
        $this->service = IISTERMS_BOL_Service::getInstance();
    }
    
    public function genericInit()
    {
        $service = IISTERMS_BOL_Service::getInstance();
        OW::getEventManager()->bind('notifications.collect_actions', array($service, 'on_notify_actions'));
        OW::getEventManager()->bind(IISEventManager::ON_RENDER_JOIN_FORM, array($service, 'on_render_join_form'));
    }

}