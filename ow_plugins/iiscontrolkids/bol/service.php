<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 * 
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iiscontrolkids.bol
 * @since 1.0
 */
class IISCONTROLKIDS_BOL_Service
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
    
    private $kidsRelationshipDao;
    
    private function __construct()
    {
        $this->kidsRelationshipDao = IISCONTROLKIDS_BOL_KidsRelationshipDao::getInstance();
    }

    /***
     * @param $kidUserId
     * @param $parentEmail
     * @param bool $checkAuth
     * @return IISCONTROLKIDS_BOL_KidsRelationship
     */
    public function addRelationship($kidUserId, $parentEmail, $checkAuth = true)
    {
        return $this->kidsRelationshipDao->addRelationship($kidUserId, $parentEmail, $checkAuth);
    }

    /***
     * @param $kidUserId
     * @param $parentUserId
     * @return bool
     */
    public function isParentExist($kidUserId, $parentUserId){
        return $this->kidsRelationshipDao->isParentExist($kidUserId, $parentUserId);
    }

    /***
     * @param $parentEmail
     * @param $kidUsername
     * @param $kidEmail
     */
    public function sendLinkToParentUser($parentEmail, $kidUsername, $kidEmail, $isForRegistration)
    {
        $mails = array();
        $mail = OW::getMailer()->createMail();
        $mail->addRecipientEmail($parentEmail);
        if($isForRegistration){
            $mail->setSubject(OW::getLanguage()->text('iiscontrolkids', 'email_registration_subject', array('site_name' => OW::getConfig()->getValue('base', 'site_name'))));
            $mail->setHtmlContent($this->getRegistrationEmailContent($parentEmail, $kidUsername, $kidEmail));
            $mail->setTextContent($this->getRegistrationEmailContent($parentEmail, $kidUsername, $kidEmail));
        }else{
            $mail->setSubject(OW::getLanguage()->text('iiscontrolkids', 'parent_email_subject', array('site_name' => OW::getConfig()->getValue('base', 'site_name'))));
            $mail->setHtmlContent($this->getParentEmailContent($parentEmail, $kidUsername, $kidEmail));
            $mail->setTextContent($this->getParentEmailContent($parentEmail, $kidUsername, $kidEmail));
        }
        $mails[] = $mail;
        OW::getMailer()->addListToQueue($mails);
    }

    public function isInChildhood($date){
        $userAge = time();
        if(is_array($date)){
            $userAge = time() - date_timestamp_get(date_create($date['birthdate']));
        }else{
            $userAge = time() - date_timestamp_get(date_create($date));
        }
        $marginTime = OW::getConfig()->getValue('iiscontrolkids','marginTime') * 7 * 24 * 60 * 60;
        $minimumAge = OW::getConfig()->getValue('iiscontrolkids','kidsAge') * 365 * 24 * 60 * 60;
        if($userAge + $marginTime < $minimumAge){
            return true;
        }
        return false;
    }

    /***
     * @param $parentEmail
     * @param $kidUsername
     * @param $kidEmail
     * @return mixed|null|string
     */
    public function getRegistrationEmailContent($parentEmail, $kidUsername, $kidEmail){
        $content = OW::getLanguage()->text('iiscontrolkids', 'email_registration_content', array('site_name' => OW::getConfig()->getValue('base', 'site_name'),'kidUsername' => $kidUsername, 'kidEmail' => $kidEmail, 'parentEmail' => $parentEmail));
        $content .= '</br></br>';
        $content .= '<a href="'. OW::getRouter()->urlForRoute('base_join').'?parentEmailValue='. $parentEmail .'">'.OW::getLanguage()->text('iiscontrolkids', 'email_registration_link_label').'</a>';
        return $content;
    }

    /***
     * @param $parentEmail
     * @param $kidUsername
     * @param $kidEmail
     * @return mixed|null|string
     */
    public function getParentEmailContent($parentEmail, $kidUsername, $kidEmail){
        $content = OW::getLanguage()->text('iiscontrolkids', 'parent_email_content', array('kidUsername' => $kidUsername, 'kidEmail' => $kidEmail, 'parentEmail' => $parentEmail));
        return $content;
    }

    /***
     * @param $parentUserId
     * @return array
     */
    public function getKids($parentUserId){
        return $this->kidsRelationshipDao->getKids($parentUserId);
    }

    /***
     * @param $parentEmail
     * @param $parentUserId
     */
    public function updateParentUserIdUsingEmail($parentEmail, $parentUserId)
    {
        return $this->kidsRelationshipDao->updateParentUserIdUsingEmail($parentEmail, $parentUserId);
    }

    /***
     * @param $kidUserId
     */
    public function deleteRelationship($kidUserId)
    {
        return $this->kidsRelationshipDao->deleteRelationship($kidUserId);
    }


    public function logout(){
        OW::getUser()->logout();
        if ( isset($_COOKIE['ow_login']) )
        {
            setcookie('ow_login', '', time() - 3600, '/');
        }
        OW::getSession()->set('no_autologin', true);
    }



    public function onAddMainConsoleItem(OW_Event $event){
        if(isset($_SESSION['sl_'.OW::getUser()->getId()])){

            //logout from child's account
            $parentUsername = BOL_UserService::getInstance()->findUserById($_SESSION['sl_'.OW::getUser()->getId()])->username;
            $label = OW::getLanguage()->text('iiscontrolkids','logoutFromShadowLogin', array('kidUsername' => OW::getUser()->getUserObject()->username, 'parentUsername' => $parentUsername));
            $url = OW::getRouter()->urlForRoute('iiscontrolkids.logout_from_shadow_login');
            $event->add(array('label' => $label, 'url' => $url));

        }
        if(sizeof($this->getKids(OW::getUser()->getId())) > 0){
            //add child item
            $event->add(array('label' => OW::getLanguage()->text('iiscontrolkids','bottom_menu_item'), 'url' => OW::getRouter()->urlForRoute('iiscontrolkids.index')));
        }
    }

    public function onBeforeUserRegistered(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['birthdate']) && $this->isInChildhood($params['birthdate'])){
            if(!isset($_REQUEST['parentEmail']) || !UTIL_Validator::isEmailValid($_REQUEST['parentEmail'])){
                $_SESSION['parentEmailValueError'] = true;
                if (OW::getRequest()->isAjax()) {
                    echo json_encode(array('result' => false));
                    exit;
                }
                OW::getFeedback()->error(OW::getLanguage()->text('iiscontrolkids', 'parentEmailEmpty'));
                OW::getApplication()->redirect();
            }
        }
    }

    public function onBeforeJoinFormRender(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['form']) && isset($params['controller'])){
            $this->addParentEmailFieldToForm($params['form']);
            $questionsSectionsList = $params['form']->getSortedQuestionsList();
            $dateFieldName = null;
            foreach($questionsSectionsList as $question){
                if(!$question['fake'] && $question['realName'] == 'birthdate'){
                    $dateFieldName = $question['name'];
                }
            }
            if($dateFieldName !=null) {
                $kidsAge = OW::getConfig()->getValue('iiscontrolkids', 'kidsAge');
                $params['controller']->assign('display_parent_email', true);
                $params['controller']->assign('kidsAge', $kidsAge);
                $js = 'function checkKidsAge(){
                        var kidsAge='. $kidsAge .';
                         var userYear =  $(\'[name="year_'.$dateFieldName.'"]\').val();
                         var today = new Date();
                         if(typeof(getCookie) == "function" && getCookie("iisjalali")==1){
                             var jalaliDate = gregorian_to_jalali(today.getFullYear(),parseInt(today.getMonth()+1),parseInt(today.getDate()));
                             if(parseInt(jalaliDate[0]) - parseInt(userYear) < kidsAge){
                                var parentEmail_input = document.getElementsByName(\'parentEmail\')[0];
                                var displayError = document.getElementById(parentEmail_input.id+\'_error\');
                                $(".parent_email").show();
                             }
                             else{
                                var parentEmail_input = document.getElementsByName(\'parentEmail\')[0];
                                var displayError = document.getElementById(parentEmail_input.id+\'_error\');
                                displayError.innerHTML="";
                                parentEmail_input.value="";
                                $(".parent_email").hide();
                             }
                         } else{
                              if(parseInt(today.getFullYear()) - parseInt(userYear) < kidsAge){
                                $(".parent_email").show();
                             }
                             else{
                                $(".parent_email").hide();
                             }
                         }
                }
                $(\'[name="year_'.$dateFieldName.'"]\').change(checkKidsAge);checkKidsAge();';
                OW::getDocument()->addOnloadScript($js);
            }
        }
        if($params['form']->getElement('email')!=null && isset($_REQUEST['parentEmailValue'])){
            $params['form']->getElement('email')->setValue($_REQUEST['parentEmailValue']);
            $params['form']->getElement('email')->addAttribute(FormElement::ATTR_READONLY);
        }
    }

    public function addParentEmailFieldToForm($form){
        $parentEmail = new TextField("parentEmail");
        $parentEmail->addValidator(new EmailValidator());
        $parentEmail->addValidator(new RequiredParentEmailValidator());
        $parentEmail->setLabel(OW_Language::getInstance()->text('iiscontrolkids', "join_parent_email_header"));
        if(isset($_SESSION['parentEmailValueError'])){
            unset($_SESSION['parentEmailValueError']);
            $parentEmail->addError(OW::getLanguage()->text('iiscontrolkids', 'parentEmailEmpty'));
        }
        $form->addElement($parentEmail);
    }

    public function onUserRegistered(OW_Event $event){
        $params = $event->getParams();
        $user = null;
        if( isset($params['userId'])){
            $user = BOL_UserService::getInstance()->findUserById($params['userId']);
        }
        if(isset($_REQUEST['parentEmail']) &&
            isset($params['userId']) && UTIL_Validator::isEmailValid($_REQUEST['parentEmail'])){
            $birthdate = BOL_QuestionService::getInstance()->getQuestionData(array($params['userId']), array('birthdate'))[$params['userId']];
            if($this->isInChildhood($birthdate)) {
                $this->addRelationship($params['userId'], $_REQUEST['parentEmail']);
            }
        }
        $email = $_REQUEST['email'];
        if(!isset($email) && isset($user)){
            $email = $user->getEmail();
        }

        if(isset($email)) {
            $this->updateParentUserIdUsingEmail($email, $params['userId']);
        }
    }
}

class RequiredParentEmailValidator extends OW_Validator
{
    /**
     * Constructor.
     *
     * @param array $params
     */
    public function __construct()
    {
        $errorMessage = OW::getLanguage()->text('base', 'form_validator_required_error_message');

        if ( empty($errorMessage) )
        {
            $errorMessage = 'Required Validator Error!';
        }

        $this->setErrorMessage($errorMessage);
    }

    /**
     * @see OW_Validator::isValid()
     *
     * @param mixed $value
     */
    public function isValid( $value )
    {
        return true;
    }

    /**
     * @see OW_Validator::getJsValidator()
     *
     * @return string
     */
    public function getJsValidator()
    {
        return "{
        	validate : function( value ){
        	    if($('.parent_email') && $('.parent_email')[0] && $('.parent_email')[0].style.display != 'none'){
                    if(  $.isArray(value) ){ if(value.length == 0  ) throw " . json_encode($this->getError()) . "; return;}
                    else if( !value || $.trim(value).length == 0 ){ throw " . json_encode($this->getError()) . "; }
                }
        },
        	getErrorMessage : function(){ return " . json_encode($this->getError()) . " }
        }";
    }



}
