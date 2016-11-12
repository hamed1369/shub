<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iiscontrolkids.controllers
 * @since 1.0
 */
class IISCONTROLKIDS_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    public function index( array $params = array() )
    {
        $language = OW::getLanguage();
        $this->setPageHeading($language->text('iiscontrolkids', 'admin_page_heading'));
        $this->setPageTitle($language->text('iiscontrolkids', 'admin_page_title'));
        $config = OW::getConfig();
        $configs = $config->getValues('iiscontrolkids');
        
        $form = new Form('settings');
        $form->setAjax();
        $form->setAjaxResetOnSuccess(false);
        $form->setAction(OW::getRouter()->urlForRoute('iiscontrolkids.admin'));
        $form->bindJsFunction(Form::BIND_SUCCESS, 'function(data){if(data.result){OW.info("'. OW::getLanguage()->text('iiscontrolkids', 'setting_saved') .'");}else{OW.error("Parser error");}}');


        $minimumKidsAge = new TextField('kidsAge');
        $minimumKidsAge->setLabel($language->text('iiscontrolkids','minimumKidsAgeLabel'));
        $minimumKidsAge->setRequired();
        $minimumKidsAge->addValidator(new IntValidator(1));
        $minimumKidsAge->setValue($configs['kidsAge']);
        $form->addElement($minimumKidsAge);

        $marginTime = new TextField('marginTime');
        $marginTime->setLabel($language->text('iiscontrolkids','marginTimeLabel'));
        $marginTime->setRequired();
        $marginTime->addValidator(new IntValidator(1));
        $marginTime->setValue($configs['marginTime']);
        $form->addElement($marginTime);

        $submit = new Submit('save');
        $form->addElement($submit);
        
        $this->addForm($form);

        if ( OW::getRequest()->isAjax() )
        {
            if ( $form->isValid($_POST) )
            {
                $config->saveConfig('iiscontrolkids', 'kidsAge', $form->getElement('kidsAge')->getValue());
                $config->saveConfig('iiscontrolkids', 'marginTime', $form->getElement('marginTime')->getValue());
                exit(json_encode(array('result' => true)));
            }
        }
    }
}
