<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisdemo.bol
 * @since 1.0
 */
class IISDEMO_CLASS_EventHandler
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
        $eventManager->bind('base.members_only_exceptions', array($this, 'catchAllRequestsExceptions'));
    }

    public function catchAllRequestsExceptions(BASE_CLASS_EventCollector $event)
    {
        $event->add(array(
            OW_RequestHandler::ATTRS_KEY_CTRL => 'IISDEMO_CTRL_Demo',
            OW_RequestHandler::ATTRS_KEY_ACTION => 'changeTheme'
        ));

    }

    public function onBeforeDocumentRender(OW_Event $event)
    {
        if (strpos($_SERVER['REQUEST_URI'], '/admin') === false && strpos($_SERVER['REQUEST_URI'], '/lock') === false) {
            $themes = BOL_ThemeService::getInstance()->findAllThemes();
            $currentTheme = OW::getConfig()->getValue('base', 'selectedTheme');
            $themeOptions = '';
            foreach ($themes as $theme) {
                $selected = '';
                if ($currentTheme == $theme->getName()) {
                    $selected = 'selected';
                }
                $themeOptions .= '<option value="' . $theme->getName() . '" ' . $selected . '>' . $theme->getTitle() . '</option>';
            }
            $remainingMinutes = 61 - date("i");
            $remainingSeconds = $remainingMinutes * 60;
            $countDownElement = ' <span id="countdown_demo_timer">' . $remainingMinutes . '</span> ';
            $countDownJs = 'startTimer(' . $remainingSeconds . ', document.getElementById(\'countdown_demo_timer\'));';
            $chooseThemeLink = OW::getLanguage()->text('iisdemo', 'theme') . ' <select id="demo_themes_items" onchange="changeDemoTheme(\'' . OW::getRouter()->urlForRoute('iisdemo.change-theme') . '\')">' . $themeOptions . '</select>';
            $adminPanelLink = '( <a href="' . OW::getRouter()->urlForRoute("admin_default") . '">' . OW::getLanguage()->text('iisdemo', 'admin_panel') . '</a> )';
            $rightOrLeftClass = 'demo-nav-span-right';
            if (BOL_LanguageService::getInstance()->getCurrent()->getRtl()) {
                $rightOrLeftClass = 'demo-nav-span-left';
            }
            $demoDiv = '<div id="div_demo" class="demo-nav">';
            $demoDiv .= '<span class="' . $rightOrLeftClass . '">' . $chooseThemeLink . '</span>';
            $demoDiv .= '<span class="' . $rightOrLeftClass . ' timer">' . OW::getLanguage()->text('iisdemo', 'reset_data') . $countDownElement . '</span>';
            $demoDiv .= '<span class="' . $rightOrLeftClass . ' link">' . $adminPanelLink . '</span></div>';
            $DemoDivJS = '$(\'body\').append(\'' . str_replace("'", "\\'", $demoDiv) . '\')';
            OW::getDocument()->addScriptDeclaration($DemoDivJS);
            OW::getDocument()->addOnloadScript($countDownJs);
            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('iisdemo')->getStaticJsUrl() . 'iisdemo.js');
            OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('iisdemo')->getStaticCssUrl() . 'iisdemo.css');
        }
    }
}