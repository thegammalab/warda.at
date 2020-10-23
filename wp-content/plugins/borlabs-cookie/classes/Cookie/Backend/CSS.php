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

class CSS
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
     * save function.
     *
     * @access public
     * @param mixed $language (default: null)
     * @return void
     */
    public function save($language = null)
    {
        // Get language code
        if (empty($language)) {
            $language = Multilanguage::getInstance()->getCurrentLanguageCode();
        }

        // Build new CSS file
        $this->buildCSSFile($language);

        // Update style version
        $styleVersion = get_option('BorlabsCookieStyleVersion_' . $language, 1);
        $styleVersion = intval($styleVersion) + 1;

        update_option('BorlabsCookieStyleVersion_' . $language, $styleVersion, false);
    }

    /**
     * buildCSSFile function.
     *
     * @access public
     * @param mixed $language (default: null)
     * @return void
     */
    public function buildCSSFile($language = null)
    {
        $pluginPath = BORLABS_COOKIE_PLUGIN_PATH;

        // Get language code
        if (empty($language)) {
            $language = Multilanguage::getInstance()->getCurrentLanguageCode();
        }

        $css = '';

        if (file_exists(WP_CONTENT_DIR . '/cache/borlabs-cookie')) {

            if (defined('BORLABS_COOKIE_DEV_MODE') && BORLABS_COOKIE_DEV_MODE === true) {
                $css .= '/* Using main css file */';
            } else {
                $css .= file_get_contents($pluginPath . 'css/borlabs-cookie.css');
            }

            // Animation CSS
            if (Config::getInstance()->get('cookieBoxAnimation')) {
                if (file_exists($pluginPath . 'vendor/animate/animations/' . Config::getInstance()->get('cookieBoxAnimationIn') . '.css')) {
                    $css .= file_get_contents($pluginPath . 'vendor/animate/animations/' . Config::getInstance()->get('cookieBoxAnimationIn') . '.css');
                }

                if (file_exists($pluginPath . 'vendor/animate/animations/' . Config::getInstance()->get('cookieBoxAnimationOut') . '.css')) {
                    $css .= file_get_contents($pluginPath . 'vendor/animate/animations/' . Config::getInstance()->get('cookieBoxAnimationOut') . '.css');
                }
            }

            $css .= $this->getCookieBoxCSS();
            $css .= Config::getInstance()->get('cookieBoxCustomCSS');
            $css .= $this->getContentBlockerCSS();

            file_put_contents(
                WP_CONTENT_DIR . '/cache/borlabs-cookie/borlabs-cookie_' . get_current_blog_id() . '_' . $language . '.css',
                preg_replace("/[ \t]+/", " ", preg_replace("/\s*$^\s*/m", "\n", $css))
            );
        }
    }

    /**
     * getCookieBoxCSS function.
     *
     * @access public
     * @return void
     */
    public function getCookieBoxCSS()
    {
        $bgColorHSL = Tools::getInstance()->hexToHsl(Config::getInstance()->get('cookieBoxBgColor'));
        $brandingColor = '#000';

        if (isset($bgColorHSL[2]) && $bgColorHSL[2] <= 50) {
            $brandingColor = '#fff';
        }

        $css = '';

        // Cookie Box
        $css .= '#BorlabsCookieBox * { font-family: %cookieBoxFontFamily%; }';
        $css .= '#BorlabsCookieBox ._brlbs-bar-advanced ul, #BorlabsCookieBox ._brlbs-box-advanced ul { justify-content: %cookieBoxCookieGroupJustification%; }';
        $css .= '#BorlabsCookieBox ._brlbs-bar, #BorlabsCookieBox ._brlbs-box { background: %cookieBoxBgColor%; border-radius: %cookieBoxBorderRadius%px; color: %cookieBoxTxtColor%; font-size: %cookieBoxFontSize%px; }';
        $css .= '#BorlabsCookieBox a { color: %cookieBoxPrimaryLinkColor%; }';
        $css .= '#BorlabsCookieBox a:hover { color: %cookieBoxPrimaryLinkHoverColor%; }';
        $css .= '#BorlabsCookieBox ._brlbs-btn { background: %cookieBoxBtnColor%; border-radius: %cookieBoxBtnBorderRadius%px; color: %cookieBoxBtnTxtColor%; }';
        $css .= '#BorlabsCookieBox ._brlbs-btn:hover { background: %cookieBoxBtnHoverColor%; border-radius: %cookieBoxBtnBorderRadius%px; color: %cookieBoxBtnHoverTxtColor%; }';
        $css .= '#BorlabsCookieBox ._brlbs-refuse-btn a { background: %cookieBoxRefuseBtnColor%; border-radius: %cookieBoxBtnBorderRadius%px; color: %cookieBoxRefuseBtnTxtColor%; }';
        $css .= '#BorlabsCookieBox ._brlbs-refuse-btn a:hover { background: %cookieBoxRefuseBtnHoverColor%; border-radius: %cookieBoxBtnBorderRadius%px; color: %cookieBoxRefuseBtnHoverTxtColor%; }';
        $css .= '#BorlabsCookieBox ._brlbs-btn-accept-all { background: %cookieBoxAcceptAllBtnColor%; border-radius: %cookieBoxBtnBorderRadius%px; color: %cookieBoxAcceptAllBtnTxtColor%; }';
        $css .= '#BorlabsCookieBox ._brlbs-btn-accept-all:hover { background: %cookieBoxAcceptAllBtnHoverColor%; border-radius: %cookieBoxBtnBorderRadius%px; color: %cookieBoxAcceptAllBtnHoverTxtColor%; }';
        $css .= '#BorlabsCookieBox ._brlbs-btn-accept-all { background: %cookieBoxAcceptAllBtnColor%; border-radius: %cookieBoxBtnBorderRadius%px; color: %cookieBoxAcceptAllBtnTxtColor%; }';
        $css .= '#BorlabsCookieBox ._brlbs-btn-accept-all:hover { background: %cookieBoxAcceptAllBtnHoverColor%; border-radius: %cookieBoxBtnBorderRadius%px; color: %cookieBoxAcceptAllBtnHoverTxtColor%; }';
        $css .= '#BorlabsCookieBox ._brlbs-legal { color: %cookieBoxSecondaryLinkColor%; }';
        $css .= '#BorlabsCookieBox ._brlbs-legal a { color: inherit; }';
        $css .= '#BorlabsCookieBox ._brlbs-legal a:hover { color: %cookieBoxSecondaryLinkHoverColor%; }';
        $css .= '#BorlabsCookieBox ._brlbs-branding { color: '.$brandingColor.'; }';
        $css .= '#BorlabsCookieBox ._brlbs-branding a { color: inherit; }';
        $css .= '#BorlabsCookieBox ._brlbs-branding a:hover { color: inherit; }';
        $css .= '#BorlabsCookieBox ._brlbs-manage a { color: %cookieBoxPrimaryLinkColor%; }';
        $css .= '#BorlabsCookieBox ._brlbs-manage a:hover { color: %cookieBoxPrimaryLinkHoverColor%; }';
        $css .= '#BorlabsCookieBox ._brlbs-refuse { color: %cookieBoxRejectionLinkColor%; }';
        $css .= '#BorlabsCookieBox ._brlbs-refuse a:hover { color: %cookieBoxRejectionLinkHoverColor%; }';

        // Change CSS values if "Show Accept all Button" is active
        if (Config::getInstance()->get('cookieBoxShowAcceptAllButton')) {
            $css .= '#BorlabsCookieBox ul li::before { color: %cookieBoxAcceptAllBtnColor%; }';
        } else {
            $css .= '#BorlabsCookieBox ul li::before { color: %cookieBoxBtnColor%; }';
        }

        // Full width button
        if (Config::getInstance()->get('cookieBoxBtnFullWidth')) {
            $css .= '#BorlabsCookieBox .cookie-box ._brlbs-btn { width: 100%; }';
        }

        // Switch Button
        $css .= '.BorlabsCookie ._brlbs-btn-switch ._brlbs-slider { background-color: %cookieBoxBtnSwitchInactiveBgColor%; }';
        $css .= '.BorlabsCookie ._brlbs-btn-switch input:checked + ._brlbs-slider { background-color: %cookieBoxBtnSwitchActiveBgColor%; }';
        $css .= '.BorlabsCookie ._brlbs-btn-switch ._brlbs-slider::before { background-color: %cookieBoxBtnSwitchInactiveColor%; }';
        $css .= '.BorlabsCookie ._brlbs-btn-switch input:checked + ._brlbs-slider:before { background-color: %cookieBoxBtnSwitchActiveColor%; }';

        // Checkbox
        $css .= '.BorlabsCookie ._brlbs-checkbox ._brlbs-checkbox-indicator { background-color: %cookieBoxCheckboxInactiveBgColor%; border-color: %cookieBoxCheckboxInactiveBorderColor%; border-radius: %cookieBoxCheckboxBorderRadius%px; }';
        $css .= '.BorlabsCookie ._brlbs-checkbox input:checked ~ ._brlbs-checkbox-indicator { background-color: %cookieBoxCheckboxActiveBgColor%; border-color: %cookieBoxCheckboxActiveBorderColor%; }';
        $css .= '.BorlabsCookie ._brlbs-checkbox input:checked ~ ._brlbs-checkbox-indicator::after { border-color: %cookieBoxCheckboxCheckMarkActiveColor%; }';
        $css .= '.BorlabsCookie ._brlbs-checkbox input:disabled ~ ._brlbs-checkbox-indicator { background-color: %cookieBoxCheckboxDisabledBgColor%; border-color: %cookieBoxCheckboxDisabledBorderColor%; }';
        $css .= '.BorlabsCookie ._brlbs-checkbox input:disabled ~ ._brlbs-checkbox-indicator::after { border-color: %cookieBoxCheckboxCheckMarkDisabledColor%; }';

        $css .= '#BorlabsCookieBox .bcac-item { background-color: %cookieBoxAccordionBgColor%; border-radius: %cookieBoxAccordionBorderRadius%px; color: %cookieBoxAccordionTxtColor%; }';
        $css .= '#BorlabsCookieBox .cookie-preference table { background-color: %cookieBoxTableBgColor%; border-radius: %cookieBoxTableBorderRadius%px; color: %cookieBoxTableTxtColor%; }';
        $css .= '#BorlabsCookieBox .cookie-preference table { background-color: %cookieBoxTableBgColor%; border-radius: %cookieBoxTableBorderRadius%px; color: %cookieBoxTableTxtColor%; }';
        $css .= '#BorlabsCookieBox .cookie-preference table tr td, #BorlabsCookieBox .cookie-preference table tr th { background-color: %cookieBoxTableBgColor%; border-color: %cookieBoxTableBorderColor%; }';

        if (Config::getInstance()->get('cookieBoxBtnSwitchRound')) {
            $css .= '.BorlabsCookie ._brlbs-btn-switch ._brlbs-slider { border-radius: 34px; }';
            $css .= '.BorlabsCookie ._brlbs-btn-switch ._brlbs-slider::before { border-radius: 50%; }';
        }

        // Content Blocker
        $bgColorHSL = Tools::getInstance()->hexToHsl(Config::getInstance()->get('contentBlockerBgColor'));
        $css .= '.BorlabsCookie ._brlbs-content-blocker { font-family: %contentBlockerFontFamily%; font-size: %contentBlockerFontSize%px; }';
        $css .= '.BorlabsCookie ._brlbs-content-blocker ._brlbs-caption { background: hsla(' . round($bgColorHSL[0]) . ', ' . round($bgColorHSL[1]) . '%, ' . round($bgColorHSL[2]) . '%, ' . (Config::getInstance()->get('contentBlockerBgOpacity') / 100) . '); color: %contentBlockerTxtColor%; }';
        $css .= '.BorlabsCookie ._brlbs-content-blocker ._brlbs-caption a { color: %contentBlockerLinkColor%; }';
        $css .= '.BorlabsCookie ._brlbs-content-blocker ._brlbs-caption a:hover { color: %contentBlockerLinkHoverColor%; }';
        $css .= '.BorlabsCookie ._brlbs-content-blocker a._brlbs-btn { background: %contentBlockerBtnColor%; border-radius: %contentBlockerBtnBorderRadius%px; color: %contentBlockerBtnTxtColor%; }';
        $css .= '.BorlabsCookie ._brlbs-content-blocker a._brlbs-btn:hover { background: %contentBlockerBtnHoverColor%; color: %contentBlockerBtnHoverTxtColor%; }';

        // Miscellaneous
        // Change CSS values if "Show Accept all Button" is active
        if (Config::getInstance()->get('cookieBoxShowAcceptAllButton')) {
            $css .= 'a._brlbs-btn-cookie-preference { background: %cookieBoxAcceptAllBtnColor% !important; border-radius: %cookieBoxBtnBorderRadius%px !important; color: %cookieBoxAcceptAllBtnTxtColor% !important; }';
            $css .= 'a._brlbs-btn-cookie-preference:hover { background: %cookieBoxAcceptAllBtnHoverColor% !important; color: %cookieBoxAcceptAllBtnHoverTxtColor% !important; }';
        } else {
            $css .= 'a._brlbs-btn-cookie-preference { background: %cookieBoxBtnColor% !important; border-radius: %cookieBoxBtnBorderRadius%px !important; color: %cookieBoxBtnTxtColor% !important; }';
            $css .= 'a._brlbs-btn-cookie-preference:hover { background: %cookieBoxBtnHoverColor% !important; color: %cookieBoxBtnHoverTxtColor% !important; }';
        }

        $css = str_replace(
            [
                '%cookieBoxCookieGroupJustification%',
                '%cookieBoxFontFamily%',
                '%cookieBoxFontSize%',
                '%cookieBoxBgColor%',
                '%cookieBoxTxtColor%',
                '%cookieBoxAccordionBgColor%',
                '%cookieBoxAccordionTxtColor%',
                '%cookieBoxTableBgColor%',
                '%cookieBoxTableTxtColor%',
                '%cookieBoxTableBorderColor%',
                '%cookieBoxBorderRadius%',
                '%cookieBoxBtnBorderRadius%',
                '%cookieBoxCheckboxBorderRadius%',
                '%cookieBoxAccordionBorderRadius%',
                '%cookieBoxTableBorderRadius%',
                '%cookieBoxBtnColor%',
                '%cookieBoxBtnHoverColor%',
                '%cookieBoxBtnTxtColor%',
                '%cookieBoxBtnHoverTxtColor%',
                '%cookieBoxRefuseBtnColor%',
                '%cookieBoxRefuseBtnHoverColor%',
                '%cookieBoxRefuseBtnTxtColor%',
                '%cookieBoxRefuseBtnHoverTxtColor%',
                '%cookieBoxAcceptAllBtnColor%',
                '%cookieBoxAcceptAllBtnHoverColor%',
                '%cookieBoxAcceptAllBtnTxtColor%',
                '%cookieBoxAcceptAllBtnHoverTxtColor%',
                '%cookieBoxBtnSwitchActiveBgColor%',
                '%cookieBoxBtnSwitchInactiveBgColor%',
                '%cookieBoxBtnSwitchActiveColor%',
                '%cookieBoxBtnSwitchInactiveColor%',
                '%cookieBoxCheckboxInactiveBgColor%',
                '%cookieBoxCheckboxInactiveBorderColor%',
                '%cookieBoxCheckboxActiveBgColor%',
                '%cookieBoxCheckboxActiveBorderColor%',
                '%cookieBoxCheckboxDisabledBgColor%',
                '%cookieBoxCheckboxDisabledBorderColor%',
                '%cookieBoxCheckboxCheckMarkActiveColor%',
                '%cookieBoxCheckboxCheckMarkDisabledColor%',
                '%cookieBoxPrimaryLinkColor%',
                '%cookieBoxPrimaryLinkHoverColor%',
                '%cookieBoxSecondaryLinkColor%',
                '%cookieBoxSecondaryLinkHoverColor%',
                '%cookieBoxRejectionLinkColor%',
                '%cookieBoxRejectionLinkHoverColor%',
                '%contentBlockerFontFamily%',
                '%contentBlockerFontSize%',
                '%contentBlockerBgColor%',
                '%contentBlockerTxtColor%',
                '%contentBlockerBtnBorderRadius%',
                '%contentBlockerBtnColor%',
                '%contentBlockerBtnHoverColor%',
                '%contentBlockerBtnTxtColor%',
                '%contentBlockerBtnHoverTxtColor%',
                '%contentBlockerLinkColor%',
                '%contentBlockerLinkHoverColor%',
            ],
            [
                Config::getInstance()->get('cookieBoxCookieGroupJustification'),
                Config::getInstance()->get('cookieBoxFontFamily'),
                Config::getInstance()->get('cookieBoxFontSize'),
                Config::getInstance()->get('cookieBoxBgColor'),
                Config::getInstance()->get('cookieBoxTxtColor'),
                Config::getInstance()->get('cookieBoxAccordionBgColor'),
                Config::getInstance()->get('cookieBoxAccordionTxtColor'),
                Config::getInstance()->get('cookieBoxTableBgColor'),
                Config::getInstance()->get('cookieBoxTableTxtColor'),
                Config::getInstance()->get('cookieBoxTableBorderColor'),
                Config::getInstance()->get('cookieBoxBorderRadius'),
                Config::getInstance()->get('cookieBoxBtnBorderRadius'),
                Config::getInstance()->get('cookieBoxCheckboxBorderRadius'),
                Config::getInstance()->get('cookieBoxAccordionBorderRadius'),
                Config::getInstance()->get('cookieBoxTableBorderRadius'),
                Config::getInstance()->get('cookieBoxBtnColor'),
                Config::getInstance()->get('cookieBoxBtnHoverColor'),
                Config::getInstance()->get('cookieBoxBtnTxtColor'),
                Config::getInstance()->get('cookieBoxBtnHoverTxtColor'),
                Config::getInstance()->get('cookieBoxRefuseBtnColor'),
                Config::getInstance()->get('cookieBoxRefuseBtnHoverColor'),
                Config::getInstance()->get('cookieBoxRefuseBtnTxtColor'),
                Config::getInstance()->get('cookieBoxRefuseBtnHoverTxtColor'),
                Config::getInstance()->get('cookieBoxAcceptAllBtnColor'),
                Config::getInstance()->get('cookieBoxAcceptAllBtnHoverColor'),
                Config::getInstance()->get('cookieBoxAcceptAllBtnTxtColor'),
                Config::getInstance()->get('cookieBoxAcceptAllBtnHoverTxtColor'),
                Config::getInstance()->get('cookieBoxBtnSwitchActiveBgColor'),
                Config::getInstance()->get('cookieBoxBtnSwitchInactiveBgColor'),
                Config::getInstance()->get('cookieBoxBtnSwitchActiveColor'),
                Config::getInstance()->get('cookieBoxBtnSwitchInactiveColor'),
                Config::getInstance()->get('cookieBoxCheckboxInactiveBgColor'),
                Config::getInstance()->get('cookieBoxCheckboxInactiveBorderColor'),
                Config::getInstance()->get('cookieBoxCheckboxActiveBgColor'),
                Config::getInstance()->get('cookieBoxCheckboxActiveBorderColor'),
                Config::getInstance()->get('cookieBoxCheckboxDisabledBgColor'),
                Config::getInstance()->get('cookieBoxCheckboxDisabledBorderColor'),
                Config::getInstance()->get('cookieBoxCheckboxCheckMarkActiveColor'),
                Config::getInstance()->get('cookieBoxCheckboxCheckMarkDisabledColor'),
                Config::getInstance()->get('cookieBoxPrimaryLinkColor'),
                Config::getInstance()->get('cookieBoxPrimaryLinkHoverColor'),
                Config::getInstance()->get('cookieBoxSecondaryLinkColor'),
                Config::getInstance()->get('cookieBoxSecondaryLinkHoverColor'),
                Config::getInstance()->get('cookieBoxRejectionLinkColor'),
                Config::getInstance()->get('cookieBoxRejectionLinkHoverColor'),
                Config::getInstance()->get('contentBlockerFontFamily'),
                Config::getInstance()->get('contentBlockerFontSize'),
                Config::getInstance()->get('contentBlockerBgColor'),
                Config::getInstance()->get('contentBlockerTxtColor'),
                Config::getInstance()->get('contentBlockerBtnBorderRadius'),
                Config::getInstance()->get('contentBlockerBtnColor'),
                Config::getInstance()->get('contentBlockerBtnHoverColor'),
                Config::getInstance()->get('contentBlockerBtnTxtColor'),
                Config::getInstance()->get('contentBlockerBtnHoverTxtColor'),
                Config::getInstance()->get('contentBlockerLinkColor'),
                Config::getInstance()->get('contentBlockerLinkHoverColor'),
            ],
            $css
        );

        return $css;
    }

    /**
     * getContentBlockerCSS function.
     *
     * @access public
     * @param mixed $language (default: null)
     * @return void
     */
    public function getContentBlockerCSS($language = null)
    {
        global $wpdb;

        // Get language code
        if (empty($language)) {
            $language = Multilanguage::getInstance()->getCurrentLanguageCode();
        }

        $css = '';

        $tableName = $wpdb->prefix . 'borlabs_cookie_content_blocker';

        $contentBlocker = $wpdb->get_results('
            SELECT
                `preview_css`
            FROM
                `'.$tableName.'`
            WHERE
                `language` = "'.esc_sql($language).'"
                AND
                `status` = 1
        ');

        if (!empty($contentBlocker)) {
            foreach ($contentBlocker as $key => $data) {
                $css .= $data->preview_css;
            }
        }

        return $css;
    }
}
