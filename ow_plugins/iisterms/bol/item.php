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
class IISTERMS_BOL_Item extends OW_Entity
{
    /**
     * @var integer
     */
    public $langId;

    /**
     * @var integer
     */
    public $use;

    /**
     * @var integer
     */
    public $notification;

    /**
     * @var integer
     */
    public $email;

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