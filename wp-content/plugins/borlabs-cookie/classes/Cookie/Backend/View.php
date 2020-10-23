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

namespace BorlabsCookie\Cookie\Backend;

use BorlabsCookie\Cookie\Multilanguage;

class View
{
    private static $instance;

    private $imagePath;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    protected function __construct()
    {
        $this->imagePath = plugins_url('images', realpath(__DIR__.'/../../'));
    }

    /**
     * __call function.
     *
     * @access public
     * @param mixed $moduleClass
     * @param mixed $args
     * @return void
     */
    public function __call($moduleClass, $args)
    {
        $this->displayHeader();

        $class = 'BorlabsCookie\Cookie\Backend\\'.$moduleClass;

        if (class_exists($class)) {
            $this->displayNavigation($moduleClass);

            $class::getInstance()->display();
        } else {
            // Fallback
            $this->displayNavigation('Dashboard');

            Dashboard::getInstance()->display();
        }

        $this->displayFooter();
    }

    /**
     * displayHeader function.
     *
     * @access public
     * @return void
     */
    public function displayHeader()
    {
        $language = Multilanguage::getInstance()->getCurrentLanguageCode();

        include Backend::getInstance()->templatePath.'/header.html.php';
    }

    /**
     * displayNavigation function.
     *
     * @access public
     * @param string $activeModule (default: 'Dashboard')
     * @return void
     */
    public function displayNavigation($activeModule = 'Dashboard')
    {
        // Give info which language setting is loaded
        $multilanguagePluginIsActive = false;

        if (Multilanguage::getInstance()->isMultilanguagePluginActive()) {

            $multilanguagePluginIsActive = true;
            $currentFlag = '';
            $currentLanguage = Multilanguage::getInstance()->getCurrentLanguageName();
            $currentFlagURL = Multilanguage::getInstance()->getCurrentLanguageFlag();

            if (!empty($currentFlagURL)) {
                $currentFlag = '<img src="'.$currentFlagURL.'" alt="'.$currentLanguage.'">';
            } else {
                $currentFlag = '<i class="fas fa-language"></i>';
            }

            $currentLanguageTooltipText = sprintf(_x('You are seeing the settings for the language <strong>%s</strong>.', 'Backend / Global / Tooltip', 'borlabs-cookie'), $currentLanguage);
        }

        include Backend::getInstance()->templatePath.'/navigation.html.php';
    }

    /**
     * displayFooter function.
     *
     * @access public
     * @return void
     */
    public function displayFooter()
    {
        include Backend::getInstance()->templatePath.'/footer.html.php';
    }
}
