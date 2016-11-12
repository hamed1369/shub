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
class IISIMPORT_BOL_UsersDao extends OW_BaseDao
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
        return 'IISIMPORT_BOL_Users';
    }
    
    public function getTableName()
    {
        return OW_DB_PREFIX . 'iisimport_users';
    }

    /***
     * @param $userId
     * @param $type
     * @return array
     */
    public function getEmailsByUserId($userId, $type)
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('userId', $userId);
        $ex->andFieldEqual('type', $type);
        return $this->findListByExample($ex);
    }

    /***
     * @param $email
     * @return array
     */
    public function getUsersByEmail($email)
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('email', $email);
        return $this->findListByExample($ex);
    }

    /***
     * @param $userId
     * @param $email
     * @param $type
     * @return mixed
     */
    public function getUser($userId, $email, $type)
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('userId', $userId);
        $ex->andFieldEqual('email', $email);
        $ex->andFieldEqual('type', $type);
        return $this->findObjectByExample($ex);
    }

    /***
     * @param $userId
     * @param $email
     * @param $type
     * @return IISIMPORT_BOL_Users
     */
    public function addUser($userId, $email, $type)
    {
        $newUser = new IISIMPORT_BOL_Users();
        $newUser->email = $email;
        $newUser->userId = $userId;
        $newUser->type = $type;
        $this->save($newUser);
        return $newUser;
    }
}
