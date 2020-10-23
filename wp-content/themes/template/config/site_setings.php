<?php

/* ==============================================================
REGISTER POST TYPES
============================================================== */

$post_types = array(
  array("events", array("name" => "Events", "singular_name" => "Event", "slug" => "events")),
  array("photos", array("name" => "Fotos", "singular_name" => "Foto", "slug" => "fotos")),
  array("videos", array("name" => "Videos", "singular_name" => "Video", "slug" => "videos")),
  array("sweepstakes", array("name" => "Gewinnspiel", "singular_name" => "Gewinnspiel", "slug" => "gewinnspiel")),
  array("vouchers", array("name" => "Gutscheine", "singular_name" => "Gutscheine", "slug" => "gutscheine")),
  array("podcasts", array("name" => "Podcasts", "singular_name" => "Podcast", "slug" => "podcasts")),
);


/* ==============================================================
REGISTER TAXONOMIES
============================================================== */

$taxonomies = array(
  array("voucher_cat", array("vouchers"), array("name" => "Categories", "singular_name" => "Category", "slug" => "voucher_cat")),
  array("sweepstakes_cat", array("sweepstakes"), array("name" => "Categories", "singular_name" => "Category", "slug" => "sweepstakes_cat")),
  array("events_cat", array("events","videos","photos"), array("name" => "Event Categories", "singular_name" => "Event Category", "slug" => "events_cat")),

  array("genre", array("events","videos","photos"), array("name" => "Genre", "singular_name" => "Genre", "slug" => "genre")),

  array("venue", array("photos","videos","events","vouchers","sweepstakes"), array("name" => "Venues", "singular_name" => "Venue", "slug" => "venue")),
  array("t", array("photos","events","videos","sweepstakes","vouchers","podcasts"), array("name" => "Tags", "singular_name" => "Tag", "slug" => "t")),
);



/* ==============================================================
REGISTER MENUS
============================================================== */

$menus = array(
  array("main_menu_header"),
  array("footer_menu_1"),
  array("footer_menu_2"),
  array("footer_menu_3"),
  array("privacy_menu"),
);

/* ==============================================================
REGISTER SIDEBAR
============================================================== */

$sidebars = array(
  array("frontpage_sidebar"),

  array("article_sidebar"),

  array("photos_list_sidebar"),
  array("photos_single_sidebar"),

  array("videos_list_sidebar"),
  array("videos_single_sidebar"),
  
  array("events_list_sidebar"),
  array("events_single_sidebar"),
    
  // array("vouchers_list_sidebar"),
  array("vouchers_single_sidebar"),
    
  // array("sweepstakes_list_sidebar"),
  array("sweepstakes_single_sidebar"),
      
  // array("podcasts_list_sidebar"),
  array("podcasts_single_sidebar"),
);

/* ==============================================================
REGISTER IMAGE SIZES
============================================================== */

$image_sizes = array();

/* ==============================================================
REGISTER POST FIELDS
============================================================== */

$post_fields = array(
  // "content_areas" => array(
  //   "name"=>"Sessions",
  //   "post_types" => array("services"),
  //   // "position" => "acf_after_title",

  //   "fields"=>array(
  //     "contentarea"=>array(
  //       "name"=>  "Sections",
  //       "type" => "repeater",
  //       "fields"=>array(
  //         "section_title"=>array(
  //           "name" => "section_title",
  //           "label" => "Title",
  //           "type" => "text",
  //         ),
  //         "section_content"=>array(
  //           "name" => "section_content",
  //           "label" => "Content",
  //           "type" => "wysiwyg",
  //         )
  //       )
  //     ),
  //   )
  // ),
  // "chapters_list" => array(
  //   "name"=>"Chapters",
  //   "post_types" => array("courses"),
  //   // "position" => "acf_after_title",

  //   "fields"=>array(
  //     "chapters"=>array(
  //       "name"=>  "Chapter",
  //       "type" => "repeater",
  //       "fields"=>array(
  //         "chapter"=>array(
  //           "name" => "name",
  //           "label" => "Chapter",
  //           "type" => "post_object",
  //           "post_type" => array("chapters")
  //         ),
  //         "chapter_label"=>array(
  //           "name" => "label",
  //           "label" => "Label",
  //           "type" => "text",
  //         ),
  //       )
  //     ),
  //   )
  // ),
  // "file_list" => array(
  //   "name"=>"File URLs",
  //   "post_types" => array("chapters"),
  //   // "position" => "acf_after_title",

  //   "fields"=>array(
  //     "video_sm"=>array(
  //       "name"=>  "Video Small",
  //       "type" => "text",
  //     ),
  //     "video_md"=>array(
  //       "name"=>  "Video Medium",
  //       "type" => "text",
  //     ),
  //     "video_lg"=>array(
  //       "name"=>  "Video Large",
  //       "type" => "text",
  //     ),
  //     "audio"=>array(
  //       "name"=>  "Audio",
  //       "type" => "text",
  //     ),
  //   )
  // ),
  // "course_attach" => array(
  //   "name"=>"Course",
  //   "post_types" => array("product"),
  //   // "position" => "acf_after_title",

  //   "fields"=>array(
  //     "course_id"=>array(
  //       "name" => "name",
  //       "label" => "Course",
  //       "type" => "post_object",
  //       "post_type" => array("courses")
  //     ),
  //   )
  // ),
);

/* ==============================================================
REGISTER USER FIELDS
============================================================== */

$user_fields = array();


/* ==============================================================
REGISTER MISC VARIABLES
============================================================== */

$variables = array();

/* ==============================================================
REGISTER THEME VARIABLES
============================================================== */

$theme_variables = array(
  "social_links" => array(
    "name" => "Social Links",
    "description" => "These are the site's social links",

    "fields" => array(
      "instagram_link" => array(
        "name" => "Instagram",
        "type" => "text",
      ),
      "youtube_link" => array(
        "name" => "YouTube",
        "type" => "text",
      ),
      "facebook_link" => array(
        "name" => "Facebook",
        "type" => "text",
      ),
      "twitter_link" => array(
        "name" => "Twitter",
        "type" => "text",
      ),
      "tiktok_link" => array(
        "name" => "Tiktok",
        "type" => "text",
      ),
      "spotify_link" => array(
        "name" => "Spotify",
        "type" => "text",
      ),
      "podcasts_link" => array(
        "name" => "Podcasts",
        "type" => "text",
      ),
    )
  ),
  "social_widgets" => array(
    "name" => "Social Widgets",
    "description" => "These are the site's social widgets",

    "fields" => array(
      "fb_link" => array(
        "name" => "Facebook Link",
        "type" => "text",
      ),
      "fb_count" => array(
        "name" => "Facebook Count",
        "type" => "text",
      ),
      "ig_link" => array(
        "name" => "Instagram Link",
        "type" => "text",
      ),
      "ig_count" => array(
        "name" => "Instagram Count",
        "type" => "text",
      ),
      "yt_link" => array(
        "name" => "YouTube Link",
        "type" => "text",
      ),
      "yt_count" => array(
        "name" => "YouTube Count",
        "type" => "text",
      ),
    )
  ),
  "tiktok_widgets" => array(
    "name" => "TikTok Widget",
    "description" => "These are the site's TikTok widget",

    "fields" => array(
      "tt_link" => array(
        "name" => "TikTok Link",
        "type" => "text",
      ),
      "tt_following_count" => array(
        "name" => "TikTok Following Count",
        "type" => "text",
      ),
      "tt_followers_count" => array(
        "name" => "TikTok Followers Count",
        "type" => "text",
      ),
      "tt_likes_count" => array(
        "name" => "TikTok Likes Count",
        "type" => "text",
      ),
      "tt_videos_count" => array(
        "name" => "TikTok Videos Count",
        "type" => "text",
      ),
    )
  ),
);

/* ==============================================================
REGISTER EMAILS
============================================================== */

$email_variables = array();
