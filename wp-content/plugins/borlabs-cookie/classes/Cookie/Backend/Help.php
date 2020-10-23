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
use BorlabsCookie\Cookie\Multilanguage;
use BorlabsCookie\Cookie\Tools;

class Help
{
    private static $instance;

    private $imagePath;

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
        $this->imagePath = plugins_url('images', realpath(__DIR__.'/../../'));
    }

    /**
     * display function.
     *
     * @access public
     * @return void
     */
    public function display()
    {
        $borlabsCookieStatus = Config::getInstance()->get('cookieStatus');
        $statusCacheFolder = SystemCheck::getInstance()->checkCacheFolders();
        $statusSSLSettings = SystemCheck::getInstance()->checkSSLSettings();

        $statusTableContentBlocker = SystemCheck::getInstance()->checkTableContentBlocker();
        $statusTableCookieConsentLog = SystemCheck::getInstance()->checkTableCookieConsentLog();
        $statusTableCookieGroups = SystemCheck::getInstance()->checkTableCookieGroups();
        $statusTableCookies = SystemCheck::getInstance()->checkTableCookies();
        $statusTableScriptBlocker = SystemCheck::getInstance()->checkTableScriptBlocker();

        $statusDefaultContentBlocker = SystemCheck::getInstance()->checkDefaultContentBlocker();
        $statusDefaultCookieGroups = SystemCheck::getInstance()->checkDefaultCookieGroups();
        $statusDefaultCookies = SystemCheck::getInstance()->checkDefaultCookies();

        // Fix Script Blocker Table
        SystemCheck::getInstance()->checkAndFixScriptBlockerTable();

        // Check and change index of log table
        SystemCheck::getInstance()->checkAndChangeCookieConsentLogIndex();

        // Check and change columns of cookie table
        SystemCheck::getInstance()->checkAndChangeCookiesTable();

        $totalConsentLogs = number_format_i18n(SystemCheck::getInstance()->getTotalConsentLogs());
        $consentLogTableSize = number_format_i18n(SystemCheck::getInstance()->getConsentLogTableSize(), 2);

        $language = Multilanguage::getInstance()->getCurrentLanguageCode();

        $loadingIcon = $this->imagePath.'/borlabs-cookie-icon-black.svg';

        include Backend::getInstance()->templatePath.'/help.html.php';
    }
}