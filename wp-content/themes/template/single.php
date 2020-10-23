<?php
$elem = (get_queried_object());
$post_type = $elem->post_type;
if($post_type=="events"){
    apply_filters( 'warda_get_event_thumbnail',get_the_ID());
}
$item = apply_filters("tdf_get_single",get_the_ID());

if(is_singular("product") && file_exists(dirname(__FILE__) . "/views/woocommerce/product-single.php")){
      include(locate_template("/views/woocommerce/product-single.php"));
}elseif (file_exists(dirname(__FILE__) . "/views/posts/" . $post_type . "/content-single.php")) {
    include(locate_template('views/posts/' . $post_type . "/content-single.php"));
} else {
    include(locate_template('views/posts/content-single.php'));
}
?>
