<?php
$elem = (get_queried_object());
if(isset($elem->taxonomy)){
  $tax_info = get_option("tax_".$elem->taxonomy);
  $post_type = $tax_info["default"];
  if($post_type=="product" && file_exists(dirname(__FILE__) . "/views/woocommerce/product-list.php")){
    if(file_exists(dirname(__FILE__) . "/views/woocommerce/product-tax-".$elem->taxonomy."-list.php")){
      include(locate_template('/views/woocommerce/product-tax-'.$elem->taxonomy.'-list.php'));
    }else{
      include(locate_template('/views/woocommerce/product-list.php'));
    }
  }elseif (file_exists(dirname(__FILE__) . "/views/posts/" . $post_type . "/taxonomy-".$elem->taxonomy."-list.php")) {
    include(locate_template('views/posts/' . $post_type . "/taxonomy-".$elem->taxonomy."-list.php"));
  }elseif (file_exists(dirname(__FILE__) . "/views/posts/" . $post_type . "/content-list.php")) {
    include(locate_template('views/posts/' . $post_type . "/content-list.php"));
  }else{
    include(locate_template('views/posts/defaults/content-list.php'));
  }
}
