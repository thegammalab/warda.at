<?php
/*
 * ----------------------------------------------------------------------
 *
 *                          Borlabs Cookie
 *                      developed by Borlabs
 *
 * ----------------------------------------------------------------------
 *
 * Copyright 2018-2020 Borlabs - Benjamin A. Bornschein. All rights reserved.
 * This file may not be redistributed in whole or significant part.
 * Content of this file is protected by international copyright laws.
 *
 * ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 * @copyright Borlabs - Benjamin A. Bornschein, https://borlabs.io
 * @author Benjamin A. Bornschein, Borlabs ben@borlabs.io
 *
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

if (version_compare(phpversion(), '5.6', '>=')) {

    include_once plugin_dir_path(__FILE__).'classes/Autoloader.php';

    $Autoloader = new \BorlabsCookie\Autoloader();
    $Autoloader->register();
	$Autoloader->addNamespace('BorlabsCookie', realpath(plugin_dir_path(__FILE__).'/classes'));

    \BorlabsCookie\Cookie\Uninstall::getInstance()->uninstallPlugin();
}
?>
