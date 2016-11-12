<?php

class IISPRELOADER_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    public function __construct()
    {
        parent::__construct();

        if ( OW::getRequest()->isAjax() )
        {
            return;
        }

        $lang = OW::getLanguage();
        $this->setPageHeading($lang->text('iispreloader', 'admin_settings_title'));
        $this->setPageTitle($lang->text('iispreloader', 'admin_settings_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

    public function settings()
    {


        $PreloaderForm = new Form('PreloaderForm');

        $lang = OW::getLanguage();
        $config = OW::getConfig();
        $configs = $config->getValues('iispreloader');

        $iispreloadertype = new Selectbox('iispreloadertype');
        $options = array();
        $options[1] = 1;
        $options[2] = 2;
        $options[3] = 3;
        $options[4] = 4;
        $iispreloadertype->setHasInvitation(false);
        $iispreloadertype->setOptions($options);
        $iispreloadertype->setRequired();
        $iispreloadertype->setValue($configs['iispreloadertype']);
        $PreloaderForm->addElement($iispreloadertype);


        $saveSettings = new Submit('saveSettings');
        $saveSettings->setValue($lang->text('iispreloader', 'admin_save_settings'));
        $PreloaderForm->addElement($saveSettings);

        $this->addForm($PreloaderForm);

        if ( OW::getRequest()->isPost())
        {
            if ( $PreloaderForm->isValid($_POST) )
            {
                $config->saveConfig('iispreloader', 'iispreloadertype', $PreloaderForm->getElement('iispreloadertype')->getValue());
            }
        }


    }
}
