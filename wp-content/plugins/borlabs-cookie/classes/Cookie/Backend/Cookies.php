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

class Cookies
{
    private static $instance;

    private $defaultServices = [
        'custom' => 'Custom',
        'ezoic' => 'Ezoic',
        'ezoic-marketing' => 'EzoicMarketing',
        'ezoic-preferences' => 'EzoicPreferences',
        'ezoic-statistics' => 'EzoicStatistics',
        'facebook-pixel' => 'FacebookPixel',
        'google-adsense' => 'GoogleAdSense',
        'google-analytics' => 'GoogleAnalytics',
        'google-tag-manager' => 'GoogleTagManager',
        'hotjar' => 'Hotjar',
        'hubspot' => 'HubSpot',
        'matomo' => 'Matomo',
        'matomo-tag-manager' => 'MatomoTagManager',
        'polylang' => 'Polylang',
        'tidio' => 'Tidio',
        'userlike' => 'Userlike',
        'woocommerce' => 'WooCommerce',
        'wpml' => 'WPML',
    ];

    /**
     * tableCookie
     *
     * (default value: '')
     *
     * @var string
     * @access private
     */
    private $tableCookie = '';

    /**
     * tableCookieGroup
     *
     * (default value: '')
     *
     * @var string
     * @access private
     */
    private $tableCookieGroup = '';

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
        global $wpdb;

        $this->tableCookie = $wpdb->prefix.'borlabs_cookie_cookies';
        $this->tableCookieGroup = $wpdb->prefix.'borlabs_cookie_groups';
    }

    /**
     * add function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function add($data)
    {
        global $wpdb;

        $default = [
            'cookieId' => '',
            'language' => '',
            'cookieGroupId' => '',
            'service' => '',
            'name' => '',
            'provider' => '',
            'purpose' => '',
            'privacyPolicyURL' => '',
            'hosts' => [],
            'cookieName' => '',
            'cookieExpiry' => '',
            'optInJS' => '',
            'optOutJS' => '',
            'fallbackJS' => '',
            'settings' => [],
            'position' => 1,
            'status' => false,
            'undeletable' => false,
        ];

        $data = array_merge($default, $data);

        if (empty($data['language'])) {
            $data['language'] = Multilanguage::getInstance()->getCurrentLanguageCode();
        }

        if ($this->checkIdExists($data['cookieId'], $data['language']) === false) {

            $wpdb->query('
                INSERT INTO
                    `'.$this->tableCookie.'`
                    (
                        `cookie_id`,
                        `language`,
                        `cookie_group_id`,
                        `service`,
                        `name`,
                        `provider`,
                        `purpose`,
                        `privacy_policy_url`,
                        `hosts`,
                        `cookie_name`,
                        `cookie_expiry`,
                        `opt_in_js`,
                        `opt_out_js`,
                        `fallback_js`,
                        `settings`,
                        `position`,
                        `status`,
                        `undeletable`
                    )
                VALUES
                    (
                        "'.esc_sql($data['cookieId']).'",
                        "'.esc_sql($data['language']).'",
                        "'.intval($data['cookieGroupId']).'",
                        "'.esc_sql($data['service']).'",
                        "'.esc_sql(stripslashes($data['name'])).'",
                        "'.esc_sql(stripslashes($data['provider'])).'",
                        "'.esc_sql(stripslashes($data['purpose'])).'",
                        "'.esc_sql(stripslashes($data['privacyPolicyURL'])).'",
                        "'.esc_sql(serialize($data['hosts'])).'",
                        "'.esc_sql(stripslashes($data['cookieName'])).'",
                        "'.esc_sql(stripslashes($data['cookieExpiry'])).'",
                        "'.esc_sql(stripslashes($data['optInJS'])).'",
                        "'.esc_sql(stripslashes($data['optOutJS'])).'",
                        "'.esc_sql(stripslashes($data['fallbackJS'])).'",
                        "'.esc_sql(serialize($data['settings'])).'",
                        "'.intval($data['position']).'",
                        "'.(intval($data['status']) ? 1 : 0).'",
                        "'.(intval($data['undeletable']) ? 1 : 0).'"
                    )
            ');

            if (!empty($wpdb->insert_id)) {
                return $wpdb->insert_id;
            }
        }

        return false;
    }

    /**
     * checkIdExists function.
     *
     * @access public
     * @param mixed $cookieId
     * @param mixed $language (default: null)
     * @return void
     */
    public function checkIdExists($cookieId, $language = null)
    {
        global $wpdb;

        if (empty($language)) {
            $language = Multilanguage::getInstance()->getCurrentLanguageCode();
        }

        $checkId = $wpdb->get_results('
            SELECT
                `cookie_id`
            FROM
                `'.$this->tableCookie.'`
            WHERE
                `cookie_id`="'.esc_sql($cookieId).'"
                AND
                `language`="'.esc_sql($language).'"
        ');

        if (!empty($checkId[0]->cookie_id)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * delete function.
     *
     * @access public
     * @param mixed $id
     * @return void
     */
    public function delete($id)
    {
        global $wpdb;

        $wpdb->query('
            DELETE FROM
                `'.$this->tableCookie.'`
            WHERE
                `id` = "'.intval($id).'"
                AND
                `undeletable` = 0
        ');

        return true;
    }

    /**
     * display function.
     *
     * @access public
     * @return void
     */
    public function display()
    {
        $id = null;

        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
        } elseif (!empty($_GET['id'])) {
            $id = $_GET['id'];
        }

        $action = false;

        if (!empty($_POST['action'])) {
            $action = $_POST['action'];
        } elseif (!empty($_GET['action'])) {
            $action = $_GET['action'];
        }

        if ($action !== false) {

            // Validate and save Cookie
            if ($action === 'save' && !empty($id) && check_admin_referer('borlabs_cookie_cookies_save')) {

                // Load service and register hooks and filters
                if (!empty($_POST['service']) && in_array($_POST['service'], $this->defaultServices)) {
                    $Service = '\BorlabsCookie\Cookie\Frontend\Services\\'.$_POST['service'];
                    $serviceData = $Service::getInstance();
                }

                // Validate
                $errorStatus = $this->validate($_POST);

                // Save
                if ($errorStatus === false) {
                    $id = $this->save($_POST);

                    Messages::getInstance()->add(_x('Saved successfully.', 'Backend / Global / Alert Message', 'borlabs-cookie'), 'success');
                }
            }

            // Switch status of Cookie
            if ($action === 'switchStatus' && !empty($id) && wp_verify_nonce($_GET['_wpnonce'], 'switchStatus_'.$id)) {
                $this->switchStatus($id);

                Messages::getInstance()->add(_x('Changed status successfully.', 'Backend / Global / Alert Message', 'borlabs-cookie'), 'success');
            }

            // Delete Cookie
            if ($action === 'delete' && !empty($id) && wp_verify_nonce($_GET['_wpnonce'], 'delete_'.$id)) {
                $this->delete($id);

                Messages::getInstance()->add(_x('Deleted successfully.', 'Backend / Global / Alert Message', 'borlabs-cookie'), 'success');
            }

            // Reset default Cookie
            if ($action === 'resetDefault' && check_admin_referer('borlabs_cookie_cookies_reset_default')) {
                $this->resetDefault();

                Messages::getInstance()->add(_x('Default <strong>Cookies</strong> successfully reset.', 'Backend / Cookies / Alert Message', 'borlabs-cookie'), 'success');
            }
        }

        if ($action === 'edit' || $action === 'save') {
            $this->displayEdit($id, $_POST);
        } elseif ($action === 'cookieServices') {
            $this->displayCookieServices($id);
        } else {
            $this->displayOverview();
        }
    }

    /**
     * displayCookieServices function.
     *
     * @access public
     * @param mixed $id
     * @return void
     */
    public function displayCookieServices($id)
    {
        global $wpdb;

        $cookieGroupData = CookieGroups::getInstance()->get($id);

        if (empty($cookieGroupData) || $cookieGroupData->language !== Multilanguage::getInstance()->getCurrentLanguageCode()) {

            Messages::getInstance()->add(_x('Selected <strong>Cookie Group</strong> does not exist.', 'Backend / Cookies / Alert Message', 'borlabs-cookie'), 'error');

            $this->displayOverview();

        } else {

            $cookieServices = [];

            foreach ($this->defaultServices as $class) {

                $Service = '\BorlabsCookie\Cookie\Frontend\Services\\'.$class;
                $serviceData = $Service::getInstance()->getDefault();

                $cookieServices[$class] = $serviceData['name'];
            }

            $cookieServices = apply_filters('borlabsCookie/cookie/service/selection', $cookieServices);

            asort($cookieServices, SORT_NATURAL | SORT_FLAG_CASE);

            include Backend::getInstance()->templatePath.'/cookies-services.html.php';
        }
    }

    /**
     * displayEdit function.
     *
     * @access public
     * @param int $id (default: 0)
     * @param mixed $formData (default: [])
     * @return void
     */
    public function displayEdit($id = 0, $formData = [])
    {
        $cookieData = new \stdClass();
        $cookieGroupData = new \stdClass();

        if (!empty($id) && $id !== 'new') {

            $cookieData = $this->get($id);
            $cookieGroupData = CookieGroups::getInstance()->get($cookieData->cookie_group_id);

            // Check if the language was switched during editing
            if ($cookieData->language !== Multilanguage::getInstance()->getCurrentLanguageCode()) {

                // Try to get the id for the switched language
                $previousCookieId = $cookieData->cookie_id;
                $cookieData = $this->getByCookieId($cookieData->cookie_id);

                // If not found
                if (empty($cookieData->id)) {

                    Messages::getInstance()->add(_x('The selected <strong>Cookie</strong> is not available in the current language. The data of the original <strong>Cookie</strong> was cloned.', 'Backend / Cookies / Alert Message', 'borlabs-cookie'), 'error');

                    $cookieData = $this->get($id);
                    $cookieData->id = null;
                    $cookieData->language = Multilanguage::getInstance()->getCurrentLanguageCode();

                    // Try to get corresponding Cookie Group
                    $cookieGroupData = CookieGroups::getInstance()->getByGroupId($cookieGroupData->group_id);

                    // If not found
                    if (empty($cookieGroupData->id)) {
                        Messages::getInstance()->add(_x('<strong>Cookie Group</strong> of selected <strong>Cookie</strong> is not available in the current language.', 'Backend / Cookies / Alert Message', 'borlabs-cookie'), 'error');

                        $cookieGroupData = new \stdClass;
                    } else {
                        $cookieData->cookie_group_id = $cookieGroupData->id;
                    }

                } else {
                    $cookieGroupData = CookieGroups::getInstance()->get($cookieData->cookie_group_id);
                }
            }
        } else {

            if (!empty($formData['cookieGroupId'])) {
                $cookieGroupData = CookieGroups::getInstance()->get($formData['cookieGroupId']);
            }
        }

        // Load service
        if (!empty($formData['service'])) {
            $Service = '\BorlabsCookie\Cookie\Frontend\Services\\'.$formData['service'];
        } elseif (!empty($cookieData->service)) {
            $Service = '\BorlabsCookie\Cookie\Frontend\Services\\'.$cookieData->service;
        } else {
            $Service = '\BorlabsCookie\Cookie\Frontend\Services\Custom';
        }

        if (class_exists($Service)) {
            $serviceDefaultData = $Service::getInstance()->getDefault();
        } else {
            $Service = '\BorlabsCookie\Cookie\Frontend\Services\Custom';
            $serviceDefaultData = $Service::getInstance()->getDefault();
        }

        $serviceDefaultData = apply_filters('borlabsCookie/cookie/service/defaultData', $serviceDefaultData, $formData);

        // Only add default data for unsaved Cookie
        if (!empty($serviceDefaultData) && $id === 'new') {
            $cookieData->cookie_id      = $serviceDefaultData['cookieId'];
            $cookieData->service        = $serviceDefaultData['service'];
            $cookieData->name           = $serviceDefaultData['name'];
            $cookieData->provider       = $serviceDefaultData['provider'];
            $cookieData->purpose        = $serviceDefaultData['purpose'];
            $cookieData->privacy_policy_url = $serviceDefaultData['privacyPolicyURL'];
            $cookieData->hosts          = $serviceDefaultData['hosts'];
            $cookieData->cookie_name    = $serviceDefaultData['cookieName'];
            $cookieData->cookie_expiry  = $serviceDefaultData['cookieExpiry'];
            $cookieData->opt_in_js      = $serviceDefaultData['optInJS'];
            $cookieData->opt_out_js     = $serviceDefaultData['optOutJS'];
            $cookieData->fallback_js    = $serviceDefaultData['fallbackJS'];
            $cookieData->settings       = $serviceDefaultData['settings'];
            $cookieData->status         = $serviceDefaultData['status'];
            $cookieData->undeletetable  = $serviceDefaultData['undeletetable'];
        }

        // If no Cookie Group was selected or not available (due language switch) load overview again
        if (empty($cookieGroupData->id)) {

            Messages::getInstance()->add(_x('Selected <strong>Cookie Group</strong> does not exist.', 'Backend / Cookies / Alert Message', 'borlabs-cookie'), 'error');

            $this->displayOverview();

        } else {
            // Re-insert data
            if (isset($formData['cookieId'])) {
                $cookieData->cookie_id = stripslashes($formData['cookieId']);
            }

            if (isset($formData['cookieGroupId'])) {
                $cookieData->cookie_group_id = intval($formData['cookieGroupId']);
            }

            if (isset($formData['service'])) {
                $cookieData->service = stripslashes($formData['service']);
            }

            if (isset($formData['status'])) {
                $cookieData->status = intval($formData['status']);
            }

            if (isset($formData['position'])) {
                $cookieData->position = intval($formData['position']);
            }

            if (isset($formData['name'])) {
                $cookieData->name = stripslashes($formData['name']);
            }

            if (isset($formData['provider'])) {
                $cookieData->provider = stripslashes($formData['provider']);
            }

            if (isset($formData['purpose'])) {
                $cookieData->purpose = stripslashes($formData['purpose']);
            }

            if (isset($formData['privacyPolicyURL'])) {
                $cookieData->privacy_policy_url = stripslashes($formData['privacyPolicyURL']);
            }

            if (isset($formData['hosts'])) {
                $cookieData->hosts = implode("\n", Tools::getInstance()->cleanHostList(stripslashes($formData['hosts'])));
            } elseif (!empty($cookieData->hosts)) {
                $cookieData->hosts = implode("\n", $cookieData->hosts);
            }

            if (isset($formData['cookieName'])) {
                $cookieData->cookie_name = stripslashes($formData['cookieName']);
            }

            if (isset($formData['cookieExpiry'])) {
                $cookieData->cookie_expiry = stripslashes($formData['cookieExpiry']);
            }

            if (isset($formData['settings']['blockCookiesBeforeConsent'])) {
                $cookieData->settings['blockCookiesBeforeConsent'] = stripslashes($formData['settings']['blockCookiesBeforeConsent']);
            }

            if (isset($formData['settings']['prioritize'])) {
                $cookieData->settings['prioritize'] = intval($formData['settings']['prioritize']);
            }

            if (isset($formData['optInJS'])) {
                $cookieData->opt_in_js = stripslashes($formData['optInJS']);
            }

            if (isset($formData['optOutJS'])) {
                $cookieData->opt_out_js = stripslashes($formData['optOutJS']);
            }

            if (isset($formData['fallbackJS'])) {
                $cookieData->fallback_js = stripslashes($formData['fallbackJS']);
            }

            // Preparing data for form mask
            $inputId            = !empty($cookieData->id) ? intval($cookieData->id) : 'new';
            $inputCookieId      = esc_attr(!empty($cookieData->cookie_id) ? $cookieData->cookie_id : '');
            $inputCookieGroupId = esc_attr(!empty($cookieData->cookie_group_id) ? $cookieData->cookie_group_id : '');
            $inputService       = esc_attr(!empty($cookieData->service) ? $cookieData->service : '');
            $inputStatus        = !empty($cookieData->status) ? 1 : 0;
            $switchStatus       = $inputStatus ? ' active' : '';
            $inputPosition      = intval(!empty($cookieData->position) ? $cookieData->position : '1');

            $inputName          = esc_attr(!empty($cookieData->name) ? $cookieData->name : '');
            $inputProvider      = esc_attr(!empty($cookieData->provider) ? $cookieData->provider : '');
            $textareaPurpose    = esc_textarea(!empty($cookieData->purpose) ? $cookieData->purpose : '');
            $inputPrivacyPolicyURL = esc_url(!empty($cookieData->privacy_policy_url) ? $cookieData->privacy_policy_url : '');
            $textareaHosts      = esc_textarea(!empty($cookieData->hosts) ? $cookieData->hosts : '');
            $inputCookieName    = esc_attr(!empty($cookieData->cookie_name) ? $cookieData->cookie_name : '');

            $inputBlockCookiesBeforeConsent  = !empty($cookieData->settings['blockCookiesBeforeConsent']) ? 1 : 0;
            $switchBlockCookiesBeforeConsent = $inputBlockCookiesBeforeConsent ? ' active' : '';

            $inputCookieExpiry  = esc_attr(!empty($cookieData->cookie_expiry) ? $cookieData->cookie_expiry : '');

            $inputSettingsPrioritize  = !empty($cookieData->settings['prioritize']) ? 1 : 0;
            $switchSettingsPrioritize = $inputSettingsPrioritize ? ' active' : '';

            $textareaOptInJS    = esc_textarea(!empty($cookieData->opt_in_js) ? $cookieData->opt_in_js : '');
            $textareaOptOutJS   = esc_textarea(!empty($cookieData->opt_out_js) ? $cookieData->opt_out_js : '');
            $textareaFallbackJS = esc_textarea(!empty($cookieData->fallback_js) ? $cookieData->fallback_js : '');

            $languageFlag = !empty($cookieData->language) ? Multilanguage::getInstance()->getLanguageFlag($cookieData->language) : '';
            $languageName = !empty($cookieData->language) ? Multilanguage::getInstance()->getLanguageName($cookieData->language) : '';

            include Backend::getInstance()->templatePath.'/cookies-edit.html.php';
        }
    }

    /**
     * displayOverview function.
     *
     * @access public
     * @return void
     */
    public function displayOverview()
    {
        global $wpdb;

        $cookieGroups = $wpdb->get_results('
            SELECT
                `id`,
                `name`,
                `position`,
                `status`,
                `undeletable`
            FROM
                `'.$this->tableCookieGroup.'`
            WHERE
                `language` = "'.esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()).'"
            ORDER BY
                `name` ASC
        ');

        if (!empty($cookieGroups)) {

            foreach ($cookieGroups as $key => $cookieGroupData) {

                $cookies = $wpdb->get_results('
                    SELECT
                        `id`,
                        `cookie_id`,
                        `name`,
                        `position`,
                        `status`,
                        `undeletable`
                    FROM
                        `'.$this->tableCookie.'`
                    WHERE
                        `language` = "'.esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()).'"
                        AND
                        `cookie_group_id` = "'.intval($cookieGroupData->id).'"
                    ORDER BY
                        `name` ASC
                ');

                if (!empty($cookies)) {
                    foreach ($cookies as $cookieData) {

                        $cookieData->undeletable = intval($cookieData->undeletable);

                        $cookieGroups[$key]->cookies[] = $cookieData;
                    }
                }
            }
        }

        include Backend::getInstance()->templatePath.'/cookies-overview.html.php';
    }

    /**
     * get function.
     *
     * @access public
     * @param mixed $id
     * @return void
     */
    public function get($id)
    {
        global $wpdb;

        $data = false;

        $cookieData = $wpdb->get_results('
            SELECT
                `id`,
                `cookie_id`,
                `language`,
                `cookie_group_id`,
                `service`,
                `name`,
                `provider`,
                `purpose`,
                `privacy_policy_url`,
                `hosts`,
                `cookie_name`,
                `cookie_expiry`,
                `opt_in_js`,
                `opt_out_js`,
                `fallback_js`,
                `settings`,
                `position`,
                `status`,
                `undeletable`
            FROM
                `'.$this->tableCookie.'`
            WHERE
                `id` = "'.esc_sql($id).'"
        ');

        if (!empty($cookieData[0]->id)) {
            $data = $cookieData[0];

            $data->hosts = unserialize($data->hosts);
            $data->settings = unserialize($data->settings);
        }

        return $data;
    }

    /**
     * getByCookieId function.
     *
     * @access public
     * @param mixed $cookieId
     * @return void
     */
    public function getByCookieId($cookieId)
    {
        global $wpdb;

        $data = false;

        $language = Multilanguage::getInstance()->getCurrentLanguageCode();

        // Get cookie id for the current language
        $cookieId = $wpdb->get_results('
            SELECT
                `id`
            FROM
                `'.$this->tableCookie.'`
            WHERE
                `language` = "'.esc_sql($language).'"
                AND
                `cookie_id` = "'.esc_sql($cookieId).'"
        ');

        if (!empty($cookieId[0]->id)) {
            $data = $this->get($cookieId[0]->id);
        }

        return $data;
    }

    /**
     * modify function.
     *
     * @access public
     * @param mixed $id
     * @param mixed $data
     * @return void
     */
    public function modify($id, $data)
    {
        global $wpdb;

        $default = [
            'name' => '',
            'provider' => '',
            'purpose' => '',
            'privacyPolicyURL' => '',
            'hosts' => [],
            'cookieName' => '',
            'cookieExpiry' => '',
            'optInJS' => '',
            'optOutJS' => '',
            'fallbackJS' => '',
            'settings' => [],
            'position' => 1,
            'status' => false,
        ];

        $data = array_merge($default, $data);

        $wpdb->query('
            UPDATE
                `'.$this->tableCookie.'`
            SET
                `name` = "'.esc_sql(stripslashes($data['name'])).'",
                `provider` = "'.esc_sql(stripslashes($data['provider'])).'",
                `purpose` = "'.esc_sql(stripslashes($data['purpose'])).'",
                `privacy_policy_url` = "'.esc_sql(stripslashes($data['privacyPolicyURL'])).'",
                `hosts` = "'.esc_sql(serialize($data['hosts'])).'",
                `cookie_name` = "'.esc_sql(stripslashes($data['cookieName'])).'",
                `cookie_expiry` = "'.esc_sql(stripslashes($data['cookieExpiry'])).'",
                `opt_in_js` = "'.esc_sql(stripslashes($data['optInJS'])).'",
                `opt_out_js` = "'.esc_sql(stripslashes($data['optOutJS'])).'",
                `fallback_js` = "'.esc_sql(stripslashes($data['fallbackJS'])).'",
                `settings` = "'.esc_sql(serialize($data['settings'])).'",
                `position` = "'.intval($data['position']).'",
                `status` = "'.(intval($data['status']) ? 1 : 0).'"
            WHERE
                `id` = "'.intval($id).'"
        ');

        return $id;
    }

    /**
     * resetDefault function.
     *
     * @access public
     * @return void
     */
    public function resetDefault()
    {
        global $wpdb;

        $language = Multilanguage::getInstance()->getCurrentLanguageCode();


        // Delete default Cookies
        $wpdb->query('
            DELETE FROM
                `'.$this->tableCookie.'`
            WHERE
                `language`="'.esc_sql($language).'"
                AND
                `cookie_id` IN ("borlabs-cookie", "facebook", "googlemaps", "instagram", "openstreetmap", "twitter", "vimeo", "youtube")
        ');

        $sqlDefaultEntriesCookies = \BorlabsCookie\Cookie\Install::getInstance()->getDefaultEntriesCookies($this->tableCookie, $language, $this->tableCookieGroup);

        $wpdb->query($sqlDefaultEntriesCookies);
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
        $formData = apply_filters('borlabsCookie/cookie/save', $formData);

        // Clean hosts
        $formData['hosts'] = Tools::getInstance()->cleanHostList($formData['hosts']);

        $id = 0;

        if (!empty($formData['id']) && $formData['id'] !== 'new') {
            // Edit
            $id = $this->modify($formData['id'], $formData);
        } else {
            // Add
            $id = $this->add($formData);
        }

        return $id;
    }

    /**
     * switchStatus function.
     *
     * @access public
     * @param mixed $id
     * @return void
     */
    public function switchStatus($id)
    {
        global $wpdb;

        $wpdb->query('
            UPDATE
                `'.$this->tableCookie.'`
            SET
                `status` = IF(`status` <> 0, 0, 1)
            WHERE
                `id` = "'.intval($id).'"
                AND
                `cookie_id` != "borlabs-cookie"
        ');

        return true;
    }

    /**
     * validate function.
     *
     * @access public
     * @param mixed $formData
     * @return void
     */
    public function validate($formData)
    {
        $errorStatus = false;

        // Check id if a new cookie is about to be added
        if (empty($formData['id']) || $formData['id'] === 'new') {

            if (empty($formData['cookieId']) || preg_match('/^[a-z\-\_]{3,}$/', $formData['cookieId']) === 0) {

                $errorStatus = true;
                Messages::getInstance()->add(_x('Please fill out the field <strong>ID</strong>. The ID must be at least 3 characters long and may only contain: <strong><em>a-z - _</em></strong>', 'Backend / Global / Alert Message', 'borlabs-cookie'), 'error');

            } elseif ($this->checkIdExists($formData['cookieId'])) {

                $errorStatus = true;
                Messages::getInstance()->add(_x('The <strong>ID</strong> already exists.', 'Backend / Global / Alert Message', 'borlabs-cookie'), 'error');

            }
        }

        if (empty($formData['name'])) {
            $errorStatus = true;
            Messages::getInstance()->add(_x('Please fill out the field <strong>Name</strong>.', 'Backend / Global / Alert Message', 'borlabs-cookie'), 'error');
        }

        $errorStatus = apply_filters('borlabsCookie/cookie/validate', $errorStatus, $formData);

        return $errorStatus;
    }
}
