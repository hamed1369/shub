<?php

class IISREVEAL_CTRL_Reveal extends OW_ActionController
{

    public function index($params)
    {
        if(!OW::getConfig()->configExists('iisreveal', 'already_loaded')){
            OW::getConfig()->addConfig('iisreveal', 'already_loaded', false);
        }else{
            OW::getConfig()->saveConfig('iisreveal', 'already_loaded', false);
        }

        $this->redirect(OW_URL_HOME);
    }

}