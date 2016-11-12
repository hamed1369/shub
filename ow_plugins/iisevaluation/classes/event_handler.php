<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 * 
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisevaluation.bol
 * @since 1.0
 */
class IISEVALUATION_CLASS_EventHandler
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
        $this->service = IISEVALUATION_BOL_Service::getInstance();
    }
    
    public function init()
    {
        $eventManager = OW::getEventManager();
        $eventManager->bind('base.add_main_console_item', array($this, 'onAddMainConsoleItem'));
    }

    public function onAddMainConsoleItem(OW_Event $event){
        $service = IISEVALUATION_BOL_Service::getInstance();
        if($service->checkUserPermission()) {
            $event->add(array('label' => OW::getLanguage()->text('iisevaluation', 'main_menu_item'), 'url' => OW::getRouter()->urlForRoute('iisevaluation.index')));
        }
    }
}