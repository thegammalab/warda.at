<?php
/**
 * User: simon
 * Date: 23.09.2019
 */

class ShortPixelJsonParser {

    protected $ctrl;
    protected $lazy;
    private $logger;

    function __construct($ctrl, $lazy = false) {
        $this->ctrl = $ctrl;
        $this->lazy = $lazy === false ? $ctrl->settings->areas->parse_json_lazy : $lazy;
        $this->logger = ShortPixelAILogger::instance();
    }

    function parse($content) {
        //return $content;
        $this->logger->log("******** JSON PARSER *********");
        return $this->parseRecursive($content);
        if(count($this->ctrl->affectedTags)) {
            $this->ctrl->recordAffectedTags($this->ctrl->affectedTags);
        }
    }

    protected function parseRecursive($content) {
        if(is_array($content) || is_object($content) ) {
            foreach ($content as $key => $value) {
                if(is_array($content)) {
                    $this->logger->log("JSON: Key $key: ", $content[$key]);
                    $content[$key] = $this->parseRecursive($value);
                    //echo ("$key changed to: " . json_encode($content[$key]) . " \n\n");
                } else {
                    $this->logger->log("JSON: Key $key: ", $content->$key);
                    $content->$key = $this->parseRecursive($value);
                    //echo ("$key changed to: " . json_encode($content->$key) . " \n\n");
                }
            }
            return $content;
        } elseif(is_string($content)) {
            $parsed = json_decode($content);
            if(json_last_error() !== JSON_ERROR_SYNTAX) {
                $this->logger->log("LEAF IS JSON");
                return json_encode($this->parseRecursive($parsed));
            } else {
                if(preg_match('/^([\s↵]*(<!--[^>]+-->)*)*<\w*(\s[^>]*|)>/s', $content)) {
                    //that's HTML, use the regex parser
                    $this->logger->log("LEAF IS HTML: " . $content);
                    $parser = new ShortPixelRegexParser($this->ctrl);
                    $ret = $parser->parse($content);
                    $this->logger->log("LEAF PARSED BY REGEX: " . $ret);
                    return $ret;
                } else {
                    //here replace the urls
                    $this->logger->log("LEAF IS PLAIN: " . $content);
                    if(strlen($content) <= 19) return $content;
                    $replaced = $this->replaceUrls($content);
                    return $replaced ? $replaced : $content;
                }
            }
        } else {
            return $content;
        }
    }

    protected function replaceUrls($text) {
        $this->logger->log("try replace URLs in $text \n\n");

        //this expression is better because it works with characters such as three bytes accented letters (é vs. é - notice any difference? the first is on three bytes, second is UTF8...)
        $pattern = '/(?:https?:\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?\.(?:jpe?g|png|gif)\b/su';
        //$pattern = "/(\bhttps?:|)\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,4}\b(?:[-a-zA-Z0-9@:%_\+.~#?&\/\/=\(\)]*)\.(jpe?g|png|gif)\b/s";
        $ret = preg_replace_callback($pattern, array($this, 'replaceUrl'), $text);

        if($this->lazy) {
            //the text might be HTML, we need to mark the possible tags that have lazy replacements
            preg_match_all('/\<([\w]+)[^\>]+data:image\/svg\+xml;/s', stripslashes($ret), $matches);
            if(isset($matches[1])) {
                $tags = array();
                foreach($matches[1] as $tag) {
                    if(strtolower($tag) !== 'img') {
                        $this->ctrl->affectedTags[$tag] = 3;
                    }
                }
            }
        }
        return $ret;
    }

    protected function replaceUrl($match) {
        $this->logger->log('Matches' . json_encode($match) . "\n");
        if(strpos($match[0], $this->ctrl->settings->behaviour->api_url) === false) {
            $url = ShortPixelUrlTools::absoluteUrl($match[0]);
            if($this->lazy) {
                $sizes = ShortPixelUrlTools::get_image_size($url);
                $ret = ShortPixelUrlTools::generate_placeholder_svg(isset($sizes[0]) ? $sizes[0]: false, isset($sizes[1]) ? $sizes[1]: false, $url);
            } else {
                $ret = $this->ctrl->get_api_url(false) . '/' . $url;
            }
            $this->logger->log("Changing to $ret.\n\n");
            return $ret;
        } else {
            return $match[0];
        }
    }
}