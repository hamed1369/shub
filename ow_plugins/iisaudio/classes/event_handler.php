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
class IISAUDIO_CLASS_EventHandler
{
    private static $classInstance;

    public static function getInstance()
    {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }


    private function __construct()
    {
    }

    public function init()
    {
        $eventManager = OW::getEventManager();
        $eventManager->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($this, 'AudioRender'));
        $eventManager->bind('iisaudio_on_after_add_audio', array($this, 'addFeedAction'));
    }

    /***
     * @param OW_Event $event
     */
    public function addFeedAction(OW_Event $event){
      $audio = $event->getParams()['audio'];
        if($audio == null){
            return;
        }
        $actionParams['userId'] = $audio->userId;
        $actionParams['ownerId'] = $audio->userId;
        $actionParams['entityId'] = $audio->getId();
        $actionParams['pluginKey'] = 'iisaudio';
        $actionParams['entityType'] = 'add_audio';
        $actionParams['visibility'] = 15;
        $actionData['string'] = array('key' => "iisaudio+feed_item_line", 'user' => $audio->userId);
        $actionData['content'] = '<div class="audio_item_name"> ' .$audio->title . '</div><div class="audio_item_player"><audio class="audio_item_player" controls src=" ' .file_get_contents(IISAUDIO_BOL_Service::getInstance()->getAudioFileUrl($audio->hash)) . '"></audio></div>';
        $event = new OW_Event('feed.action', $actionParams, $actionData);
        OW::getEventManager()->trigger($event);
    }

    public function AudioRender(OW_Event $event)
    {
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('iisaudio')->getStaticJsUrl() . 'iisaudio.js');
        OW::getDocument()->addStyleSheet(OW_PluginManager::getInstance()->getPlugin("iisaudio")->getStaticCssUrl() . 'audio.css');
        OW::getDocument()->addOnloadScript('$(\'.dashboard-NEWSFEED_CMP_MyFeedWidget .ow_status_update_btn_block\').append(\'<span class="ow_smallmargin iisaudio_mic"><span class="iisaudio_mic" onclick="CreateAudio()"><span class="buttons clearfix"><a class="iisaudio_mic"></a></span></span></span>\')');
        OW::getDocument()->addOnloadScript('$(\'.profile-NEWSFEED_CMP_UserFeedWidget .ow_status_update_btn_block\').append(\'<span class="ow_smallmargin iisaudio_mic"><span class="iisaudio_mic" onclick="CreateAudio()"><span class="buttons clearfix"><a class="iisaudio_mic"></a></span></span></span>\')');
        $css = '
            .iisaudio_mic{
                background-image: url("' . OW::getPluginManager()->getPlugin('iisaudio')->getStaticUrl() . 'img/mic.svg' . '") !important; background-size: 20px !important; background-repeat: no-repeat;background-size: 20px !important;background-position-y: 3px;
                cursor: pointer;float: right;height: 22px;overflow: hidden;text-decoration: none;width: 22px;}
            input.start{
                background-image: url("' . OW::getPluginManager()->getPlugin('iisaudio')->getStaticUrl() . 'img/record.svg' . '");}
            input.stop{
                background-image: url("' . OW::getPluginManager()->getPlugin('iisaudio')->getStaticUrl() . 'img/stop.svg' . '");}
            input.start:hover {
                color: black;
                background-image: url("' . OW::getPluginManager()->getPlugin('iisaudio')->getStaticUrl() . 'img/record.svg' . '");}
            input.stop:hover {
                color: black;
                background-image: url("' . OW::getPluginManager()->getPlugin('iisaudio')->getStaticUrl() . 'img/stop.svg' . '");}
            ';
        Ow::getDocument()->addStyleDeclaration($css);
        $lang = OW::getLanguage();
        $lang->addKeyForJs('iisaudio', 'Recording');
        $lang->addKeyForJs('iisaudio', 'Converting');
        $defineMP3PathTemp = 'defineMP3Recorder("'. OW_PluginManager::getInstance()->getPlugin("iisaudio")->getStaticJsUrl() . 'recorderWorker.js' . '");';
        OW::getDocument()->addOnloadScript($defineMP3PathTemp);
        $defineMP3workerTemp = 'defineMP3Worker("'. OW_PluginManager::getInstance()->getPlugin("iisaudio")->getStaticJsUrl() . 'mp3Worker.js' . '");';
        OW::getDocument()->addOnloadScript($defineMP3workerTemp);
    }

}