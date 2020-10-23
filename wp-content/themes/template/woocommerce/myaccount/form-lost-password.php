<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.2
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>
<div class="container pt-7 pb-8">
	<div style="max-width:600px; margin:auto; padding:30px; background:#FFF;">
<form method="post" class="woocommerce-ResetPassword lost_reset_password">
	<h1>Passwort vergessen?</h1>
	<p><?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Haben Sie Ihr Passwort vergessen? Bitte geben Sie Ihre E-Mail-Adresse ein. Sie erhalten per E-Mail einen Link zur Erstellung eines neuen Passworts.', 'woocommerce' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>

	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<label for="user_login"><?php esc_html_e( 'E-Mail Adresse', 'woocommerce' ); ?></label>
		<input class="woocommerce-Input woocommerce-Input--text input-text form-control" type="text" name="user_login" id="user_login" autocomplete="username" />
	</p>

	<div class="clear"></div>

	<?php do_action( 'woocommerce_lostpassword_form' ); ?>

	<p class="woocommerce-form-row form-row">
		<input type="hidden" name="wc_reset_password" value="true" />
		<button type="submit" class="btn-primary w-100" value="<?php esc_attr_e( 'Passwort zurücksetzen', 'woocommerce' ); ?>"><?php esc_html_e( 'Passwort zurücksetzen', 'woocommerce' ); ?></button>
	</p>

	<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

</form>
</div>
</div>
<?php
do_action( 'woocommerce_after_lost_password_form' );
