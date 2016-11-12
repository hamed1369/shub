<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iispasswordchangeinterval.bol
 * @since 1.0
 */
class IISPASSWORDCHANGEINTERVAL_BOL_PasswordValidation extends OW_Entity
{
    public $username;
    public $valid;
    public $token;
    public $email;
    public $tokentime;
    public $passwordtime;

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setValid($valid)
    {
        $this->valid = $valid;
    }

    public function getValid()
    {
        return $this->valid;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setTokentime($tokentime)
    {
        $this->tokentime = $tokentime;
    }

    public function getTokentime()
    {
        return $this->tokentime;
    }

    public function setPasswordtime($passwordtime)
    {
        $this->passwordtime = $passwordtime;
    }

    public function getPasswordtime()
    {
        return $this->passwordtime;
    }
}
