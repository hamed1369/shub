<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisimport.controllers
 * @since 1.0
 */
class IISIMPORT_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    public function index( array $params = array() )
    {
        $language = OW::getLanguage();
        $this->setPageHeading($language->text('iisimport', 'admin_page_heading'));
        $this->setPageTitle($language->text('iisimport', 'admin_page_title'));
        $config = OW::getConfig();
        $configs = $config->getValues('iisimport');
        
        $form = new Form('settings');
        $form->setAjax();
        $form->setAjaxResetOnSuccess(false);
        $form->setAction(OW::getRouter()->urlForRoute('iisimport.admin'));
        $form->bindJsFunction(Form::BIND_SUCCESS, 'function(data){if(data.result){OW.info("' . OW::getLanguage()->text("iisimport", "settings_successfuly_saved") . '");}else{OW.error("Parser error");}}');

        $useImportYahooField = new CheckboxField('use_import_yahoo');
        $useImportYahooField->setValue($configs['use_import_yahoo']);
        $form->addElement($useImportYahooField);

        $yahooIdField = new TextField('yahoo_id');
        $yahooIdField->setLabel($language->text('iisimport','yahoo_client_id'));
        $yahooIdField->setRequired();
        $yahooIdField->setValue($configs['yahoo_id']);
        $form->addElement($yahooIdField);

        $yahooSecretField = new TextField('yahoo_secret');
        $yahooSecretField->setLabel($language->text('iisimport','yahoo_client_secret'));
        $yahooSecretField->setRequired();
        $yahooSecretField->setValue($configs['yahoo_secret']);
        $form->addElement($yahooSecretField);

        $useImportGoogleField = new CheckboxField('use_import_google');
        $useImportGoogleField->setValue($configs['use_import_google']);
        $form->addElement($useImportGoogleField);

        $googleIdField = new TextField('google_id');
        $googleIdField->setLabel($language->text('iisimport','google_client_id'));
        $googleIdField->setRequired();
        $googleIdField->setValue($configs['google_id']);
        $form->addElement($googleIdField);

        $googleSecretField = new TextField('google_secret');
        $googleSecretField->setLabel($language->text('iisimport','google_client_secret'));
        $googleSecretField->setRequired();
        $googleSecretField->setValue($configs['google_secret']);
        $form->addElement($googleSecretField);

        $submit = new Submit('save');
        $form->addElement($submit);
        
        $this->addForm($form);

        if ( OW::getRequest()->isAjax() )
        {
            if ( $form->isValid($_POST) )
            {
                $config->saveConfig('iisimport', 'use_import_yahoo', $form->getElement('use_import_yahoo')->getValue());
                $config->saveConfig('iisimport', 'yahoo_id', $form->getElement('yahoo_id')->getValue());
                $config->saveConfig('iisimport', 'yahoo_secret', $form->getElement('yahoo_secret')->getValue());
                $config->saveConfig('iisimport', 'use_import_google', $form->getElement('use_import_google')->getValue());
                $config->saveConfig('iisimport', 'google_id', $form->getElement('google_id')->getValue());
                $config->saveConfig('iisimport', 'google_secret', $form->getElement('google_secret')->getValue());

                exit(json_encode(array('result' => true)));
            }
        }
    }
}
