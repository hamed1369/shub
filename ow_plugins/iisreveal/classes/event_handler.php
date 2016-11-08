<?php

/**
 * Copyright (c) 2016, Mohammad Aghaabbasloo
 * All rights reserved.
 */

/**
 *
 *
 * @author Mohammad Aghaabbasloo <a.mohammad85@gmail.com>
 * @package ow_plugins.iisreveal.classes
 * @since 1.0
 */
class IISREVEAL_CLASS_EventHandler
{
    private static $classInstance;

    public static function getInstance()
    {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }


    private function __construct()
    {
    }

    public function init()
    {
        $eventManager = OW::getEventManager();
        $eventManager->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($this, 'onAfterRoute'));
    }

    public function onAfterRoute(OW_Event $event)
    {

        if(!OW::getConfig()->configExists('iisreveal', 'already_loaded')){
            OW::getConfig()->addConfig('iisreveal', 'already_loaded', false);
        }

        if(!OW::getConfig()->getValue('iisreveal', 'already_loaded')) {
            OW::getConfig()->saveConfig('iisreveal', 'already_loaded', true);
            $jsDir = OW::getPluginManager()->getPlugin('iisreveal')->getStaticJsUrl();
            OW::getDocument()->addScript($jsDir . 'iisreveal.js');
            OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('iisreveal')->getStaticCssUrl() . 'iisreveal.css', "all", 100000);

            $css = '
            .curtain__panel.curtain__panel--left{
                background-image: url("' . OW::getPluginManager()->getPlugin('iisreveal')->getStaticUrl(). 'img/first_left.jpg' . '");
            }
            .curtain__panel.curtain__panel--right{
                background-image: url("' . OW::getPluginManager()->getPlugin('iisreveal')->getStaticUrl(). 'img/first_right.jpg' . '");
            }
            .curtain__panel2.curtain__panel--left2{
                background-image: url("' . OW::getPluginManager()->getPlugin('iisreveal')->getStaticUrl(). 'img/second_left.jpg' . '");
            }
            .curtain__panel2.curtain__panel--right2{
                background-image: url("' . OW::getPluginManager()->getPlugin('iisreveal')->getStaticUrl(). 'img/second_right.jpg' . '");
            }
            ';

            Ow::getDocument()->addStyleDeclaration($css);
        }
    }

}