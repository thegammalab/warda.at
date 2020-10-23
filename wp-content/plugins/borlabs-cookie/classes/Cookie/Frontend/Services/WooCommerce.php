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

namespace BorlabsCookie\Cookie\Frontend\Services;

class WooCommerce
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

    /**
     * __construct function.
     *
     * @access protected
     * @return void
     */
    protected function __construct()
    {
    }

    /**
     * getDefault function.
     *
     * @access public
     * @return void
     */
    public function getDefault()
    {
        $data = [
            'cookieId' => 'woocommerce',
            'service' => 'WooCommerce',
            'name' => 'WooCommerce',
            'provider' => _x('Owner of this website', 'Frontend / Cookie / WooCommerce / Name', 'borlabs-cookie'),
            'purpose' => _x('Helps WooCommerce determine when cart contents/data changes. Contains a unique code for each customer so that it knows where to find the cart data in the database for each customer. Allows customers to dismiss the store notifications.', 'Frontend / Cookie / WooCommerce / Text', 'borlabs-cookie'),
            'privacyPolicyURL' => '',
            'hosts' => [],
            'cookieName' => 'woocommerce_cart_hash, woocommerce_items_in_cart, wp_woocommerce_session_, woocommerce_recently_viewed, store_notice[notice id]',
            'cookieExpiry' => _x('Session / 2 Days', 'Frontend / Cookie / WooCommerce / Text', 'borlabs-cookie'),
            'optInJS' => '',
            'optOutJS' => '',
            'fallbackJS' => '',
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => false,
            ],
            'status' => true,
            'undeletetable' => false,
        ];

        return $data;
    }
}
