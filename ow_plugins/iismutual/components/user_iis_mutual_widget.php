<?php

/**
 * IIS Mutual widget
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @since 1.0
 */
class IISMUTUAL_CMP_UserIisMutualWidget extends BASE_CLASS_Widget
{

    /**
     * @return Constructor.
     */
    public function __construct( BASE_CLASS_WidgetParameter $params )
    {
        parent::__construct();
        $this->assignList($params);
    }

    private function assignList($params)
    {
        $profileOwnerId = (int) $params->additionalParamList['entityId'];
        if(!OW::getUser()->isAuthenticated() || $profileOwnerId == OW::getUser()->getId()){
            OW::getDocument()->addStyleDeclaration('.ow_dnd_widget.profile-IISMUTUAL_CMP_UserIisMutualWidget {display: none;}');
        }else {

            $mutualFriensdId = IISMUTUAL_CLASS_Mutual::getInstance()->getMutualFriends($profileOwnerId, OW::getUser()->getId());
            if (sizeof($mutualFriensdId) >= OW::getConfig()->getValue('iismutual', 'numberOfMutualFriends')) {
                $toolbar = array(array('label' => OW::getLanguage()->text('iismutual', 'view_all', array('number' => sizeof($mutualFriensdId))), 'href' => OW::getRouter()->urlForRoute('iismutual.mutual.firends', array('userId' => $profileOwnerId))));
                $this->assign('toolbar', $toolbar);
            }

            if (sizeof($mutualFriensdId) == 0) {
                $this->assign('empty_list', true);
            } else {
                $this->addComponent('userList', new BASE_CMP_AvatarUserList($mutualFriensdId));
            }
        }
    }

    public static function getStandardSettingValueList()
    {
        return array(
            self::SETTING_SHOW_TITLE => true,
            self::SETTING_WRAP_IN_BOX => true,
            self::SETTING_TITLE => OW_Language::getInstance()->text('iismutual', 'main_menu_item'),
            self::SETTING_ICON => self::ICON_PICTURE
        );
    }

    public static function getAccess()
    {
        return self::ACCESS_MEMBER;
    }
}