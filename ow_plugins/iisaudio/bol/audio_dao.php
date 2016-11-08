<?php

/**
 * Copyright (c) 2016, Milad Heshmati
 * All rights reserved.
 */

/**
 * @author Milad Heshmati <milad.heshmati@gmail.com>
 * @package ow_plugins.iisaudio.bol
 * @since 1.0
 */

class IISAUDIO_BOL_AudioDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var IISAUDIO_BOL_AudioDao
     */
    private static $classInstance;

    /***
     * @return IISAUDIO_BOL_AudioDao
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * Constructor.
     */
    protected function __construct()
    {
        parent::__construct();
    }

    /***
     * @return string
     */
    public function getDtoClassName()
    {
        return 'IISAUDIO_BOL_Audio';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'iis_audio';
    }

    /***
     * @param $userId
     * @return array
     */
    public function findAudiosByUserId($userId)
    {
        $ex=new OW_Example();
        $ex->andFieldEqual('userId',$userId);
        return  $this->findListByExample($ex);
    }

    /***
     * @param $id
     * @return mixed
     */
    public function findAudiosById($id)
    {
        $ex=new OW_Example();
        $ex->andFieldEqual('id',$id);
        return  $this->findObjectByExample($ex);
    }

    /***
     * @param $userId
     * @param int $first
     * @param int $count
     * @return array
     */
    public function findListOrderedByDate($userId, $first = 0, $count = 10)
    {
        $example = new OW_Example();
        $example->andFieldEqual('userId', $userId);
        $example->setLimitClause($first, $count);
        $example->setOrder("`addDateTime` DESC");
        return $this->findListByExample($example);
    }
}