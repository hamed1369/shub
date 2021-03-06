<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisblockingip.controllers
 * @since 1.0
 */
class IISBLOCKINGIP_CTRL_Iisblockingip extends OW_ActionController
{
    private $service;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->service = IISBLOCKINGIP_BOL_Service::getInstance();
    }
    
    public function index( $params = NULL )
    {
        if ( !$this->service->isLocked() )
        {
            $this->redirect(OW_URL_HOME);
        }

        $userBlockedTime = $this->service->getCurrentUser()->getTime();

        OW::getDocument()->setJavaScripts(array('added' => array()));
        $this->setPageTitle(OW::getLanguage()->text("iisblockingip", "title_locked"));
        $release_time = date_create();
        date_timestamp_set($release_time, $userBlockedTime + (int)OW::getConfig()->getValue('iisblockingip', IISBLOCKINGIP_BOL_Service::EXPIRE_TIME) * 60);
        $release_time =  date_format($release_time, 'Y-m-d H:i:s');
        $this->assign("release_time", $release_time);
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'jquery.min.js', 'text/javascript', (-100));
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'jquery-migrate.min.js', 'text/javascript', (-100));
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'ow.js');
        $masterPageFileDir = OW::getThemeManager()->getMasterPageTemplate('blank');
        OW::getDocument()->getMasterPage()->setTemplate($masterPageFileDir);
    }
}
