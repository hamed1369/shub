<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iismobilesupport.bol
 * @since 1.0
 */
class IISMOBILESUPPORT_BOL_DeviceDao extends OW_BaseDao
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
        return 'IISMOBILESUPPORT_BOL_Device';
    }
    
    public function getTableName()
    {
        return OW_DB_PREFIX . 'iismobilesupport_device';
    }

    /***
     * @param $userId
     * @return array
     */
    public function getUsersDevices($userId){
        if($userId==null){
            return array();
        }
        $ex = new OW_Example();
        $ex->andFieldEqual('userId', $userId);
        return $this->findListByExample($ex);
    }

    /***
     * @param $userId
     * @param $token
     */
    public function deleteUserDevice($userId, $token){
        $ex = new OW_Example();
        $ex->andFieldEqual('userId', $userId);
        $ex->andFieldEqual('token', $token);
        $this->deleteByExample($ex);
    }

    /***
     * @param $userId
     */
    public function deleteAllDevicesOfUser($userId){
        $ex = new OW_Example();
        $ex->andFieldEqual('userId', $userId);
        $this->deleteByExample($ex);
    }

    /***
     * @param $userId
     * @param $token
     * @return array|bool
     */
    public function hasUserDevice($userId, $token){
        if($userId==null){
            return array();
        }
        $ex = new OW_Example();
        $ex->andFieldEqual('userId', $userId);
        $ex->andFieldEqual('token', $token);
        return $this->findObjectByExample($ex)!=null;
    }


    /***
     * @param $userId
     * @param $token
     * @return IISMOBILESUPPORT_BOL_Device
     */
    public function saveDevice($userId, $token){
        $device = new IISMOBILESUPPORT_BOL_Device();
        $device->userId = $userId;
        $device->token = $token;
        $device->time = time();
        $this->save($device);
        return $device;
    }
}
