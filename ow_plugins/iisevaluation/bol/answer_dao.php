<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisevaluation.bol
 * @since 1.0
 */
class IISEVALUATION_BOL_AnswerDao extends OW_BaseDao
{
    private static $classInstance;

    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function getDtoClassName()
    {
        return 'IISEVALUATION_BOL_Answer';
    }
    
    public function getTableName()
    {
        return OW_DB_PREFIX . 'iisevaluation_answer';
    }

    /***
     * @param $answerId
     * @return array
     */
    public function getAnswer($answerId){
        $ex = new OW_Example();
        $ex->andFieldEqual('id', $answerId);
        return $this->findListByExample($ex);
    }

    /***
     * @param $questionId
     * @return IISEVALUATION_BOL_Answer
     */
    public function getAnswerByQuestionIdAndUserId($questionId, $userId){
        $ex = new OW_Example();
        $ex->andFieldEqual('questionId', $questionId);
        $ex->andFieldEqual('userId', $userId);
        return $this->findObjectByExample($ex);
    }

    /***
     * @return array
     */
    public function getUsers(){
        $query = 'select DISTINCT  userId from `' . $this->getTableName() . '`';
        return OW::getDbo()->queryForList($query);
    }

    /***
     * @param $categoryId
     * @return int
     */
    public function getCountOfAnswersOfCategory($categoryId, $userId){
        $questions = IISEVALUATION_BOL_Service::getInstance()->getQuestions($categoryId);
        $questionsIds = array();
        foreach($questions as $question){
            $questionsIds[] = $question->id;
        }
        if(sizeof($questionsIds)>0) {
            $ex = new OW_Example();
            $ex->andFieldInArray('questionId', $questionsIds);
            $ex->andFieldEqual('userId', $userId);
            return sizeof($this->findListByExample($ex));
        }
        return 0;
    }

    /***
     * @param $questionId
     * @param $userId
     * @return null
     */
    public function checkQuestionsAnswered($questionId, $userId){
        $ex = new OW_Example();
        $ex->andFieldEqual('questionId', $questionId);
        $ex->andFieldEqual('userId', $userId);
        $answer = $this->findObjectByExample($ex);
        if($answer!=null){
            return IISEVALUATION_BOL_Service::getInstance()->getValue($answer->valueId)->name;
        }
        return null;
    }

    /***
     * @param $sign
     * @param $questionId
     * @param $description
     * @param $file
     * @param $valueId
     * @return IISEVALUATION_BOL_Answer
     */
    public function saveAnswer($sign, $questionId, $description, $file, $valueId){
        $answer = new IISEVALUATION_BOL_Answer();
        $answer->sign = $sign;
        $answer->questionId = $questionId;
        $answer->description = $description;
        $answer->file = $file;
        $answer->valueId = $valueId;
        $answer->userId = OW::getUser()->getId();
        $this->save($answer);
        return $answer;
    }

    /***
     * @param $answerId
     * @param $sign
     * @param $questionId
     * @param $description
     * @param $file
     * @param $valueId
     * @return IISEVALUATION_BOL_Answer
     */
    public function updateAnswer($answerId, $sign, $questionId, $description, $file, $valueId){
        $ex = new OW_Example();
        $ex->andFieldEqual('id', $answerId);
        $answer = $this->findObjectByExample($ex);
        $answer->sign = $sign;
        $answer->questionId = $questionId;
        $answer->description = $description;
        $answer->file = $file;
        $answer->valueId = $valueId;
        $this->save($answer);
        return $answer;
    }
}
