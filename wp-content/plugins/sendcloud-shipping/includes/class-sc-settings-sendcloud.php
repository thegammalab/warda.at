<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists('SendCloudShipping_Settings') ):

class SendCloudShipping_Settings extends WC_Settings_Page {
    const API_DESCRIPTION = 'SendCloud API';

    public function __construct() {
        $this->id = 'sendcloud';
        $this->label = __('SendCloud', 'sendcloud-shipping');
        parent::__construct();
    }

    public function output() {
        echo $this->auto_connect_screen();
    }

    public function auto_connect_screen() {
        global $hide_save_button;
        $hide_save_button = true;
        include_once(plugin_dir_path(__FILE__) . '../views/html-admin-auto-connect.php');
    }

    private function get_api_key() {
        global $wpdb;

        return $wpdb->get_row( $wpdb->prepare( "
            SELECT key_id, user_id, description, permissions, consumer_key, consumer_secret, last_access
            FROM {$wpdb->prefix}woocommerce_api_keys
            WHERE user_id = %d AND description = %s
        ", get_current_user_id(), self::API_DESCRIPTION ), ARRAY_A );
    }

    private function generate_consumer_key() {
        $consumer_key = 'ck_' . wc_rand_hash();
        $consumer_key_hash = wc_api_hash($consumer_key);
        return array($consumer_key, $consumer_key_hash);
    }

    private function create_api_key() {
        global $wpdb;

        list($consumer_key, $consumer_key_hash) = $this->generate_consumer_key();
        $consumer_secret = 'cs_' . wc_rand_hash();

        $data = array(
            'user_id' => get_current_user_id(),
            'description' => self::API_DESCRIPTION,
            'permissions' => 'read_write',
            'consumer_key' => $consumer_key_hash,
            'consumer_secret' => $consumer_secret,
            'truncated_key' => substr($consumer_key, -7)
        );

        $wpdb->insert(
            $wpdb->prefix.'woocommerce_api_keys',
            $data,
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );

        return array($consumer_key, $consumer_secret);
    }

    private function update_api_key($api_key) {
        global $wpdb;

        list($consumer_key, $consumer_key_hash) = $this->generate_consumer_key();

        $wpdb->update(
            $wpdb->prefix.'woocommerce_api_keys',
            array('consumer_key' => $consumer_key_hash),
            array('key_id' => $api_key['key_id']),
            array('%s'),
            array('%d')
        );

        return array($consumer_key, $api_key['consumer_secret']);
    }

    private function get_panel_url() {
        $panel_url = getenv('SENDCLOUDSHIPPING_PANEL_URL');
        if (empty($panel_url)) {
            $panel_url = 'https://panel.sendcloud.sc';
        }
        return $panel_url;
    }

    public function save() {
        $permalinks_enabled = get_option('permalink_structure');
        if (! $permalinks_enabled) {
            $url = admin_url( 'admin.php?page=wc-settings&tab=' . $this->id );
        } else {
            $site_url = urlencode(get_option('home'));
            update_option('woocommerce_api_enabled', 'yes');

            $sendcloudshipping_key = $this->get_api_key();

            if ( is_null($sendcloudshipping_key) ) {
                list($consumer_key, $consumer_secret) = $this->create_api_key();
            } else {
                list($consumer_key, $consumer_secret) = $this->update_api_key($sendcloudshipping_key);
            }

            $url = sprintf('%s/shops/woocommerce/connect/?url_webshop=%s&api_key=%s&api_secret=%s',
                           $this->get_panel_url(), $site_url, $consumer_key, $consumer_secret);
        }

        if ( !headers_sent() ) {
            header('Location: ' . $url);
            exit;
        } else {
            echo <<<EOT
<script type="text/javascript">
    window.location.href="$url";
</script>
<noscript>
    <meta http-equiv="refresh" content="0;url=$url" />
</noscript>
EOT;
        }
    }
}
endif;

return new SendCloudShipping_Settings();
