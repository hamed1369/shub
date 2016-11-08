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
class IISEVALUATION_BOL_UserDao extends OW_BaseDao
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
        return 'IISEVALUATION_BOL_User';
    }

    public function getTableName()
    {
        return OW_DB_PREFIX . 'iisevaluation_user';
    }

    /***
     * @param $userId
     * @return IISEVALUATION_BOL_User
     */
    public function getUser($userId){
        $ex = new OW_Example();
        $ex->andFieldEqual('userId', $userId);
        return $this->findObjectByExample($ex);
    }

    /***
     * @return array
     */
    public function getUsers(){
        $ex = new OW_Example();
        return $this->findListByExample($ex);
    }

    /***
     * @return array
     */
    public function getLockedUsers(){
        $ex = new OW_Example();
        $ex->andFieldEqual('lock', 1);
        return $this->findListByExample($ex);
    }

    /***
     * @param $userId
     * @return bool
     */
    public function isUserAssigned($userId){
        $ex = new OW_Example();
        $ex->andFieldEqual('userId', $userId);
        $user =  $this->findObjectByExample($ex);
        if($user == null){
            return false;
        }else{
            return true;
        }
    }

    /***
     * @param $userId
     * @return bool
     */
    public function isUserLocked($userId){
        $ex = new OW_Example();
        $ex->andFieldEqual('userId', $userId);
        $ex->andFieldEqual('lock', 1);
        $user =  $this->findObjectByExample($ex);
        if($user == null){
            return false;
        }else{
            return true;
        }
    }

    /***
     * @return array
     */
    public function getActiveUsers(){
        $ex = new OW_Example();
        $ex->andFieldEqual('lock', 0);
        return $this->findListByExample($ex);
    }

    /***
     * @param $userId
     * @param int $lock
     * @return IISEVALUATION_BOL_User
     */
    public function saveUser($userId, $username, $lock = 0){
        $user = new IISEVALUATION_BOL_User();
        $user->userId = $userId;
        $user->lock = $lock;
        $user->username = $username;
        $this->save($user);
        return $user;
    }

    /***
     * @param $userId
     * @param $lock
     * @return IISEVALUATION_BOL_User
     */
    public function update($userId, $lock){
        $user = $this->getUser($userId);
        $user->lock = $lock;
        $this->save($user);
        return $user;
    }

    /***
     * @param $username
     */
    public function deleteUser($username){
        $ex = new OW_Example();
        $ex->andFieldEqual('username', $username);
        $this->deleteByExample($ex);
    }

}
