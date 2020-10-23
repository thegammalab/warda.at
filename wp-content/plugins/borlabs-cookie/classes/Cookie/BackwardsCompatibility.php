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

use BorlabsCookie\Cookie\Frontend\Shortcode;

class BackwardsCompatibility
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
     * shortcodeBlockedContent function.
     *
     * @access public
     * @param mixed $atts
     * @param mixed $content
     * @return void
     */
    public function shortcodeBlockedContent($atts, $content)
    {
        if (!empty($atts['type'])) {
            $atts['id'] = $atts['type'];
        }

        $content = Shortcode::getInstance()->handleTypeContentBlocker($atts, $content);

        return $content;
    }
}