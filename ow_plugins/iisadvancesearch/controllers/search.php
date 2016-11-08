<?php

class IISADVANCESEARCH_CTRL_Search extends OW_ActionController
{

    public function searchAll($params)
    {
        if(isset($_POST['searchValue']) && OW::getUser()->isAuthenticated()){
            $resultData = array();
            $searchValue = $_POST['searchValue'];
            $searchValue = trim($searchValue);


            $resultData['users'] = $this->getUsersBySearchValue($searchValue);
            $resultData['forum_posts'] = $this->getForumPosts($searchValue);
            $resultData['searchedValue'] = $searchValue;

            exit(json_encode($resultData));
        }
        exit(true);
    }

    public function getUsersBySearchValue($searchValue){
        $users = array();
        $userIdList = array();

        $userIdListByUsername = $this->getUsersByQuestionAndValue('username', $searchValue);
        $userIdListByRealName = $this->getUsersByQuestionAndValue('realname', $searchValue);

        foreach($userIdListByUsername as $userId){
            if(!in_array($userId, $userIdList)){
                $userIdList[] = $userId;
            }
        }

        foreach($userIdListByRealName as $userId){
            if(!in_array($userId, $userIdList)){
                $userIdList[] = $userId;
            }
        }

        if ( count($userIdList) > 0 ){
            $avatars = BOL_AvatarService::getInstance()->getDataForUserAvatars($userIdList);
            foreach($avatars as $avatar){
                $user = array();
                $user['url'] = $avatar['url'];
                $user['src'] = $avatar['src'];
                $user['title'] = $avatar['title'];
                $users[] = $user;
            }
        }

        return $users;
    }


    public function getUsersByQuestionAndValue($questionName, $searchValue){
        $questionData = array($questionName => $searchValue);
        $first = (int) 0;
        $count = (int) 12;
        $data = array(
            'data' => $questionData,
            'first' => $first,
            'count' => $count,
            'isAdmin' => OW::getUser()->isAdmin(),
            'aditionalParams' => array()
        );

        $event = new OW_Event("base.question.before_user_search", $data, $data);
        OW_EventManager::getInstance()->trigger($event);
        $data = $event->getData();

        $userIdList = BOL_UserService::getInstance()->findUserIdListByQuestionValues($data['data'], $data['first'], $data['count'], $data['isAdmin'], $data['aditionalParams']);
        return $userIdList;
    }

    public function getForumPosts($searchValue){
        $result = array();
        $topics = FORUM_BOL_ForumService::getInstance()->advancedFindEntities($searchValue, '1', null, array(""), null, 'date', 'decrease', true);
        $topicsUsingTitle = FORUM_BOL_ForumService::getInstance()->advancedFindEntities($searchValue, '1', null, array(""), null, 'date', 'decrease', false);
        foreach($topicsUsingTitle as $key => $topic){
            if(!isset($topics[$key])){
                $topics[] = $topic;
            }
        }

        $count = 0;
        $numberOfCount = 12;

        foreach($topics as $topic){
            $topicInformation = array();
            $topicInformation['title'] = $topic['title'];
            $topicInformation['groupName'] = $topic['groupName'];
            $topicInformation['sectionName'] = $topic['sectionName'];
            $topicInformation['topicUrl'] = $topic['topicUrl'];
            $result[] = $topicInformation;
            $count++;
            if($count == $numberOfCount){
                return $result;
            }
        }

        return $result;

    }
}