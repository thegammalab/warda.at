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

class Messages
{
    private static $instance;

    private $messages = [];

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
    }

    /**
     * getAll function.
     *
     * @access public
     * @return void
     */
    public function getAll()
    {
        return implode("\n", $this->messages);
    }

    /**
     * add function.
     *
     * @access public
     * @param mixed $message
     * @param mixed $type
     * @return void
     */
    public function add($message, $type)
    {
        if ($type === 'error') {
            $type = 'alert-danger';

        } elseif ($type === 'success') {
            $type = 'alert-success';

        } elseif ($type === 'info') {
            $type = 'alert-info';

        } elseif ($type === 'warning') {
            $type = 'alert-warning';

        } elseif ($type === 'offer') {
            $type = 'alert-offer';

        } elseif ($type === 'critical') {
            $type = 'alert-critical';
        }

        $this->messages[] = '<div class="alert '.\esc_attr($type).'" role="alert">'.$message.'</div>';
    }
}