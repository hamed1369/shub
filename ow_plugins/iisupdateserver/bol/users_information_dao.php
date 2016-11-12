<?php

/**
 * IIS Terms
 */

/**
 * Data Access Object for `UsersInformation` table.
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisupdateserver.bol
 * @since 1.0
 */
class IISUPDATESERVER_BOL_UsersInformationDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var IISUPDATESERVER_BOL_UsersInformationDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return IISUPDATESERVER_BOL_UsersInformationDao
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

    /**
     * @see OW_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassName()
    {
        return 'IISUPDATESERVER_BOL_UsersInformation';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'iisupdateserver_users_information';
    }
}