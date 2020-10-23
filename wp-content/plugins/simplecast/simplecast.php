<?php
/**
 * Plugin Name: Simplecast Embed
 * Plugin URI: https://www.simplecast.com
 * Description: Display content using a shortcode to insert in a page or post. eg: [simplecast-embed src="[simplecast/episode/embed/link]"]
 * Version: 0.1
 * Text Domain: simplecast-embed
 * Author: Simplecast
 * Author URI: https://www.simplecast.com
 */

function simplecast_embed($atts) {
  if (!isset($atts['src'])) {
    return '[simplecast-embed error="src attribute needs to be set"]';
  }

  $response = wp_remote_get('https://api.simplecast.com/oembed?url=' . rawurlencode($atts["src"]));

  if (!is_array($response) || isset($response['errors']) || !isset($response['body'])) {
    return '[simplecast-embed error="Could not find episode"]';
  }

  $jsonData = json_decode($response['body'], true);

  if (!$jsonData || !isset($jsonData['html']) || !is_string($jsonData['html'])) {
    return '[simplecast-embed error="Could not get iframe html"]';
  }

  $whitelist = array(
    'iframe' => array(
      'frameborder' => array(),
      'height' => array(),
      'scrolling' => array(),
      'src' => array(),
      'title' => array(),
      'width' => array()
    )
  );

  return wp_kses($jsonData['html'], $whitelist);
}

add_shortcode('simplecast-embed', 'simplecast_embed');
