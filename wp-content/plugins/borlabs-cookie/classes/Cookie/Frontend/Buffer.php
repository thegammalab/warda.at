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

namespace BorlabsCookie\Cookie\Frontend;

class Buffer
{
    private static $instance;

    private $buffer = '';
    private $bufferActive = false;

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
     * endBuffering function.
     *
     * @access public
     * @return void
     */
    public function endBuffering()
    {
        ob_end_clean();

        echo $this->buffer;

        unset($this->buffer);

        $this->bufferActive = false;
    }

    /**
     * getBuffer function.
     *
     * @access public
     * @return void
     */
    public function &getBuffer()
    {
        $this->buffer = ob_get_contents();

        return $this->buffer;
    }

    /**
     * handleBuffering function.
     *
     * @access public
     * @return void
     */
    public function handleBuffering()
    {
        $this->startBuffering();
    }

    /**
     * isBufferActive function.
     *
     * @access public
     * @return void
     */
    public function isBufferActive()
    {
        return $this->bufferActive;
    }

    /**
     * startBuffering function.
     *
     * @access public
     * @return void
     */
    public function startBuffering()
    {
        if (ScriptBlocker::getInstance()->isScanActive() || ScriptBlocker::getInstance()->hasScriptBlocker()) {
            ob_start();

            $this->bufferActive = true;
        }
    }
}