<?php

/**
 * Template Functions
 *
 * @package     MASHNET
 * @subpackage  Functions/Templates
 * @copyright   Copyright (c) 2014, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.8
 */

/* Extend the core array for Facebook Twitter and Subscribe with additional networks
 * 
 * @param array
 * @since 2.0.8
 * @return array
 * 
 */


function mashnet_modify_arrNetworks($array){
     global $mashsb_options, $post;
        $singular = isset( $mashsb_options['singular'] ) ? $singular = true : $singular = false;

        $url = $array['url'];
        $title = $array['title'];
        $whatsapp_title = str_replace('+', '%20', $title);

        
        !empty($mashsb_options['mashnet_subjecttext']) ? $subject = rawurlencode($mashsb_options['mashnet_subjecttext']) : $subject = '%20';
        !empty($mashsb_options['mashnet_bodytext']) ? $body = rawurlencode($mashsb_options['mashnet_bodytext']) : $body = '%20';

    $networkArray = array(
        'google' => 'https://plus.google.com/share?text=' . $title . '&amp;url=' . $url,
        'whatsapp' =>  'whatsapp://send?text=' . $whatsapp_title . '%20' . $url,
        'pinterest' => 'https://pinterest.com/pin/create/button/?url=' . $url . '&amp;media=' . urlencode(mashnet_get_pinterest_image()) . '&amp;description=' . urlencode(mashnet_get_pinterest_desc()),
        'digg' => 'http://digg.com/submit?phase=2%20&amp;url=' . $url . '&amp;title=' . $title,
        'linkedin' => 'https://www.linkedin.com/shareArticle?trk=' . $title . '&amp;url=' . $url,
        'linkedin ' => 'https://www.linkedin.com/shareArticle?trk=' . $title . '&amp;url=' . $url, // Blank character fix ()
        'reddit' => 'http://www.reddit.com/submit?url=' . $url . '&amp;title=' . $title, 
        'reddit ' => 'http://www.reddit.com/submit?url=' . $url . '&amp;title=' . $title, // Blank character fix ()
        'stumbleupon' => 'http://www.stumbleupon.com/submit?url=' . $url,
        'stumbleupon ' => 'http://www.stumbleupon.com/submit?url=' . $url, // Blank character fix ()
        'vk' => 'http://vkontakte.ru/share.php?url=' . $url . '&amp;item=' . $title . mashnet_get_url_image_path(),
        'print' => 'http://www.printfriendly.com/print/?url=' . $url . '&amp;item=' . $title,
        'delicious' => 'https://delicious.com/save?v=5&amp;noui&amp;jump=close&amp;url=' . $url . '&amp;title=' . $title,
        'buffer' => 'https://bufferapp.com/add?url=' . $url . '&amp;text=' . $title,
        'weibo' => 'http://service.weibo.com/share/share.php?url=' . $url . '&amp;title=' . $title,
        'pocket' => 'https://getpocket.com/save?title=' . $title . '&amp;url=' . $url,
        'xing' => 'https://www.xing.com/social_plugins/share?h=1;url=' . $url . '&amp;title=' . $title,
        'tumblr' => 'https://www.tumblr.com/share?v=3&amp;u='. $url . '&amp;t=' . $title,
        'mail' => 'mailto:?subject=' . $subject . '&amp;body=' . $body . $url,
        'meneame' => 'http://www.meneame.net/submit.php?url=' . $url . '&amp;title=' . $title,
        'odnoklassniki' => 'http://www.odnoklassniki.ru/dk?st.cmd=addShare&amp;st.s=1&amp;st._surl=' . $url . '&amp;title=' . $title,
        'managewp' => 'http://managewp.org/share/form?url=' . $url . '&amp;title=' . $title,
        'mailru' => 'http://connect.mail.ru/share?share_url=' . $url,
        'line' => 'http://line.me/R/msg/text/?' . $title .'%20'. $url,
        'yummly' => 'http://www.yummly.com/urb/verify?url=' . $url . '&amp;title=' . $title,
        'frype' => 'http://www.draugiem.lv/say/ext/add.php?title='. $title .'&amp;link='.$url,
        'skype' => 'https://web.skype.com/share?url='.$url.'&lang=en-en',
        'telegram' => 'https://telegram.me/share/url?url='.$url.'&text=' . $title,
        'flipboard' => 'https://share.flipboard.com/bookmarklet/popout?v=2&title=' . urlencode($title) . '&url=' . $url,
        'hackernews' => 'http://news.ycombinator.com/submitlink?u='.$url.'&t='.urlencode($title),
        );
        return array_merge($array, $networkArray);         
}
add_filter( 'mashsb_array_networks', 'mashnet_modify_arrNetworks' );


/**
 * Get social image from open graph data. If og:image is empty use the featured image
 * 
 * @global array $post
 * @global obj $mashsb_meta_tags
 */
function mashnet_get_og_image() {
   global $post, $mashsb_meta_tags;
   if( is_singular() && class_exists( 'MASHSB_HEADER_META_TAGS' ) && method_exists( $mashsb_meta_tags, 'get_pinterest_image_url' ) ) {
      $image = $mashsb_meta_tags->get_og_image();
   } else {
      $image = function_exists( 'MASHOG' ) ? MASHOG()->MASHOG_OG_Output->_add_image() : mashsb_get_image( $post->ID );
   }
    return apply_filters('mashnet_og_image', $image);
}

/**
 * Return url image param
 * @return string
 */
function mashnet_get_url_image_path(){
    $image = mashnet_get_og_image();
   if ( empty( $image ) ){
    return '';  
   }
   return '&amp;image=' . $image;
}

/**
 * Get Pinterest image
 * 
 * @global obj $mashsb_meta_tags
 * @return string
 */
function mashnet_get_pinterest_image() {
    global $post, $mashsb_meta_tags;
    if( is_singular() && class_exists( 'MASHSB_HEADER_META_TAGS' ) && method_exists($mashsb_meta_tags, 'get_pinterest_image_url') ) {
        $image =  $mashsb_meta_tags->get_pinterest_image_url();
    }else{
        $image = function_exists( 'MASHOG' ) ? MASHOG()->MASHOG_OG_Output->_add_image() : mashsb_get_image( $post->ID );
    }
    return apply_filters('mashnet_pinterest_image', $image);
}

/**
 * Get Pinterest description
 * 
 * @global obj $mashsb_meta_tags
 * @return type
 */
function mashnet_get_pinterest_desc() {
    global $post, $mashsb_meta_tags;
    if( is_singular() && class_exists( 'MASHSB_HEADER_META_TAGS' ) && method_exists($mashsb_meta_tags, 'get_pinterest_description') ) {
        global $mashsb_meta_tags;
        return $mashsb_meta_tags->get_pinterest_description();
    } else{
        return mashsb_get_excerpt_by_id($post);
    }
}

?>
