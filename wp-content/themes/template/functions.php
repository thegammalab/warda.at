<?php

error_reporting(0);
setlocale(LC_TIME, 'de_DE', 'deu_deu');


/* ==============================================================
  INCLUDE LIBS
  ============================================================== */

$path = get_stylesheet_directory() . '/includes/lib/';
$files = scandir($path);
foreach ($files as $file) {
  if ($file != '.' && $file != '..') {
    include_once(get_stylesheet_directory() . "/includes/lib/" . $file);
  }
}

/* ==============================================================
  INCLUDE SITE SETTINGS
  ============================================================== */

include_once(get_stylesheet_directory() . "/config/site_setings.php");
$setup = new TDF_Setup;
$email = new TDF_Email_Model;

$setup->register_post_types($post_types);
$setup->register_post_fields($post_fields);
$setup->register_user_fields($user_fields);

$setup->register_taxonomies($taxonomies);
$setup->register_menus($menus);
$setup->register_sidebars($sidebars);
$setup->register_variables($variables);
$setup->register_theme_variables($theme_variables);

$email->register_emails($email_variables);

include_once(get_stylesheet_directory() . "/config/site_functions.php");


/* ==============================================================
  INCLUDE CONTROLLERS
  ============================================================== */

$path = get_stylesheet_directory() . '/controllers/';
$files = scandir($path);
foreach ($files as $file) {
  if ($file != '.' && $file != '..') {
    include_once(get_stylesheet_directory() . "/controllers/" . $file);
  }
}

/* ==============================================================
  INCLUDE MODELS
  ============================================================== */

$path = get_stylesheet_directory() . '/models/';
$files = scandir($path);
foreach ($files as $file) {
  if ($file != '.' && $file != '..') {
    include_once(get_stylesheet_directory() . "/models/" . $file);
  }
}
