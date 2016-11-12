<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 * 
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisimport.bol
 * @since 1.0
 */
class IISIMPORT_CLASS_EventHandler
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
    
    private $service;
    
    private function __construct()
    {
        $this->service = IISIMPORT_BOL_Service::getInstance();
    }
    
    public function init()
    {
        $eventManager = OW::getEventManager();
        $eventManager->bind(OW_EventManager::ON_USER_REGISTER, array($this, 'onUserRegistered'));
    }

    public function onUserRegistered(OW_Event $event){
        $params = $event->getParams();
        $user = null;
        if(isset($params['userId'])){
            $user = BOL_UserService::getInstance()->findUserById($params['userId']);
            if($user!=null){
                $inviters = $this->service->getUsersByEmail($user->email);
                foreach($inviters as $inviter){
                    $this->service->sendEmailToInviter($inviter->email, $user->email);
                }
            }

        }
        $email = $_REQUEST['email'];
        if(!isset($email) && isset($user)){
            $email = $user->getEmail();
        }

        if(isset($email)) {
            IISCONTROLKIDS_BOL_Service::getInstance()->updateParentUserIdUsingEmail($email, $params['userId']);
        }
    }

}