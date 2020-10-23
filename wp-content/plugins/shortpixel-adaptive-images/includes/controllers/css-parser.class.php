<?php
/**
 * User: simon
 * Date: 18.07.2019
 */

class ShortPixelCssParser {
    const REGEX_CSS = '/(\s|{|;)(background-image|background)(\s*:(?:[^;]*?[,\s]|\s*))url\(\s*(?:\'|")?([^\'"\)]+)(\'|"|)?\s*\)/s';
    const REGEX_IN_TAG = '/\<([\w]+)(?:[^\<\>]*?)(background-image|background)(\s*:(?:[^;]*?[,\s]|\s*))url\(\s*(?:\'|")?([^\'"\)]+)(\'|"|)?\s*\)/s';
	/**
	 * @var null|ShortPixelAI
	 */
    private $ctrl;
    private $logger;

    private $regexCSS;
    private $regexInTag;
    private $crowd2replaced;

    public $cssFilePath = false;

    public function __construct($controller) {
        $this->ctrl = $controller;
        $this->logger = ShortPixelAILogger::instance();
    }


    public function replace_inline_style_backgrounds($style) {
        $style = preg_replace_callback(
            self::REGEX_CSS,
            array(&$this, 'replace_background_image_from_style'),
            $style);

        $integrations = $this->ctrl->getActiveIntegrations();
        if($integrations['theme'] == 'CROWD 2' && strpos($style, '--img-') !== false) {
            $this->logger->log("CROWD2 - inline stile block has --img-");
            $style = $this->replace_crowd2_img_styles($style);
        }
        return $style;
    }

    public function replace_in_tag_style_backgrounds($style) {
        if(strpos($style, 'background') === false) return $style;
        return preg_replace_callback(
            self::REGEX_IN_TAG,
            //'/(^|\s|;)(background-image|background)\s*:([^;]*[,\s]|\s*)url\((?:\'|")?([^\'"\)]*)(\'|")?\s*\)/s',
            array(&$this, 'replace_background_image_from_tag'),
            $style);
    }

    public function replace_background_image_from_tag($matches) {
        $this->logger->log("REPLACE TAG BK RECEIVES: ", $matches);
        $ret = $this->replace_background_image($matches, $this->ctrl->settings->areas->backgrounds_lazy_style);
        $this->logger->log("REPLACE BK RETURNS: ", $ret->text);
        return $ret->text;
    }

    public function replace_background_image_from_style($matches) {
        $this->logger->log("REPLACE STYLE BK RECEIVES: ", $matches);
        //if($this->cssFilePath) {
        //    $this->logger->log('URL is ' . $matches[4] . ' (homepath: ' . get_home_path() . ' , css File path: ' . $this->cssFilePath . ') and will be converted to ' . ShortPixelUrlTools::absoluteUrl($matches[4], $this->cssFilePath));
        //}
        //doesn't make sense to replace lazily in <style> blocks
        //actually it does because otherwise we don't have WebP
        $ret = $this->replace_background_image($matches, $this->ctrl->settings->areas->backgrounds_lazy/*false*/);
        if($ret->replaced) {
            $this->ctrl->affectedTags['script'] = 2;
        }
        return $ret->text;
    }

    public function replace_wp_bakery_data_ultimate_bg($matches) {
        $this->logger->log("REPLACE BAKERY BK RECEIVES: ", $matches);
        $ret = $this->replace_background_image($matches, $this->ctrl->settings->areas->backgrounds_lazy);
        $this->logger->log("REPLACE BK RETURNS: ", $ret->text);
        return $ret->text;
    }

    public function replace_background_image($matches, $lazy = true) {
        $text = $matches[0];
        if(!isset($matches[4])) {
            $this->logger->log("REPLACE BG - NO URL", $matches);
            return (object)array('text' => $text, 'replaced' => false);
        }
        $url = trim($matches[4]);
        $tag = trim($matches[1]);
        $type = $matches[2]; //this mostly is background-image or background
        $extra = $matches[3]; //what lies between the type and url()
        $q = isset($matches[5]) ? $matches[5] : '';

        $pristineUrl = $url;
        //WP is encoding some characters, like & ( to &#038; )
        $url = trim(html_entity_decode($url));
        //some URLs in css are delimited by &quot; which becomes " after html_entity_decode
        $urlUnquot = trim($url, '"');
        if($urlUnquot !== $url) {
            $this->logger->log('Removed quote ' . $urlUnquot);
            $url = $urlUnquot;
            $pristineUrl = trim($pristineUrl, '"');
        }

        //        if(strpos($url, 'data:image/svg+xml;u=') !== false) { // old implementation
        if(ShortPixelUrlTools::url_from_placeholder_svg($url) !== false) {
            if($lazy) {
                return (object)array('text' => $text, 'replaced' => false);
            } else {
                //this is collected CSS, need to change it back and make it eager
                $url = ShortPixelUrlTools::url_from_placeholder_svg($url);

            }
        }
        if(strpos($url, $this->ctrl->settings->behaviour->api_url) !== false) {
            return (object)array('text' => $text, 'replaced' => false);
        }
	    if ( !$this->ctrl->lazyNoticeThrown && ( strpos( $text, 'data-bg=' ) !== false ) ) {
		    set_transient( "shortpixelai_thrown_notice", [ 'when' => 'lazy', 'extra' => false, 'causer' => 'css parser', 'text' => $text ], 86400 );
		    $this->ctrl->lazyNoticeThrown = true;
	    }
        if($this->ctrl->lazyNoticeThrown) {
            return (object)array('text' => $text, 'replaced' => false);
        }
        if($this->ctrl->tagIs('excluded', $text)) {
            return (object)array('text' => $text, 'replaced' => false);
        }

        $this->logger->log('******** REPLACE BACKGROUND IMAGE ' . ($lazy ? '' : 'FROM STYLE ') . $url);

        if(   $this->ctrl->urlIsApi($url)
           || !ShortPixelUrlTools::isValid($url)
           || $this->ctrl->urlIsExcluded($url)) {
            return (object)array('text' => $text, 'replaced' => false);
        }

        if(!$lazy || $this->ctrl->tagIs('noresize', $text) || $this->ctrl->tagIs('eager', $text)) {
            $width = $this->ctrl->settings->areas->backgrounds_max_width ? $this->ctrl->settings->areas->backgrounds_max_width : false;
            //cssFilePath present means that's a CSS file from the cache plugin (WP Rocket)
            $inlinePlaceholder = $this->ctrl->get_api_url($width, false, $this->ctrl->get_extension( $url )) . '/' . ShortPixelUrlTools::absoluteUrl($url, $this->cssFilePath ? $this->cssFilePath : false);
            $this->logger->log("API URL: " . $inlinePlaceholder);
        } else {
            $sizes = ShortPixelUrlTools::get_image_size($url);
            $inlinePlaceholder = isset($sizes[0]) ? ShortPixelUrlTools::generate_placeholder_svg($sizes[0], $sizes[1], $url) : ShortPixelUrlTools::generate_placeholder_svg(false, false, $url);
        }

//        $this->logger->log("REPLACE REGEX: " . '/' . $type . '\s*:' . preg_quote($extra, '/') . 'url\(\s*' . preg_quote($q . $pristineUrl . $q, '/') . '/'
//              . " WITH: " . ' '. $type . ':' . $extra . 'url(' . $q . $inlinePlaceholder . $q);
        $replacement = ' '. $type . $extra . 'url(' . $q . $inlinePlaceholder . $q;
        $str = preg_replace('/' . $type . preg_quote($extra, '/') . 'url\(\s*' . preg_quote($q . $pristineUrl . $q, '/') . '/',
            $replacement, $text);

        if($lazy && ctype_alnum($tag)) {
            $this->ctrl->affectedTags[$tag] = 2 | (isset($this->ctrl->affectedTags[$tag]) ? $this->ctrl->affectedTags[$tag] : 0);
        }
        $this->logger->log('******** WITH ', $replacement);
        return (object)array('text' => $str, 'replaced' => true);// . "<!-- original url: $url -->";
    }

    public function replace_crowd2_img_styles($style) {
        // CROWD2 uses --img-small --img-medium and --img-large styles
        return preg_replace_callback(
            '/(--img-small|--img-medium|--img-large)(\s*:(?:[^;]*?[,\s]|\s*))url\((?:\'|")?([^\'"\)]+)(\'|"|)?/s',
            array(&$this, 'replace_crowd2_img_style'),
            $style);
    }

    protected function replace_crowd2_img_style($matches) {
        $text = $matches[0];
        $type = trim($matches[1]);
        $extra = trim($matches[2]);
        $url = trim($matches[3]);
        $q = isset($matches[4]) ? $matches[4] : '';
        if($this->ctrl->urlIsApi($url)) {
            return $text;
        }
        $this->crowd2replaced = true;
        $inlinePlaceholder = $this->ctrl->get_api_url(false, false, $this->ctrl->get_extension( $url )) . '/' . ShortPixelUrlTools::absoluteUrl($url, $this->cssFilePath ? $this->cssFilePath : false);
        return preg_replace('/' . $type . preg_quote($extra, '/') . 'url\(\s*' . preg_quote($q . $url . $q, '/') . '/',
            ' '. $type . $extra . 'url(' . $q . $inlinePlaceholder . $q, $text);

    }
}