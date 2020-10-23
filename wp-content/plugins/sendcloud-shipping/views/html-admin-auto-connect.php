<?php
if ( ! defined('ABSPATH') ) {
    exit;
}

$permalinks_enabled = get_option('permalink_structure');

?>
<!-- Required by Woocommerce 3.5.5 onwards -->
<input type="hidden" name="save" value="yes" />
<!-- End required -->
<div id="sendcloud_shipping_connect" class="submit">
    <div class="connect">
        <input type="submit" name="connect" class= "button button-primary" <?php if (!$permalinks_enabled) echo 'disabled="true"' ?>value="<?php _e('Connect with SendCloud', 'sendcloud-shipping'); ?>" />
        <p class="description">
            <?php _e('Allows you to see your WooCommerce orders in the SendCloud Panel.', 'sendcloud-shipping'); ?>
        </p>
        <?php if(!$permalinks_enabled): ?>
        <p style="color: red;">
            <?php _e('You need to enable permalinks. On Settings -> Permalinks.', 'sendcloud-shipping'); ?>
        </p>
        <?php endif ?>
    </div>
    <div class="goto-panel">
        <a class="button button-primary" href="<?php echo $this->get_panel_url(); ?>" target="_blank" rel="noopener noreferrer"><?php _e('Go to SendCloud', 'sendcloud-shipping'); ?></a>
        <p class="description">
            <?php _e('Open the SendCloud panel and start shipping.', 'sendcloud-shipping'); ?>
        </p>
    </div>
</div>
<script>
    (function ($) {
        'use strict';
        $(document).ready(function () {
            var form = $('#sendcloud_shipping_connect').parents('form');
            form.attr({target: '_blank', rel: 'noopener noreferrer'});
            form.on('submit', function () {
                form.find('input.button').prop('disabled', true);
                setTimeout(function () {
                    window.location.reload(true);
                }, 5000);
            })
        });
    })(jQuery);
</script>
<style>
    #sendcloud_shipping_connect .connect {
        margin-bottom: 15px;
    }
</style>
