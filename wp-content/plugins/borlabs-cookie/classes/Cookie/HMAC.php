<?php
/*
 * ----------------------------------------------------------------------
 *
 *                          Borlabs Cookie
 *                      developed by Borlabs
 *
 * ----------------------------------------------------------------------
 *
 * Copyright 2018-2020 Borlabs - Benjamin A. Bornschein. All rights reserved.
 * This file may not be redistributed in whole or significant part.
 * Content of this file is protected by international copyright laws.
 *
 * ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 * @copyright Borlabs - Benjamin A. Bornschein, https://borlabs.io
 * @author Benjamin A. Bornschein, Borlabs ben@borlabs.io
 *
 */

namespace BorlabsCookie\Cookie;

class HMAC
{
    private static $instance = null;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public function __construct()
    {
    }

    /**
     * isValid function.
     *
     * @access public
     * @param mixed $data
     * @param mixed $salt
     * @param mixed $hash
     * @return void
     */
    public function isValid($data, $salt, $hash)
    {
        $isValid = false;

        if (!is_string($data)) {
            $data = json_encode($data);
        }

        $dataHash = hash_hmac('sha256', $data, $salt);

        if ($dataHash == $hash) {
            $isValid = true;
        }

        return $isValid;
    }

    /**
     * hash function.
     *
     * @access public
     * @param mixed $data
     * @param mixed $salt
     * @return void
     */
    public function hash($data, $salt)
    {
        if (!is_string($data)) {
            $data = json_encode($data);
        }

        $hash = hash_hmac('sha256', $data, $salt);

        return $hash;
    }
}