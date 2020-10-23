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

namespace BorlabsCookie\Cookie\Frontend\ThirdParty\Themes;

use BorlabsCookie\Cookie\Frontend\ContentBlocker;
use BorlabsCookie\Cookie\Frontend\JavaScript;

class Elementor
{
    private static $instance;

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
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * detectFacebook function.
     *
     * @access public
     * @param mixed $content
     * @return void
     */
    public function detectFacebook($content, $widget)
    {
        if (strpos($content, 'elementor-facebook-widget fb-page') !== false || strpos($content, 'elementor-facebook-widget fb-post') !== false) {

            // Get settings of the Content Blocker
            $contentBlockerData = ContentBlocker::getInstance()->getContentBlockerData('facebook');

            // Add updated settings, global js, and init js of the Content Blocker
            JavaScript::getInstance()->addContentBlocker(
                $contentBlockerData['content_blocker_id'],
                $contentBlockerData['globalJS'],
                $contentBlockerData['initJS'] . " if (typeof elementorFrontend.init === \"function\") { elementorFrontend.init(); } ",
                $contentBlockerData['settings']
            );

            $content = ContentBlocker::getInstance()->handleContentBlocking($content, '', 'facebook');
        }

        return $content;
    }

    /**
     * detectIframes function.
     *
     * @access public
     * @param mixed $content
     * @param mixed $widget
     * @return void
     */
    public function detectIframes($content, $widget)
    {
        $content = ContentBlocker::getInstance()->detectIframes($content);

        return $content;
    }
}
