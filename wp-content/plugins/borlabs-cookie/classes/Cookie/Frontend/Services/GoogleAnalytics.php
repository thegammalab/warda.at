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

namespace BorlabsCookie\Cookie\Frontend\Services;

class GoogleAnalytics
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

    /**
     * __construct function.
     *
     * @access protected
     * @return void
     */
    protected function __construct()
    {
        add_action('borlabsCookie/cookie/edit/template/settings/GoogleAnalytics', [$this, 'additionalSettingsTemplate']);
        add_action('borlabsCookie/cookie/save', [$this, 'save']);
    }

    /**
     * getDefault function.
     *
     * @access public
     * @return void
     */
    public function getDefault()
    {
        $data = [
            'cookieId' => 'google-analytics',
            'service' => 'GoogleAnalytics',
            'name' => 'Google Analytics',
            'provider' => 'Google LLC',
            'purpose' => _x('Cookie by Google used for website analytics. Generates statistical data on how the visitor uses the website.', 'Frontend / Cookie / Google Analytics / Text', 'borlabs-cookie'),
            'privacyPolicyURL' => _x('https://policies.google.com/privacy?hl=en', 'Frontend / Cookie / Google Analytics / Text', 'borlabs-cookie'),
            'hosts' => [],
            'cookieName' => '_ga,_gat,_gid',
            'cookieExpiry' => _x('2 Years', 'Frontend / Cookie / Google Analytics / Text', 'borlabs-cookie'),
            'optInJS' => $this->optInJS(),
            'optOutJS' => '',
            'fallbackJS' => '',
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => true,
                'trackingId' => '',
            ],
            'status' => true,
            'undeletetable' => false,
        ];

        return $data;
    }

    /**
     * additionalSettingsTemplate function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function additionalSettingsTemplate($data)
    {
        $inputTrackingId = esc_html(!empty($data->settings['trackingId']) ? $data->settings['trackingId'] : '');
        ?>
        <div class="form-group row">
            <label for="trackingId" class="col-sm-4 col-form-label"><?php _ex('Tracking ID', 'Backend / Cookie / Google Analytics / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="trackingId" name="settings[trackingId]" value="<?php echo $inputTrackingId; ?>" placeholder="<?php _ex('Example', 'Backend / Global / Input Placeholder', 'borlabs-cookie'); ?>: UA-123456789" required>
                <span data-toggle="tooltip" title="<?php _ex('Enter your Google Analytics Tracking ID.', 'Backend / Cookie / Google Analytics / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                <div class="invalid-feedback"><?php _ex('This is a required field and cannot be empty.', 'Backend / Global / Validation Message', 'borlabs-cookie'); ?></div>
            </div>
        </div>
        <?php
    }

    /**
     * optInJS function.
     *
     * @access private
     * @return void
     */
    private function optInJS()
    {
        $code = <<<EOT
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=%%trackingId%%"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag("js", new Date());

gtag("config", "%%trackingId%%", { "anonymize_ip": true });
</script>
EOT;
        return $code;
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
        if (!empty($formData['service']) && $formData['service'] === 'GoogleAnalytics') {

            if (!empty($formData['settings']['trackingId'])) {

                $formData['settings']['trackingId'] = trim($formData['settings']['trackingId']);

            }
        }

        return $formData;
    }
}
