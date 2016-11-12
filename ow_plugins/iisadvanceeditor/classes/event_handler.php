<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisadvanceeditor.bol
 * @since 1.0
 */
class IISADVANCEEDITOR_CLASS_EventHandler
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
        $eventManager->bind(OW_EventManager::ON_FINALIZE, array($this, 'onFinalize'));
        $eventManager->bind(OW_EventManager::ON_AFTER_ROUTE, array($this, 'onAfterRoute'));
    }

    public function onFinalize(OW_Event $event)
    {
        $requri = OW::getRequest()->getRequestUri();
        $config = OW::getConfig();
        $htmlDisableStatus = false;
        $mediaDisableStatus = false;
        $ck_enabled = false;
        if ((strpos($requri, 'edit') !== false && strpos($requri, 'admin') === false) ||
            (strpos($requri, 'add') !== false && strpos($requri, 'admin') === false) ||
            strpos($requri, 'create') !== false ||
            strpos($requri, 'new') !== false ||
            strpos($requri, 'admin/guideline') !== false ||
            strpos($requri, 'admin/questions') !== false ||
            strpos($requri, 'admin/edit-question') !== false ||
            strpos($requri, 'admin/mass-mailing') !== false ||
            strpos($requri, 'forum/topic/') !== false
        ) {

            $ck_enabled = true;
        }
        if($config->configExists('base','tf_user_custom_html_disable'))
        {
            $htmlDisableStatus= $config->getValue('base','tf_user_custom_html_disable');
        }
        if($config->configExists('base','tf_user_rich_media_disable'))
        {
            $mediaDisableStatus= $config->getValue('base','tf_user_rich_media_disable');
        }
        if ($ck_enabled === true && !$htmlDisableStatus) {
            $mediaPlugins = '';
            if(!$mediaDisableStatus){
                $mediaPlugins = 'ow_video,ow_image,';
            }
            OW::getDocument()->addStyleSheet(OW_URL_STATIC_PLUGINS . 'iisadvanceeditor/css/init.css');
            OW::getDocument()->addScript(OW_URL_STATIC_PLUGINS . 'iisadvanceeditor/js/ckeditor/ckeditor.js');
            OW::getDocument()->addScript(OW_URL_STATIC_PLUGINS . 'iisadvanceeditor/js/init.js');
            OW::getDocument()->addOnloadScript("
                window.CKCONFIG=
                {
                toolbar: 'Basic',
                customConfig : '',
                ow_imagesUrl : '" . OW::getRouter()->urlFor('BASE_CTRL_MediaPanel', 'index', array('pluginKey' => 'blog', 'id' => '__id__')) . "',
                language : '" . BOL_LanguageService::getInstance()->getCurrent()->getTag() . "',
                disableNativeSpellChecker : false,
                extraPlugins: '" . $mediaPlugins ."ow_more',
                removePlugins : 'elementspath,image',
                linkShowAdvancedTab : false,
                allowedContent:'h1 h2 h3 h4 h5 h6 ul ol blockquote div tr p li td strong em i b u span; a[href,target]; img[src,height,width]; *[id,alt,title]{*}(*)',
                autoGrow_onStartup: true,
                uiColor: '#fdfdfd'
                };
                iisadvanceeditor_textarea_check();
            ", 900);
        }
    }

    public function onAfterRoute(OW_Event $event)
    {
        OW::getDocument()->addStyleSheet(OW_PluginManager::getInstance()->getPlugin('iisadvanceeditor')->getStaticJsUrl() . 'ckeditor/contents.css');
        OW::getDocument()->addStyleSheet(OW_PluginManager::getInstance()->getPlugin('iisadvanceeditor')->getStaticCssUrl() . 'init.css');
    }

}