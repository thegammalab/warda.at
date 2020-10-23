<?php
$uid = get_current_user_id();
if($uid){
  $the_title = get_user_meta($uid,"first_name",true)."'s Cunning Account";
}else{
  $the_title = "Login to your Account";
}
?>
<div class="course_list_section" style="background:none;">
 <?php do_action( 'woocommerce_account_navigation' ); ?>



      <?php the_content(); ?>

</div>

<div class="modal fade" id="avatar_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
		    <h3 class="text-center"><b><?php esc_html_e('Change Avatar', 'woocommerce'); ?></b></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="<?= admin_url("admin-post.php"); ?>" enctype='multipart/form-data' method="POST" class="">
          <div id="avatar_change_popup" style="overflow: hidden;" class="d-flex align-items-center">
            <?php echo apply_filters("tdf_get_update_user_field", "avatar", $uid, "", array("classes" => "form-control required")); ?>
          </div>
          <hr style="margin: 20px 0 10px !important;" />
              <p class="woocommerce-FormRow form-row">
                <input type="hidden" name="action" value="tdf_update_user" />
                <input type="hidden" name="user_core_id" value="<?php echo $uid; ?>" />
                <input type="hidden" name="success_url" value="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>/?action=update_success" />
                <input type="hidden" name="error_url" value="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" />

                <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
                <button type="submit" class="btn w-100" name="register" value="<?php esc_attr_e('Update', 'woocommerce'); ?>"><?php esc_html_e('Update', 'woocommerce'); ?></button>
              </p>
			  </form>
      </div>
    </div>
  </div>
</div>