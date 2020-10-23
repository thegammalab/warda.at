<?php ob_start(); ?>
<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=0.8, maximum-scale=0.8, user-scalable=0"/> <!--320-->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <?php do_action("tdf_get_head"); ?>
</head>
<body <?php body_class(); ?> data-spy="scroll" data-target="#category_content_nav">
  <!--[if lt IE 8]>
    <div class="alert alert-warning">
    <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'tdf'); ?>
  </div>
  <![endif]-->
  <?php
  do_action('tdf_get_header');
  do_action('tdf_get_template_path');
  //include roots_template_path();

  do_action('tdf_get_footer');
  ?>
</body>
</html>
<?php
$cont = ob_get_contents();
ob_end_clean();
$cont = str_replace("woocommerce-Input","form-control",$cont);
new TDF_Speed($cont);
