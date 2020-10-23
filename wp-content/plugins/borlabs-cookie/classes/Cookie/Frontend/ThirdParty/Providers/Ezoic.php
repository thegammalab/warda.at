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

namespace BorlabsCookie\Cookie\Frontend\ThirdParty\Providers;

use BorlabsCookie\Cookie\Frontend\Cookies;

class Ezoic
{
    private static $instance;

    private $isEzoicActive = false;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $allCookiesGroups = Cookies::getInstance()->getAllCookieGroups();

        if (!empty($allCookiesGroups)) {

            foreach ($allCookiesGroups as $cookieGroupData) {
                if ($cookieGroupData->group_id === 'essential') {

                    if (!empty($cookieGroupData->cookies['ezoic'])) {
                        $this->isEzoicActive = true;
                    }

                    break;
                } else {
                    continue;
                }
            }
        }
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * addDataAttribute function.
     *
     * @access public
     * @param mixed $tag
     * @param mixed $handle
     * @param mixed $src
     * @return void
     */
    public function addDataAttribute($tag, $handle, $src)
    {
        if ($this->isEzoicActive) {
            if ($handle === 'borlabs-cookie' || $handle === 'borlabs-cookie-prioritize') {
                $tag = preg_replace('/\<script/', '<script data-pagespeed-no-defer', $tag, 1);
            }
        }

        return $tag;
    }
}
