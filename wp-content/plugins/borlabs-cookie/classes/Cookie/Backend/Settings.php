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

class Settings
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
     * display function.
     *
     * @access public
     * @return void
     */
    public function display()
    {
        $action = false;

        if (!empty($_POST['action'])) {
            $action = $_POST['action'];
        }

        if ($action !== false) {

            // Save Cookie Settings
            if ($action === 'save' && check_admin_referer('borlabs_cookie_settings_save')) {

                $this->save($_POST);

                Messages::getInstance()->add(_x('Saved successfully.', 'Backend / Global / Alert Message', 'borlabs-cookie'), 'success');
            }
        }

        $this->displayOverview();
    }

    /**
     * displayOverview function.
     *
     * @access public
     * @return void
     */
    public function displayOverview()
    {
        $siteURLInfo = parse_url(home_url());
        $networkDomain = $siteURLInfo['host'];
        $networkPath = !empty($siteURLInfo['path']) ? $siteURLInfo['path'] : '/';
        $postTypes = $this->getPostTypes();

        $inputCookieStatus              = !empty(Config::getInstance()->get('cookieStatus')) ? 1 : 0;
        $switchCookieStatus             = $inputCookieStatus ? ' active' : '';
        $cookieVersion                  = esc_html(get_site_option('BorlabsCookieCookieVersion', 1));
        $inputCookieBeforeConsent       = !empty(Config::getInstance()->get('cookieBeforeConsent')) ? 1 : 0;
        $switchCookieBeforeConsent      = $inputCookieBeforeConsent ? ' active' : '';
        $inputAggregateCookieConsent    = !empty(Config::getInstance()->get('aggregateCookieConsent')) ? 1 : 0;
        $switchAggregateCookieConsent   = $inputAggregateCookieConsent ? ' active' : '';
        $inputCookiesForBots            = !empty(Config::getInstance()->get('cookiesForBots')) ? 1 : 0;
        $switchCookiesForBots           = $inputCookiesForBots ? ' active' : '';
        $inputRespectDoNotTrack         = !empty(Config::getInstance()->get('respectDoNotTrack')) ? 1 : 0;
        $switchRespectDoNotTrack        = $inputRespectDoNotTrack ? ' active' : '';
        $inputReloadAfterConsent        = !empty(Config::getInstance()->get('reloadAfterConsent')) ? 1 : 0;
        $switchReloadAfterConsent       = $inputReloadAfterConsent ? ' active' : '';
        $inputJqueryHandle              = esc_attr(!empty(Config::getInstance()->get('jQueryHandle')) ? Config::getInstance()->get('jQueryHandle') : 'jquery');
        $enabledPostTypes               = !empty(Config::getInstance()->get('metaBox')) ? Config::getInstance()->get('metaBox') : [];

        $inputAutomaticCookieDomainAndPath  = !empty(Config::getInstance()->get('automaticCookieDomainAndPath')) ? 1 : 0;
        $switchAutomaticCookieDomainAndPath = $inputAutomaticCookieDomainAndPath ? ' active' : '';
        $inputCookieDomain              = esc_attr(!empty(Config::getInstance()->get('cookieDomain')) ? Config::getInstance()->get('cookieDomain') : $networkDomain);
        $inputCookiePath                = esc_attr(!empty(Config::getInstance()->get('cookiePath')) ? Config::getInstance()->get('cookiePath') : '');
        $inputCookieLifetime            = esc_attr(!empty(Config::getInstance()->get('cookieLifetime')) ? Config::getInstance()->get('cookieLifetime') : 365);
        $textareaCrossDomainCookie      = esc_textarea(!empty(Config::getInstance()->get('crossDomainCookie')) ? implode("\n", Config::getInstance()->get('crossDomainCookie')) : '');

        // Check if Do Not Track is enabled
        $doNotTrackIsActive = false;

        if (!empty($_SERVER['HTTP_DNT'])) {
            $doNotTrackIsActive = true;
        }

        // Check if host is different
        $cookieDomainIsDifferent = false;

        if (!empty(Config::getInstance()->get('cookieDomain')) && $networkDomain !== Config::getInstance()->get('cookieDomain')) {

            if (strpos(Config::getInstance()->get('cookieDomain'), '.') !== 0 || strpos($networkDomain, ltrim(Config::getInstance()->get('cookieDomain'), '.')) === false) {
                $cookieDomainIsDifferent = true;
            }
        }

        include Backend::getInstance()->templatePath.'/settings.html.php';
    }

    /**
     * getPostTypes function.
     *
     * @access public
     * @return void
     */
    public function getPostTypes()
    {
        $postTypes = get_post_types(['public'=>true], 'objects');

        $orderedPostTypes = [];

        // Build list
        foreach ($postTypes as $postType) {
            $orderedPostTypes[$postType->name] = $postType->label;
        }

        // Order list
        asort($orderedPostTypes, SORT_NATURAL | SORT_FLAG_CASE);

        $newOrderedPostTypes = [];

        foreach ($orderedPostTypes as $postType => $postTypeData) {

            // Exclude attachments from list
            if (!in_array($postType, ['attachment'])) {
                $newOrderedPostTypes[$postType] = $postTypes[$postType];
            }
        }

        unset($postTypes);
        unset($orderedPostTypes);

        return $newOrderedPostTypes;
    }

    /**
     * save function.
     *
     * @access public
     * @param mixed $formData
     * @return void
     */
    public function save($formData)
    {
        $updatedConfig = Config::getInstance()->get();

        $updatedConfig['cookieStatus'] = !empty($formData['cookieStatus']) ? true : false;

        if (!empty($formData['updateCookieVersion'])) {
            $currentVersion = get_site_option('BorlabsCookieCookieVersion', 1);

            update_site_option('BorlabsCookieCookieVersion', $currentVersion + 1);
        }

        $updatedConfig['cookieBeforeConsent']       = !empty($formData['cookieBeforeConsent']) ? true : false;
        $updatedConfig['aggregateCookieConsent']    = !empty($formData['aggregateCookieConsent']) ? true : false;
        $updatedConfig['cookiesForBots']            = !empty($formData['cookiesForBots']) ? true : false;
        $updatedConfig['respectDoNotTrack']         = !empty($formData['respectDoNotTrack']) ? true : false;
        $updatedConfig['reloadAfterConsent']        = !empty($formData['reloadAfterConsent']) ? true : false;
        $updatedConfig['jQueryHandle']              = !empty($formData['jQueryHandle']) ? preg_replace('/[^a-zA-z0-9\-_\.]+/', '', stripslashes($formData['jQueryHandle'])) : 'jquery';
        $updatedConfig['metaBox']                   = !empty($formData['metaBox']) ? $formData['metaBox'] : [];

        $siteURLInfo = parse_url(home_url());
        $networkDomain = $siteURLInfo['host'];

        if (!empty($formData['cookieDomain'])) {
            $formData['cookieDomain'] = str_replace(
                ['https://', 'http://'],
                '',
                stripslashes($formData['cookieDomain'])
            );
        }

        $updatedConfig['automaticCookieDomainAndPath']  = !empty($formData['automaticCookieDomainAndPath']) ? true : false;
        $updatedConfig['cookieDomain']                  = !empty($formData['cookieDomain']) ? stripslashes($formData['cookieDomain']) : $networkDomain;
        $updatedConfig['cookiePath']                    = !empty($formData['cookiePath']) ? stripslashes($formData['cookiePath']) : '/';
        $updatedConfig['cookieLifetime']                = !empty($formData['cookieLifetime']) ? intval($formData['cookieLifetime']) : 365;

        // Clean hosts
        $updatedConfig['crossDomainCookie'] = Tools::getInstance()->cleanHostList($formData['crossDomainCookie'], true);

        // Save config
        Config::getInstance()->saveConfig($updatedConfig);

        // Update CSS File
        CSS::getInstance()->save();
    }
}
