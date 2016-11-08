<?php

/**
 * Cover Photo
 */

/**
 * Data Access Object for `cover_photo` table.
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.coverphoto.bol
 * @since 1.0
 */
class COVERPHOTO_BOL_CoverDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var COVERPHOTO_BOL_CoverDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return COVERPHOTO_BOL_CoverDao
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
        return 'COVERPHOTO_BOL_Cover';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'cover_photo';
    }


    /**
     * @return COVERPHOTO_BOL_Cover
     */
    public function findCoverById( $id )
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('id', $id);
        return $this->findObjectByExample($ex);
    }

    /**
     * @return array
     */
    public function findCoversByUserId( $userId )
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('userId', $userId);
        return $this->findListByExample($ex);
    }

    /**
     * @return array
     */
    public function findCoverByUserId( $userId )
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('userId', $userId);
        $ex->andFieldEqual('isCurrent', 1);
        return $this->findObjectByExample($ex);
    }

    /**
     *
     * @return array
     */
    public function findListOrderedByTitle($userId)
    {
        $example = new OW_Example();
        $example->andFieldEqual('userId', $userId);
        $example->setOrder("`title` DESC");

        return $this->findListByExample($example);
    }
}