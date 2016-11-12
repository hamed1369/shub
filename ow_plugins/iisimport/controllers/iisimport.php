<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisimport.controllers
 * @since 1.0
 */
class IISIMPORT_CTRL_Iisimport extends OW_ActionController
{
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = IISIMPORT_BOL_Service::getInstance();
    }

    public function index($params = NULL)
    {
        $this->service->checkUserAuth();

        //Yahoo Account
        if($this->service->adminAccessToType('yahoo')) {
            if ($this->service->accessToAccount(OW::getUser()->getId(), 'yahoo')) {
                $callback = OW::getRouter()->urlForRoute('iisimport.yahoo.callback');
                $retarr = $this->service->get_request_token(OW::getConfig()->getValue('iisimport', 'yahoo_id'), OW::getConfig()->getValue('iisimport', 'yahoo_secret'), $callback);
                if (!empty($retarr)) {
                    list($info, $headers, $body, $body_parsed) = $retarr;
                    if ($info['http_code'] == 200 && !empty($body)) {
                        $_SESSION['oauth_token'] = $body_parsed['oauth_token'];
                        $_SESSION['oauth_token_secret'] = $body_parsed['oauth_token_secret'];
                        $this->assign('importYahooContactsUrl', urldecode($body_parsed['xoauth_request_auth_url']));
                    }
                }
            } else {
                $this->assign('importYahooContactsUrl', OW::getRouter()->urlForRoute('iisimport.import.request', array('type' => 'yahoo')));
            }
            $this->assign('importYahooContactsImageUrl', OW::getPluginManager()->getPlugin('iisimport')->getStaticUrl() . 'images/' . 'yahoo.png');
        }

        //Google Account
        if($this->service->adminAccessToType('google')) {
            if ($this->service->accessToAccount(OW::getUser()->getId(), 'google')) {
                $callback = OW::getRouter()->urlForRoute('iisimport.google.callback');
                $oauth = new IISIMPORT_CLASS_GmailOath(OW::getConfig()->getValue('iisimport', 'google_id'), OW::getConfig()->getValue('iisimport', 'google_secret'), $callback);
                $getcontact = new IISIMPORT_CLASS_GmailGetContacts();
                $this->assign('importGoogleContactsUrl', $getcontact->get_request_token($oauth, true, false));
            } else {
                $this->assign('importGoogleContactsUrl', OW::getRouter()->urlForRoute('iisimport.import.request', array('type' => 'google')));
            }
            $this->assign('importGoogleContactsImageUrl', OW::getPluginManager()->getPlugin('iisimport')->getStaticUrl() . 'images/' . 'google.png');
        }

        $this->assign('one_account_exist',$this->service->adminAccessToType('yahoo') || $this->service->adminAccessToType('google'));

        $cssDir = OW::getPluginManager()->getPlugin("iisimport")->getStaticCssUrl();
        OW::getDocument()->addStyleSheet($cssDir . "iisimport.css");
    }

    public function googleCallBack($params = NULL)
    {
        $this->service->checkUserAuth();
        if (OW::getRequest()->isPost() && isset($_POST['access_token']) && isset($_POST['token_type']) && isset($_POST['expires_in'])) {
            $access_token = $_POST['access_token'];
            $token_type = $_POST['token_type'];
            $expires_in = $_POST['expires_in'];
            $error = false;
            if ($access_token == -1 || $token_type == -1 || $expires_in == -1) {
                $error = true;
            } else {
                $findContactList = false;
                $callback = OW::getRouter()->urlForRoute('iisimport.google.callback');
                $oauth = new IISIMPORT_CLASS_GmailOath(OW::getConfig()->getValue('iisimport', 'google_id'), OW::getConfig()->getValue('iisimport', 'google_secret'), $callback);
                $getcontact = new IISIMPORT_CLASS_GmailGetContacts();
                $entries = $getcontact->callcontact($oauth, $access_token, OW::getConfig()->getValue('iisimport', 'google_secret'), true);
                foreach ($entries as $k => $value) {
                    foreach ($value['gd$email'] as $email) {
                        $user_email = $email["address"];
                        if ($this->service->getUser(OW::getUser()->getId(), $user_email, 'google') == null && $user_email != OW::getUser()->getEmail()) {
                            $this->service->addUser(OW::getUser()->getId(), $user_email, 'google');
                        }
                    }
                    $findContactList = true;
                }

                if ($error) {
                    OW::getFeedback()->info(OW::getLanguage()->text('iisimport', 'error_find_list'));
                    exit(json_encode(array('url' => OW::getRouter()->urlForRoute('iisimport.import.index'))));
                } else if ($findContactList) {
                    $this->service->addOrUpdateUserTry(OW::getUser()->getId(), 'google');
                    OW::getFeedback()->info(OW::getLanguage()->text('iisimport', 'find_list_successfully'));
                    exit(json_encode(array('url' => OW::getRouter()->urlForRoute('iisimport.import.request', array('type' => 'google')))));
                } else {
                    OW::getFeedback()->info(OW::getLanguage()->text('iisimport', 'find_list_empty'));
                    exit(json_encode(array('url' => OW::getRouter()->urlForRoute('iisimport.import.index'))));
                }
            }
        } else {
            $this->assign('importGoogleContactsImageUrl', OW::getPluginManager()->getPlugin('iisimport')->getStaticUrl() . 'images/' . 'google.png');
            $jsDir = OW::getPluginManager()->getPlugin("iisimport")->getStaticJsUrl();
            OW::getDocument()->addScript($jsDir . "iisimport.js");
            OW::getDocument()->addScriptDeclaration('getAccessToken()');
        }
    }

    public function yahooCallBack($params = NULL)
    {
        $this->service->checkUserAuth();
        $oauth_verifier = $_GET['oauth_verifier'];
        $findContactList = false;
        $error = false;
        $retarr = $this->service->get_access_token_yahoo(OW::getConfig()->getValue('iisimport', 'yahoo_id'), OW::getConfig()->getValue('iisimport', 'yahoo_secret'), $_SESSION['oauth_token'], $_SESSION['oauth_token_secret'], $oauth_verifier);
        if (!empty($retarr)) {
            list($info, $headers, $body, $body_parsed) = $retarr;
            if ($info['http_code'] == 200 && !empty($body)) {
                $guid = $body_parsed['xoauth_yahoo_guid'];
                $access_token = $this->service->rfc3986_decode($body_parsed['oauth_token']);
                $access_token_secret = $body_parsed['oauth_token_secret'];
                $emails = IISIMPORT_BOL_Service::getInstance()->callcontact_yahoo(OW::getConfig()->getValue('iisimport', 'yahoo_id'), OW::getConfig()->getValue('iisimport', 'yahoo_secret'), $guid, $access_token, $access_token_secret);
                foreach ($emails as $email) {
                    if ($this->service->getUser(OW::getUser()->getId(), $email, 'yahoo') == null && $email != OW::getUser()->getEmail()) {
                        $this->service->addUser(OW::getUser()->getId(), $email, 'yahoo');
                    }
                    $findContactList = true;
                }
            } else {
                $error = true;
            }
        } else {
            $error = true;
        }

        if ($error) {
            OW::getFeedback()->info(OW::getLanguage()->text('iisimport', 'error_find_list'));
            $this->redirect(OW::getRouter()->urlForRoute('iisimport.import.index'));
        } else if ($findContactList) {
            $this->service->addOrUpdateUserTry(OW::getUser()->getId(), 'yahoo');
            OW::getFeedback()->info(OW::getLanguage()->text('iisimport', 'find_list_successfully'));
            $this->redirect(OW::getRouter()->urlForRoute('iisimport.import.request', array('type' => 'yahoo')));
        } else {
            OW::getFeedback()->info(OW::getLanguage()->text('iisimport', 'find_list_empty'));
            $this->redirect(OW::getRouter()->urlForRoute('iisimport.import.index'));
        }
    }


    public function request($params = NULL)
    {
        $this->service->checkUserAuth();
        if ($params['type'] == 'yahoo' || $params['type'] == 'google') {
            $classFriendsExist = class_exists(FRIENDS_BOL_Service);
            if (!$classFriendsExist) {
                $this->redirect(OW::getRouter()->urlForRoute('iisimport.import.invitation', array('type' => $params['type'])));
            } else {
                $service = IISIMPORT_BOL_Service::getInstance();
                $emails = $service->getEmailsByUserId(OW::getUser()->getId(), $params['type']);
                $emailsInformation =  $this->service->getRegisteredExceptFriendEmails($emails,OW::getUser()->getId());
                if (OW::getRequest()->isPost()) {
                    $sendToAnyOne = false;
                    foreach ($emailsInformation as $emailInformation) {
                        $email = $emailInformation['email'];
                        if ($_POST[str_replace('.', '_', $email)] == 'on') {
                            FRIENDS_BOL_Service::getInstance()->request(OW::getUser()->getId(), BOL_UserService::getInstance()->findByEmail($email)->getId());
                            $sendToAnyOne = true;
                        }
                    }

                    if ($sendToAnyOne) {
                        OW::getFeedback()->info(OW::getLanguage()->text('iisimport', 'send_successfully'));
                    } else {
                        OW::getFeedback()->info(OW::getLanguage()->text('iisimport', 'send_empty'));
                    }

                    $this->redirect(OW::getRouter()->urlForRoute('iisimport.import.invitation', array('type' => $params['type'])));
                }

                if (sizeof($emailsInformation) == 0) {
                    $this->redirect(OW::getRouter()->urlForRoute('iisimport.import.invitation', array('type' => $params['type'])));
                } else {
                    $this->assign('requestFormName', 'requestForm');
                    $this->assign('actionRequestForm', OW::getRouter()->urlForRoute('iisimport.import.request', array('type' => $params['type'])));
                    $this->assign('skipUrl', OW::getRouter()->urlForRoute('iisimport.import.invitation', array('type' => $params['type'])));
                    $this->assign('emailsInformation', $emailsInformation);

                    $js = '$("#select_all_imported").change(function () {$("input:checkbox").prop(\'checked\', $(this).prop("checked"));});';
                    OW::getDocument()->addScriptDeclaration($js);
                }
            }
        }
    }

    public function invitation($params = NULL)
    {
        $this->service->checkUserAuth();
        if ($params['type'] == 'yahoo' || $params['type'] == 'google') {
            $service = IISIMPORT_BOL_Service::getInstance();
            $emails = $service->getEmailsByUserId(OW::getUser()->getId(), $params['type']);
            $emailsInformation =  $this->service->getNotSubscribedUserEmails($emails);
            if (OW::getRequest()->isPost()) {
                $sendToAnyOne = false;
                foreach ($emailsInformation as $emailInformation) {
                    $email = $emailInformation['email'];
                    if ($_POST[str_replace('.', '_', $email)] == 'on') {
                        IISIMPORT_BOL_Service::getInstance()->sendEmailForInvitation($email, OW::getUser()->getUserObject()->username);
                        $sendToAnyOne = true;
                    }
                }

                if ($sendToAnyOne) {
                    OW::getFeedback()->info(OW::getLanguage()->text('iisimport', 'send_successfully'));
                } else {
                    OW::getFeedback()->info(OW::getLanguage()->text('iisimport', 'send_empty'));
                }
                $this->redirect(OW::getRouter()->urlForRoute('iisimport.import.index'));
            }

            if (sizeof($emailsInformation) == 0) {
                $this->assign('iisimport', 'list_empty');
            }

            $this->assign('invitationFormName', 'invitationForm');
            $this->assign('actionInvitationForm', OW::getRouter()->urlForRoute('iisimport.import.invitation', array('type' => $params['type'])));
            $this->assign('emailsInformation', $emailsInformation);
            $this->assign('skipUrl', OW_URL_HOME);
            $js = '$("#select_all_imported").change(function () {$("input:checkbox").prop(\'checked\', $(this).prop("checked"));});';
            OW::getDocument()->addScriptDeclaration($js);
        }
    }
}
