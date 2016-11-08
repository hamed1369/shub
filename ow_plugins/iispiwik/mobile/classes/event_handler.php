<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iispiwik.bol
 * @since 1.0
 */
class IISPIWIK_MCLASS_EventHandler
{
    /**
     * @var IISPIWIK_MCLASS_EventHandler
     */
    private static $classInstance;

    /**
     * @return IISPIWIK_MCLASS_EventHandler
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
        $eventManager = OW::getEventManager();
        $eventManager->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($this, 'onBeforeDocumentRender'));
    }

    public function onBeforeDocumentRender(OW_Event $event){
        $jsFile = OW::getPluginManager()->getPlugin('iispiwik')->getStaticJsUrl() . 'iispiwik.js';
        OW::getDocument()->addScript($jsFile, "text/javascript", (-100));
    }

}