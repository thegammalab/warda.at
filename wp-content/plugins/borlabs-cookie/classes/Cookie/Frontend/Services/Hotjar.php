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

class Hotjar
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
        add_action('borlabsCookie/cookie/edit/template/settings/Hotjar', [$this, 'additionalSettingsTemplate']);
        add_action('borlabsCookie/cookie/edit/template/settings/help/Hotjar', [$this, 'additionalSettingsHelpTemplate']);
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
            'cookieId' => 'hotjar',
            'service' => 'Hotjar',
            'name' => 'Hotjar',
            'provider' => 'Hotjar Ltd.',
            'purpose' => _x('Hotjar is an user behavior analytic tool by Hotjar Ltd.. We use Hotjar to understand how users interact with our website.', 'Frontend / Cookie / Hotjar / Text', 'borlabs-cookie'),
            'privacyPolicyURL' => _x('https://www.hotjar.com/legal/policies/privacy/', 'Frontend / Cookie / Hotjar / Text', 'borlabs-cookie'),
            'hosts' => [
                '*.hotjar.com',
            ],
            'cookieName' => '_hjClosedSurveyInvites, _hjDonePolls, _hjMinimizedPolls, _hjDoneTestersWidgets, _hjIncludedInSample, _hjShownFeedbackMessage, _hjid, _hjRecordingLastActivity, hjTLDTest, _hjUserAttributesHash, _hjCachedUserAttributes, _hjLocalStorageTest, _hjptid',
            'cookieExpiry' => _x('Session / 1 Year', 'Frontend / Cookie / Hotjar / Text', 'borlabs-cookie'),
            'optInJS' => $this->optInJS(),
            'optOutJS' => '',
            'fallbackJS' => '',
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => false,
                'siteId' => '',
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
        $inputSiteId = esc_html(!empty($data->settings['siteId']) ? $data->settings['siteId'] : '');
        ?>
        <div class="form-group row">
            <label for="siteId" class="col-sm-4 col-form-label"><?php _ex('Site ID', 'Backend / Cookie / Hotjar / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="siteId" name="settings[siteId]" value="<?php echo $inputSiteId; ?>" placeholder="<?php _ex('Example', 'Backend / Global / Input Placeholder', 'borlabs-cookie'); ?>: 1234567" required>
                <span data-toggle="tooltip" title="<?php _ex('Enter your Site ID.', 'Backend / Cookie / Hotjar / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                <div class="invalid-feedback"><?php _ex('This is a required field and cannot be empty.', 'Backend / Global / Validation Message', 'borlabs-cookie'); ?></div>
            </div>
        </div>
        <?php
    }

    /**
     * additionalSettingsHelpTemplate function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function additionalSettingsHelpTemplate($data)
    {
        ?>
        <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
            <div class="px-3 pt-3 pb-3 mb-4">
                <h3 class="border-bottom mb-3"><?php _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>
                <h4><?php _ex('Where can I find the Site ID?', 'Backend / Cookie / Hotjar / Tips / Headline', 'borlabs-cookie'); ?></h4>
                <p><?php _ex('In Hotjar, click <strong>Tracking</strong> in the upper right corner.', 'Backend / Cookie / Hotjar / Tips / Text', 'borlabs-cookie'); ?></p>
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
<!-- Hotjar Tracking Code -->
<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:%%siteId%%,hjsv:6};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
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
        if (!empty($formData['service']) && $formData['service'] === 'Hotjar') {

            if (!empty($formData['settings']['siteId'])) {

                $formData['settings']['siteId'] = trim($formData['settings']['siteId']);

            }
        }

        return $formData;
    }
}
