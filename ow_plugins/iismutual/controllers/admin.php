<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iismutual.controllers
 * @since 1.0
 */
class IISMUTUAL_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    public function index(array $params = array())
    {
        $language = OW::getLanguage();
        $this->setPageHeading($language->text('iismutual', 'admin_page_heading'));
        $this->setPageTitle($language->text('iismutual', 'admin_page_title'));
        $config = OW::getConfig();
        $configs = $config->getValues('iismutual');

        $form = new Form('settings');
        $form->setAjax();
        $form->setAjaxResetOnSuccess(false);
        $form->setAction(OW::getRouter()->urlForRoute('iismutual.admin'));
        $form->bindJsFunction(Form::BIND_SUCCESS, 'function(data){if(data.result){OW.info("' . OW::getLanguage()->text("iismutual", "settings_successfuly_saved") . '");}else{OW.error("Parser error");}}');

        $numberOfMutualFriends = new TextField('numberOfMutualFriends');
        $numberOfMutualFriends->setLabel($language->text('iismutual','numberOfMutualFriends'));
        $numberOfMutualFriends->setRequired();
        $numberOfMutualFriends->setValue($configs['numberOfMutualFriends']);
        $numberOfMutualFriends->addValidator(new IntValidator(1));
        $form->addElement($numberOfMutualFriends);

        $submit = new Submit('save');
        $form->addElement($submit);

        $this->addForm($form);

        if (OW::getRequest()->isAjax()) {
            if ($form->isValid($_POST)) {
                $config->saveConfig('iismutual', 'numberOfMutualFriends', $form->getElement('numberOfMutualFriends')->getValue());
                exit(json_encode(array('result' => true)));
            }
        }
    }

}
