<?php

/**
 * Cover Photo
 */

/**
 * Data Transfer Object for `cover_photo` table.
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.coverphoto.bol
 * @since 1.0
 */
class COVERPHOTO_BOL_Cover extends OW_Entity
{
    /**
     * @var integer
     */
    public $userId;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $hash;

    /**
     * @var string
     */
    public $croppedHash;

    /**
     * @var integer
     */
    public $addDateTime;

    /**
     * @var integer
     */
    public $isCurrent;
}