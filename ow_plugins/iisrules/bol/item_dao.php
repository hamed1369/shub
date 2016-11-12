<?php

/**
 * IIS Terms
 */

/**
 * Data Access Object for `IISRULES_BOL_Item` table.
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisrules.bol
 * @since 1.0
 */
class IISRULES_BOL_ItemDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var IISRULES_BOL_ItemDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return IISRULES_BOL_ItemDao
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
        return 'IISRULES_BOL_Item';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'iisrules_items';
    }

    /***
     * @param $categoryId
     * @param $name
     * @param $description
     * @param $order
     * @param $tag
     * @param $icon
     * @return IISRULES_BOL_Item
     */
    public function saveItem($categoryId, $name, $description, $order, $tag, $icon){
        $item = new IISRULES_BOL_Item();
        $item->name = $name;
        $item->description = $description;
        $item->categoryId = $categoryId;
        $item->order = $order;
        $item->tag = $tag;
        $item->icon = $icon;
        $this->save($item);
        return $item;
    }

    /***
     * @param $id
     * @return mixed
     */
    public function getItem($id){
        $ex = new OW_Example();
        $ex->andFieldEqual('id', $id);
        return $this->findObjectByExample($ex);
    }

    /***
     * @param $categoryId
     * @return array
     */
    public function getItemsByCategory($categoryId){
        $ex = new OW_Example();
        $ex->andFieldEqual('categoryId', $categoryId);
        return $this->findListByExample($ex);
    }

    /***
     * @param $catIds
     * @return int|mixed
     */
    public function getMaxOrder($catIds){
        $query = "SELECT MAX(`order`) FROM `{$this->getTableName()}` where categoryId in (".implode(',',$catIds).")";
        $maxOrder = $this->dbo->queryForColumn($query);
        if ($maxOrder == null) {
            $maxOrder = 0;
        }
        return $maxOrder;
    }

    /**
     * @return IISRULES_BOL_Item
     */
    public function getItemById( $id )
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('id', $id);
        return $this->findObjectByExample($ex);
    }

    /***
     * @param $categoriesId
     * @return array
     */
    public function getAllItems($categoriesId){
        if(empty($categoriesId)){
            return array();
        }
        $ex = new OW_Example();
        $ex->andFieldInArray('categoryId', $categoriesId);
        $ex->setOrder('`order` ASC');
        return $this->findListByExample($ex);
    }

    /***
     * @param $itemId
     * @param $catId
     * @param $name
     * @param $description
     * @param $tag
     * @param $icon
     * @param $order
     * @return mixed
     */
    public function update($itemId, $catId,  $name, $description, $tag, $icon, $order){
        $ex = new OW_Example();
        $ex->andFieldEqual('id', $itemId);
        $item = $this->findObjectByExample($ex);
        $item->name = $name;
        $item->description = $description;
        $item->categoryId = $catId;
        $item->tag = $tag;
//        $item->order = $order;
        $item->icon = $icon;
        $this->save($item);
        return $item;
    }

}