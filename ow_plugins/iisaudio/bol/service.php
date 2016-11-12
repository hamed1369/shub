<?php

/**
 * Copyright (c) 2016, Milad Heshmati
 * All rights reserved.
 */

/**
 * @author Milad Heshmati <milad.heshmati@gmail.com>
 * @package ow_plugins.iisaudio
 * @since 1.0
 */

final class IISAUDIO_BOL_Service
{
    /***
     * @var IISAUDIO_BOL_AudioDao
     */
    private $audioDao;


    /***
     * IISAUDIO_BOL_Service constructor.
     */
    private function __construct()
    {
        $this->audioDao = IISAUDIO_BOL_AudioDao::getInstance();
    }

    /***
     * @var
     */
    private static $classInstance;

    /***
     * @return IISAUDIO_BOL_Service
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /***
     * @param $title
     * @param $hash
     * @return mixed
     */
    public function addAudio($title, $hash)
    {
        $audio= new IISAUDIO_BOL_Audio();
        $audio->userId=OW::getUser()->getId();
        $audio->title=$title;
        $audio->hash=$hash;
        $audio->addDateTime= time();
        $this->audioDao->save($audio);
        OW::getEventManager()->trigger(new OW_Event('iisaudio_on_after_add_audio',array('audio' => $audio)));
        return  $audio;
     }

    /***
     * @return Form
     */
    public function getAddAudioForm(){

        $form = new Form("add_audio_form");
        $form->setAction(OW::getRouter()->urlForRoute('iisaudio.add_audio'));
        $form->setAjax();
        $form->setAjaxResetOnSuccess(false);
        $form->bindJsFunction(Form::BIND_SUCCESS, 'function(data){if(data.result){addAudioComplete(audioFloatBox);OW.info("' . OW::getLanguage()->text("iisaudio", "Audio_inserterd") . '");}else{OW.error("Parser error");}}');

        $nameField = new TextField("name");
        $nameField->setLabel(OW::getLanguage()->text('iisaudio','audionamefield'));
        $nameField->setRequired();
        $form->addElement($nameField);

        $upload=new HiddenField("audioFile");
        $upload->setLabel("upload");
        $upload->addAttribute("id", "blobField");
        $upload->setRequired();
        $form->addElement($upload);

        $submitField = new Submit("submit");
        $form->addElement($submitField);
        return $form;
    }

    /***
     * @param $id
     */
    public function deleteDatabaseRecord($id)
    {
        $this->audioDao->deleteById($id);
    }

    /***
     * @param $userId
     * @return array
     */
    public function findAudiosByUserId($userId){
        return $this->audioDao->findAudiosByUserId($userId);
    }

    /***
     * @param $id
     * @return mixed
     */
    public function findAudiosById($id){
        return $this->audioDao->findAudiosById($id);
    }

    /***
     * @param $userId
     * @param int $first
     * @param int $count
     * @return array
     */
    public function findListOrderedByDate($userId, $first = 0, $count = 10)
    {
        return $this->audioDao->findListOrderedByDate($userId, $first, $count);
    }

    /***
     * @param $audioName
     * @return string
     */
    public function getAudioFileDirectory($audioName){
        return OW::getPluginManager()->getPlugin('iisaudio')->getUserFilesDir() . $audioName . ".txt";
    }

    /***
     * @param $audioName
     * @return string
     */
    public function getAudioFileUrl($audioName){
        return OW::getPluginManager()->getPlugin('iisaudio')->getUserFilesUrl() . $audioName . ".txt";
    }

    /***
     *
     */
    public function getAudioJS(){
        OW::getDocument()->addScript(OW_PluginManager::getInstance()->getPlugin("iisaudio")->getStaticJsUrl() . 'app.js');
        OW::getDocument()->addScript(OW_PluginManager::getInstance()->getPlugin("iisaudio")->getStaticJsUrl() . 'libmp3lame.min.js');
        OW::getDocument()->addScript(OW_PluginManager::getInstance()->getPlugin("iisaudio")->getStaticJsUrl() . 'mp3recorder.js');
        OW::getDocument()->addOnloadScript('initAudioApp();initMP3Recorder();');
    }
}