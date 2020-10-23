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

class Config
{
    private static $instance;

    private $config;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct()
    {
        // Get all config values
        $this->loadConfig();
    }

    /**
     * loadConfig function.
     *
     * @access public
     * @param mixed $language (default: null)
     * @return void
     */
    public function loadConfig($language = null)
    {
        $this->config = $this->getConfig($language);

        return $this->config;
    }

    /**
     * getConfig function.
     *
     * @access public
     * @param mixed $language (default: null)
     * @return void
     */
    public function getConfig($language = null)
    {
        $config = [];

        if (empty($language)) {
            $configLanguage = Multilanguage::getInstance()->getCurrentLanguageCode();
        } else {
            $configLanguage = strtolower($language);
        }

        $config = get_option('BorlabsCookieConfig_'.$configLanguage, 'does not exist');

        if ($config === 'does not exist') {
            $config = $this->defaultConfig();
        }

        return $config;
    }

    /**
     * defaultConfig function.
     *
     * @access public
     * @param bool $installRoutine (default: false)
     * @return void
     */
    public function defaultConfig()
    {
        $imagePath = plugins_url('images', realpath(__DIR__.'/../'));

        $defaultConfig = [
            'cookieStatus' => false,
            'cookieBeforeConsent' => false,
            'aggregateCookieConsent' => false,
            'cookiesForBots' => true,
            'respectDoNotTrack' => false,
            'reloadAfterConsent' => false,
            'jQueryHandle' => 'jquery',
            'metaBox' => [],

            'automaticCookieDomainAndPath' => false,
            'cookieDomain' => '',
            'cookiePath' => '/',
            'cookieLifetime' => 365,
            'crossDomainCookie' => [],

            'showCookieBox' => true,
            'showCookieBoxOnLoginPage' => false,
            'cookieBoxIntegration' => 'javascript',
            'cookieBoxBlocksContent' => true,
            'cookieBoxRefuseOptionType' => 'button',
            'cookieBoxHideRefuseOption' => true,
            'privacyPageId' => 0,
            'privacyPageURL' => '',
            'privacyPageCustomURL' => '',
            'imprintPageId' => 0,
            'imprintPageURL' => '',
            'imprintPageCustomURL' => '',
            'hideCookieBoxOnPages' => [],
            'supportBorlabsCookie' => true,

            'cookieBoxShowAcceptAllButton' => true,
            'cookieBoxIgnorePreSelectStatus' => true,

            'cookieBoxLayout' => 'box-advanced',
            'cookieBoxPosition' => 'top-center',
            'cookieBoxCookieGroupJustification' => 'space-between',
            'cookieBoxAnimation' => true,
            'cookieBoxAnimationDelay' => false,
            'cookieBoxAnimationIn' => 'fadeInDown',
            'cookieBoxAnimationOut' => 'flipOutX',
            'cookieBoxShowLogo' => true,
            'cookieBoxLogo' => $imagePath.'/borlabs-cookie-logo.svg',
            'cookieBoxLogoHD' => $imagePath.'/borlabs-cookie-logo.svg',
            'cookieBoxFontFamily' => 'inherit',
            'cookieBoxFontSize' => 14,
            'cookieBoxBgColor' => '#fff',
            'cookieBoxTxtColor' => '#555',
            'cookieBoxAccordionBgColor' => '#f7f7f7',
            'cookieBoxAccordionTxtColor' => '#555',
            'cookieBoxTableBgColor' => '#fff',
            'cookieBoxTableTxtColor' => '#555',
            'cookieBoxTableBorderColor' => '#eee',
            'cookieBoxBorderRadius' => 4,
            'cookieBoxBtnBorderRadius' => 4,
            'cookieBoxCheckboxBorderRadius' => 4,
            'cookieBoxAccordionBorderRadius' => 0,
            'cookieBoxTableBorderRadius' => 0,

            'cookieBoxBtnFullWidth' => true,
            'cookieBoxBtnColor' => '#f7f7f7',
            'cookieBoxBtnHoverColor' => '#e6e6e6',
            'cookieBoxBtnTxtColor' => '#555',
            'cookieBoxBtnHoverTxtColor' => '#555',
            'cookieBoxRefuseBtnColor' => '#f7f7f7',
            'cookieBoxRefuseBtnHoverColor' => '#e6e6e6',
            'cookieBoxRefuseBtnTxtColor' => '#555',
            'cookieBoxRefuseBtnHoverTxtColor' => '#555',
            'cookieBoxAcceptAllBtnColor' => '#28a745',
            'cookieBoxAcceptAllBtnHoverColor' => '#30c553',
            'cookieBoxAcceptAllBtnTxtColor' => '#fff',
            'cookieBoxAcceptAllBtnHoverTxtColor' => '#fff',
            'cookieBoxBtnSwitchActiveBgColor' => '#28a745',
            'cookieBoxBtnSwitchInactiveBgColor' => '#bdc1c8',
            'cookieBoxBtnSwitchActiveColor' => '#fff',
            'cookieBoxBtnSwitchInactiveColor' => '#fff',
            'cookieBoxBtnSwitchRound' => true,

            'cookieBoxCheckboxInactiveBgColor' => '#fff',
            'cookieBoxCheckboxInactiveBorderColor' => '#a72828',
            'cookieBoxCheckboxActiveBgColor' => '#28a745',
            'cookieBoxCheckboxActiveBorderColor' => '#28a745',
            'cookieBoxCheckboxDisabledBgColor' => '#e6e6e6',
            'cookieBoxCheckboxDisabledBorderColor' => '#e6e6e6',
            'cookieBoxCheckboxCheckMarkActiveColor' => '#fff',
            'cookieBoxCheckboxCheckMarkDisabledColor' => '#999',

            'cookieBoxPrimaryLinkColor' => '#28a745',
            'cookieBoxPrimaryLinkHoverColor' => '#30c553',
            'cookieBoxSecondaryLinkColor' => '#aaa',
            'cookieBoxSecondaryLinkHoverColor' => '#aaa',
            'cookieBoxRejectionLinkColor' => '#888',
            'cookieBoxRejectionLinkHoverColor' => '#888',
            'cookieBoxCustomCSS' => '',
            'cookieBoxTextHeadline' => _x('Privacy Preference', 'Frontend / Cookie Box / Headline', 'borlabs-cookie'),
            'cookieBoxTextDescription' => _x('We use cookies on our website. Some of them are essential, while others help us to improve this website and your experience.', 'Frontend / Cookie Box / Text', 'borlabs-cookie'),
            'cookieBoxTextAcceptButton' => _x('I accept', 'Frontend / Cookie Box / Button Title', 'borlabs-cookie'),
            'cookieBoxTextManageLink' => _x('Individual Privacy Preferences', 'Frontend / Cookie Box / Link Text', 'borlabs-cookie'),
            'cookieBoxTextRefuseLink' => _x('Accept only essential cookies', 'Frontend / Cookie Box / Link Text', 'borlabs-cookie'),
            'cookieBoxTextCookieDetailsLink' => _x('Cookie Details', 'Frontend / Cookie Box / Link Text', 'borlabs-cookie'),
            'cookieBoxTextPrivacyLink' => _x('Privacy Policy', 'Frontend / Cookie Box / Link Text', 'borlabs-cookie'),
            'cookieBoxTextImprintLink' => _x('Imprint', 'Frontend / Cookie Box / Link Text', 'borlabs-cookie'),
            'cookieBoxPreferenceTextHeadline' => _x('Privacy Preference', 'Frontend / Cookie Box / Headline', 'borlabs-cookie'),
            'cookieBoxPreferenceTextDescription' => _x('Here you will find an overview of all cookies used. You can give your consent to whole categories or display further information and select certain cookies.', 'Frontend / Cookie Box / Text', 'borlabs-cookie'),
            'cookieBoxPreferenceTextSaveButton' => _x('Save', 'Frontend / Cookie Box / Button Title', 'borlabs-cookie'),
            'cookieBoxPreferenceTextAcceptAllButton' => _x('Accept all', 'Frontend / Cookie Box / Button Title', 'borlabs-cookie'),
            'cookieBoxPreferenceTextRefuseLink' => _x('Accept only essential cookies', 'Frontend / Cookie Box / Link Text', 'borlabs-cookie'),
            'cookieBoxPreferenceTextBackLink' => _x('Back', 'Frontend / Cookie Box / Link Text', 'borlabs-cookie'),
            'cookieBoxPreferenceTextSwitchStatusActive' => _x('On', 'Frontend / Cookie Box / Switch Button Status', 'borlabs-cookie'),
            'cookieBoxPreferenceTextSwitchStatusInactive' => _x('Off', 'Frontend / Cookie Box / Switch Button Status', 'borlabs-cookie'),
            'cookieBoxPreferenceTextShowCookieLink' => _x('Show Cookie Information', 'Frontend / Cookie Box / Link Text', 'borlabs-cookie'),
            'cookieBoxPreferenceTextHideCookieLink' => _x('Hide Cookie Information', 'Frontend / Cookie Box / Link Text', 'borlabs-cookie'),

            'cookieBoxCookieDetailsTableAccept' => _x('Accept', 'Frontend / Cookie Box / Table Headline', 'borlabs-cookie'),
            'cookieBoxCookieDetailsTableName' => _x('Name', 'Frontend / Cookie Box / Table Headline', 'borlabs-cookie'),
            'cookieBoxCookieDetailsTableProvider' => _x('Provider', 'Frontend / Cookie Box / Table Headline', 'borlabs-cookie'),
            'cookieBoxCookieDetailsTablePurpose' => _x('Purpose', 'Frontend / Cookie Box / Table Headline', 'borlabs-cookie'),
            'cookieBoxCookieDetailsTablePrivacyPolicy' => _x('Privacy Policy', 'Frontend / Cookie Box / Table Headline', 'borlabs-cookie'),
            'cookieBoxCookieDetailsTableHosts' => _x('Host(s)', 'Frontend / Cookie Box / Table Headline', 'borlabs-cookie'),
            'cookieBoxCookieDetailsTableCookieName' => _x('Cookie Name', 'Frontend / Cookie Box / Table Headline', 'borlabs-cookie'),
            'cookieBoxCookieDetailsTableCookieExpiry' => _x('Cookie Expiry', 'Frontend / Cookie Box / Table Headline', 'borlabs-cookie'),

            'cookieBoxConsentHistoryTableDate' => _x('Date', 'Frontend / Consent History / Table Headline', 'borlabs-cookie'),
            'cookieBoxConsentHistoryTableVersion' => _x('Version', 'Frontend / Consent History / Table Headline', 'borlabs-cookie'),
            'cookieBoxConsentHistoryTableConsents' => _x('Consents', 'Frontend / Consent History / Table Headline', 'borlabs-cookie'),

            'contentBlockerHostWhitelist' => [],
            'removeIframesInFeeds' => true,

            'contentBlockerFontFamily' => 'inherit',
            'contentBlockerFontSize' => 14,
            'contentBlockerBgColor' => '#000',
            'contentBlockerTxtColor' => '#fff',
            'contentBlockerBgOpacity' => 80,
            'contentBlockerBtnBorderRadius' => 4,
            'contentBlockerBtnColor' => '#28a745',
            'contentBlockerBtnHoverColor' => '#30c553',
            'contentBlockerBtnTxtColor' => '#fff',
            'contentBlockerBtnHoverTxtColor' => '#fff',
            'contentBlockerLinkColor' => '#28a745',
            'contentBlockerLinkHoverColor' => '#30c553',

            'testEnvironment' => false,
        ];

        return $defaultConfig;
    }

    /**
     * get function.
     *
     * @access public
     * @param mixed $configKey (default: null)
     * @return void
     */
    public function get($configKey = null)
    {
        // Get complete config
        if (empty($configKey)) {
            if (!empty($this->config)) {
                return $this->config;
            } else {
                return false;
            }
        } else {
            if (isset($this->config[$configKey])) {
                return $this->config[$configKey];
            } else {
                // Fallback
                if (isset($this->defaultConfig()[$configKey])) {
                    return $this->defaultConfig()[$configKey];
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * saveConfig function.
     *
     * @access public
     * @param mixed $configData
     * @return void
     */
    public function saveConfig($configData)
    {
        $configLanguage = Multilanguage::getInstance()->getCurrentLanguageCode();

        update_option('BorlabsCookieConfig_'.$configLanguage, $configData, 'no');

        $this->loadConfig();
    }
}
