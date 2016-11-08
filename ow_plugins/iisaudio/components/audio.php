<?php

/**
 * @author Milad Heshmati <milad.heshmati@gmail.com>
 * @package ow_plugins.iisaudio
 * @since 1.0
 */
class IISAUDIO_CMP_Audio extends OW_Component
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        IISAUDIO_BOL_Service::getInstance()->getAudioJS();
        $form = IISAUDIO_BOL_Service::getInstance()->getAddAudioForm();

        $event = OW::getEventManager()->trigger(new OW_Event(IISEventManager::ON_BEFORE_CREATE_FORM_USING_FIELD_PRIVACY, array('privacyKey' => 'add_audio')));
        if(isset($event->getData()['privacyElement'])){
            $form->addElement($event->getData()['privacyElement']);
            $this->assign('statusPrivacy', true);
        }

        $this->addForm($form);
    }
}