<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iissecurityessentials.bol
 * @since 1.0
 */
class IISSECURITYESSENTIALS_BOL_QuestionPrivacyDao extends OW_BaseDao
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
        return 'IISSECURITYESSENTIALS_BOL_QuestionPrivacy';
    }

    public function getTableName()
    {
        return OW_DB_PREFIX . 'iissecurityessentials_question_privacy';
    }

    /***
     * @param $userId
     * @param $questionId
     * @return mixed
     */
    public function getQuestionPrivacy($userId, $questionId){
        $ex = new OW_Example();
        $ex->andFieldEqual('userId', $userId);
        $ex->andFieldEqual('questionId', $questionId);
        $questionPrivacy = $this->findObjectByExample($ex);
        if($questionPrivacy==null){
            return null;
        }
        return $questionPrivacy->privacy;
    }

    /***
     * @param $userIds
     * @param $privacy
     * @param $questionId
     * @return array
     */
    public function getQuestionsPrivacyByExceptPrivacy($userIds, $privacy, $questionId){
        if(!is_array($userIds) || empty($userIds)){
            return array();
        }
        $ex = new OW_Example();
        $ex->andFieldInArray('userId', $userIds);
        $ex->andFieldNotEqual('privacy', $privacy);
        $ex->andFieldEqual('questionId', $questionId);
        return $this->findListByExample($ex);
    }

    /***
     * @param $userId
     * @param $questionId
     * @param $privacy
     * @return IISSECURITYESSENTIALS_BOL_QuestionPrivacy
     */
    public function setQuestionPrivacy($userId, $questionId, $privacy){
        $ex = new OW_Example();
        $ex->andFieldEqual('userId', $userId);
        $ex->andFieldEqual('questionId', $questionId);
        $questionPrivacy = $this->findObjectByExample($ex);

        if($questionPrivacy==null) {
            $questionPrivacy = new IISSECURITYESSENTIALS_BOL_QuestionPrivacy();
            $questionPrivacy->userId = $userId;
            $questionPrivacy->questionId = $questionId;
        }
        $questionPrivacy->privacy = $privacy;
        $this->save($questionPrivacy);
        return $questionPrivacy;
    }



}
