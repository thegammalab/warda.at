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

namespace BorlabsCookie\Cookie\Backend;

use BorlabsCookie\Cookie\Config;
use BorlabsCookie\Cookie\Tools;

class Maintenance
{
    private static $instance;

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

    protected function __construct()
    {
    }

    /**
     * cleanUp function.
     *
     * @access public
     * @param bool $optimizeTable (default: false)
     * @return void
     */
    public function cleanUp($optimizeTable = false)
    {
        global $wpdb;

        $table = (Config::getInstance()->get('aggregateCookieConsent') ? $wpdb->base_prefix : $wpdb->prefix)."borlabs_cookie_consent_log";
        $cookieLifetime = Config::getInstance()->get('cookieLifetime');

        // Delete old entries
        $wpdb->query('
            DELETE FROM
                `'.$table.'`
            WHERE
                `stamp` < NOW() - INTERVAL '.intval($cookieLifetime).' DAY
        ');

        // Optimize
        if ($optimizeTable === true) {
            $wpdb->query('OPTIMIZE TABLE `'.$table.'`');
        }
    }
}
