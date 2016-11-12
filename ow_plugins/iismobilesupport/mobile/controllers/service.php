<?php

class IISMOBILESUPPORT_MCTRL_Service extends OW_MobileActionController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($params)
    {
        if (isset($params['key'])) {
            if ($params['key'] == 'information') {
                $information = array();
                $information['menuTypeMainItems'] = $this->getMenuTypeMainItems();
                $information['menuTypeBottomItems'] = $this->getMenuTypeBottomItems();
                $information['languageItems'] = $this->findActiveLanguages();
                $information['userInformation'] = $this->getUserInformation();
                exit(json_encode($information));
            }
        }
        exit();
    }

    public function getUserInformation(){
        $information = array();
        if(!OW::getUser()->isAuthenticated()){
            return $information;
        }

        $information['avatarUrl'] = BOL_AvatarService::getInstance()->getAvatarUrl(OW::getUser()->getId());
        $information['email'] = OW::getUser()->getEmail();
        $information['name'] = BOL_UserService::getInstance()->getDisplayName(OW::getUser()->getId());
        $information['profileUrl'] = OW::getRouter()->urlForRoute('base_user_profile', array('username' => OW::getUser()->getUserObject()->username));
        return $information;
    }

    public function getMenuTypeMainItems(){
        $menuTypeMainItems = array();
        $items = array();
        if (OW::getApplication()->getContext() == OW::CONTEXT_MOBILE) {
            $menuTypeMainItems = BOL_NavigationService::getInstance()->getMenuItems(BOL_NavigationService::getInstance()->findMenuItems(BOL_MobileNavigationService::MENU_TYPE_TOP));
        } else {
            $menuTypeMainItems = BOL_NavigationService::getInstance()->getMenuItems(BOL_NavigationService::getInstance()->findMenuItems(BOL_NavigationService::MENU_TYPE_MAIN));
        }

        foreach($menuTypeMainItems as $menuTypeMainItem){
            $items[] = array('label' => $menuTypeMainItem->getLabel(), 'url' => $menuTypeMainItem->getUrl());
        }

        return $items;
    }

    public function getMenuTypeBottomItems(){
        $menuTypeBottomItems = array();
        $items = array();
        if (OW::getApplication()->getContext() == OW::CONTEXT_MOBILE) {
            $menuTypeBottomItems = BOL_NavigationService::getInstance()->getMenuItems(BOL_NavigationService::getInstance()->findMenuItems(BOL_MobileNavigationService::MENU_TYPE_BOTTOM));
        } else {
            $menuTypeBottomItems = BOL_NavigationService::getInstance()->getMenuItems(BOL_NavigationService::getInstance()->findMenuItems(BOL_NavigationService::MENU_TYPE_BOTTOM));
        }

        foreach($menuTypeBottomItems as $menuTypeBottomItem){
            $items[] = array('label' => $menuTypeBottomItem->getLabel(), 'url' => $menuTypeBottomItem->getUrl());
        }

        return $items;
    }

    public function findActiveLanguages()
    {
        $languages = BOL_LanguageService::getInstance()->getLanguages();
        $session_language_id = BOL_LanguageService::getInstance()->getCurrent()->getId();

        $active_languages = array();

        foreach ($languages as $id => $language) {
            if ($language->status == 'active') {
                $tag = $this->parseCountryFromTag($language->tag);
                if ($tag['label'] == 'fa') {
                    $tag['label'] = 'فا';
                }

                $active_lang = array(
                    'id' => $language->id,
                    'label' => $tag['label'],
                    'order' => $language->order,
                    'tag' => $language->tag,
                    'url' => OW::getRequest()->buildUrlQueryString(null, array("language_id" => $language->id)),
                    'is_current' => false
                );

                $active_lang['url'] = str_replace('"', '%22', $active_lang['url']);
                $active_lang['url'] = str_replace('\'', '\\\'', $active_lang['url']);

                if ($session_language_id == $language->id) {
                    $active_lang['is_current'] = true;
                }

                $active_languages[] = $active_lang;
            }
        }

        return $active_languages;
    }

    protected function parseCountryFromTag($tag)
    {
        $tags = preg_match("/^([a-zA-Z]{2})$|^([a-zA-Z]{2})-([a-zA-Z]{2})(-\w*)?$/", $tag, $matches);

        if (empty($matches)) {
            return array("label" => $tag, "country" => "");
        }
        if (!empty($matches[1])) {
            $country = strtolower($matches[1]);
            return array("label" => $matches[1], "country" => "_" . $country);
        } else if (!empty($matches[2])) {
            $country = strtolower($matches[3]);
            return array("label" => $matches[2], "country" => "_" . $country);
        }

        return "";
    }
}

