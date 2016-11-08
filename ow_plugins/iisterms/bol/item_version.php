<?php

/**
 * IIS Terms
 */

/**
 * Data Transfer Object for `iisterms` table.
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisterms.bol
 * @since 1.0
 */
class IISTERMS_BOL_ItemVersion extends OW_Entity
{
    /**
     * @var integer
     */
    public $langId;

    /**
     * @var integer
     */
    public $time;

    /**
     * @var integer
     */
    public $version;

    /**
     * @var integer
     */
    public $order;

    /**
     * @var integer
     */
    public $sectionId;

    /**
     * @var string
     */
    public $header;

    /**
     * @var string
     */
    public $description;

}