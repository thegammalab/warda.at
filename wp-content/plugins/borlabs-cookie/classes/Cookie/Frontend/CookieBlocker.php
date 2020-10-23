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

namespace BorlabsCookie\Cookie\Frontend;

use BorlabsCookie\Cookie\Config;
use BorlabsCookie\Cookie\Multilanguage;

class CookieBlocker
{
    private static $instance;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * handleBlocking function.
     *
     * @access public
     * @return void
     */
    public function handleBlocking()
    {
        if (!empty($_COOKIE)) {

            // Get all Cookies were blocking is active
            $cookieGroups = Cookies::getInstance()->getAllCookieGroups();

            if (!empty($cookieGroups)) {
                foreach ($cookieGroups as $groupData) {
                    if (!empty($groupData->cookies)) {
                        foreach ($groupData->cookies as $cookieData) {
                            if (!empty($cookieData->settings['blockCookiesBeforeConsent'])) {

                                // Check if consent was given
                                if (Cookies::getInstance()->checkConsent($cookieData->cookie_id) === false) {

                                    // Find and block/delete cookies
                                    $cookieNameList = $this->prepareCookieNamesList($cookieData->cookie_name);

                                    if (!empty($cookieNameList)) {
                                        foreach ($cookieNameList as $cookieName) {
                                            if (strpos($cookieName, '*') !== false) {
                                                $this->deleteImpreciseCookie($cookieName);
                                            } else {
                                                $this->deletePreciseCookie($cookieName);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * prepareCookieNamesList function.
     *
     * @access public
     * @param mixed $cookieNames
     * @return void
     */
    public function prepareCookieNamesList ($cookieNames)
    {
        $cookieNameList = [];

        if (!empty($cookieNames) && is_string($cookieNames)) {

            $cookieNames = explode(',', $cookieNames);

            if (!empty($cookieNames)) {
                foreach ($cookieNames as $cookieName) {
                    $cookieName = trim($cookieName);

                    if (!empty($cookieName)) {
                        $cookieNameList[$cookieName] = $cookieName;
                    }
                }
            }
        }

        return $cookieNameList;
    }

    /**
     * deletePreciseCookie function.
     *
     * @access public
     * @param mixed $cookieName
     * @return void
     */
    public function deletePreciseCookie($cookieName)
    {
        if (!empty($_COOKIE[$cookieName])) {

            unset($_COOKIE[$cookieName]);

            setcookie($cookieName, null, -1, '/');
        }
    }

    /**
     * deleteImpreciseCookie function.
     *
     * @access public
     * @param mixed $cookieName
     * @return void
     */
    public function deleteImpreciseCookie($impreciseCookieName)
    {
        if (!empty($_COOKIE)) {

            $impreciseCookieName = str_replace('*', '', $impreciseCookieName);

            foreach ($_COOKIE as $cookieName => $cookieData) {
                if (strpos($cookieName, $impreciseCookieName) !== false) {

                    unset($_COOKIE[$cookieName]);

                    setcookie($cookieName, null, -1, '/');
                }
            }
        }
    }
}