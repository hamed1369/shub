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
class IISEVALUATION_BOL_ValueDao extends OW_BaseDao
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
        return 'IISEVALUATION_BOL_Value';
    }
    
    public function getTableName()
    {
        return OW_DB_PREFIX . 'iisevaluation_value';
    }

    /***
     * @param $questionId
     * @return array
     */
    public function getValues($questionId){
        $ex = new OW_Example();
        $ex->andFieldEqual('questionId', $questionId);
        return $this->findListByExample($ex);
    }

    /***
     * @param $valueId
     * @return IISEVALUATION_BOL_Value
     */
    public function getValue($valueId){
        $ex = new OW_Example();
        $ex->andFieldEqual('id', $valueId);
        return $this->findObjectByExample($ex);
    }


    /***
     * @param OW_Entity $name
     * @param $value
     * @param $questionId
     * @return IISEVALUATION_BOL_Value
     */
    public function saveValue($name, $value, $questionId){
        $valueObj = new IISEVALUATION_BOL_Value();
        $valueObj->name = $name;
        $valueObj->value = $value;
        $valueObj->questionId = $questionId;
        $this->save($valueObj);
        return $valueObj;
    }

    /***
     * @param $valueId
     * @param $name
     * @param $value
     * @param $questionId
     * @return IISEVALUATION_BOL_Value
     */
    public function updateValue($valueId, $name, $value){
        $ex = new OW_Example();
        $ex->andFieldEqual('id', $valueId);
        $valueObj = $this->findObjectByExample($ex);
        $valueObj->name = $name;
        $valueObj->value = $value;
        $this->save($valueObj);
        return $valueObj;
    }
}
