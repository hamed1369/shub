<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 * 
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisuserlogin.bol
 * @since 1.0
 */
class IISUSERLOGIN_BOL_Service
{
    CONST EXPIRE_TIME = 'expiredTimeOfLoginDetails';
    CONST NUMBER_OF_LOGIN_DETAILS = 'numberOfLastLoginDetails';

    private static $classInstance;
    
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    
    private $loginDetailsDao;
    
    private function __construct()
    {
        $this->loginDetailsDao = IISUSERLOGIN_BOL_LoginDetailsDao::getInstance();
    }
    
    public function deleteLoginDetails()
    {
        return $this->loginDetailsDao->deleteLoginDetails();
    }

    public function getCurrentIP(){
        $ip = OW::getRequest()->getRemoteAddress();
        if($ip == '::1' || empty($ip)){
            $ip = '127.0.0.1';
        }
        return $ip;
    }

    public function getCurrentBrowserInformation(){
        $event = OW::getEventManager()->trigger(new OW_Event('iismobilesupport.browser.information'));
        if(isset($event->getData()['browser_name'])){
            return $event->getData()['browser_name'];
        }

        return empty($_SERVER['HTTP_USER_AGENT'])?'no-browser':$this->getBrowser()['platform'].'-'.$this->getBrowser()['name'].'-'.$this->getBrowser()['version'];
    }

    function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/Android/i', $u_agent)) {
            $platform = 'android';
        }
        else if (preg_match('/iPhone/i', $u_agent)) {
            $platform = 'iPhone';
        }
        else if (preg_match('/iPad/i', $u_agent)) {
            $platform = 'iPad';
        }
        else if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        $is_native_android = preg_match('/Mozilla/i', $u_agent) && preg_match('/Android/i', $u_agent) && preg_match('/AppleWebKit/i', $u_agent) && !preg_match('/Chrome/i', $u_agent);

        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Internet Explorer browser';
            $ub = "MSIE";
        }
        elseif(preg_match('/Trident/i',$u_agent) && preg_match('/rv/i',$u_agent) && preg_match('/Mozilla/i',$u_agent))
        {
            $bname = 'Internet Explorer browser';
            $ub = "rv";
        }
        elseif(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox browser';
            $ub = "Firefox";
        }
        elseif(preg_match('/Edge/i',$u_agent))
        {
            $bname = 'Microsoft Edge browser';
            $ub = "Edge";
        }
        elseif(preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Opera browser';
            $ub = "Opera";
        }
        elseif(preg_match('/OPR/i',$u_agent))
        {
            $bname = 'Opera browser';
            $ub = "OPR";
        }
        elseif(preg_match('/Chrome/i',$u_agent))
        {
            $bname = 'Google Chrome browser';
            $ub = "Chrome";
        }
        elseif(preg_match('/CriOS/i',$u_agent))
        {
            $bname = 'Google Chrome browser';
            $ub = "CriOS";
        }
        else if($is_native_android){
            $bname = 'Android Native browser';
            $ub = "Version";
        }
        elseif(preg_match('/Safari/i',$u_agent))
        {
            $bname = 'Safari browser';
            $ub = "Safari";
        }
        elseif(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape browser';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ :]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    }

    /***
     * @param $userId
     * @param bool $checkAuth
     * @return IISUSERLOGIN_BOL_LoginDetails|void
     */
    public function addLoginDetails($userId, $checkAuth = true)
    {
        return $this->loginDetailsDao->addLoginDetails($userId, $checkAuth);
    }

    /***
     * @param $userId
     * @param bool $checkAuth
     * @return array
     */
    public function getUserLoginDetails($userId, $checkAuth = true){
        if($checkAuth && !OW::getUser()->isAuthenticated()){
            return null;
        }
        $numberOfLoginDetails = (int)OW::getConfig()->getValue('iisuserlogin', IISUSERLOGIN_BOL_Service::NUMBER_OF_LOGIN_DETAILS);
        return $this->loginDetailsDao->getUserLoginDetails($userId, $numberOfLoginDetails);
    }

    public function deleteUserLoginDetails($userId, $checkAuth = true){
        if($checkAuth && !OW::getUser()->isAuthenticated() && $userId!=OW::getUser()->getId()){
            return null;
        }
        return $this->loginDetailsDao->deleteUserLoginDetails($userId);
    }

    public function sendEmailToUsers($userId)
    {
        $user = BOL_UserService::getInstance()->findUserById($userId);
        if($user==null){
            return;
        }
        $mails = array();
        $detail = $this->loginDetailsDao->getUserLoginDetails($userId, 1)[0];
        $mail = OW::getMailer()->createMail();
        $mail->addRecipientEmail($user->email);
        $mail->setSubject(OW::getLanguage()->text('iisuserlogin', 'email_login_to_network'));
        $mail->setHtmlContent(OW::getLanguage()->text('iisuserlogin', 'email_login_details', array('username' => $user->username, 'email' => $user->email, 'ip' => $detail->ip, 'time' => UTIL_DateTime::formatSimpleDate($detail->time), 'browser' => $detail->browser)));
        $mail->setTextContent(OW::getLanguage()->text('iisuserlogin', 'email_login_details', array('username' => $user->username, 'email' => $user->email, 'ip' => $detail->ip, 'time' => UTIL_DateTime::formatSimpleDate($detail->time), 'browser' => $detail->browser)));
        $mails[] = $mail;
        OW::getMailer()->addListToQueue($mails);
    }

    public function onUserLogin(OW_Event $event){
        $params = $event->getParams();
        $this->addLoginDetails($params['userId']);
    }

    public function onPreferenceAddFormElement( BASE_CLASS_EventCollector $event )
    {
        $language = OW::getLanguage();

        $params = $event->getParams();
        $values = $params['values'];

        $fromElementList = array();

        $fromElement = new CheckboxField('iisuserlogin_login_detail_subscribe');
        $fromElement->setLabel($language->text('iisuserlogin', 'preference_login_detail_subscribe_label'));
        $fromElement->setDescription($language->text('iisuserlogin', 'preference_login_detail_subscribe_description'));

        if ( isset($values['iisuserlogin_login_detail_subscribe']) )
        {
            $fromElement->setValue($values['iisuserlogin_login_detail_subscribe']);
        }

        $fromElementList[] = $fromElement;

        $event->add($fromElementList);
    }
}
