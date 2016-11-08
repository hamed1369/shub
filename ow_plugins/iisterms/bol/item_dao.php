<?php

/**
 * IIS Terms
 */

/**
 * Data Access Object for `IISTERMS_BOL_Item` table.
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisterms.bol
 * @since 1.0
 */
class IISTERMS_BOL_ItemDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var IISTERMS_BOL_ItemDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return IISTERMS_BOL_ItemDao
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
        return 'IISTERMS_BOL_Item';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'iisterms_items';
    }

    /**
     * @return IISTERMS_BOL_Item
     */
    public function getItemById( $id )
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('id', $id);
        return $this->findObjectByExample($ex);
    }

    public function getItemsUsingStatus($use,$sectionId){
        $ex = new OW_Example();
        $ex->andFieldEqual('langId', OW::getLanguage()->getInstance()->getCurrentId());
        $ex->andFieldEqual('use', $use);
        $ex->andFieldEqual('sectionId', $sectionId);
        $ex->setOrder('`order` ASC');
        return $this->findListByExample($ex);
    }

    public function getAllItemSorted($sectionId){
        $ex = new OW_Example();
        $ex->andFieldEqual('langId', OW::getLanguage()->getInstance()->getCurrentId());
        $ex->andFieldEqual('sectionId', $sectionId);
        $ex->setOrder('`order` ASC');
        return $this->findListByExample($ex);
    }

    /***
     * @param $sectionId
     * @param $header
     * @param $description
     * @param $langId
     * @return mixed
     */
    public function getItem($sectionId, $header, $description, $langId)
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('langId', $langId);
        $ex->andFieldEqual('sectionId', $sectionId);
        $ex->andFieldEqual('header', $header);
        $ex->andFieldEqual('description', $description);
        return $this->findObjectByExample($ex);
    }

    public function getMaxOrder($use, $sectionId)
    {
        $query = "SELECT MAX(`order`) FROM `{$this->getTableName()}` WHERE `sectionId` = :sectionId and `use` = :use";
        return $this->dbo->queryForColumn($query,array('sectionId' => $sectionId, 'use' => (int)$use));
    }

    /**
     * @param int $sectionId
     */
    public function deleteItemsBySectionId($sectionId)
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('langId', OW::getLanguage()->getInstance()->getCurrentId());
        $ex->andFieldEqual('sectionId', $sectionId);
        $this->deleteByExample($ex);
    }
}