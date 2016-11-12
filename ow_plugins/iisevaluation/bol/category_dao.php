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
class IISEVALUATION_BOL_CategoryDao extends OW_BaseDao
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
        return 'IISEVALUATION_BOL_Category';
    }
    
    public function getTableName()
    {
        return OW_DB_PREFIX . 'iisevaluation_category';
    }

    /***
     * @param $categoryId
     * @return IISEVALUATION_BOL_Category
     */
    public function getCategory($categoryId){
        $ex = new OW_Example();
        $ex->andFieldEqual('id', $categoryId);
        return $this->findObjectByExample($ex);
    }

    /***
     * @return array
     */
    public function getCategories(){
        $ex = new OW_Example();
        $ex->setOrder('`order` ASC');
        return $this->findListByExample($ex);
    }

    /***
     * @param OW_Entity $name
     * @param $description
     * @param $icon
     * @return IISEVALUATION_BOL_Category
     */
    public function saveCategory($name, $description, $order, $icon){
        $category = new IISEVALUATION_BOL_Category();
        $category->name = $name;
        $category->description = $description;
        $category->icon = $icon;
        $category->order = $order;
        $this->save($category);
        return $category;
    }

    public function getMaxOrder(){
        $query = "SELECT MAX(`order`) FROM `{$this->getTableName()}`";
        $maxOrder = $this->dbo->queryForColumn($query);
        if ($maxOrder == null) {
            $maxOrder = 0;
        }
        return $maxOrder;
    }

    /***
     * @param $categoryId
     * @param $name
     * @param $description
     * @param $icon
     * @return IISEVALUATION_BOL_Category
     */
    public function update($categoryId, $name, $description, $icon){
        $ex = new OW_Example();
        $ex->andFieldEqual('id', $categoryId);
        $category = $this->findObjectByExample($ex);
        $category->name = $name;
        $category->description = $description;
        $category->icon = $icon;
        $this->save($category);
        return $category;
    }

}
