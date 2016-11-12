<?php

/**
 * @author Milad Heshmati <milad.heshmati@gmail.com>
 * @package ow_plugins.iisaudio
 * @since 1.0
 */

class IISAUDIO_CTRL_Audio extends OW_ActionController
{

    public function addAudio($params)
    {
        $form = IISAUDIO_BOL_Service::getInstance()->getAddAudioForm();
        $this->addForm($form);

        if(OW::getRequest()->isAjax()) {
            if ($form->isValid($_POST) && OW::getUser()->isAuthenticated()) {
                $values = $form->getValues();
                $service = IISAUDIO_BOL_Service::getInstance();
                $audioName = OW::getUser()->getId() . "_" . UTIL_String::getRandomString(16);
                $audioDir = $service->getAudioFileDirectory($audioName);
                OW::getStorage()->fileSetContent($audioDir, $values["audioFile"]);
                $service->addAudio($values["name"], $audioName);
                exit(json_encode(array('result' => true)));
            }
            exit(json_encode(array('result ' => false)));
        }
        throw new Redirect404Exception();
    }

    public function viewList($params)
    {
        $this->setPageTitle(OW::getLanguage()->text('iisaudio', 'index_page_title'));
        $this->setPageHeading(OW::getLanguage()->text('iisaudio', 'index_page_heading'));

        $page = (!empty($_GET['page']) && intval($_GET['page']) > 0 ) ? $_GET['page'] : 1;
        $count = 5;
        $first = ($page - 1) * $count;


        $this->addForm(IISAUDIO_BOL_Service::getInstance()->getAddAudioForm());
        if (OW::getUser()->isAuthenticated()) {
           $service = IISAUDIO_BOL_Service::getInstance();
            $allAudiosListOfUser = IISAUDIO_BOL_Service::getInstance()->findAudiosByUserId(OW::getUser()->getId());
            $sizeOfAllAudiosOfUser = 0;
            if($allAudiosListOfUser!=null){
                $sizeOfAllAudiosOfUser = sizeof($allAudiosListOfUser);
            }
            $list = IISAUDIO_BOL_Service::getInstance()->findListOrderedByDate(OW::getUser()->getId(), $first, $count);
            $tplList = array();
            foreach ($list as $listItem) {
                $autherUserName = BOL_UserService::getInstance()->findUserById($listItem->userId)->getUsername();
                $tplList[] = array(
                    "title" => $listItem->title,
                    "autherName" => $autherUserName,
                    "autherUrl" => OW::getRouter()->urlForRoute('base_user_profile', array('username' => $autherUserName)),
                    "addDateTime" => UTIL_DateTime::formatDate($listItem->addDateTime),
                    "audioUrl" => file_get_contents($service->getAudioFileUrl($listItem->hash)),
                    'deleteUrl' => "if(confirm('".OW::getLanguage()->text('iisaudio','delete_item_warning')."')){location.href='" . OW::getRouter()->urlForRoute('iisaudio-audio-delete-item', array('id' => $listItem->getId())) . "';}"

                );
            }
            $this->assign("list", $tplList);


            $paging = new BASE_CMP_Paging($page, ceil($sizeOfAllAudiosOfUser / $count), 5);
            $this->addComponent('paging', $paging);

        } else {
            throw new Redirect404Exception();
        }
    }

    public function deleteItem($params)
    {
        if(!isset($params['id']) && !OW::getUser()->isAuthenticated()){
            throw new Redirect404Exception();
        }else {
            $service = IISAUDIO_BOL_Service::getInstance();
            $audio = $service->findAudiosById($params['id']);
            if(OW::getUser()->getId() != $audio->userId){
                throw new Redirect404Exception();
            }else {
                $service->deleteDatabaseRecord($params['id']);
                OW::getFeedback()->info(OW::getLanguage()->text('iisaudio', 'database_record_deleted'));
            }

        }
        $this->redirect(OW::getRouter()->urlForRoute('iisaudio-audio'));
    }



}