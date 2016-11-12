<?php

class IISMOBILESUPPORT_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    public function __construct()
    {
        parent::__construct();

        if ( OW::getRequest()->isAjax() )
        {
            return;
        }

        $lang = OW::getLanguage();

        $this->setPageHeading($lang->text('iismobilesupport', 'admin_settings_title'));
        $this->setPageTitle($lang->text('iismobilesupport', 'admin_settings_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

    public function settings()
    {
        $adminForm = new Form('adminForm');      

        $lang = OW::getLanguage();
        $config = OW::getConfig();

        $field = new TextField('fcm_api_key');
        $field->setLabel($lang->text('iismobilesupport','fcm_api_key_label'));
        $field->setValue($config->getValue('iismobilesupport', 'fcm_api_key'));
        $adminForm->addElement($field);

        $field = new TextField('fcm_api_url');
        $field->setLabel($lang->text('iismobilesupport','fcm_api_url_label'));
        $field->setValue($config->getValue('iismobilesupport', 'fcm_api_url'));
        $adminForm->addElement($field);

        $field = new TextField('constraint_user_device');
        $field->setRequired();
        $validator = new IntValidator();
        $validator->setMinValue(2);
        $validator->setMaxValue(999);
        $field->addValidator($validator);
        $field->setLabel($lang->text('iismobilesupport','constraint_user_device_label'));
        $field->setValue($config->getValue('iismobilesupport', 'constraint_user_device'));
        $adminForm->addElement($field);
        
        $element = new Submit('saveSettings');
        $element->setValue($lang->text('iismobilesupport', 'admin_save_settings'));
        $adminForm->addElement($element);

        if ( OW::getRequest()->isPost() ) {
            if ($adminForm->isValid($_POST)) {
                $config = OW::getConfig();
                $values = $adminForm->getValues();
                $config->saveConfig('iismobilesupport', 'fcm_api_key', $values['fcm_api_key']);
                $config->saveConfig('iismobilesupport', 'fcm_api_url', $values['fcm_api_url']);
                OW::getFeedback()->info($lang->text('iismobilesupport', 'user_save_success'));
            }
        }

       $this->addForm($adminForm);
   } 
}
