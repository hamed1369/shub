<?php

class COVERPHOTO_CMP_FormsFloatBox extends OW_Component
{
    public function __construct()
    {
        parent::__construct();

        OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('coverphoto')->getStaticCssUrl() . 'coverphoto.css');
        $service = COVERPHOTO_BOL_Service::getInstance();
        $user_cover =  COVERPHOTO_BOL_Service::getInstance()->getUserCover(OW::getUser()->getId());
        $list = $service->findList(OW::getUser()->getId());
        $tplList = array();
        foreach ( $list as $listItem )
        {
            /* @var $listItem COVERPHOTO_BOL_Record */
            $autherUserName = BOL_UserService::getInstance()->findUserById($listItem->userId)->getUsername();
            $tplList[] = array(
                "title" => $listItem->title,
                "AutherName" => $autherUserName,
                "AutherUrl" => OW::getRouter()->urlForRoute('base_user_profile', array('username' => $autherUserName)),
                "addDateTime" => UTIL_DateTime::formatDate($listItem->addDateTime),
                "coverPhotoImageUrl" => OW::getStorage()->getFileUrl(OW::getPluginManager()->getPlugin('coverphoto')->getUserFilesDir().$listItem->hash),
                "useThisCoverIcon" => OW::getPluginManager()->getPlugin('coverphoto')->getStaticUrl().'img/' . 'choose.png',
                "deleteThisCoverIcon" => OW::getPluginManager()->getPlugin('coverphoto')->getStaticUrl().'img/' . 'remove.png',
                "deleteUrl" => OW::getRouter()->urlForRoute('coverphoto-forms-delete-item-float', array('id'=>$listItem->getId())),
                "useCoverUrl" => OW::getRouter()->urlForRoute('coverphoto-forms-use-item-float', array('id'=>$listItem->getId())),
                "isCurrent" => ($user_cover && $user_cover->id == $listItem->id)?true:false
            );
        }

        $this->assign("list", $tplList);
        $this->assign("coverPhotosUrl", OW::getRouter()->urlForRoute('coverphoto-index'));
        $this->assign("new_cover_icon", OW::getPluginManager()->getPlugin('coverphoto')->getStaticUrl().'img/' . 'new.png');
        $this->assign("is_current_icon", OW::getPluginManager()->getPlugin('coverphoto')->getStaticUrl().'img/' . 'is_current.png');
    }

}
