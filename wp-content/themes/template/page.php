<?php
the_post();
$item = apply_filters("tdf_get_single", get_the_ID());

if (!function_exists("is_woocommerce")) {
  include("views/pages/default.php");
} elseif (is_shop()) {
  if (locate_template("views/woocommerce/page-shop.php")) {
    include("views/woocommerce/page-shop.php");
  } else {
    include("views/woocommerce/product-list.php");
  }
} elseif (is_archive("product") || is_tax("product_cat")) {
  include("views/woocommerce/product-list.php");
} elseif (is_cart()) {
  if (locate_template("views/woocommerce/page-cart.php")) {
    include("views/woocommerce/page-cart.php");
  } elseif (locate_template("views/pages/woocommerce.php")) {
    include("views/pages/woocommerce.php");
  } else {
    include("views/pages/default.php");
  }
} elseif (is_checkout()) {
  if (locate_template("views/woocommerce/page-checkout.php")) {
    include("views/woocommerce/page-checkout.php");
  } elseif (locate_template("views/pages/woocommerce.php")) {
    include("views/pages/woocommerce.php");
  } else {
    include("views/pages/default.php");
  }
} elseif (is_account_page()) {
  if (locate_template("views/woocommerce/page-my-account.php")) {
    include("views/woocommerce/page-my-account.php");
  } elseif (locate_template("views/pages/woocommerce.php")) {
    include("views/pages/woocommerce.php");
  } else {
    include("views/pages/default.php");
  }
} elseif (is_woocommerce()) {
  if (locate_template("views/pages/woocommerce.php")) {
    include("views/pages/woocommerce.php");
  } else {
    include("views/pages/default.php");
  }

}elseif (is_page('versand') || is_page('faq')) {
  include("views/pages/big-title.php");
}elseif (is_page('agbs') || is_page('impressum') || is_page('datenschutz')) {
  include("views/pages/big-title-no-header-img.php");
} else {
  include("views/pages/default.php");
}
