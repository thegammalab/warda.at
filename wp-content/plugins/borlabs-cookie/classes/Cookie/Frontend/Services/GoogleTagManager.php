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

class GoogleTagManager
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
        add_action('borlabsCookie/cookie/edit/template/settings/GoogleTagManager', [$this, 'additionalSettingsTemplate']);
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
            'cookieId' => 'google-tag-manager',
            'service' => 'GoogleTagManager',
            'name' => 'Google Tag Manager',
            'provider' => 'Google LLC',
            'purpose' => _x('Cookie by Google used to control advanced script and event handling.', 'Frontend / Cookie / Google Tag Manager / Text', 'borlabs-cookie'),
            'privacyPolicyURL' => _x('https://policies.google.com/privacy?hl=en', 'Frontend / Cookie / Google Tag Manager / Text', 'borlabs-cookie'),
            'hosts' => [],
            'cookieName' => '_ga,_gat,_gid',
            'cookieExpiry' => _x('2 Years', 'Frontend / Cookie / Google Tag Manager / Text', 'borlabs-cookie'),
            'optInJS' => $this->optInJS(),
            'optOutJS' => '',
            'fallbackJS' => '',
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => true,
                'gtmId' => '',
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
        $inputGtmId = esc_html(!empty($data->settings['gtmId']) ? $data->settings['gtmId'] : '');
        ?>
        <div class="form-group row">
            <label for="gtmId" class="col-sm-4 col-form-label"><?php _ex('GTM ID', 'Backend / Cookie / Google Tag Manager / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="gtmId" name="settings[gtmId]" value="<?php echo $inputGtmId; ?>" placeholder="<?php _ex('Example', 'Backend / Global / Input Placeholder', 'borlabs-cookie'); ?>: GTM-1234" required>
                <span data-toggle="tooltip" title="<?php _ex('Enter your Google Tag Manager ID.', 'Backend / Cookie / Google Tag Manager / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
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
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({"gtm.start":
new Date().getTime(),event:"gtm.js"});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!="dataLayer"?"&l="+l:"";j.async=true;j.src=
"https://www.googletagmanager.com/gtm.js?id="+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,"script","dataLayer","%%gtmId%%");</script>
<!-- End Google Tag Manager -->
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
        if (!empty($formData['service']) && $formData['service'] === 'GoogleTagManager') {

            if (!empty($formData['settings']['gtmId'])) {

                $formData['settings']['gtmId'] = trim($formData['settings']['gtmId']);

            }
        }

        return $formData;
    }
}
