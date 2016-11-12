<?php

class IISCONTROLKIDS_CTRL_Iiscontrolkids extends OW_ActionController
{

    public function index($params)
    {
        if(!OW::getUser()->isAuthenticated()){
            OW::getApplication()->redirect(OW_URL_HOME);
        }
        $service = IISCONTROLKIDS_BOL_Service::getInstance();
        $kids = $service->getKids(OW::getUser()->getId());
        $items = array();
        foreach ($kids as $kid) {
            $user = BOL_UserService::getInstance()->findUserById($kid->kidUserId);
            $items[] = array(
                'username' => $user->username,
                'email' => $user->email,
                'shadowLoginUrl' => OW::getRouter()->urlForRoute('iiscontrolkids.shadow_login_by_parent',array('kidUserId' => $user->getId()))
            );
        }
        $this->assign("items", $items);
    }

    public function shadowLoginByParent($params){
        if(!OW::getUser()->isAuthenticated()){
            OW::getApplication()->redirect(OW_URL_HOME);
        }
        $kid_user_id = $params['kidUserId'];
        $service = IISCONTROLKIDS_BOL_Service::getInstance();
        if($service->isParentExist($kid_user_id, OW::getUser()->getId())){
            $parentId = OW::getUser()->getId();
            $service->logout();
            OW_User::getInstance()->login($kid_user_id);
            $_SESSION['sl_'.$kid_user_id] = $parentId;
        }
        OW::getApplication()->redirect(OW_URL_HOME);
    }

    public function logoutFromShadowLogin(){
        $user = OW::getUser();
        $service = IISCONTROLKIDS_BOL_Service::getInstance();
        if($_SESSION['sl_'.$user->getId()]){
            $parentId = $_SESSION['sl_'.$user->getId()];
            unset($_SESSION['sl_'.$user->getId()]);
            $service->logout();
            OW_User::getInstance()->login($parentId);
            OW::getApplication()->redirect(OW_URL_HOME);
        }
    }
}