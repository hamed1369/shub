<?php

/**
 * Copyright (c) 2016,
 * All rights reserved.
 */

/**
 * 
 *
 * @author
 * @package ow_plugins.iisaparatsupport.bol
 * @since 1.0
 */
class IISAPARATSUPPORT_BOL_Service
{

    private static $classInstance;
    
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function onAfterVideoProvidersDefined(OW_Event $event){
        $event->setData(array('aparat'=>'https://www.aparat.com/'));
    }
    public function onBeforeVideoAdded(OW_Event $event){
        $params = $event->getParams();
        $code = preg_replace('/\s+/', '', $params['code']);
        if ($this->checkIfItIsAparatUrl($code)){
            $newCode = str_replace('aparat.com/v/','aparat.com/video/video/embed/videohash/',$code);
            $newCode = str_replace('http://','https://',$newCode);
            $newCode = $newCode.'/vt/frame';
            $params['code'] = '<iframe src="'.$newCode.'" height="360" width="640" allowFullScreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" ></iframe>';
            $event->setData($params);
        }
    }
    public function onVideoUrlValidation(OW_Event $event){
        $code = $event->getParams()['code'];
        if ($this->checkIfItIsAparatNewEmbedCode($code)){
            $event->setData(array('result'=>true));
            return;
        }
//        if ($this->checkIfItIsAparatOldEmbedCode($code)){
//            $event->setData(array('result'=>true));
//            return;
//        }
        if ($this->checkIfItIsAparatUrl($code)){
            $event->setData(array('result'=>true));
            return;
        }
        $event->setData(array('result' => false));

    }
    public function checkIfItIsAparatUrl($url){
        if (preg_match_all('/^(?:http|https):\/\/w{3}?\.aparat\.com/i',$url,$match))
            return true;
        return false;
    }
    public function getAparatNewEmbedCodeRegex(){
        return '/<div\s+id="\d+">\s*<script\s+type="text\/JavaScript"\s+src="((?:https|http):\/\/www\.aparat\.com\/[\/\w\?\[\]=\&]+)">\s*<\/script>\s*<\/div>/i';
    }
    public function getAparatOldEmbedCodeRegex(){
        return '/<iframe\s+src="((?:https|http):\/\/www\.aparat\.com\/[\/\w\?\[\]=\&]+)"[\s\w="]+>\s*<\/iframe>/i';
    }
    public function checkIfItIsAparatNewEmbedCode($code){
        if (preg_match_all($this->getAparatNewEmbedCodeRegex(),$code,$match))
            return true;
        return false;
    }
    public function checkIfItIsAparatOldEmbedCode($code){
        if (preg_match_all($this->getAparatOldEmbedCodeRegex(),$code,$match))
            return true;
        return false;
    }
}
