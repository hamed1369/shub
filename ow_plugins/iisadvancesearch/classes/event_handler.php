<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisadvancesearch.classes
 * @since 1.0
 */
class IISADVANCESEARCH_CLASS_EventHandler
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
        $eventManager->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($this, 'onBeforeDocumentRender'));
        $eventManager->bind('console.collect_items', array($this, 'collectItems'));
    }

    public function collectItems(OW_Event $event)
    {
        if(!OW::getUser()->isAuthenticated()){
            return;
        }

        $item = new IISADVANCESEARCH_CMP_ConsoleSearch();
        $event->addItem($item, 6);
    }

    public function onBeforeDocumentRender(OW_Event $event)
    {
        if(!OW::getUser()->isAuthenticated()){
            return;
        }

        $jsFile = OW::getPluginManager()->getPlugin('iisadvancesearch')->getStaticJsUrl() . 'iisadvancesearch.js';
        OW::getDocument()->addScript($jsFile);

        $cssFile = OW::getPluginManager()->getPlugin('iisadvancesearch')->getStaticCssUrl() . 'iisadvancesearch.css';
        OW::getDocument()->addStyleSheet($cssFile);

        $lang = OW::getLanguage();
        $lang->addKeyForJs('iisadvancesearch', 'search_title');
        $lang->addKeyForJs('iisadvancesearch', 'no_data_found');
        $lang->addKeyForJs('iisadvancesearch', 'users');
        $lang->addKeyForJs('iisadvancesearch', 'minimum_two_character');
        $lang->addKeyForJs('iisadvancesearch', 'forum_posts_title');
        $lang->addKeyForJs('iisadvancesearch', 'forum_post_title');
        $lang->addKeyForJs('iisadvancesearch', 'forum_post_group_name');
        $lang->addKeyForJs('iisadvancesearch', 'forum_post_section_name');


    }

}