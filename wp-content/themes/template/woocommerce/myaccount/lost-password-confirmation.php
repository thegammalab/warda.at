<?php
/**
 * Lost password confirmation text.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/lost-password-confirmation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.9.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="container pt-7 pb-8">
    <div style="max-width:600px; margin:auto; padding:30px; background:#FFF;">
    <h1><?=esc_html__( 'Die E-Mail zum Zurücksetzen des Passworts wurde gesendet.', 'woocommerce' );?></h1>
<?php do_action( 'woocommerce_before_lost_password_confirmation_message' ); ?>

<p><?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', esc_html__( 'Eine E-Mail zum Zurücksetzen des Passworts wurde an die für Ihr Konto hinterlegte E-Mail-Adresse gesendet, es kann jedoch einige Minuten dauern, bis sie in Ihrem Posteingang erscheint. Bitte warten Sie mindestens 10 Minuten, bevor Sie ein weiteres Zurücksetzen versuchen.', 'woocommerce' ) ) ); ?></p>

<?php do_action( 'woocommerce_after_lost_password_confirmation_message' ); ?>
</div>
</div>