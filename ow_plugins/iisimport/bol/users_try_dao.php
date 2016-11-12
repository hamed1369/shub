<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisimport.bol
 * @since 1.0
 */
class IISIMPORT_BOL_UsersTryDao extends OW_BaseDao
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
        return 'IISIMPORT_BOL_UsersTry';
    }

    public function getTableName()
    {
        return OW_DB_PREFIX . 'iisimport_users_try';
    }

    /***
     * @param $userId
     * @param $type
     * @return array
     */
    public function getUserTry($userId, $type)
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('userId', $userId);
        $ex->andFieldEqual('type', $type);
        return $this->findObjectByExample($ex);
    }

    /***
     * @param $userId
     * @param $type
     * @return array|IISIMPORT_BOL_UsersTry
     */
    public function addOrUpdateUserTry($userId, $type)
    {
        $user = $this->getUserTry($userId, $type);
        if($user != null) {
            $user->time = time();
            $this->save($user);
        }else{
            $user = new IISIMPORT_BOL_UsersTry();
            $user->time = time();
            $user->userId = $userId;
            $user->type = $type;
            $this->save($user);
        }
        return $user;
    }
}
