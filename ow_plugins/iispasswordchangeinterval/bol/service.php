<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iispasswordchangeinterval.bol
 * @since 1.0
 */
class IISPASSWORDCHANGEINTERVAL_BOL_Service
{
    CONST CATCH_REQUESTS_KEY = 'iispasswordchangeinterval.catch';
    CONST SECTION_PASSWORD_VALIDATION_INFORMATION = 1;
    CONST SECTION_PASSWORD_VALIDATION_VALID_USERS = 2;
    CONST SECTION_PASSWORD_VALIDATION_INVALID_USERS = 3;
    CONST EXPIRED_TIME_OF_TOKEN = 864000; //5 day

    CONST DEAL_WITH_EXPIRED_PASSWORD_NORMAL_WITHOUT_NOTIF = 'normal';
    CONST DEAL_WITH_EXPIRED_PASSWORD_NORMAL_WITH_NOTIF = 'normal_notif';
    CONST DEAL_WITH_EXPIRED_PASSWORD_FORCE_WITH_NOTIF = 'force_notif';

    private static $classInstance;

    public static function getInstance()
    {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private $passwordValidationDao;

    private function __construct()
    {
        $this->passwordValidationDao = IISPASSWORDCHANGEINTERVAL_BOL_PasswordValidationDao::getInstance();
    }

    /**
     * @return IISPASSWORDCHANGEINTERVAL_BOL_ChangePassword
     */
    public function updateTimePasswordChanged()
    {
        return $this->passwordValidationDao->updateTimePasswordChanged();
    }

    public function deleteAllUsersFromPasswordValidation()
    {
        $this->passwordValidationDao->deleteAllUsersFromPasswordValidation();
    }

    /**
     * @return IISPASSWORDCHANGEINTERVAL_BOL_ChangePassword
     */
    public function getCurrentUser()
    {
        return $this->passwordValidationDao->getCurrentUser();
    }

    /**
     * @param $passwordValidation
    /**
     * @param $passwordValidation
     * @return bool
     */
    public function isChangable($passwordValidation)
    {
        $dealWithExpiredPassword = OW::getConfig()->getValue('iispasswordchangeinterval', 'dealWithExpiredPassword');
        if($this->isUserPasswordExpired($passwordValidation) || ($passwordValidation!=null && !$passwordValidation->valid)){
            if($dealWithExpiredPassword==IISPASSWORDCHANGEINTERVAL_BOL_Service::DEAL_WITH_EXPIRED_PASSWORD_NORMAL_WITHOUT_NOTIF){
                return false;
            }else if($dealWithExpiredPassword==IISPASSWORDCHANGEINTERVAL_BOL_Service::DEAL_WITH_EXPIRED_PASSWORD_NORMAL_WITH_NOTIF){
                $this->sendNotificationToCurrentUserForChangingPassword(OW::getUser()->getId());
                return false;
            }else if($dealWithExpiredPassword==IISPASSWORDCHANGEINTERVAL_BOL_Service::DEAL_WITH_EXPIRED_PASSWORD_FORCE_WITH_NOTIF){
                $this->sendNotificationToCurrentUserForChangingPassword(OW::getUser()->getId());
                return true;
            }
        }
        return false;
    }

    /**
     * @param $userId
     */
    public function sendNotificationToCurrentUserForChangingPassword($userId)
    {
        $adminId = BOL_AuthorizationService::getInstance()->getSuperModeratorUserId();

        $notificationParams = array(
            'pluginKey' => 'iispasswordchangeinterval',
            'action' => 'change-password',
            'entityType' => 'iispasswordchangeinterval',
            'entityId' => $userId,
            'userId' => $userId,
            'time' => time()
        );
        $avatars = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($adminId));

        $notificationData = array(
            'string' => array(
                'key' => 'iispasswordchangeinterval+description_change_password',
                'vars' => array()
            ),
            'avatar' => $avatars[$adminId]
        );
        $event = new OW_Event('notifications.add', $notificationParams, $notificationData);
        OW::getEventManager()->trigger($event);
    }

    /**
     * @param $passwordValidation
     * @return bool
     */
    public function isUserPasswordExpired($passwordValidation){
        $expired_time = OW::getConfig()->getValue('iispasswordchangeinterval', 'expire_time') * 60 * 60 * 24;
        if ($passwordValidation == null) {
            if (time() - OW::getUser()->getUserObject()->getJoinStamp() > $expired_time) {
                return true;
            }
        } else {
            if (time() - $passwordValidation->passwordtime > $expired_time) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $tokenTime
     * @return boolean
     */
    public function isTokenExpired($tokenTime){
        $expired_time = IISPASSWORDCHANGEINTERVAL_BOL_Service::EXPIRED_TIME_OF_TOKEN;
        if (time() - $tokenTime > $expired_time) {
            return true;
        } else {
            return false;
        }
    }

    public function setAllUsersPasswordInvalid($sendEmail)
    {
        $this->passwordValidationDao->setAllUsersPasswordInvalid($sendEmail);
    }

    /**
     * @param $username
     */
    public function setUserPasswordValid($username)
    {
        $this->passwordValidationDao->setUserPasswordValid($username);
    }

    /**
     * @param $username
     */
    public function setUserPasswordInvalid($username)
    {
        $this->passwordValidationDao->setUserPasswordInvalid($username);
    }

    /**
     * @param $searchValue
     * @return array
     */
    public function getAllUsersValid($searchValue = null)
    {
        return $this->passwordValidationDao->getAllUsersValid($searchValue);
    }

    /**
     * @param $regenerate
     * $username
     */
    public function resendLinkTotUserByUsername($regenerate, $username){
        $this->passwordValidationDao->resendLinkTotUserByUsername($regenerate, $username);
    }

    /**
     * @param $searchValue
     * @return array
     */
    public function getAllUsersInvalid($searchValue = null)
    {
        return $this->passwordValidationDao->getAllUsersInvalid($searchValue);
    }

    /**
     * @param int $sectionId
     * @return array
     */
    public function getSections($sectionId)
    {
        $sections = array();
        $sections[] = array(
            'sectionId' => IISPASSWORDCHANGEINTERVAL_BOL_Service::SECTION_PASSWORD_VALIDATION_INFORMATION,
            'active' => $sectionId == IISPASSWORDCHANGEINTERVAL_BOL_Service::SECTION_PASSWORD_VALIDATION_INFORMATION ? true : false,
            'url' => OW::getRouter()->urlForRoute('iispasswordchangeinterval.admin.section-id', array('sectionId' => IISPASSWORDCHANGEINTERVAL_BOL_Service::SECTION_PASSWORD_VALIDATION_INFORMATION)),
            'label' => OW::getLanguage()->text('iispasswordchangeinterval','password_validation_header')
        );
        $sections[] = array(
            'sectionId' => IISPASSWORDCHANGEINTERVAL_BOL_Service::SECTION_PASSWORD_VALIDATION_VALID_USERS,
            'active' => $sectionId == IISPASSWORDCHANGEINTERVAL_BOL_Service::SECTION_PASSWORD_VALIDATION_VALID_USERS ? true : false,
            'url' => OW::getRouter()->urlForRoute('iispasswordchangeinterval.admin.section-id', array('sectionId' => IISPASSWORDCHANGEINTERVAL_BOL_Service::SECTION_PASSWORD_VALIDATION_VALID_USERS)),
            'label' => OW::getLanguage()->text('iispasswordchangeinterval','valid_users_header')
        );
        $sections[] = array(
            'sectionId' => IISPASSWORDCHANGEINTERVAL_BOL_Service::SECTION_PASSWORD_VALIDATION_INVALID_USERS,
            'active' => $sectionId == IISPASSWORDCHANGEINTERVAL_BOL_Service::SECTION_PASSWORD_VALIDATION_INVALID_USERS ? true : false,
            'url' => OW::getRouter()->urlForRoute('iispasswordchangeinterval.admin.section-id', array('sectionId' => IISPASSWORDCHANGEINTERVAL_BOL_Service::SECTION_PASSWORD_VALIDATION_INVALID_USERS)),
            'label' => OW::getLanguage()->text('iispasswordchangeinterval','invalid_users_header')
        );
        return $sections;
    }

    /**
     * @param $sectionId
     * @param $searchType
     * @return array
     */
    public function getUsersBySectionId($sectionId, $searchValue = null){
        if($sectionId==IISPASSWORDCHANGEINTERVAL_BOL_Service::SECTION_PASSWORD_VALIDATION_VALID_USERS){
            return $this->getAllUsersValid($searchValue);
        }else if($sectionId==IISPASSWORDCHANGEINTERVAL_BOL_Service::SECTION_PASSWORD_VALIDATION_INVALID_USERS){
            return $this->getAllUsersInvalid($searchValue);
        }
    }


    /**
     * @param $username
     * @param $sectionId
     * @return string
     */
    public function getChangeStatusUrl($username, $sectionId){
        if($sectionId==IISPASSWORDCHANGEINTERVAL_BOL_Service::SECTION_PASSWORD_VALIDATION_VALID_USERS){
            return "javascript:if(confirm('".OW::getLanguage()->text('iispasswordchangeinterval','invalidate_user_passwprd_warning')."')){location.href='" . OW::getRouter()->urlForRoute('iispasswordchangeinterval.admin.invalidate-password', array('username' => $username, 'sectionId' => $sectionId)) . "';}";
        }
        return "";
    }

    /**
     * @param $token
     * @return IISPASSWORDCHANGEINTERVAL_BOL_PasswordValidation
     */
    public function getUserByToken($token){
        return $this->passwordValidationDao->getUserByToken($token);
    }

    /**
     * @param $username
     * @return IISPASSWORDCHANGEINTERVAL_BOL_PasswordValidation
     */
    public function getUserByUsername($username){
        return $this->passwordValidationDao->getUserByUsername($username);
    }

    /**
     * @param OW_Event $event
     */
    public function onUserRegistered(OW_Event $event)
    {
        $params = $event->getParams();
        if(isset($params['userId'])){
            $user = BOL_UserService::getInstance()->findUserById($params['userId']);
            if($user != null){
                $userInformation = $this->getUserByUsername($user->username);
                if($userInformation != null) {
                    $this->setUserPasswordValid($user->username);
                }
            }
        }
    }

    public function catchAllRequestsExceptions( BASE_CLASS_EventCollector $event )
    {
        $event->add(array(
            OW_RequestHandler::ATTRS_KEY_CTRL => 'IISPASSWORDCHANGEINTERVAL_CTRL_Iispasswordchangeinterval',
            OW_RequestHandler::ATTRS_KEY_ACTION => 'changeUserPassword'
        ));

        $event->add(array(
            OW_RequestHandler::ATTRS_KEY_CTRL => 'IISPASSWORDCHANGEINTERVAL_CTRL_Iispasswordchangeinterval',
            OW_RequestHandler::ATTRS_KEY_ACTION => 'checkValidatePassword'
        ));

    }

    /**
     * @param OW_Event $event
     */
    public function onBeforeResetPasswordFormRenderer(OW_Event $event)
    {
        $params = $event->getParams();
        if ($params['user']) {
            $user = $params['user'];
            $passwordValidation = $this->getUserByUsername($user->username);
            if ($passwordValidation != null && (!$passwordValidation->valid || $this->isTokenExpired($passwordValidation->tokentime))) {
                UTIL_Url::redirect(OW::getRouter()->urlForRoute('iispasswordchangeinterval.invalid-password', array('username' => $passwordValidation->username)));
            }
        }
    }

    /**
     * @param OW_Event $event
     */
    public function onAfterPasswordUpdate(OW_Event $event)
    {
        $params = $event->getParams();
        if ($params['userId'] != null && (!OW::getUser()->isAuthenticated() || OW::getUser()->getId() != $params['userId'])) {
            OW_User::getInstance()->login($params['userId']);
        }
        $this->updateTimePasswordChanged();
        if (!OW::getRequest()->isAjax()) {
            OW::getFeedback()->info(OW::getLanguage()->text('iispasswordchangeinterval', 'password_changed_successfully'));
        }
    }

    /**
     * @param OW_Event $event
     */
    public function onAfterRoute(OW_Event $event)
    {
        if (OW::getRequest()->isAjax() || $this->isUrlInWhiteList() || $this->isUserInWhitelist()) {
            return;
        }
        $passwordValidation = $this->getCurrentUser();
        if ($passwordValidation != null && !$passwordValidation->valid && $this->isChangable($passwordValidation)) {
            OW::getRequestHandler()->setCatchAllRequestsAttributes(IISPASSWORDCHANGEINTERVAL_BOL_Service::CATCH_REQUESTS_KEY, array(
                OW_RequestHandler::ATTRS_KEY_CTRL => 'IISPASSWORDCHANGEINTERVAL_CTRL_Iispasswordchangeinterval',
                OW_RequestHandler::ATTRS_KEY_ACTION => 'index'
            ));
            OW::getRequestHandler()->addCatchAllRequestsExclude(IISPASSWORDCHANGEINTERVAL_BOL_Service::CATCH_REQUESTS_KEY, 'IISPASSWORDCHANGEINTERVAL_CTRL_Iispasswordchangeinterval', 'index');
        }
    }

    /**
     * @return bool
     */
    public function isUserInWhitelist()
    {
        if (!OW::getUser()->isAuthenticated() || OW::getUser()->isAdmin()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isUrlInWhitelist()
    {
        if (OW::getRequest()->getRequestUri() == 'sign-out' || strpos(OW::getRequest()->getRequestUri(), 'changeuserpassword') > -1 || strpos(OW::getRequest()->getRequestUri(), 'changeuserpasswordwithusername') > -1 || strpos(OW::getRequest()->getRequestUri(), 'checkvalidatepassword') > -1 || strpos(OW::getRequest()->getRequestUri(), 'resendlLink') > -1) {
            return true;
        }

        return false;
    }

    /**
     * @param BASE_CLASS_EventCollector $event
     */
    function on_notify_actions(BASE_CLASS_EventCollector $event)
    {
        $event->add(array(
            'section' => 'iispasswordchangeinterval',
            'action' => 'change-password',
            'description' => OW::getLanguage()->text('iispasswordchangeinterval', 'description_change_password'),
            'selected' => true,
            'sectionLabel' => OW::getLanguage()->text('iispasswordchangeinterval', 'title_change_password'),
            'sectionIcon' => 'ow_ic_clock'
        ));
    }

}
