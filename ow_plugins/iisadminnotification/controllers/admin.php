<?php

class IISADMINNOTIFICATION_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    public function __construct()
    {
        parent::__construct();

        if ( OW::getRequest()->isAjax() )
        {
            return;
        }

        $lang = OW::getLanguage();

        $this->setPageHeading($lang->text('iisadminnotification', 'admin_settings_title'));
        $this->setPageTitle($lang->text('iisadminnotification', 'admin_settings_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

    public function settings()
    {
        $adminForm = new Form('adminForm');      

        $lang = OW::getLanguage();
        $config = OW::getConfig();

        $field = new TextField('emailSendTo');
        $field->addValidator(new EmailValidator());
        $field->setLabel($lang->text('iisadminnotification','emailSendTo'));
        $field->setValue($config->getValue('iisadminnotification', 'emailSendTo'));
        $field->setDescription($lang->text('iisadminnotification', 'emailSendToDescription'));
        $adminForm->addElement($field);
        
        $field = new CheckboxField('newsCommentNotification');
        $field->setLabel($lang->text('iisadminnotification','newsCommentNotification'));
        $field->setValue($config->getValue('iisadminnotification', 'newsCommentNotification'));
        $adminForm->addElement($field);


        $field = new CheckboxField('topicForumNotification');
        $field->setLabel($lang->text('iisadminnotification','topicForumNotification'));
        $field->setValue($config->getValue('iisadminnotification', 'topicForumNotification'));
        $adminForm->addElement($field);

        $field = new CheckboxField('registerNotification');
        $field->setLabel($lang->text('iisadminnotification','registerNotification'));
        $field->setValue($config->getValue('iisadminnotification', 'registerNotification'));
        $adminForm->addElement($field);
        
        $element = new Submit('saveSettings');
        $element->setValue($lang->text('iisadminnotification', 'admin_save_settings'));
        $adminForm->addElement($element);

        if ( OW::getRequest()->isPost() ) {
            if ($adminForm->isValid($_POST)) {
                $config = OW::getConfig();
                $values = $adminForm->getValues();
                $config->saveConfig('iisadminnotification', 'newsCommentNotification', $values['newsCommentNotification']);
                $config->saveConfig('iisadminnotification', 'topicForumNotification', $values['topicForumNotification']);
                $config->saveConfig('iisadminnotification', 'registerNotification', $values['registerNotification']);
                $config->saveConfig('iisadminnotification', 'emailSendTo', $values['emailSendTo']);
                OW::getFeedback()->info($lang->text('iisadminnotification', 'user_save_success'));
            }
        }

       $this->addForm($adminForm);
   } 
}
