<?php

/**
 * IIS Terms
 */

/**
 * Data Transfer Object for `iisrules` table.
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisrules.bol
 * @since 1.0
 */
class IISRULES_BOL_Item extends OW_Entity
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $icon;

    /**
     * @var string
     */
    public $tag;

    /**
     * @var integer
     */
    public $order;

    /**
     * @var integer
     */
    public $categoryId;

}