<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iissecurityessentials.bol
 * @since 1.0
 */
class IISSECURITYESSENTIALS_BOL_Service
{
    private static $classInstance;
    public static $PRIVACY_EVERYBODY = 'everybody';
    public static $PRIVACY_ONLY_FOR_ME = 'only_for_me';
    public static $PRIVACY_FRIENDS_ONLY = 'friends_only';

    public static function getInstance()
    {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private $questionPrivacy;

    private function __construct()
    {
        $this->questionPrivacy = IISSECURITYESSENTIALS_BOL_QuestionPrivacyDao::getInstance();
    }

    /***
     * @param $userId
     * @param $questionId
     * @return mixed
     */
    public function getQuestionPrivacy($userId, $questionId){
        return $this->questionPrivacy->getQuestionPrivacy($userId, $questionId);
    }

    /***
     * @param $userId
     * @param $questionId
     * @param $privacy
     * @return IISSECURITYESSENTIALS_BOL_QuestionPrivacy
     */
    public function setQuestionPrivacy($userId, $questionId, $privacy){
        return $this->questionPrivacy->setQuestionPrivacy($userId, $questionId,$privacy);
    }

    /***
     * @param $userIds
     * @param $privacy
     * @param $questionId
     * @return array
     */
    public function getQuestionsPrivacyByExceptPrivacy($userIds, $privacy, $questionId){
        return $this->questionPrivacy->getQuestionsPrivacyByExceptPrivacy($userIds, $privacy, $questionId);
    }

    public function getSections($currentSection = null){
        if($currentSection==null){
            $currentSection = 1;
        }

        $sectionsInformation = array();

        for ($i = 1; $i <= 2; $i++) {
            $sections[] = array(
                'sectionId' => $i,
                'active' => $currentSection == $i ? true : false,
                'url' => OW::getRouter()->urlForRoute('iissecurityessentials.admin.currentSection', array('currentSection' => $i)),
                'label' => $this->getPageHeaderLabel($i)
            );
        }

        $sectionsInformation['sections'] = $sections;
        $sectionsInformation['currentSection'] = $currentSection;
        return $sectionsInformation;
    }

    public function getPageHeaderLabel($sectionId)
    {
        if ($sectionId == 1) {
            return OW::getLanguage()->text('iissecurityessentials', 'general_setting');
        } else if ($sectionId == 2) {
            return OW::getLanguage()->text('iissecurityessentials', 'privacy_setting');
        }
    }


    public function onBeforeUsersInformationRender(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['userIdList']) && isset($params['questionList'])){
            $questionList = $params['questionList'];
            $userIdList = $params['userIdList'];
            $notGrantUsersWithPublicSexType = array();
            $qSex = BOL_QuestionService::getInstance()->findQuestionByName('sex');
            $usersWithoutPublicSexType = IISSECURITYESSENTIALS_BOL_Service::getInstance()->getQuestionsPrivacyByExceptPrivacy($userIdList, self::$PRIVACY_EVERYBODY, $qSex->id);
            foreach($usersWithoutPublicSexType as $userWithoutPublicSexType){
                $notGrantUsersWithPublicSexType[] = $userWithoutPublicSexType->userId;
            }

            $notGrantUsersWithPublicBirthdateType = array();
            $qBdate = BOL_QuestionService::getInstance()->findQuestionByName('birthdate');
            $usersWithoutPublicBirthdateType = IISSECURITYESSENTIALS_BOL_Service::getInstance()->getQuestionsPrivacyByExceptPrivacy($userIdList, self::$PRIVACY_EVERYBODY, $qBdate->id);
            foreach($usersWithoutPublicBirthdateType as $userWithoutPublicBirthdateType){
                $notGrantUsersWithPublicBirthdateType[] = $userWithoutPublicBirthdateType->userId;
            }

            $newQuestionList = array();
            foreach ( $questionList as $uid => $question )
            {
                if(in_array($uid, $notGrantUsersWithPublicSexType)){
                    unset($question['sex']);
                }

                if(in_array($uid, $notGrantUsersWithPublicBirthdateType)){
                    unset($question['birthdate']);
                }

                $newQuestionList[$uid] = $question;
            }
            $event->setData(array('questionList' => $newQuestionList));
        }
    }


    public function onBeforePrivacyItemAdd(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['key'])){
            $value = $this->getAdminDefaultValueOfPrivacy($params['key']);
            if($value!=null){
                $event->setData(array('value' => $value));
            }
        }
    }

    public function onBeforeEmailVerifyFormRender( OW_Event $event )
    {
        $params = $event->getParams();
        if(isset($params['this'])){
            if(isset($params['page']) && $params['page']=='verifyForm'){
                $params['this']->assign('verifyLater', '</br><p class="ow_center"><a class="ow_lbutton" href="' . OW::getRouter()->urlForRoute('base_email_verify') . '">' . OW::getLanguage()->text('iissecurityessentials', 'verify_using_resend_email') . '</a></p>');
            }else {
                $params['this']->assign('verifyLater', '</br><p class="ow_center"><a class="ow_lbutton" href="' . OW::getRouter()->urlForRoute('base_email_verify_code_form') . '">' . OW::getLanguage()->text('iissecurityessentials', 'verify_using_code') . '</a></p></br><p class="ow_center"><a class="ow_lbutton" href="' . OW::getRouter()->urlForRoute('base_sign_out') . '">' . OW::getLanguage()->text('iissecurityessentials', 'verify_later') . '</a></p>');
            }
        }
    }

    public function onBeforeQuestionsDataProfileRender( OW_Event $event )
    {
        $params = $event->getParams();
        $ownerId = $params['userId'];
        $questions = $params['questions'];
        if(isset($params['questions']) && isset($params['userId']) && isset($params['component'])){
            $service = IISSECURITYESSENTIALS_BOL_Service::getInstance();
            $questionsPrivacyButton = array();
            $questionsPrivacyIgnoreList = array();
            $actionType = 'questionsPrivacy';
            $change_privacy_label = OW::getLanguage()->text('iissecurityessentials', 'change_privacy_label');
            foreach($questions as $question){
                $privacy = $service->getQuestionPrivacy($ownerId, $question['id']);
                if($privacy == null){
                    $privacy = self::$PRIVACY_EVERYBODY;
                }

                $privacyButton = array('label' => $this->getPrivacyLabelByFeedId($privacy, $ownerId),
                    'imgSrc' => OW::getPluginManager()->getPlugin('iissecurityessentials')->getStaticUrl() . 'images/' . $privacy . '.png');
                if ($ownerId == OW::getUser()->getId()) {
                    $privacyButton['onClick'] = 'javascript:showAjaxFloatBoxForChangePrivacy(\'' . $question['id'] . '\', \'' . $change_privacy_label . '\',\''. $actionType .'\',\''.$ownerId.'\')';
                    $privacyButton['id'] = 'sec-' . $question['id'] . '-' . $ownerId;
                }

                if(!$this->checkPrivacyOfObject($privacy, $ownerId, null, false)){
                    $questionsPrivacyIgnoreList[$question['id']] = false;
                }else if(OW::getUser()->isAuthenticated() && $ownerId==OW::getUser()->getId()){
                    $questionsPrivacyButton[$question['id']] = $privacyButton;
                }
            }
            $params['component']->assign('questionsPrivacyIgnoreList',$questionsPrivacyIgnoreList);
            $params['component']->assign('questionsPrivacyButton',$questionsPrivacyButton);
            $params['component']->assign('isOwner',OW::getUser()->isAuthenticated() && $ownerId==OW::getUser()->getId());

        }
    }


    public function onBeforeAlbumCreateForStatusUpdate( OW_Event $event )
    {
        $params = $event->getParams();
        if (isset($params['albumName'])) {
            $count = 0;
            while($count<20){
                $randomName = $params['albumName'] . ' ' . rand(0,9999999999);
                $albumName = PHOTO_BOL_PhotoAlbumService::getInstance()->findAlbumByName($randomName, OW::getUser()->getId());
                if($albumName==null){
                    $event->setData(array('albumName' => $randomName));
                    break;
                }
                $count++;
            }
        }
    }

    public function onAfterLastPhotoRemoved( OW_Event $event )
    {
        $params = $event->getParams();
        if (isset($params['photoIdList']) && isset($params['fromAlbumLastPhoto'])) {
            if(in_array($params['fromAlbumLastPhoto']->id, $params['photoIdList'])){
                $fromAlbumLastPhoto = PHOTO_BOL_PhotoDao::getInstance()->getLastPhoto($params['fromAlbumLastPhoto']->albumId, $params['photoIdList']);
                $event->setData(array('fromAlbumLastPhoto' => $fromAlbumLastPhoto));
            }
        }
    }

    public function eventAfterPhotoMove( OW_Event $event )
    {
        $params = $event->getParams();
        if(isset($params['toAlbum']) && isset($params['fromAlbum']) && isset($params['photoIdList'])){
            $privacyOfToAlbum = $this->getPrivacyOfAlbum($params['toAlbum'], $params['photoIdList']);
            $privacyOfFromAlbum = $this->getPrivacyOfAlbum($params['fromAlbum']);
            foreach($params['photoIdList'] as $photoId) {
                $photo = PHOTO_BOL_PhotoService::getInstance()->findPhotoById($photoId);
                if($privacyOfToAlbum==null){
                    if(isset($_REQUEST['statusPrivacy'])){
                        $privacyOfToAlbum = $this->validatePrivacy($_REQUEST['statusPrivacy']);
                    }else{
                        $privacyOfToAlbum = $photo->privacy;
                    }
                }
                $this->updatePrivacyOfPhoto($photo->id, $privacyOfToAlbum, OW::getUser()->getId());
            }

            $actionIds = $this->findActionOfDependenciesPhoto($params['toAlbum']);
            $this->updateNewsFeedActivitiesByActionIds($actionIds, $privacyOfToAlbum, OW::getUser()->getId());

            if($privacyOfFromAlbum!=null){
                $actionIds = $this->findActionOfDependenciesPhoto($params['fromAlbum']);
                $this->updateNewsFeedActivitiesByActionIds($actionIds, $privacyOfFromAlbum, OW::getUser()->getId());
            }
        }
    }

    public function findActionOfDependenciesPhoto($albumId){
        $actionIds = array();

        $count = PHOTO_BOL_PhotoService::getInstance()->countAlbumPhotos($albumId, array());
        $photosOfAlbum = PHOTO_BOL_PhotoService::getInstance()->findPhotoListByAlbumId($albumId,1,$count);
        foreach($photosOfAlbum as $photoItem){
            $action = NEWSFEED_BOL_Service::getInstance()->findAction('multiple_photo_upload', $photoItem['uploadKey']);
            if($action!=null) {
                $actionIds[] = $action->id;
            }

            $action = NEWSFEED_BOL_Service::getInstance()->findAction('multiple_photo_upload', $photoItem['id']);
            if($action!=null) {
                $actionIds[] = $action->id;
            }

            $action = NEWSFEED_BOL_Service::getInstance()->findAction('photo_comments', $photoItem['uploadKey']);
            if($action!=null) {
                $actionIds[] = $action->id;
            }

            $action = NEWSFEED_BOL_Service::getInstance()->findAction('photo_comments', $photoItem['id']);
            if($action!=null){
                $actionIds[] = $action->id;
                return $actionIds;
            }

        }
        return $actionIds;
    }

    public function check_permission( BASE_CLASS_EventCollector $event )
    {
        $params = $event->getParams();
        if(isset($params['action']) && $params['action']=='view_my_feed'){
            $privacies = array(self::$PRIVACY_EVERYBODY, self::$PRIVACY_FRIENDS_ONLY, self::$PRIVACY_ONLY_FOR_ME, null);
            foreach($privacies as $privacy){
                $data = array($privacy => array('blocked' => false));
                $event->add($data);
            }

        }
    }

    public function onBeforeFeedActivity( OW_Event $event )
    {
        $params = $event->getParams();
        if(isset($params['activityType'])){
            $activityType = $params['activityType'];
            if(in_array($activityType,array('like','comment'))){
                $event->setData(array('createFeed' => false));
            }else{
                if(isset($params['actionId'])){
                    $action = NEWSFEED_BOL_Service::getInstance()->findActionById($params['actionId']);
                    if($action!=null && $action->entityType=='friend_add'){
                        $event->setData(array('createFeed' => false));
                    }
                }
            }
        }

    }

    public function getActionPrivacy( OW_Event $event )
    {
        $params = $event->getParams();

        if (isset($params['ownerId']) && isset($params['action']) && isset($_REQUEST['statusPrivacy']) && ($params['action']=='photo_view_album' || $params['action']=='video_view_video'))
        {
            if(isset($_REQUEST['album-name']) && isset($_REQUEST['album']) && $_REQUEST['album-name']==$_REQUEST['album']){
                $album = PHOTO_BOL_PhotoAlbumService::getInstance()->findAlbumByName($_REQUEST['album-name'],$params['ownerId']);
                $privacy= $this->getPrivacyOfAlbum($album->id);
                if($privacy!=null){
                    $event->setData(array('privacy' => $privacy));
                }else{
                    $event->setData(array('privacy' => $this->validatePrivacy($_REQUEST['statusPrivacy'])));
                }
            }else{
                $event->setData(array('privacy' => $this->validatePrivacy($_REQUEST['statusPrivacy'])));
            }
        }
    }

    public function onBeforeVideoUploadFormRenderer(OW_Event $event)
    {
        $params = $event->getParams();
        if(isset($params['form'])){
            $form = $params['form'];
            $form->addElement($this->createStatusPrivacyElement('video_default_privacy'));
        }
    }

    public function onBeforeVideoUploadComponentRenderer(OW_Event $event)
    {
        $params = $event->getParams();
        if(isset($params['form']) && isset($params['component'])){
            $form = $params['form'];
            if($form->getElement('statusPrivacy')!=null){
                $params['component']->assign('statusPrivacyField',true);
            }
        }
    }

    public function getActionValueOfPrivacy($privacyKey, $userId){
        if(OW::getUser()->isAuthenticated() && class_exists('PRIVACY_BOL_ActionService')) {
            $userPrivacy = PRIVACY_BOL_ActionService::getInstance()->getActionValue($privacyKey, $userId);
            if($userPrivacy!=null){
                return $userPrivacy;
            }
        }
        $adminValue = OW::getConfig()->getValue('iissecurityessentials', $privacyKey);
        if($adminValue!=null){
            return $adminValue;
        }
        return self::$PRIVACY_FRIENDS_ONLY;
    }


    public function onBeforePhotoUploadFormRenderer(OW_Event $event)
    {
        $params = $event->getParams();
        if(isset($params['form'])){
            $form = $params['form'];
            $form->addElement($this->createStatusPrivacyElement('photo_default_privacy', $params));
            if(isset($params['this'])){
                $params['this']->assign('statusPrivacy',true);
            }
        }
    }

    public function onBeforeCreateFormUsingFieldPrivacy(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['privacyKey'])){
            $event->setData(array('privacyElement' => $this->createStatusPrivacyElement($params['privacyKey'])));
        }
    }

    public function getPrivacyOfAlbum($albumId, $excludeIds = array()){
        $photosOfAlbum = PHOTO_BOL_PhotoService::getInstance()->findPhotoListByAlbumId($albumId,1,1,$excludeIds);
        if(sizeof($photosOfAlbum)>0){
            return $photosOfAlbum[0]['privacy'];
        }

        return null;
    }

    public function onReadyResponseOfPhoto(OW_Event $event){
        $data = $event->getData();
        if(isset($data['data']['photoList'])){
            $change_privacy_label = OW::getLanguage()->text('iissecurityessentials', 'change_privacy_label');
            $photos = array();
            foreach($data['data']['photoList'] as $photo){
                $objectId = $photo['id'];
                $feedId = $photo['userId'];
                $privacy = null;
                if(isset($photo['privacy'])) {
                    $privacy = $photo['privacy'];
                    $actionType = 'photo_comments';
                }else if(!isset($photo['albumId']) && isset($photo['albumUrl'])){
                    $albumPrivacy = $this->getPrivacyOfAlbum($photo['id']);
                    if($albumPrivacy!=null){
                        $privacy = $albumPrivacy;
                        $actionType = 'album';
                    }
                }
                $privacyButton = array('label' => $this->getPrivacyLabelByFeedId($privacy, $feedId),
                    'imgSrc' => OW::getPluginManager()->getPlugin('iissecurityessentials')->getStaticUrl() . 'images/' . $privacy . '.png');
                if ($feedId == OW::getUser()->getId()) {
                    $privacyButton['onClick'] = 'javascript:showAjaxFloatBoxForChangePrivacy(\'' . $objectId . '\', \'' . $change_privacy_label . '\',\''. $actionType .'\',\''.$feedId.'\')';
                    $privacyButton['id'] = 'sec-' . $objectId . '-' . $feedId;
                }
                $photo['privacy_label'] = $privacyButton;
                $photos[] = $photo;
            }
            $data['data']['photoList'] = $photos;
            $event->setData($data);
        }
    }

    public function createStatusPrivacyElement($privacyKey, $params = null){
        $statusPrivacy = new Selectbox('statusPrivacy');
        $statusPrivacy->setLabel(OW::getLanguage()->text('iissecurityessentials', 'change_privacy_label'));
        $options = array();
        $options[self::$PRIVACY_EVERYBODY] = OW::getLanguage()->text("privacy", "privacy_everybody");
        $options[self::$PRIVACY_ONLY_FOR_ME] = OW::getLanguage()->text("privacy", "privacy_only_for_me");
        $options[self::$PRIVACY_FRIENDS_ONLY] = OW::getLanguage()->text("friends", "privacy_friends_only");
        $statusPrivacy->setHasInvitation(false);
        $statusPrivacy->setOptions($options);
        $statusPrivacy->addAttribute('class', 'statusPrivacy');
        $statusPrivacy->setRequired();
        $defaultPrivacy = $this->getActionValueOfPrivacy($privacyKey,OW::getUser()->getId());
        if(isset($params['albumId'])){
            $albumPrivacy = $this->getPrivacyOfAlbum($params['albumId']);
            if($albumPrivacy!=null){
                $defaultPrivacy = $albumPrivacy;
            }
        }
        if($params!=null && array_key_exists('albumId',$params)){
            $statusPrivacy->setLabel(OW::getLanguage()->text('iissecurityessentials', 'change_privacy_of_album_label'));
        }
        if(isset($params['data']) && isset($params['data']['statusPrivacy'])){
            $defaultPrivacy = $params['data']['statusPrivacy'];
        }
        $statusPrivacy->setValue($defaultPrivacy);
        return $statusPrivacy;
    }

    public function privacyOnChangeActionPrivacy(OW_Event $event)
    {
        $params = $event->getParams();
        $userId = $params['userId'];
        $actionList = $params['actionList'];
        if(isset($actionList) && isset($userId) && isset($actionList['last_post_of_others_newsfeed'])){
            $privacy = $actionList['last_post_of_others_newsfeed'];
            $getActivityQuery = 'select a.id from ow_newsfeed_activity a, ow_newsfeed_action_feed ff where a.id = ff.activityId and ff.feedId = '.$userId.' and a.userId!='.$userId;
            $activityIds = OW::getDbo()->queryForList($getActivityQuery);
            $activityIdsImplodes = array();
            foreach($activityIds as $activityId){
                $activityIdsImplodes[] = $activityId['id'];
            }
            if(count($activityIdsImplodes)>0) { //issa added. don't remove
                $updateQuery = 'update ow_newsfeed_activity activity set activity.privacy = \'' . $privacy . '\' where activity.id in(' . implode(",", $activityIdsImplodes) . ')';
                OW::getDbo()->query($updateQuery);
            }
        }

        if(isset($actionList) && isset($userId) && isset($actionList['last_post_of_myself_newsfeed'])){
            $privacy = $actionList['last_post_of_myself_newsfeed'];
            $getActivityQuery = 'select a.id from ow_newsfeed_activity a, ow_newsfeed_action_feed ff where a.id = ff.activityId and ff.feedId = '.$userId.' and a.userId='.$userId;
            $activityIds = OW::getDbo()->queryForList($getActivityQuery);
            $activityIdsImplodes = array();
            foreach($activityIds as $activityId){
                $activityIdsImplodes[] = $activityId['id'];
            }
            if(count($activityIdsImplodes)>0) { //issa added. don't remove
                $updateQuery = 'update ow_newsfeed_activity activity set activity.privacy = \'' . $privacy . '\' where activity.id in(' . implode(",", $activityIdsImplodes) . ')';
                OW::getDbo()->query($updateQuery);
            }
        }
    }

    public function onQueryFeedCreate(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['feedId'])){
            $feedId = $params['feedId'];
            if($feedId==OW::getUser()->getId()){
                $event->setData(array('privacy' => '\''.self::$PRIVACY_EVERYBODY.'\',\''.self::$PRIVACY_FRIENDS_ONLY.'\',\''.self::$PRIVACY_ONLY_FOR_ME.'\''));
            }else{
                $ownerFriendsId = OW::getEventManager()->call('plugin.friends.get_friend_list', array('userId' => $feedId));
                if(!in_array(OW::getUser()->getId(),$ownerFriendsId)){
                    $event->setData(array('privacy' => '\''.self::$PRIVACY_EVERYBODY.'\''));
                }else{
                    $event->setData(array('privacy' =>'\''.self::$PRIVACY_EVERYBODY.'\',\''.self::$PRIVACY_FRIENDS_ONLY.'\''));
                }
            }
        }

    }

    public function onBeforeUpdateStatusFormCreateInProfile(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['userId'])){
            $userId = $params['userId'];
            if($userId != OW::getUser()->getId()) {
                $whoCanPostPrivacy = $this->getActionValueOfPrivacy('who_post_on_newsfeed', $userId);
                if ($whoCanPostPrivacy == self::$PRIVACY_FRIENDS_ONLY) {
                    $ownerFriendsId = OW::getEventManager()->call('plugin.friends.get_friend_list', array('userId' => $userId));
                    if(!in_array(OW::getUser()->getId(),$ownerFriendsId)){
                        $event->setData(array('showUpdateStatusForm' => false));
                    }
                } else if ($whoCanPostPrivacy == self::$PRIVACY_ONLY_FOR_ME) {
                    $event->setData(array('showUpdateStatusForm' => false));
                }
            }
        }
    }

    public function onBeforeUpdateStatusFormCreate(OW_Event $event){
        //Descide to show update status form in public page (false=hide)
        $event->setData(array('showUpdateStatusForm' => false));
    }

    public function privacyAddAction( BASE_CLASS_EventCollector $event )
    {
        $language = OW::getLanguage();

        $actions = array('my_post_on_feed_newsfeed','other_post_on_feed_newsfeed','last_post_of_others_newsfeed','who_post_on_newsfeed','video_default_privacy','last_post_of_myself_newsfeed');
        foreach ($actions as $action) {
            $information = $this->getInformationOfPrivacyField($action);
            $description = '';
            if(isset($information['description'])){
                $description = $information['description'];
            }

            $defaultValue = self::$PRIVACY_FRIENDS_ONLY;
            if(isset($information['defaultValue'])){
                $defaultValue = $information['defaultValue'];
            }

            $action = array(
                'key' => $action,
                'pluginKey' => 'iissecurityessentials',
                'label' => $language->text('iissecurityessentials', $action),
                'description' => $description,
                'defaultValue' => $defaultValue
            );

            $event->add($action);
        }
    }

    public function getInformationOfPrivacyField($privacyKey){
        $information = array();
        if($privacyKey == 'last_post_of_myself_newsfeed'){
            $information['description'] = OW::getLanguage()->text('iissecurityessentials','last_post_of_myself_newsfeed_description');
        }else if($privacyKey == 'last_post_of_others_newsfeed'){
            $information['description'] = OW::getLanguage()->text('iissecurityessentials','last_post_of_others_newsfeed_description');
        }

        $adminDefaultValue = $this->getAdminDefaultValueOfPrivacy($privacyKey);
        if($adminDefaultValue != null){
            $information['defaultValue'] = $adminDefaultValue;
        }

        return $information;
    }

    public function getAdminDefaultValueOfPrivacy($privacyKey){
        return OW::getConfig()->getValue('iissecurityessentials', $privacyKey);
    }

    public function updatePrivacyOfVideo($objectId, $privacy, $feedId){
        $videoService = VIDEO_BOL_ClipService::getInstance();
        $video = $videoService->findClipById($objectId);
        $this->checkUserOwnerId($video->userId,$feedId);
        $video->privacy = $privacy;
        $videoService->updateClip($video);
        return $video->userId;
    }

    public function getActionOwner($actionId){
        $activities = NEWSFEED_BOL_ActivityDao::getInstance()->findIdListByActionIds(array($actionId));
        foreach($activities as $activityId){
            $activity = NEWSFEED_BOL_Service::getInstance()->findActivity($activityId)[0];
            if($activity->activityType='create'){
                $feedObject = NEWSFEED_BOL_Service::getInstance()->findFeedListByActivityids(array($activity->id));
                $feedId = $feedObject[$activity->id][0]->feedId;
                if($feedId!=null){
                    return $feedId;
                }
            }
        }
        return null;
    }

    public function updatePrivacyOfPhoto($objectId, $privacy, $feedId){
        $photoService = PHOTO_BOL_PhotoService::getInstance();
        $photo = $photoService->findPhotoById($objectId);
        $photoOwner = $photoService->findPhotoOwner($photo->id);
        $this->checkUserOwnerId($photoOwner, $feedId);
        $photo->privacy = $privacy;
        $photoService->updatePhoto($photo);
        return $photoOwner;
    }

    public function getPhotoOwner($objectId){
        $photoService = PHOTO_BOL_PhotoService::getInstance();
        $photo = $photoService->findPhotoById($objectId);
        $photoOwner = $photoService->findPhotoOwner($photo->id);
        return $photoOwner;
    }

    public function updatePrivacyOfMultiplePhoto($photoIds, $privacy, $feedId){
        $photoOwner = '';
        $photoSampleId = null;
        foreach($photoIds as $photoId){
            $photoSampleId = $photoId;
            $photoOwner = $this->updatePrivacyOfPhoto($photoId, $privacy, $feedId);
        }
        if($photoSampleId!=null){
            $albumId = PHOTO_BOL_PhotoService::getInstance()->findPhotoById($photoSampleId)->albumId;
            $this->updatePrivacyOfPhotosByAlbumId($albumId, $privacy, $feedId);
        }
        return $photoOwner;
    }

    public function updatePrivacyOfPhotosByAlbumId($objectId, $privacy, $feedId){
        $actionId = array();
        $album = PHOTO_BOL_PhotoAlbumService::getInstance()->findAlbumById($objectId);
        $count = PHOTO_BOL_PhotoService::getInstance()->countAlbumPhotos($album->id, array());
        $photosOfAlbum = PHOTO_BOL_PhotoService::getInstance()->findPhotoListByAlbumId($album->id,1,$count);
        foreach($photosOfAlbum as $photo){
            $photoOwner = $this->updatePrivacyOfPhoto($photo['id'], $privacy, $feedId);
            $action = NEWSFEED_BOL_Service::getInstance()->findAction('photo_comments', $photo['id']);
            if($action!=null){
                if($this->getActionOwner($action->id)==$photoOwner) {
                    $actionId[] = $action->id;
                }
            }else{
                $action = NEWSFEED_BOL_Service::getInstance()->findAction('multiple_photo_upload', $photo['uploadKey']);
                if($action!=null){
                    if($this->getActionOwner($action->id)==$photoOwner) {
                        $actionId[] = $action->id;
                    }
                }
            }
        }
        return array('userId' => $album->userId, 'actionId' => $actionId);
    }

    public function updateNewsFeedActivitiesByActionId($activities, $privacy, $feedId){
        $privacy = $this->validatePrivacy($privacy);
        foreach($activities as $activityId){
            $activity = NEWSFEED_BOL_Service::getInstance()->findActivity($activityId)[0];
            $this->checkUserOwnerId($activity->userId,$feedId);
            $activity->privacy = $privacy;
            NEWSFEED_BOL_Service::getInstance()->saveActivity($activity);
        }
    }

    public function checkUserOwnerId($ownerId,$feedId){
        if($feedId!=null && $feedId!='' && $feedId==OW::getUser()->getId()){
            return;
        }else if(!OW::getUser()->isAuthenticated() || OW::getUser()->getId()!=$ownerId){
            exit(json_encode(array('result' => false)));
        }
    }

    public function updateNewsFeedActivitiesByActionIds($actionIds, $privacy, $feedId){
        $activities = array();
        if(is_array($actionIds)){
            $activities = NEWSFEED_BOL_ActivityDao::getInstance()->findIdListByActionIds($actionIds);
        }else{
            $activities = NEWSFEED_BOL_ActivityDao::getInstance()->findIdListByActionIds(array($actionIds));
        }
        $this->updateNewsFeedActivitiesByActionId($activities, $privacy, $feedId);
    }

    public function onAfterActivity(OW_Event $event){
        $params = $event->getParams();
        $feedId = $params['feedId'];
        $feedType = $params['feedType'];
        $entityType = $params['entityType'];
        $entityId = $params['entityId'];
        $actionId = $params['actionId'];
        $privacy = null;
        $findActivity = true;
        if($entityType == 'friend_add'){
            $privacy = self::$PRIVACY_FRIENDS_ONLY;
        }else if($feedType == 'user') {
            $privacy = $this->setPrivacy($feedId);
        }else if(($entityType == 'photo_comments' || $entityType == 'multiple_photo_upload') && isset($_REQUEST['statusPrivacy'])){
            if($entityType=='photo_comments'){
                $albumId = PHOTO_BOL_PhotoService::getInstance()->findPhotoById($entityId)->albumId;
                $privacyOfAlbum = $this->getPrivacyOfAlbum($albumId);
                if($privacyOfAlbum!=null){
                    $privacy = $privacyOfAlbum;
                }
                $results = $this->updatePrivacyOfPhotosByAlbumId($albumId, $privacy, $feedId);
                $this->updateNewsFeedActivitiesByActionIds($results['actionId'], $privacy, $feedId);
                $findActivity = false;
            }else if($entityType=='multiple_photo_upload'){
                $photoSampleId = null;
                $photoIdList = $event->getData()['photoIdList'];
                $privacy = $this->validatePrivacy($_REQUEST['statusPrivacy']);
                if($photoIdList!=null && !isEmpty($photoIdList)) {
                    $photoSampleId = $photoIdList[0];
                }else{
                    $actionObj = NEWSFEED_BOL_Service::getInstance()->findAction('multiple_photo_upload', $entityId);
                    if($actionObj!=null){
                        $data = $actionObj->data;
                        if($data!=null && isset(json_decode($data)->photoIdList[0])) {
                            $photoSampleId = json_decode($data)->photoIdList[0];
                        }
                    }
                }

                if($photoSampleId!=null) {
                    $albumId = PHOTO_BOL_PhotoService::getInstance()->findPhotoById($photoSampleId)->albumId;
                    $privacyOfAlbum = $this->getPrivacyOfAlbum($albumId);
                    if ($privacyOfAlbum != null) {
                        $privacy = $privacyOfAlbum;
                    }
                    $results = $this->updatePrivacyOfPhotosByAlbumId($albumId, $privacy, $feedId);
                    $this->updateNewsFeedActivitiesByActionIds($results['actionId'], $privacy, $feedId);
                    $findActivity = false;
                }
            }
        }else if($entityType == 'video_comments' && isset($_REQUEST['statusPrivacy'])){
            $privacy = $this->validatePrivacy($_REQUEST['statusPrivacy']);
        }else if($entityType == 'add_audio'){
            $privacy = $this->validatePrivacy($_REQUEST['statusPrivacy']);
        }

        if ($actionId!=null && $privacy!=null && $findActivity) {
            $activities = NEWSFEED_BOL_ActivityDao::getInstance()->findIdListByActionIds(array($actionId));
            foreach ($activities as $activityId) {
                $activity = NEWSFEED_BOL_Service::getInstance()->findActivity($activityId)[0];
                $privacy = $this->validatePrivacy($privacy);
                $activity->privacy = $privacy;
                NEWSFEED_BOL_Service::getInstance()->saveActivity($activity);
            }
        }
    }

    public function validatePrivacy($privacy){
        if($privacy == self::$PRIVACY_EVERYBODY || $privacy == self::$PRIVACY_ONLY_FOR_ME || $privacy == self::$PRIVACY_FRIENDS_ONLY){
            return $privacy;
        }
        return self::$PRIVACY_ONLY_FOR_ME;
    }

    public function onAfterUpdateStatusFormRenderer(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['form']) && isset($params['component'])){
            $form = $params['form'];
            if($form->getElement('statusPrivacy')!=null){
                $params['component']->assign('statusPrivacyField',true);
            }else{
                $profileOwner = $this->findUserByProfile();
                if($profileOwner!=null && $profileOwner->getId() != OW::getUser()->getId()){
                    $profileOwnerPrivacy = $this->getActionValueOfPrivacy('other_post_on_feed_newsfeed',$profileOwner->getId());
                    $text = '';
                    if($profileOwnerPrivacy == self::$PRIVACY_ONLY_FOR_ME){
                        $text = OW::getLanguage()->text('iissecurityessentials', 'show_to_user',array('username' => $profileOwner->username));
                    }else if($profileOwnerPrivacy == self::$PRIVACY_FRIENDS_ONLY){
                        $text = OW::getLanguage()->text('iissecurityessentials', 'show_to_friends',array('username' => $profileOwner->username));
                    }else if($profileOwnerPrivacy == self::$PRIVACY_EVERYBODY){
                        $text = OW::getLanguage()->text('iissecurityessentials', 'show_to_everybody');
                    }
                    $params['component']->assign('statusPrivacyLabel',$text);
                }
            }
        }
    }

    public function onBeforeUpdateStatusFormRenderer(OW_Event $event){
        $params = $event->getParams();
        $user = $this->findUserByProfile();
        if(isset($params['form']) && ($user==null || ($user->getId()==OW::getUser()->getId())) && $params['form']->getElement('feedType')->getValue()=='user'){
            $form = $params['form'];
            $form->addElement($this->createStatusPrivacyElement('my_post_on_feed_newsfeed'));
        }
    }

    public function onBeforeObjectRenderer(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['privacy']) && isset($params['ownerId'])){
            $this->checkPrivacyOfObject($params['privacy'], $params['ownerId']);
        }
    }

    public function onBeforeFeedItemRenderer(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['actionId']) && isset($params['feedId'])){
            $activities = NEWSFEED_BOL_ActivityDao::getInstance()->findIdListByActionIds(array($params['actionId']));
            foreach($activities as $activityId){
                $activity = NEWSFEED_BOL_Service::getInstance()->findActivity($activityId)[0];
                if($activity->activityType=='create'){
                    $this->checkPrivacyOfObject($activity->privacy, $params['feedId'], $activity->userId);
                }
            }
        }
    }

    public function checkPrivacyOfObject($privacy, $ownerId, $activityOwner = null, $throwEx = true){
        if(OW::getUser()->isAuthenticated() && $ownerId==OW::getUser()->getId()){
            return true;
        }else if($privacy==self::$PRIVACY_EVERYBODY || ($activityOwner!=null && OW::getUser()->isAuthenticated() && $activityOwner==OW::getUser()->getId())){
            return true;
        }else if($privacy==self::$PRIVACY_ONLY_FOR_ME && $ownerId!=OW::getUser()->getId()){
            if($throwEx){
                throw new Redirect404Exception();
            }else{
                return false;
            }
        }else if($privacy == self::$PRIVACY_FRIENDS_ONLY && $ownerId!=OW::getUser()->getId()){
            $ownerFriendsId = OW::getEventManager()->call('plugin.friends.get_friend_list', array('userId' => $ownerId));
            if(!in_array(OW::getUser()->getId(),$ownerFriendsId)){
                if($throwEx){
                    throw new Redirect404Exception();
                }else{
                    return false;
                }
            }else{
                return true;
            }
        }
    }

    public function onCollectPhotoContextActions( BASE_CLASS_EventCollector $event ){
        $params = $event->getParams();
        $photoId = $params['photoId'];

        if(OW::getUser()->isAuthenticated() && PHOTO_BOL_PhotoService::getInstance()->findPhotoOwner($photoId) == OW::getUser()->getId()) {
            $change_privacy_label = OW::getLanguage()->text('iissecurityessentials', 'change_privacy_label');
            $change_privacy_of_album_label = OW::getLanguage()->text('iissecurityessentials', 'change_privacy_of_album_label');

            $changePrivacyData = array(
                'url' => 'javascript:showAjaxFloatBoxForChangePrivacy(\'' . $photoId . '\', \'' . $change_privacy_label . '\',\'photo_comments\',\'\');',
                'id' => 'btn-video-change-privacy',
                'label' => $change_privacy_of_album_label,
                'order' => 4
            );

            $event->add($changePrivacyData);
        }
    }

    public function onCollectVideoToolbarItems( BASE_CLASS_EventCollector $event ){
        $params = $event->getParams();
        $clipId = $params['clipId'];
        $clipDto = $params['clipDto'];
        $change_privacy_label = OW::getLanguage()->text('iissecurityessentials', 'change_privacy_label');
        $iconUrl = OW::getPluginManager()->getPlugin('iissecurityessentials')->getStaticUrl() . 'images/'.$clipDto->privacy.'.png';
        $changePrivacyData = array(
            'label' => '<img title="'. $this->getPrivacyLabelByFeedId($clipDto->privacy, $clipDto->userId) .'" class="feed_image_privacy" src="' . $iconUrl . '" />'
        );

        if(OW::getUser()->isAuthenticated() && $clipDto->userId == OW::getUser()->getId()) {
            $changePrivacyData['href'] = 'javascript:showAjaxFloatBoxForChangePrivacy(\'' . $clipId . '\', \'' . $change_privacy_label . '\',\'video_comments\',\'\');';
            $changePrivacyData['id'] = 'sec-'.$clipId.'-'.$clipDto->userId;
        }
        $event->add($changePrivacyData);
    }

    public function getPrivacyButtonInformation($objectId, $userId, $privacy, $objectType, $linkable = true){
        $change_privacy_label = OW::getLanguage()->text('iissecurityessentials', 'change_privacy_label');
        $privacyButton = array('label' => $this->getPrivacyLabelByFeedId($privacy, $userId),
            'imgSrc' => OW::getPluginManager()->getPlugin('iissecurityessentials')->getStaticUrl() . 'images/' . $privacy . '.png');
        if (OW::getUser()->isAuthenticated() && $userId == OW::getUser()->getId() && $linkable) {
            $privacyButton['onClick'] = 'javascript:showAjaxFloatBoxForChangePrivacy(\'' . $objectId . '\', \'' . $change_privacy_label . '\',\''.$objectType.'\',\''.$userId.'\')';
            $privacyButton['id'] = 'sec-'.$objectId.'-'.$userId;
        }

        return $privacyButton;
    }

    public function onBeforeDocumentRenderer( OW_Event $event )
    {
        $jsFile = OW::getPluginManager()->getPlugin('iissecurityessentials')->getStaticJsUrl() . 'iissecurityessentials.js';
        OW::getDocument()->addScript($jsFile);

        $cssFile = OW::getPluginManager()->getPlugin('iissecurityessentials')->getStaticCssUrl() . 'iissecurityessentials.css';
        OW::getDocument()->addStyleSheet($cssFile);
    }

    public function onFeedItemRenderer( OW_Event $event )
    {
        $data = $event->getData();
        $params = $event->getParams();
        if(isset($params['data']) && isset($params['data']['privacy_label'])){
            $data['privacy_label'] = $params['data']['privacy_label'];
            $event->setData($data);
        }
    }

    public function onBeforeAlbumInfoRenderer(OW_Event $event)
    {
        $params = $event->getParams();
        if (isset($params['this']) && isset($params['album'])) {
            $album = $params['album'];
            $userId = $album->userId;
            $privacy = $this->getPrivacyOfAlbum($album->id);
            if($privacy!=null) {
                $params['this']->assign('privacy_label', $this->getPrivacyButtonInformation('', $userId, $privacy, '', false));
            }
        }
    }

    public function onBeforeAlbumsRenderer(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['this']) && isset($params['album'])){
            $album = $params['album'];
            $privacy = $this->getPrivacyOfAlbum($album->id);
            if($privacy!=null){
                $params['this']->assign('privacy_label',$this->getPrivacyButtonInformation($album->id, $album->userId, $privacy, 'album'));
            }
        }
    }

    public function onFeedItemRender( OW_Event $event )
    {
        $data = $event->getData();
        $params = $event->getParams();
        $feedType = $params['feedType'];
        $ignoreByEntityTypes = false;
        if(isset($params['action']['entityType']) && $params['action']['entityType']=='friend_add'){
            $ignoreByEntityTypes = true;
        }
        if ( in_array($feedType , array('user', 'my', 'site')) && !$ignoreByEntityTypes)
        {
            $activities = $params['activity'];
            foreach($activities as $activity){
                if($activity['activityType'] == 'create') {
                    $feedObject = NEWSFEED_BOL_Service::getInstance()->findFeedListByActivityids(array($activity['id']));
                    $feedId = $feedObject[$activity['id']][sizeof($feedObject[$activity['id']])-1]->feedId;
                    $data['privacy_label'] = $this->getPrivacyButtonInformation($params['createActivity']->actionId, $feedId, $activity['privacy'], 'user_status');
                }
            }
        }

        $event->setData($data);
    }

    public function onBeforeVideoRender( OW_Event $event )
    {
        $params = $event->getParams();
        if(isset($params['objectId']) && isset($params['this']) && isset($params['privacy']) && isset($params['userId'])){
            $item = array();
            $item['privacy_label'] = $this->getPrivacyButtonInformation($params['objectId'], $params['userId'], $params['privacy'], 'video_comments');
            $params['this']->assign('item', $item);
        }
    }

    public function onBeforePhotoRender( OW_Event $event )
    {
        $params = $event->getParams();
        if(isset($params['objectId']) && isset($params['this']) && isset($params['privacy']) && isset($params['userId'])){
            $item = array();
            $item['privacy_label'] = $this->getPrivacyButtonInformation($params['objectId'], $params['userId'], $params['privacy'], 'album');
            $params['this']->assign('item', $item);
        }
    }

    public function getPrivacyLabelByFeedId($privacy, $feedId){
        $username = BOL_UserService::getInstance()->findUserById($feedId)->username;
        return $this->getPrivacyLabel($privacy, $username);
    }

    public function getPrivacyLabel($privacy, $username){
        if(self::$PRIVACY_FRIENDS_ONLY == $privacy){
            return OW::getLanguage()->text('iissecurityessentials', 'show_to_friends', array('username' => $username));
        }else if(self::$PRIVACY_ONLY_FOR_ME == $privacy){
            return OW::getLanguage()->text('iissecurityessentials', 'show_to_user', array('username' => $username));
        }else if(self::$PRIVACY_EVERYBODY == $privacy){
            return OW::getLanguage()->text('iissecurityessentials', 'show_to_everybody');
        }
    }

    public function onFeedCollectPrivacy( BASE_CLASS_EventCollector $event )
    {
        $event->add(array('*:*', 'view_my_feed'));
    }

    public function setPrivacy($ownerId){
        $privacy = self::$PRIVACY_FRIENDS_ONLY;
        if($ownerId!=null && $ownerId==OW::getUser()->getId()){
            if(isset($_REQUEST['statusPrivacy'])){
                $privacy = $this->validatePrivacy($_REQUEST['statusPrivacy']);
            }else{
                $my_post_on_feed_newsfeed = $this->getActionValueOfPrivacy('my_post_on_feed_newsfeed',$ownerId);
                if($my_post_on_feed_newsfeed!=null){
                    $privacy = $my_post_on_feed_newsfeed;
                }
            }
        }else if($ownerId!=null && $ownerId!=OW::getUser()->getId()){
            $other_post_on_feed_newsfeed = $this->getActionValueOfPrivacy('other_post_on_feed_newsfeed',$ownerId);
            if($other_post_on_feed_newsfeed!=null){
                $privacy = $other_post_on_feed_newsfeed;
            }
        }
        return $privacy;
    }

    public function findUserByProfile(){
        $user  = null;
        if(strpos($_SERVER['REQUEST_URI'],'/user/')!==false){
            $username = substr($_SERVER['REQUEST_URI'],strpos($_SERVER['REQUEST_URI'],'/user/')+6);
            if(strpos($username,'/')!==false){
                $username = substr($username,0,strpos($username,'/'));
            }
            $user = BOL_UserService::getInstance()->findByUsername($username);
        }
        return $user;
    }

    public function catchAllRequestsExceptions( BASE_CLASS_EventCollector $event )
    {
        $event->add(array(
            OW_RequestHandler::ATTRS_KEY_CTRL => 'BASE_CTRL_EmailVerify',
            OW_RequestHandler::ATTRS_KEY_ACTION => 'verify'
        ));

        $event->add(array(
            OW_RequestHandler::ATTRS_KEY_CTRL => 'BASE_CTRL_EmailVerify',
            OW_RequestHandler::ATTRS_KEY_ACTION => 'verifyForm'
        ));
    }

    public function onBeforeIndexStatusEnabled(OW_Event $event){
        $params = $event->getParams();
        $config =  OW::getConfig();
        $indexStatus = null;
        if($config->configExists('newsfeed', 'index_status_enabled')) {
            $config->saveConfig('newsfeed', 'index_status_enabled',null);
        }
        else{
            $config->addConfig('newsfeed', 'index_status_enabled',null);
        }
        if(isset($params['checkBoxField'])){
            $field = $params['checkBoxField'];
            $field->removeAttribute("checked");
            $field->addAttribute('disabled', 'disabled');
        }
    }

    /*
    * return the correct invitation feed status
    * @param OW_Event $event
    */
    public static function onBeforeFeedRendered(OW_Event $event)
    {
        $params = $event->getParams();
        if(isset($params['userId'])) {
            if($params['userId'] == ow::getUser()->getId())
            {
                IISSecurityProvider::setStatusMessage(OW::getLanguage()->text('iissecurityessentials', 'status_field_ownUser'));
            }
            else
            {
                $user = BOL_UserService::getInstance()->findUserById($params['userId']);
                $username = $user->getUsername();
                IISSecurityProvider::setStatusMessage(OW::getLanguage()->text('iissecurityessentials', 'status_field_otherUser',array('username' => $username)));
            }
        }
        else
        {
            IISSecurityProvider::setStatusMessage(OW::getLanguage()->text('iissecurityessentials', 'status_field_invintation'));
        }
    }

    public function regenerateSessionID(OW_Event $event){
        $userContext = null;
        if(OW::getSession()->isKeySet(OW_Application::CONTEXT_NAME)){
            $userContext = OW::getSession()->get(OW_Application::CONTEXT_NAME);
        }
        OW::getSession()->regenerate();
        if($userContext!=null){
            OW::getSession()->set(OW_Application::CONTEXT_NAME, $userContext);
        }
    }

    public function logoutIfIdle(OW_Event $event){
        $user = OW::getUser();
        if ( !$user->isAuthenticated() || $user->getUserObject()==null)
        {
            return;
        }
        $timestamp = $user->getUserObject()->getActivityStamp();
        $now = time();
        if (isset($_COOKIE['ow_login']) && !$_COOKIE['ow_login'] && $now - $timestamp > OW::getConfig()->getValue('iissecurityessentials', 'idleTime')*60){
            OW::getUser()->logout();
            if ( isset($_COOKIE['ow_login']) )
            {
                setcookie('ow_login', '', time() - 3600, '/');
            }
            OW::getSession()->set('no_autologin', true);
            OW::getApplication()->redirect(OW_URL_HOME);
        }
    }

}
