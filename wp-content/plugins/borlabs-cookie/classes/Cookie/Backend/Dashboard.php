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

use BorlabsCookie\Cookie\API;
use BorlabsCookie\Cookie\Config;
use BorlabsCookie\Cookie\Multilanguage;
use BorlabsCookie\Cookie\Tools;

class Dashboard
{
    private static $instance;

    /**
     * imagePath
     *
     * @var mixed
     * @access private
     */
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
        $news = $this->getNews();

        $borlabsCookieStatus = Config::getInstance()->get('cookieStatus');
        $cookieVersion = esc_html(get_site_option('BorlabsCookieCookieVersion', 1));
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

        $chartData = json_encode($this->getChartData());

        $latestUIDData = $this->getLatestUID();

        include Backend::getInstance()->templatePath.'/dashboard.html.php';
    }

    /**
     * getChartData function.
     *
     * @access public
     * @return void
     */
    public function getChartData()
    {
        global $wpdb;

        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'labels' => '',
                    'backgroundColor' => [],
                    'borderColor' => [],
                    'borderWidth' => 1,
                    'data' => [],
                ]
            ]
        ];

        // Get Chart data
        $tableCookieConsentLog = (Config::getInstance()->get('aggregateCookieConsent') ? $wpdb->base_prefix : $wpdb->prefix)."borlabs_cookie_consent_log";

        $cookieVersion = get_site_option('BorlabsCookieCookieVersion', 1);

        // Get last 10000 entries
        $consentsLogs = $wpdb->get_results('
            SELECT
                `uid`,
                `consents`
            FROM
                `'.$tableCookieConsentLog.'`
            WHERE
                `is_latest` = 1
                AND
                `cookie_version` = "'.esc_sql($cookieVersion).'"
            ORDER BY
                `stamp` DESC
            LIMIT
                0, 10000
        ');

        $chartDataValues = [];

        foreach ($consentsLogs as $logData) {

            $consentData = unserialize($logData->consents);

            foreach ($consentData as $cookieGroup => $cookies) {

                if (empty($chartDataValues[$cookieGroup])) {
                    $chartDataValues[$cookieGroup] = 0;
                }

                $chartDataValues[$cookieGroup]++;
            }
        }

        // Get all Cookie Groups
        $tableCookieGroup = $wpdb->prefix.'borlabs_cookie_groups';

        $cookieGroups = $wpdb->get_results('
            SELECT
                `group_id`,
                `name`
            FROM
                `'.$tableCookieGroup.'`
            WHERE
                `language` = "'.esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()).'"
                AND
                `status` = 1
            ORDER BY
                `position` ASC
        ');

        $index = 0;

        foreach ($cookieGroups as $data) {
            $chartData['labels'][] = $data->name;
            $chartData['datasets'][0]['backgroundColor'][$index] = $this->getColor($index, 0.8);
            $chartData['datasets'][0]['borderColor'][$index] = $this->getColor($index, 1);
            $chartData['datasets'][0]['data'][$index] = isset($chartDataValues[$data->group_id]) ? $chartDataValues[$data->group_id] : 0;

            $index++;
        }

        return $chartData;
    }

    /**
     * getColor function.
     *
     * @access public
     * @param mixed $index
     * @param int $opacity (default: 1)
     * @return void
     */
    public function getColor($index, $opacity = 1)
    {
        $colors = [
            'rgba(255, 99, 132, %opacity%)',
            'rgba(255, 159, 64, %opacity%)',
            'rgba(255, 205, 86, %opacity%)',
            'rgba(75, 192, 192, %opacity%)',
            'rgba(54, 162, 235, %opacity%)',
            'rgba(153, 102, 255, %opacity%)',
            'rgba(201, 203, 207, %opacity%)',
        ];

        $color = str_replace(
            '%opacity%',
            round($opacity, 2),
            isset($colors[$index]) ? $colors[$index] : $colors[0]
        );

        return $color;
    }

    public function getLatestUID()
    {
        global $wpdb;

        $tableCookieConsentLog = (Config::getInstance()->get('aggregateCookieConsent') ? $wpdb->base_prefix : $wpdb->prefix)."borlabs_cookie_consent_log";

        $consentLogs = $wpdb->get_results('
            SELECT
                `uid`,
                `cookie_version`,
                `stamp`
            FROM
                `'.$tableCookieConsentLog.'`
            WHERE
                `is_latest` = 1
            ORDER BY
                `stamp` DESC
            LIMIT
                0, 5
        ');

        return $consentLogs;
    }

    /**
     * getNews function.
     *
     * @access public
     * @return void
     */
    public function getNews()
    {
        $newsData = [];

        $lastCheck = intval(get_site_option('BorlabsCookieNewsLastCheck', 0));

        if (empty($lastCheck) || $lastCheck < intval(date('Ymd', mktime(date('H'), date('i'), date('s'), date('m'), date('d')-3)))) {
            $responseNews = API::getInstance()->getNews();
        }

        $borlabsCookieNews = get_site_option('BorlabsCookieNews');

        if (!empty($borlabsCookieNews)) {

            $currentLanguageCode = Multilanguage::getInstance()->getCurrentLanguageCode();

            if (!empty($borlabsCookieNews->{$currentLanguageCode})) {
                $newsData = $borlabsCookieNews->{$currentLanguageCode};
            } else {
                if (!empty($borlabsCookieNews->en)) {
                    $newsData = $borlabsCookieNews->en;
                }
            }
        }

        return $newsData;
    }
}