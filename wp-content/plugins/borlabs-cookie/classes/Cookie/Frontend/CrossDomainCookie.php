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
use BorlabsCookie\Cookie\Tools;

class CrossDomainCookie
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
     * handleRequest function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function handleRequest($data)
    {
        if (!empty($data['cookieData']) && !empty($_SERVER['HTTP_REFERER'])) {

            // Validate referer
            $refererURLInfo = parse_url($_SERVER['HTTP_REFERER']);

            if (in_array($refererURLInfo['scheme'] . '://' . $refererURLInfo['host'] .'/', Config::getInstance()->get('crossDomainCookie')) === false) {

                return;
            }

            $cookieData = stripslashes($data['cookieData']);

            if (Tools::getInstance()->isStringJSON($cookieData)) {

                $cookieData = json_decode($cookieData, true);
                $language = !empty($_GET['cookieLang']) ? strtolower(preg_replace('/[^a-z\-_]+/', '', $_GET['cookieLang'])) : 'en';

                $language = apply_filters('borlabsCookie/crossDomainCookie/language', $language);

                $consents = [];

                $allowedCookieGroups = Cookies::getInstance()->getAllCookieGroupsOfLanguage($language);
                $allowedCookies = Cookies::getInstance()->getAllCookiesOfLanguage($language);

                if (empty($allowedCookieGroups) && empty($allowedCookies)) {

                    $language = Multilanguage::getInstance()->getCurrentLanguageCode();

                    $allowedCookieGroups = Cookies::getInstance()->getAllCookieGroupsOfLanguage($language);
                    $allowedCookies = Cookies::getInstance()->getAllCookiesOfLanguage($language);
                }

                // Validate consents
                if (!empty($cookieData['consents'])) {
                    foreach ($cookieData['consents'] as $cookieGroup => $cookies) {
                        if (!empty($allowedCookieGroups[$cookieGroup])) {
                            $consents[$cookieGroup] = [];

                            if (!empty($cookies)) {
                                foreach ($cookies as $cookie) {
                                    if (!empty($allowedCookies[$cookie])) {
                                        $consents[$cookieGroup][] = $cookie;
                                    }
                                }
                            }
                        }
                    }
                }

                $consents = apply_filters('borlabsCookie/crossDomainCookie/consents', $consents, $cookieData);

                $cookieData['consents'] = $consents;

                $siteURL = get_home_url();
                $siteURLInfo = parse_url($siteURL);
                $cookiePath = !empty($siteURLInfo['path']) ? $siteURLInfo['path'] : "/";

                if (Config::getInstance()->get('automaticCookieDomainAndPath') === false) {
                    $cookiePath = Config::getInstance()->get('cookiePath');
                }

                $cookieData['domainPath'] = Config::getInstance()->get('cookieDomain') . $cookiePath;

                Log::getInstance()->add($cookieData, $language);

                $cookieInformation = [];

                $cookieInformation[] = 'borlabs-cookie=' . rawurlencode(json_encode($cookieData));

                /* Cookie Domain */
                if (!empty(Config::getInstance()->get('cookieDomain')) && empty(Config::getInstance()->get('automaticCookieDomainAndPath'))) {
                    $cookieInformation[] = 'domain=' . Config::getInstance()->get('cookieDomain');
                }

                /* Cookie Path */
                $cookieInformation[] = 'path=' . Config::getInstance()->get('cookiePath');

                /* Expiration Date */
                $cookieInformation[] = 'expires=' . $cookieData['expires'];

                /* Set cookie */
                $javascript = '<script>document.cookie = "' . implode(';', $cookieInformation) . '";</script>';

                /* Cross-Cookie workaround due SameSite Policy - Does not work in incognito mode because browsers block third party cookies in that mode by default */
                header('Set-Cookie: borlabs-cookie=' . rawurlencode(json_encode($cookieData)) . '; SameSite=None; Secure');

                echo "<html><head><meta name=\"robots\" content=\"noindex,nofollow,norarchive\"></head><body>" . $javascript . "</body></html>";
            }
        }
    }
}