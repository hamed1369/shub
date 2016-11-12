<?php

/**
 * IIS Terms
 */

/**
 * Data Transfer Object for `iisupdateserver` table.
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisupdateserver.bol
 * @since 1.0
 */
class IISUPDATESERVER_BOL_Item extends OW_Entity
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
    public $key;

    /**
     * @var string
     */
    public $image;

    /**
     * @var string
     */
    public $type;

    /**
     * @var integer
     */
    public $order;

    /**
     * @var string
     */
    public $guidelineurl;

}