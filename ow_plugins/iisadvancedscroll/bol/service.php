<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisadvancedscroll.bol
 * @since 1.0
 */
class IISADVANCEDSCROLL_BOL_Service
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

    public function onBeforeDocumentRenderer(OW_Event $event)
    {
        $CSSUrl = OW::getPluginManager()->getPlugin('iisadvancedscroll')->getStaticCssUrl() . 'ui.totop.css';
        $toTopMinUrl = OW::getPluginManager()->getPlugin('iisadvancedscroll')->getStaticJsUrl() . 'jquery.ui.totop.min.js';
        $easingUrl = OW::getPluginManager()->getPlugin('iisadvancedscroll')->getStaticJsUrl() . 'easing.js';

        $document = OW::getDocument();
        $document->addScript($easingUrl, "text/javascript");
        $document->addScript($toTopMinUrl, "text/javascript");
        $document->addStyleSheet($CSSUrl);
    }

    public function onFinalize(OW_Event $event){
        $config = OW::getConfig();

        $speed = $config->getValue('iisadvancedscroll', 'EaseSpeed');
        $type = $config->getValue('iisadvancedscroll', 'Easing');
        $indelay = $config->getValue('iisadvancedscroll', 'InDelay');
        $outdelay = $config->getValue('iisadvancedscroll', 'OutDelay');
        $bottom = $config->getValue('iisadvancedscroll', 'bottom');
        $right = $config->getValue('iisadvancedscroll', 'right');
        $left = $config->getValue('iisadvancedscroll', 'left');
        $adminAreaAllowed = $config->getValue('iisadvancedscroll', 'adminarea');//0 is false,1 is true
        $uri = OW::getRouter()->getUri();
        if ($adminAreaAllowed == 'disable' and explode('/', $uri[0] == 'admin'))
        {
            return;
        }


        $script = "$(document).ready(function() {
			   $().UItoTop({ easingType: '{$type}', scrollSpeed : $speed, inDelay: $indelay, outDelay: $outdelay});
	        });";


        $css = "#toTop{bottom: {$bottom}px; right: {$right}px;";
        if ((int) $left != 0) { $css.= "left: {$left}px;";}
        $css .= "}";

        OW::getDocument()->addStyleDeclaration($css);
        OW::getDocument()->addScriptDeclaration($script);
    }

}