<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iispasswordchangeinterval.bol
 * @since 1.0
 */
class IISPASSWORDCHANGEINTERVAL_BOL_PasswordValidationDao extends OW_BaseDao
{
    private static $classInstance;

    public static function getInstance()
    {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function getDtoClassName()
    {
        return 'IISPASSWORDCHANGEINTERVAL_BOL_PasswordValidation';
    }

    public function getTableName()
    {
        return OW_DB_PREFIX . 'iispasswordchangeinterval_password_validation';
    }

    public function resendLinkTotUserByUsername($regenerate, $username)
    {
        $passwordValidation = $this->getUserByUsername($username);
        if ($regenerate || empty($passwordValidation->token)) {
            $passwordValidation->setToken(md5(UTIL_String::getRandomString(8, 5)));
            $passwordValidation->setTokentime(time());
            $this->save($passwordValidation);
        }
        OW::getMailer()->addToQueue($this->createEmailForChangingPassword($passwordValidation->email, $passwordValidation->token, $username));
    }

    public function setAllUsersPasswordInvalid($sendEmail)
    {
        $this->deleteAllUsersFromPasswordValidation();

        $numberOfUsers = BOL_UserService::getInstance()->count(true);
        $users = BOL_UserService::getInstance()->findList(0, $numberOfUsers, true);
        $savedPasswordValidation = array();

        foreach ($users as $key => $user) {
            $token = md5(UTIL_String::getRandomString(8, 5));
            $savedPasswordValidation[] = $this->createPasswordValidationObject($user->email, $user->username, false, $token);
            IISPASSWORDCHANGEINTERVAL_BOL_Service::getInstance()->sendNotificationToCurrentUserForChangingPassword($user->getId());
        }

        if($sendEmail) {
            $this->sendEmailForChangingPassword($savedPasswordValidation);
        }
    }

    /**
     * @param $email
     * @param $username
     * @param $valid
     * @param $token
     * @return IISPASSWORDCHANGEINTERVAL_BOL_PasswordValidation
     */
    public function createPasswordValidationObject($email, $username, $valid, $token)
    {
        $passwordValidation = new IISPASSWORDCHANGEINTERVAL_BOL_PasswordValidation();
        $passwordValidation->setUsername($username);
        $passwordValidation->setValid($valid);
        $passwordValidation->setToken($token);
        $passwordValidation->setEmail($email);
        $passwordValidation->setTokentime(time());
        $passwordValidation->setPasswordtime(time());
        $this->save($passwordValidation);
        return $passwordValidation;
    }

    /**
     * @param $users
     */
    public function sendEmailForChangingPassword($passwordValidations)
    {
        $mails = array();

        if (is_array($passwordValidations)) {
            foreach ($passwordValidations as $key => $passwordValidation) {
                $mails[] = $this->createEmailForChangingPassword($passwordValidation->email, $passwordValidation->token, $passwordValidation->username);
            }
        } else {
            $mails[] = $this->createEmailForChangingPassword($passwordValidations->email, $passwordValidations->token, $passwordValidations->username);
        }

        OW::getMailer()->addListToQueue($mails);
    }

    /**
     * @param $email
     * @param $token
     * @return BASE_CLASS_Mail
     */
    public function createEmailForChangingPassword($email, $token, $username)
    {
        $mail = OW::getMailer()->createMail();
        $mail->addRecipientEmail($email);
        $mail->setSubject(OW::getLanguage()->text('iispasswordchangeinterval', 'email_for_changing_password_subject'));
        $mail->setHtmlContent($this->getEmailHTMLContent($token, $username));
        $mail->setTextContent($this->getEmailHTMLContent($token, $username));
        return $mail;
    }

    /**
     * @param $token
     * @return string
     */
    public function getEmailHTMLContent($token, $username)
    {
        $html = '<p>' . OW::getLanguage()->text('iispasswordchangeinterval', 'email_for_changing_password_description', array('username' => $username)) . '</p>';
        $html .= '</br>';
        $html .= OW::getRouter()->urlForRoute('iispasswordchangeinterval.check-validate-password', array('token' => $token));
        return $html;
    }

    public function deleteAllUsersFromPasswordValidation()
    {
        $this->dbo->delete('TRUNCATE TABLE ' . $this->getTableName());
    }

    /**
     * @param $username
     */
    public function setUserPasswordValid($username)
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('username', $username);
        $passwordValidation = $this->findObjectByExample($ex);
        $passwordValidation->setValid(true);
        $passwordValidation->setToken(null);
        $this->save($passwordValidation);
    }

    /**
     * @param $username
     */
    public function setUserPasswordInvalid($username)
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('username', $username);
        $passwordValidation = $this->findObjectByExample($ex);
        if($passwordValidation == null){
            $user = BOL_UserService::getInstance()->findByUsername($username);
            $passwordValidation = $this->createPasswordValidationObject($user->email, $username, false, md5(UTIL_String::getRandomString(8, 5)));
        }else {
            $passwordValidation->setValid(false);
            $passwordValidation->setToken(md5(UTIL_String::getRandomString(8, 5)));
            $passwordValidation->setTokentime(time());
            $this->save($passwordValidation);
        }

        $this->sendEmailForChangingPassword($passwordValidation);
    }

    /**
     * @param $searchValue
     * @return array
     */
    public function getAllUsersValid($searchValue = null)
    {
        $count = 20;
        if(empty($searchValue)) {
            $ex = new OW_Example();
            $ex->andFieldEqual('valid', true);
            $ex->setLimitClause(0,$count);
            $activatedUsers = $this->findListByExample($ex);

            $remainedCount = $count - sizeof($activatedUsers);
            $activeUsersNotInvalidatedYet = $this->getValidUsersFromUserTable($remainedCount);

            $result = array();
            $markedUsername = array();

            foreach($activatedUsers as $activatedUser){
                if(!in_array($activatedUser->username, $markedUsername)){
                    $markedUsername[] = $activatedUser->username;
                    $result[] = $activatedUser;
                }
            }

            foreach($activeUsersNotInvalidatedYet as $activeUserNotInvalidatedYet){
                if(!in_array($activeUserNotInvalidatedYet->username, $markedUsername)){
                    $markedUsername[] = $activeUserNotInvalidatedYet->username;
                    $result[] = $activeUserNotInvalidatedYet;
                }
            }

            return $result;

        }

        $markedUsername = array();
        $result = array();

        $ex = new OW_Example();
        $ex->andFieldLike('username','%'.$searchValue.'%');
        $ex->andFieldEqual('valid', true);
        $ex->setLimitClause(0,$count);
        $resultsByUsername = $this->findListByExample($ex);

        $resultsByEmail = array();
        $remainedCount = $count - sizeof($resultsByUsername);
        if($remainedCount>0) {
            $ex = new OW_Example();
            $ex->andFieldLike('email', '%' . $searchValue . '%');
            $ex->andFieldEqual('valid', true);
            $ex->setLimitClause(0, $remainedCount);
            $resultsByEmail = $this->findListByExample($ex);
        }

        foreach($resultsByUsername as $resultByUsername){
            if(!in_array($resultByUsername->username, $markedUsername)){
                $markedUsername[] = $resultByUsername->username;
                $result[] = $resultByUsername;
            }
        }

        foreach($resultsByEmail as $resultByEmail){
            if(!in_array($resultByEmail->username, $markedUsername)){
                $markedUsername[] = $resultByEmail->username;
                $result[] = $resultByEmail;
            }
        }

        $remainedCount = $count - sizeof($result);
        $activeUsersNotInvalidatedYet = $this->getValidUsersFromUserTable($remainedCount, $searchValue);

        foreach($activeUsersNotInvalidatedYet as $activeUserNotInvalidatedYet){
            if(!in_array($activeUserNotInvalidatedYet->username, $markedUsername)){
                $markedUsername[] = $activeUserNotInvalidatedYet->username;
                $result[] = $activeUserNotInvalidatedYet;
            }
        }

        return $result;
    }

    /***
     * @param $remainedCount
     * @param $searchValue
     * @return array
     */
    public function getValidUsersFromUserTable($remainedCount, $searchValue = null){
        $activeUsersNotInvalidatedYet = array();
        if($remainedCount>0) {
            $invalidatedUsersUsername = $this->getAllUsersInvalidusername();
            if (empty($searchValue)) {
                $ex = new OW_Example();
                if(!empty($invalidatedUsersUsername)) {
                    $ex->andFieldNotInArray('username', $invalidatedUsersUsername);
                }
                $ex->andFieldEqual('emailVerify', 1);
                $ex->setLimitClause('`joinIp` DESC');
                $ex->setLimitClause(0, $remainedCount);
                $activeUsersNotInvalidatedYet = BOL_UserDao::getInstance()->findListByExample($ex);
                return $activeUsersNotInvalidatedYet;
            }

            $ex = new OW_Example();
            $ex->andFieldLike('username', '%' . $searchValue . '%');
            if(!empty($invalidatedUsersUsername)) {
                $ex->andFieldNotInArray('username', $invalidatedUsersUsername);
            }
            $ex->andFieldEqual('emailVerify', 1);
            $ex->setLimitClause('`joinIp` DESC');
            $ex->setLimitClause(0, $remainedCount);
            $resultsByUsername = BOL_UserDao::getInstance()->findListByExample($ex);

            $resultsByEmail = array();
            $remainedCount = $remainedCount - sizeof($resultsByUsername);
            if ($remainedCount > 0) {
                $ex = new OW_Example();
                $ex->andFieldLike('email', '%' . $searchValue . '%');
                if(!empty($invalidatedUsersUsername)) {
                    $ex->andFieldNotInArray('username', $invalidatedUsersUsername);
                }
                $ex->andFieldEqual('emailVerify', 1);
                $ex->setLimitClause('`joinIp` DESC');
                $ex->setLimitClause(0, $remainedCount);
                $resultsByEmail = BOL_UserDao::getInstance()->findListByExample($ex);
            }

            $markedUsername = array();
            foreach($resultsByUsername as $resultByUsername){
                if(!in_array($resultByUsername->username, $markedUsername)){
                    $markedUsername[] = $resultByUsername->username;
                    $activeUsersNotInvalidatedYet[] = $resultByUsername;
                }
            }

            foreach($resultsByEmail as $resultByEmail){
                if(!in_array($resultByEmail->username, $markedUsername)){
                    $markedUsername[] = $resultByEmail->username;
                    $activeUsersNotInvalidatedYet[] = $resultByEmail;
                }
            }
        }

        return $activeUsersNotInvalidatedYet;
    }

    /***
     * @return array
     */
    public function getAllUsersInvalidusername(){
        $result = array();
        $allUsersInvalid = $this->getAllUsersInvalid(null, true);
        foreach($allUsersInvalid as $allUserInvalid){
            $result[] = $allUserInvalid->username;
        }

        return $result;
    }

    /**
     * @param $token
     * @return IISPASSWORDCHANGEINTERVAL_BOL_PasswordValidation
     */
    public function getUserByToken($token)
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('token', $token);
        return $this->findObjectByExample($ex);
    }

    /**
     * @param $searchValue
     * @param $allUsers
     * @return array
     */
    public function getAllUsersInvalid($searchValue = null, $allUsers = false)
    {
        $count = 20;
        if(empty($searchValue)) {
            $ex = new OW_Example();
            $ex->andFieldEqual('valid', false);
            if(!$allUsers) {
                $ex->setLimitClause(0, $count);
            }
            $ex->setOrder('`tokentime` DESC');
            return $this->findListByExample($ex);
        }

        $markedId = array();
        $result = array();

        $ex = new OW_Example();
        $ex->andFieldLike('username','%'.$searchValue.'%');
        $ex->andFieldEqual('valid', false);
        $ex->setLimitClause(0,$count);
        $resultsByUsername = $this->findListByExample($ex);

        $resultsByEmail = array();
        $remainedCount = $count - sizeof($resultsByUsername);
        if($remainedCount>0) {
            $ex = new OW_Example();
            $ex->andFieldLike('email', '%' . $searchValue . '%');
            $ex->andFieldEqual('valid', false);
            $ex->setLimitClause(0, $remainedCount);
            $resultsByEmail = $this->findListByExample($ex);
        }

        foreach($resultsByUsername as $resultByUsername){
            if(!in_array($resultByUsername->id, $markedId)){
                $markedId[] = $resultByUsername->id;
                $result[] = $resultByUsername;
            }
        }

        foreach($resultsByEmail as $resultByEmail){
            if(!in_array($resultByEmail->id, $markedId)){
                $markedId[] = $resultByEmail->id;
                $result[] = $resultByEmail;
            }
        }

        return $result;
    }

    /**
     * @param $username
     * @return IISPASSWORDCHANGEINTERVAL_BOL_PasswordValidation
     */
    public function getUserByUsername($username)
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('username', $username);
        return $this->findObjectByExample($ex);
    }

    /**
     * @return IISPASSWORDCHANGEINTERVAL_BOL_ChangePassword
     */
    public function getCurrentUser()
    {
        return $this->getUserByUsername(OW::getUser()->getUserObject()->getUsername());
    }

    /**
     * @return IISPASSWORDCHANGEINTERVAL_BOL_ChangePassword
     */
    public function updateTimePasswordChanged()
    {
        $passwordValidation = $this->getCurrentUser();
        if ($passwordValidation == null) {
            $passwordValidation = $this->createPasswordValidationObject(OW::getUser()->getEmail(), OW::getUser()->getUserObject()->username, true, null);
        } else {
            $passwordValidation->setPasswordtime(time());
            $passwordValidation->setValid(true);
            $passwordValidation->setToken(null);
            $this->save($passwordValidation);
        }
        return $passwordValidation;
    }
}
