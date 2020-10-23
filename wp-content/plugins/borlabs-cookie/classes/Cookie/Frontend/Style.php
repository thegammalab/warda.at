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
use BorlabsCookie\Cookie\Backend\CSS;

class Style
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

    public function register()
    {
        $language = Multilanguage::getInstance()->getCurrentLanguageCode();

        // Avoid cached styles
        $styleVersion = get_option('BorlabsCookieStyleVersion_' . $language, 1);

        $contentURL = content_url();

        // If CSS does not exist, try to create it on the fly
        if (file_exists(WP_CONTENT_DIR.'/cache/borlabs-cookie/borlabs-cookie_' . get_current_blog_id() . '_' . $language . '.css') === false) {
            CSS::getInstance()->save();
        }

        // If CSS does not exist, try fallback
        if (file_exists(WP_CONTENT_DIR.'/cache/borlabs-cookie/borlabs-cookie_' . get_current_blog_id() . '_' . $language . '.css')) {

            if (defined('BORLABS_COOKIE_DEV_MODE') && BORLABS_COOKIE_DEV_MODE === true) {
                wp_enqueue_style('borlabs-cookie-origin', BORLABS_COOKIE_PLUGIN_URL.'css/borlabs-cookie.css', [], BORLABS_COOKIE_VERSION.'-'.$styleVersion);
            }

            wp_enqueue_style('borlabs-cookie', $contentURL . '/cache/borlabs-cookie/borlabs-cookie_' . get_current_blog_id() . '_' . $language . '.css', [], BORLABS_COOKIE_VERSION . '-' . $styleVersion);

        } else {

            // Fallback
            $inlineCSS = \BorlabsCookie\Cookie\Backend\CSS::getInstance()->getCookieBoxCSS();
            $inlineCSS .= Config::getInstance()->get('cookieBoxCustomCSS');
            $inlineCSS .= \BorlabsCookie\Cookie\Backend\CSS::getInstance()->getContentBlockerCSS($language);

            wp_enqueue_style('borlabs-cookie', BORLABS_COOKIE_PLUGIN_URL.'css/borlabs-cookie.css', [], BORLABS_COOKIE_VERSION . '-' . $styleVersion);
            wp_add_inline_style('borlabs-cookie', $inlineCSS);
        }
    }
}