<?php

/**
 * Copyright (c) 2016, Milad Heshmati
 * All rights reserved.
 */

/**
 * @author Milad Heshmati <milad.heshmati@gmail.com>
 * @package ow_plugins.iispreloader
 * @since 1.0
 */

class IISPRELOADER_CLASS_EventHandler
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
        $eventManager->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($this, 'PreloaderRender'));
    }
    public function PreloaderRender(OW_Event $event)
    {
        $preloaderDiv = $this->getPreloaderDesign(OW::getConfig()->getValue('iispreloader', 'iispreloadertype'));
        $PreloaderDivJS = '$(\'body\').append(\'' . str_replace("'", "\\'", $preloaderDiv) . '\')';
        OW::getDocument()->addScriptDeclaration($PreloaderDivJS);
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('iispreloader')->getStaticJsUrl() . 'iispreloader.js');

    }

    public function getPreloaderDesign($id){
        $preloaderDiv = '';
        if($id == 1){
            $preloaderDiv = '<div id="loading">';
            $preloaderDiv .= '<div id="loading-center">';
            $preloaderDiv .= '<div id="loading-center-absolute">';
            $preloaderDiv .= '<div class="object" id="object_four"></div>';
            $preloaderDiv .= '<div class="object" id="object_three"></div>';
            $preloaderDiv .= '<div class="object" id="object_two"></div>';
            $preloaderDiv .= '<div class="object" id="object_one"></div>';
            $preloaderDiv .='</div>';
            $preloaderDiv .='</div>';
            $preloaderDiv .='</div>';
            OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('iispreloader')->getStaticCssUrl() . 'iispreloader1.css');
            return $preloaderDiv;
        }else if($id == 2){
            $preloaderDiv = '<div id="loading">';
            $preloaderDiv .= '<div id="loading-center">';
            $preloaderDiv .= '<div id="loading-center-absolute">';
            $preloaderDiv .= '<div class="object" id="object_four"></div>';
            $preloaderDiv .= '<div class="object" id="object_three"></div>';
            $preloaderDiv .= '<div class="object" id="object_two"></div>';
            $preloaderDiv .= '<div class="object" id="object_one"></div>';
            $preloaderDiv .='</div>';
            $preloaderDiv .='</div>';
            $preloaderDiv .='</div>';
            OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('iispreloader')->getStaticCssUrl() . 'iispreloader2.css');
            return $preloaderDiv;
        }else if($id == 3){
            $preloaderDiv = '<div id="loading">';
            $preloaderDiv .= '<div id="loading-center">';
            $preloaderDiv .= '<div id="loading-center-absolute">';
            $preloaderDiv .= '<div class="object" id="object_four"></div>';
            $preloaderDiv .= '<div class="object" id="object_three"></div>';
            $preloaderDiv .= '<div class="object" id="object_two"></div>';
            $preloaderDiv .= '<div class="object" id="object_one"></div>';
            $preloaderDiv .= '<div class="object" id="object_big"></div>';
            $preloaderDiv .='</div>';
            $preloaderDiv .='</div>';
            $preloaderDiv .='</div>';
            OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('iispreloader')->getStaticCssUrl() . 'iispreloader3.css');
            return $preloaderDiv;
        }else if($id == 4){
            $preloaderDiv = '<div id="loading">';
            $preloaderDiv .= '<div id="loading-center">';
            $preloaderDiv .= '<div id="loading-center-absolute">';
            $preloaderDiv .= '<div class="object" id="object_four"></div>';
            $preloaderDiv .= '<div class="object" id="object_three"></div>';
            $preloaderDiv .= '<div class="object" id="object_two"></div>';
            $preloaderDiv .= '<div class="object" id="object_one"></div>';
            $preloaderDiv .='</div>';
            $preloaderDiv .='</div>';
            $preloaderDiv .='</div>';
            OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('iispreloader')->getStaticCssUrl() . 'iispreloader4.css');
            return $preloaderDiv;

        }else{
            $this->getPreloaderDesign(1);
        }
    }
}