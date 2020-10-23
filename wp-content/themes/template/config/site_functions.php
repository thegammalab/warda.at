<?php

function wpa_course_post_link($url, $post, $leavename=false){
    if ( is_object( $post ) ){
      if($post->post_type=="post"){
        $terms = wp_get_object_terms( $post->ID, 'category' );
        if( $terms ){
          return str_replace( get_bloginfo("url") , get_bloginfo("url")."/magazin/".$terms[0]->slug , $url );
        }
      }
    }
    return $url;
}
add_filter( 'post_link', 'wpa_course_post_link', 99, 3 );

function custom_rewrite_basic() {
  $terms = get_terms("category","hide_empty=0");
  foreach($terms as $term){
    add_rewrite_rule('magazin/'.$term->slug.'/([^/]+)/?', 'index.php?name=$matches[1]', 'top');
  }
  flush_rewrite_rules();
}
add_action('init', 'custom_rewrite_basic');

//German Form Validation message
add_filter( 'gform_validation_message', 'change_message', 10, 2 );

function change_message( $message, $form ) {
  $message = "<div class='validation_error'>Es gab einen Fehler bei Ihrer Eingabe, bitte überprüfen Sie es nochmal.</div>";
   return $message;
}

add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts()
{
  echo '<style>
  .wrap{
    min-height: 0px !important;
  }
  </style>';
}

function cc1_mime_types($mimes)
{
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc1_mime_types');


add_filter( 'body_class','my_body_classes' );
function my_body_classes( $classes ) {
  if ($cat_name = get_query_var("category_name")) {
    $the_cat = get_term_by("slug", $cat_name, "category");
    if($the_cat->parent){
      $level0 = $the_cat->parent;
    }else{
        $level0 = $the_cat->term_id;
    }
    $classes[] = "cat_".get_term_meta($level0,"theme_color",true);
  }

  return $classes;
}

 add_filter( 'body_class', 'my_body_single_post_classes' );
function my_body_single_post_classes( $classes ){
    if( is_singular() ) {
        global $post;
        $taxonomy_terms = get_the_terms($post->ID, 'category');
        if ( $taxonomy_terms ) {
            foreach ( $taxonomy_terms as $taxonomy_term ) {
            $classes[] = 'single_post_category_' . $taxonomy_term->slug;
            }
        }
    }
    return $classes;
}

function xx__update_custom_roles() {
  global $wpdb;
  add_role( 'photographer', 'Photographer', array( 'read' => true, 'level_0' => true ) );

  if(isset($_GET["action"]) && $_GET["action"]=="import_event_thumbnails"){
    $results = $wpdb->get_results("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_event_id'");
    foreach($results as $item){
      if(!get_post_meta($post_id,"_thumbnail_id",true)){
        apply_filters( 'warda_get_event_thumbnail',$item->post_id);
      }
    }
    die();
  }

}
add_action( 'init', 'xx__update_custom_roles' );



if(isset($_GET["action"]) && $_GET["action"]=="import_data_header"){
  $row = 0;
  global $wpdb;

  $columns = array();
  $csvData = file_get_contents (get_bloginfo("stylesheet_directory")."/db/".$_GET["file"].".csv");
  $lines = explode(PHP_EOL, $csvData);
  // if(strpos($csvData,"||||||")){
  //   $lines = explode("||||||", $csvData);
  // }else{
  //   $lines = explode(PHP_EOL, $csvData);
  // }


  $ignore = array();
  foreach($lines as $f=>$line){
    // echo $line."\n\n";
    if(!in_array($f,$ignore)){
      $the_line = str_replace("\n","",$line);
      $the_line = str_replace('\""','\"',$the_line);
      $the_line = str_replace('""','',$the_line);
      $count = substr_count($the_line,'"');
      $i = $f;
      if($count%2==1){
        do{
          $i++;
          $the_line.= $lines[$i];
          $count += substr_count($lines[$i],'"');
          $ignore[]=$i;
          // unset($lines[$i]);
        }while($count%2==1);
      }
      // echo $the_line."<br><br>

      // ";
      $data = str_getcsv($the_line);
      if($f==0){
            $sql = "CREATE TABLE IF NOT EXISTS `db_".$_GET["file"]."` (";
            foreach($data as $f=>$v){
              if($v){
                if($f==0){
                  $sql .= "`".sanitize_title($v)."` int NOT NULL AUTO_INCREMENT PRIMARY KEY,";
                }else{
                  $sql .= "`".sanitize_title($v)."`  longtext COLLATE 'utf8_general_ci' NOT NULL,";
                }
                $columns[$f]=sanitize_title($v);
              }
            }
            $sql = substr($sql,0,strlen($sql)-1).");";
            $wpdb->query($sql);
      }else{
            $sql = "INSERT INTO `db_".$_GET["file"]."` SET ";
            foreach($data as $f=>$v){
              if($columns[$f]){
                if($f==0){
                  $sql .= "`".$columns[$f]."`='".(intval($v))."', ";
                }else{
                  $sql .= "`".$columns[$f]."`='".(esc_sql($v))."', ";
                }
              }
            }
            $sql = substr($sql,0,strlen($sql)-2).";";
            $wpdb->query($sql);
      }
    }

  }
  die();

  if (($handle = fopen(get_bloginfo("stylesheet_directory")."/db/".$_GET["file"].".csv", "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 100000000, ",")) !== FALSE) {
          $num = count($data);
          $row++;

      }
      fclose($handle);
  }
  die();
}


if(isset($_GET["action"]) && $_GET["action"]=="import_data_users"){
  $row = 0;
  global $wpdb;
  $results = $wpdb->get_results("SELECT * FROM `db_users`");
  foreach($results as $item){
    if($user_id = $wpdb->get_var("SELECT `user_id` FROM `wp_usermeta` WHERE `meta_key`='orig_user_id' AND `meta_value`='".$item->id."'")){
      $wpdb->query("UPDATE `wp_users` SET `user_registered`='".date('Y-m-d H:i:s',strtotime(substr($item->created_at,0,19)))."' WHERE `id`='".$user_id."'");
      update_user_meta($user_id,"last_update",strtotime(substr($item->updated_at,0,19)));

    }else{
      $userdata = array(
        'user_email' =>  $item->email,
        'user_login' =>  $item->email,
        'user_pass'  =>  "123456",
        'user_registered' => date('Y-m-d H:i:s',strtotime($item->updated_at))
      );
      $user_id = wp_insert_user( $userdata ) ;
      update_user_meta($user_id,"orig_user_id",$item->id);
      update_user_meta($user_id,"last_update",strtotime(substr($item->created_at,0,19)));

    }

    update_user_meta($user_id,"first_name",get_user_meta($user_id,"firstname",true));
    update_user_meta($user_id,"last_name",get_user_meta($user_id,"lastname",true));
    update_user_meta($user_id,"last_name",get_user_meta($user_id,"lastname",true));

    $roles = explode(",",substr($item->roles,1,strlen($item->roles)-2));

    $the_user = new WP_User( $user_id );
    foreach($roles as $role){
      $the_user->add_role(trim(str_replace('"','',$role)));
    }

    foreach($item as $f=>$v){
      update_user_meta($user_id,$f,$v);
    }
  }

  die();
}

function update_post_content($post_id){
  global $wpdb;
  $pieces = array();
  $orig_post_id = get_post_meta($post_id,"orig_post_id",true);
    $content_pieces = $wpdb->get_results("SELECT * FROM `db_content_elements` WHERE `post_id`='".$orig_post_id."' ORDER BY `position` ASC");
    foreach($content_pieces as $content_piece){
      $c = $content_piece->content;
      $c = str_replace('"','\"',$c);
      $c = str_replace('\":\"','":"',$c);
      $c = str_replace('{\"','{"',$c);
      $c = str_replace('\"}','"}',$c);
      $cont = json_decode($c);
      if(isset($cont->body)){
        $pieces[intval($content_piece->position)]=$cont->body;
      }elseif(isset($cont->event_id)){
        $pieces[intval($content_piece->position)]='[event_widget old_id='.$cont->event_id.']';
      }elseif(isset($cont->video_id)){
        $pieces[intval($content_piece->position)]='[video_widget old_id='.$cont->video_id.']';
      }elseif(isset($cont->gallery_id)){
        $pieces[intval($content_piece->position)]='[gallery_widget old_id='.$cont->gallery_id.']';
      }elseif(($content_piece->type=="ContentElement::SingleImage")){
        $pieces[intval($content_piece->position)]='[img_widget image="'.$content_piece->data.'" link="'.$cont->link.'" title="'.addslashes($cont->title).'"]';
      }elseif(($content_piece->type=="ContentElement::RelatedPosts")){
        $rel_articles = $cont;
        $related_ids = array();
        foreach($rel_articles as $rel_article){
          if($rel_id = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_post_id' AND `meta_value`='".$rel_article."'")){
            $related_ids[]=$rel_id;
          }
        }
        update_post_meta($post_id,"related_articles",$related_ids);
        $pieces[intval($content_piece->position)]='[related_widget old_ids="'.implode(",",$related_ids).'"]';
      }
    }

    ksort($pieces);
    $the_content = implode("",$pieces);
    $my_post = array(
      "ID"=>$post_id,
      "post_content" => $the_content

    );
    $post_id = wp_update_post( $my_post );

    $elementor_pieces = array();
    foreach($pieces as $cont){
      $elementor_pieces[] = array(
        "id" => rand(100000,999999),
        "elType" => "section",
        "settings" => array(),
        "elements" => array(array(
          "id" => rand(100000,999999),
          "elType" => "column",
          "settings" => array("_column_size"=>100),
          "elements" => array(array(
            "id" => rand(100000,999999),
            "elType" => "widget",
            "settings" => array("editor"=>addslashes($cont)),
            "elements" => array(),
            "widgetType" => "text-editor"
          )),
          "isInner"=> ""
          )
        ),
        "isInner"=> ""
      );
    }

    update_post_meta($post_id,"content_updated_new",time());
    update_post_meta($post_id,"_elementor_data",json_encode($elementor_pieces));
    update_post_meta($post_id,"_elementor_edit_mode","builder");
    update_post_meta($post_id,"_elementor_template_type","wp-post");
    update_post_meta($post_id,"_elementor_version","2.9.13");
    update_post_meta($post_id,"_elementor_controls_usage",'a:3:{s:11:"text-editor";a:3:{s:5:"count";i:'.count($pieces).';s:15:"control_percent";i:0;s:8:"controls";a:1:{s:7:"content";a:1:{s:14:"section_editor";a:1:{s:6:"editor";i:'.count($pieces).';}}}}s:6:"column";a:3:{s:5:"count";i:'.count($pieces).';s:15:"control_percent";i:0;s:8:"controls";a:1:{s:6:"layout";a:1:{s:6:"layout";a:1:{s:12:"_inline_size";i:2;}}}}s:7:"section";a:3:{s:5:"count";i:'.count($pieces).';s:15:"control_percent";i:0;s:8:"controls";a:0:{}}}');
}

function update_post_image($post_id){
  $cover = get_post_meta($post_id,"cover",true);
  $upload_dir = wp_upload_dir();

  if($cover){
    $image_url = "https://s3.eu-central-1.amazonaws.com/assets.warda.at/uploads/post/".get_post_meta($post_id,"orig_post_id",true)."/cover/".$cover;

    if(!get_post_meta($post_id,"_thumbnail_id",true)){
      $image_data = file_get_contents( $image_url );
      if($image_data){
        $filename = basename( $image_url );

        if ( wp_mkdir_p( $upload_dir['path'] ) ) {
          $file = $upload_dir['path'] . '/' . $filename;
        }
        else {
          $file = $upload_dir['basedir'] . '/' . $filename;
        }

        file_put_contents( $file, $image_data );
        $wp_filetype = wp_check_filetype( $filename, null );

        $attachment = array(
          'post_mime_type' => $wp_filetype['type'],
          'post_title' => sanitize_file_name( $filename ),
          'post_content' => get_post_meta($post_id,"cover_image_credits",true),
          'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment( $attachment, $file, $post_id);
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        update_post_meta($post_id,"_thumbnail_id",$attach_id);
      }
    }
  }
}



if(isset($_GET["action"]) && $_GET["action"]=="import_images_posts"){
  $results = $wpdb->get_results("SELECT * FROM `db_posts`");
  foreach($results as $item){
    if($post_id = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_post_id' AND `meta_value`='".$item->id."'")){
      update_post_image($post_id);
    }
  }
  die();
}

if(isset($_GET["action"]) && $_GET["action"]=="import_posts_date"){
  $row = 0;
  global $wpdb;
  $results = $wpdb->get_results("SELECT * FROM `db_posts`");
  foreach($results as $item){
    if($post_id = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_post_id' AND `meta_value`='".$item->id."'")){
      $created_at= substr($item->created_at,0,19);
      $updated_at = substr($item->updated_at,0,19);
      echo $post_id.": ".substr($item->created_at,0,19)." -- ".substr($item->updated_at,0,19)."<br>";
      $sql = "UPDATE `wp_posts` SET  `post_date`='".$created_at."', `post_date_gmt`='".$created_at."', `post_modified`='".$updated_at."', `post_modified_gmt`='".$updated_at."' WHERE `ID`='".$post_id."'";
      $wpdb->query($sql);
    }
  }
  die();
}

if(isset($_GET["action"]) && $_GET["action"]=="import_data_posts"){
  $row = 0;
  global $wpdb;
  $results = $wpdb->get_results("SELECT * FROM `db_posts`");
  foreach($results as $item){
    if($post_id = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_post_id' AND `meta_value`='".$item->id."'")){
      $cats = array();
      $post_author = $wpdb->get_var("SELECT `user_id` FROM `wp_usermeta` WHERE `meta_key`='orig_user_id' AND `meta_value`='".$item->creator_id."'");
      $cats = array();
      $cat = get_term_by("name",$item->category,"category");
      if(!$cat){
        $the_cat = wp_insert_term($item->category,"category");
        if(!is_wp_error($the_cat)){
          $cats[]=$the_cat["term_id"];
        }else{
          print_r($the_cat);
        }
      }else{
        $cats[]=$cat->term_id;
      }

      $my_post = array(
        'ID' => $post_id,
        'post_title'    => wp_strip_all_tags($item->title),
        'post_status'   => 'publish',
        'post_author' => $post_author,
        'post_excerpt' => $item->introduction,
        'post_category' => $cats,
      );
      $post_id = wp_update_post( $my_post ) ;
      update_post_meta($post_id,"_yoast_wpseo_primary_category",$cats[0]);
      foreach($item as $f=>$v){
        update_post_meta($post_id,$f,$v);
      }
    }else{
      $cats = array();
      $post_author = $wpdb->get_var("SELECT `user_id` FROM `wp_usermeta` WHERE `meta_key`='orig_user_id' AND `meta_value`='".$item->creator_id."'");
      $cats = array();
      $cat = get_term_by("name",$item->category,"category");
      if(!$cat){
        $the_cat = wp_insert_term($item->category,"category");
        if(!is_wp_error($the_cat)){
          $cats[]=$the_cat["term_id"];
        }else{
          print_r($the_cat);
        }
      }else{
        $cats[]=$cat->term_id;
      }

      $my_post = array(
        'post_title'    => wp_strip_all_tags($item->title),
        'post_status'   => 'publish',
        'post_author' => $post_author,
        'post_excerpt' => $item->introduction,
        'post_category' => $cats,
      );

      // Insert the post into the database
      $post_id = wp_insert_post( $my_post );
      update_post_meta($post_id,"orig_post_id",$item->id);
      update_post_meta($post_id,"_yoast_wpseo_primary_category",$cats[0]);

      foreach($item as $f=>$v){
        update_post_meta($post_id,$f,$v);
      }
    }


    if(!get_post_meta($post_id,"content_updated_new",true) || !get_post_field("post_content",$post_id)){
      echo "<br>".$post_id."<br>";
      update_post_content($post_id);
    }

  }

  die();
}



if(isset($_GET["action"]) && $_GET["action"]=="import_data_galleries"){
  $row = 0;
  global $wpdb;
  $results = $wpdb->get_results("SELECT * FROM `db_galleries`");
  foreach($results as $item){
    if($post_id = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_gal_id' AND `meta_value`='".$item->id."'")){
      $post_author = $wpdb->get_var("SELECT `user_id` FROM `wp_usermeta` WHERE `meta_key`='orig_user_id' AND `meta_value`='".$item->creator_id."'");

      $my_post = array(
        'ID' => $post_id,
        'post_title'    => wp_strip_all_tags($item->name),
        'post_status'   => 'publish',
        'post_type'     => 'photos',
        'post_date'     => substr($item->created_at,0,19),
        'post_modified' => substr($item->updated_at,0,19),
        'post_author'   => $post_author,
      );

      $post_id = wp_update_post( $my_post ) ;
      foreach($item as $f=>$v){
        update_post_meta($post_id,$f,$v);
      }
      update_post_meta($post_id,"gal_orig_event_id",$item->event_id);

      // $gal_images = $wpdb->get_results("SELECT * FROM `db_images` WHERE `imageable_id`='".$item->id."'");
      // $i = 0;
      // foreach($gal_images as $gal_image){
      //   if(!get_post_meta($post_id,"gallery_imgs_".$i."_id",true)){
      //     foreach($gal_image as $f=>$v){
      //       update_post_meta($post_id,"gallery_imgs_".$i."_".$f,$v);
      //     }
      //   }
      //   $i++;
      // }
      // update_post_meta($post_id,"gallery_imgs",$i);
    }else{
      $cats = array();
      $post_author = $wpdb->get_var("SELECT `user_id` FROM `wp_usermeta` WHERE `meta_key`='orig_user_id' AND `meta_value`='".$item->creator_id."'");

      $my_post = array(
        'post_title'    => wp_strip_all_tags($item->name),
        'post_status'   => 'publish',
        'post_type'     => 'photos',
        'post_date'     => substr($item->created_at,0,19),
        'post_modified' => substr($item->updated_at,0,19),
        'post_author'   =>  $post_author,
      );

      // Insert the post into the database
      $post_id = wp_insert_post( $my_post );
      update_post_meta($post_id,"orig_gal_id",$item->id);

      // $gal_images = $wpdb->get_results("SELECT * FROM `db_images` WHERE `imageable_id`='".$item->id."'");
      // $i = 0;
      // foreach($gal_images as $gal_image){
      //   foreach($gal_image as $f=>$v){
      //     update_post_meta($post_id,"gallery_imgs_".$i."_".$f,$v);
      //   }
      //   $i++;
      // }
      // update_post_meta($post_id,"gallery_imgs",$i);

      foreach($item as $f=>$v){
        update_post_meta($post_id,$f,$v);
      }
      update_post_meta($post_id,"gal_orig_event_id",$item->event_id);

    }
  }

  die();
}

if(isset($_GET["action"]) && $_GET["action"]=="process_content_areas22"){
  $row = 0;
  global $wpdb;
  $results = $wpdb->get_results("SELECT * FROM `db_content_elements`");
  foreach($results as $item){
    $content = trim($item->content);
    $content = str_replace("\'",'\"',$content);
    $content = str_replace("':'",'":"',$content);
    $content = str_replace("', '","','",$content);
    $content = str_replace("','",'","',$content);
    $content = '{"'.substr($content,1,strlen($content)-2).'"}';
    $cont_array = json_decode($content);
    if($item->id>100){
      if(is_object($cont_array)){
        $wpdb->query("UPDATE `db_content_elements` SET `content`='".$content."' WHERE `id`='".$item->id."'");
      }
    }

  }

  die();
}


if(isset($_GET["action"]) && $_GET["action"]=="process_related_posts"){
  $row = 0;
  global $wpdb;
  $results = $wpdb->get_results("SELECT * FROM `db_content_elements` WHERE `type` = 'ContentElement::RelatedPosts'");
  foreach($results as $item){
    $post_id = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_post_id' AND `meta_value`='".$item->post_id."'");
    $rel_articles = json_decode(trim($item->content));
    $related_ids = array();
    foreach($rel_articles as $rel_article){
      if($rel_id = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_post_id' AND `meta_value`='".$rel_article."'")){
        $related_ids[]=$rel_id;
      }
    }
    echo "//".$post_id."//";
    print_r($related_ids);
    update_post_meta($post_id,"related_articles",$related_ids);
  }

  die();
}



if(isset($_GET["action"]) && $_GET["action"]=="import_data_events"){
  $row = 0;
  global $wpdb;
  $results = $wpdb->get_results("SELECT * FROM `db_events`");
  foreach($results as $item){


  }

  die();
}




/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////




if(isset($_GET["action"]) && $_GET["action"]=="import_data_videos"){
  $row = 0;
  global $wpdb;
  $results = $wpdb->get_results("SELECT * FROM `db_videos`");
  foreach($results as $item){
    if($post_id = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_video_id' AND `meta_value`='".$item->id."'")){
      $post_author = $wpdb->get_var("SELECT `user_id` FROM `wp_usermeta` WHERE `meta_key`='orig_user_id' AND `meta_value`='".$item->creator_id."'");

      $my_post = array(
        'ID' => $post_id,
        'post_title'    => wp_strip_all_tags($item->name),
        'post_status'   => 'publish',
        'post_type'     => 'videos',
        'post_date'     => substr($item->created_at,0,19),
        'post_modified' => substr($item->updated_at,0,19),
        'post_author'   => $post_author,
        'post_content'  => $item->description,
      );

      $post_id = wp_update_post( $my_post ) ;
      foreach($item as $f=>$v){
        update_post_meta($post_id,$f,$v);
      }
      update_post_meta($post_id,"video_orig_event_id",$item->event_id);


    }else{
      $cats = array();
      $post_author = $wpdb->get_var("SELECT `user_id` FROM `wp_usermeta` WHERE `meta_key`='orig_user_id' AND `meta_value`='".$item->creator_id."'");

      $my_post = array(
        'post_title'    => wp_strip_all_tags($item->name),
        'post_status'   => 'publish',
        'post_type'     => 'videos',
        'post_date'     => substr($item->created_at,0,19),
        'post_modified' => substr($item->updated_at,0,19),
        'post_content'  =>  $item->description,
        'post_author'   =>  $post_author,
      );

      // Insert the post into the database
      $post_id = wp_insert_post( $my_post );
      update_post_meta($post_id,"orig_video_id",$item->id);

      foreach($item as $f=>$v){
        update_post_meta($post_id,$f,$v);
      }
      update_post_meta($post_id,"video_orig_event_id",$item->event_id);

    }
  }

  die();
}

function update_video_image($post_id){
  $hoster = get_post_meta($post_id,"hoster",true);
  $upload_dir = wp_upload_dir();

  if($hoster){
    if($hoster == "vimeo"){
      $image_url = "https://i.vimeocdn.com/video/".get_post_meta($post_id,"video_id",true)."_640.webp";
    }elseif($hoster == "youtube"){
      $image_url = "https://img.youtube.com/vi/".get_post_meta($post_id,"video_id",true)."/maxresdefault.jpg";
    }

    if($image_url){
      echo $image_url."<br>";

      if(!get_post_meta($post_id,"_thumbnail_id",true) || 1==1){
        $image_data = file_get_contents( $image_url );
        if($image_data){
          $filename = $post_id."_".basename( $image_url );

          if ( wp_mkdir_p( $upload_dir['path'] ) ) {
            $file = $upload_dir['path'] . '/' . $filename;
          }
          else {
            $file = $upload_dir['basedir'] . '/' . $filename;
          }

          file_put_contents( $file, $image_data );
          $wp_filetype = wp_check_filetype( $filename, null );

          $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name( $filename ),
            'post_content' => get_post_meta($post_id,"cover_image_credits",true),
            'post_status' => 'inherit'
          );

          $attach_id = wp_insert_attachment( $attachment, $file, $post_id);
          require_once( ABSPATH . 'wp-admin/includes/image.php' );
          $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
          wp_update_attachment_metadata( $attach_id, $attach_data );
          update_post_meta($post_id,"_thumbnail_id",$attach_id);
        }
      }
    }
  }
}



function get_event_info($event_id,$post_id){
  global $wpdb;
  $event = $wpdb->get_row("SELECT * FROM `db_events` WHERE `ID`='".$event_id."'");

  $the_location = $wpdb->get_row("SELECT * FROM `db_locations` WHERE `id`='".$event->location_id."'");

  /////////////

      $events_loc = array();
      $cat = get_term_by("name",$the_location->name,"venue");
      if(!$cat){
        $the_cat = wp_insert_term($the_location->name,"venue");
        if(!is_wp_error($the_cat)){
          $events_loc[]=$the_cat["term_id"];
          foreach($the_location as $f=>$v){
            update_term_meta($the_cat["term_id"],"loc_".$f,$v);
          }
          update_term_meta($cat->term_id,"address", $the_location->street.", ".$the_location->city);
        }else{
          print_r($the_cat);
        }
      }else{
        $events_loc[]=$cat->term_id;
        foreach($the_location as $f=>$v){
            update_term_meta($cat->term_id,"loc_".$f,$v);
          }
        update_term_meta($cat->term_id,"address", $the_location->street.", ".$the_location->city);
      }
      wp_set_post_terms($post_id,$events_loc,"venue");
      apply_filters( 'warda_get_event_thumbnail',$post_id);

      update_post_meta($post_id,"_yoast_wpseo_primary_venue",$events_loc[0]);

      //////////

      $events_cat = array();
      $cat = get_term_by("name",$event->category,"events_cat");
      if(!$cat){
        $the_cat = wp_insert_term($event->category,"events_cat");
        if(!is_wp_error($the_cat)){
          $events_cat[]=$the_cat["term_id"];
        }else{
          print_r($the_cat);
        }
      }else{
        $events_cat[]=$cat->term_id;
      }
      wp_set_post_terms($post_id,$events_cat,"events_cat");
      update_post_meta($post_id,"_yoast_wpseo_primary_venue",$events_cat[0]);

      ////////////////

      $post_author = $wpdb->get_var("SELECT `user_id` FROM `wp_usermeta` WHERE `meta_key`='orig_user_id' AND `meta_value`='".$event->creator_id."'");

      if($ev_id = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_event_id' AND `meta_value`='".$event_id."'")){
        $my_post = array(
          'ID' => $ev_id,
          'post_title'    => wp_strip_all_tags($event->name),
          'post_status'   => 'publish',
          'post_type'   => 'events',
          'post_author' => $post_author,
          'post_content' => $event->description,
          'post_date'     => substr($event->created_at,0,19),
          'post_modified' => substr($event->updated_at,0,19),
        );
        $ev_id = wp_update_post( $my_post ) ;

      }else{

        $my_post = array(
          'post_title'    => wp_strip_all_tags($event->name),
          'post_status'   => 'publish',
          'post_type'   => 'events',
          'post_author' => $post_author,
          'post_content' => $event->description,
          'post_date'     => substr($event->created_at,0,19),
          'post_modified' => substr($event->updated_at,0,19),
        );

        // Insert the post into the database
        $ev_id = wp_insert_post( $my_post );

        wp_set_post_terms($ev_id,$events_cat,"events_cat");
        update_post_meta($ev_id,"_yoast_wpseo_primary_events_cat",$events_cat[0]);

        wp_set_post_terms($ev_id,$events_loc,"venue");
        update_post_meta($ev_id,"_yoast_wpseo_primary_venue",$events_loc[0]);
      }

      $all_events = $wpdb->get_results("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_event_id' AND `meta_value`='".$event_id."'");
      foreach($all_events as $all_event){
        if($all_events->post_id!=$ev_id){
          wp_delete_post($all_events->post_id,true);
          $wpdb->get_results("DELETE FROM `wp_postmeta` WHERE `post_id`='".$all_events->post_id."'");
        }
      }


      $res = $wpdb->get_results("SELECT * FROM `wp_postmeta` WHERE `meta_key`='video_orig_event_id' AND `meta_value`='".$event_id."'");
      foreach($res as $it){
        update_post_meta($it->post_id,"new_event_id",$post_id);
      }
      $res = $wpdb->get_results("SELECT * FROM `wp_postmeta` WHERE `meta_key`='gal_orig_event_id' AND `meta_value`='".$event_id."'");
      foreach($res as $it){
        update_post_meta($it->post_id,"new_event_id",$post_id);
      }

      foreach($event as $f=>$v){
        update_post_meta($ev_id,$f,$v);
      }

      if($event->starts_at){
        update_post_meta($ev_id,"start_date",date("Ymd",strtotime(substr($event->starts_at,0,19))));
        update_post_meta($ev_id,"start_time",date("G:i:s",strtotime(substr($event->starts_at,0,19))));
      }

      if($event->ends_at){
        update_post_meta($ev_id,"end_date",date("Ymd",strtotime(substr($event->ends_at,0,19))));
        update_post_meta($ev_id,"end_time",date("G:i:s",strtotime(substr($event->ends_at,0,19))));
      }

      update_post_meta($post_id,"the_date",strtotime(substr($event->starts_at,0,19)));
      update_post_meta($ev_id,"the_date",strtotime(substr($event->starts_at,0,19)));

      update_post_meta($ev_id,"orig_event_id",$event_id);

      echo $ev_id;

    if(!get_post_meta($ev_id,"_thumbnail_id",true)){
      $img_url = $wpdb->get_var("SELECT `filename` FROM `db_images` WHERE `imageable_id`='".$event_id."'");
      if($img_url){
        $image_url = "https://s3.eu-central-1.amazonaws.com/assets.warda.at/uploads/events/".$event_id."/".$img_url;

        $curl = curl_init($image_url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $image_data = curl_exec($curl);
                    curl_close($curl);

                    if($image_data){
                        $upload_dir = wp_upload_dir();
                        $filename = basename( $image_url );

                        if ( wp_mkdir_p( $upload_dir['path'] ) ) {
                            $file = $upload_dir['path'] . '/' . $filename;
                        }
                        else {
                            $file = $upload_dir['basedir'] . '/' . $filename;
                        }

                        file_put_contents( $file, $image_data );
                        $wp_filetype = wp_check_filetype( $filename, null );

                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => sanitize_file_name( $filename ),
                            'post_status' => 'inherit'
                        );

                        $attach_id = wp_insert_attachment( $attachment, $file, $ev_id);
                        require_once( ABSPATH . 'wp-admin/includes/image.php' );
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                        wp_update_attachment_metadata( $attach_id, $attach_data );
                        update_post_meta($ev_id,"_thumbnail_id",$attach_id);
                    }
      }
    }
}

if(isset($_GET["action"]) && $_GET["action"]=="import_videos_data"){
  $results = $wpdb->get_results("SELECT * FROM `db_videos`");
  foreach($results as $item){
    if($post_id = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_video_id' AND `meta_value`='".$item->id."'")){
      if(!get_post_meta($post_id,"_thumbnail_id",true) || 1==1){
        update_video_image($post_id);
      }
      if(get_post_meta($post_id,"event_id",true) && !get_post_meta($post_id,"video_data_updated",true)){
        $event_id = get_post_meta($post_id,"event_id",true);
        get_event_info($event_id, $post_id);
        update_post_meta($post_id,"video_data_updated",time());
      }
    }
  }
  die();
}


if(isset($_GET["action"]) && $_GET["action"]=="import_gallery_data"){
  $results = $wpdb->get_results("SELECT * FROM `db_galleries`");
  foreach($results as $item){
    if($post_id = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_gal_id' AND `meta_value`='".$item->id."'")){
      if(!get_post_meta($post_id,"_thumbnail_id",true)){
        update_video_image($post_id);
      }
      if(get_post_meta($post_id,"event_id",true) && !get_post_meta($post_id,"gal_data_updated",true)){
        $event_id = get_post_meta($post_id,"event_id",true);
        get_event_info($event_id, $post_id);
        update_post_meta($post_id,"gal_data_updated",time());
      }
    }
  }
  die();
}





if(isset($_GET["action"]) && $_GET["action"]=="import_gallery_genres"){
  $results = $wpdb->get_results("SELECT * FROM `db_galleries`");
  foreach($results as $item){
    if($post_id = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_gal_id' AND `meta_value`='".$item->id."'")){
      $event_id = get_post_meta($post_id,"gal_orig_event_id",true);
      $new_event_id = get_post_meta($post_id,"new_event_id",true);

      $sql = "SELECT `db_tags`.`name` FROM `db_taggings`,`db_tags` WHERE `db_tags`.`id`=`db_taggings`.`tag_id` AND `db_taggings`.`context`='genres' AND `db_taggings`.`taggable_id`='".$event_id."'";
      $genres = $wpdb->get_results($sql);
      $events_genre = array();
      foreach($genres as $genre_item){
        $genre_name = $genre_item->name;
        $cat = get_term_by("name",$genre_name,"genre");
        if(!$cat){
          $the_cat = wp_insert_term($genre_name,"genre");
          if(!is_wp_error($the_cat)){
            $events_genre[]=$the_cat["term_id"];
          }else{
            print_r($the_cat);
          }
        }else{
          $events_genre[]=$cat->term_id;
        }
      }
      wp_set_post_terms($post_id,$events_genre,"genre");
      wp_set_post_terms($new_event_id,$events_genre,"genre");

    }
  }
  die();
}



/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////



if(isset($_GET["action"]) && $_GET["action"]=="update_photos_date"){
  global $wpdb;
  $results = $wpdb->get_results("SELECT * FROM `wp_posts` WHERE `post_type`='photos'");
  foreach($results as $it){
    echo $it->ID."<br>";
    $the_date=get_post_meta($it->ID,"the_date",true);
    $date=get_post_meta($it->ID,"date",true);
    if($the_date && !$date){
      update_post_meta($it->ID,"date",date("Ymd",$the_date));
    }
  }
  die();
}


function save_post_meta( $post_id, $post, $update ) {
  global $wpdb;

  if(get_post_meta($post_id,"date",true)){
    update_post_meta($post_id,"the_date",strtotime(get_post_meta($post_id,"date",true)));
  }
  if(get_post_meta($post_id,"start_date",true)){
    update_post_meta($post_id,"the_date",strtotime(get_post_meta($post_id,"start_date",true)." ".get_post_meta($post_id,"start_time",true)));
  }
}

add_action( 'save_post', 'save_post_meta', 100, 3 );


/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////


add_shortcode('img_widget', 'img_widget');

function img_widget($atts){
  global $wpdb;
  $the_id = $wpdb->get_var("SELECT `id` FROM `db_content_elements` WHERE `data` = '".$atts["image"]."'");
  ob_start();
  echo "<div class='mb-3'><img src='https://assets.warda.at/uploads/content_element/single_image/".$the_id."/data/".$atts["image"]."' /></div>";
  $cont = ob_get_contents();
  ob_end_clean();
  return $cont;
}



/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////



if(isset($_GET["action"]) && $_GET["action"]=="generate_post_redirects"){
  $results = $wpdb->get_results("SELECT * FROM `wp_posts` WHERE `post_type`='post' AND `post_status`='publish'");
  foreach($results as $item){
    if(get_post_meta($item->ID,"orig_post_id",true)){

      $old_url = get_bloginfo("url")."/magazin/".get_post_meta($item->ID,"orig_post_id",true)."-".$item->post_name."/";
      $new_url = get_the_permalink($item->ID);


      if($lid = $wpdb->get_var("SELECT `id` FROM `ts_redirects` WHERE `old_link`='".$old_url."'")){
        $wpdb->query("UPDATE `ts_redirects` SET `title`='".$new_url."', `new_link`='".$new_url."', `old_link`='".$old_url."' WHERE `id`='".$lid."'");
      }else{
        $wpdb->query("INSERT INTO `ts_redirects` SET `title`='".$new_url."', `new_link`='".$new_url."', `old_link`='".$old_url."'");
      }
    }
  }
  die();
}


/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////



if(isset($_GET["action"]) && $_GET["action"]=="generate_photos_redirects"){
  $results = $wpdb->get_results("SELECT * FROM `wp_posts` WHERE `post_type`='photos' AND `post_status`='publish'");
  foreach($results as $item){
    if(get_post_meta($item->ID,"orig_gal_id",true)){
      $old_url = get_bloginfo("url")."/fotos/".get_post_meta($item->ID,"orig_gal_id",true)."-".str_replace("__trashed","",$item->post_name)."/";
      $new_url = get_the_permalink($item->ID);

      if($lid = $wpdb->get_var("SELECT `id` FROM `ts_redirects` WHERE `old_link`='".$old_url."'")){
        $wpdb->query("UPDATE `ts_redirects` SET `title`='".$new_url."', `new_link`='".$new_url."', `old_link`='".$old_url."' WHERE `id`='".$lid."'");
      }else{
        $wpdb->query("INSERT INTO `ts_redirects` SET `title`='".$new_url."', `new_link`='".$new_url."', `old_link`='".$old_url."'");
      }
    }
  }
  die();
}

/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////



if(isset($_GET["action"]) && $_GET["action"]=="import_redirects"){
  $row = 0;
  global $wpdb;

  $columns = array();
  $csvData = file_get_contents (get_template_directory()."/db/redirects3.csv");
  $lines = explode(PHP_EOL, $csvData);

  $ignore = array();
  foreach($lines as $f=>$line){
    $pieces = explode(",",$line);
    $old_url = str_replace("www.","",$pieces[0]);
    $old_url = str_replace(get_bloginfo("url"),"",$old_url);
    if(substr($old_url,0,1)=="/"){
      $old_url = substr($old_url,1);
    }
    $old_url = get_bloginfo("url")."/".$old_url;
    $new_url = $pieces[1];
    echo $new_url." -- ".$old_url."<br>";
    if($lid = $wpdb->get_var("SELECT `id` FROM `ts_redirects` WHERE `old_link`='".$old_url."'")){
        $wpdb->query("UPDATE `ts_redirects` SET `title`='".$new_url."', `new_link`='".$new_url."', `old_link`='".$old_url."' WHERE `id`='".$lid."'");
      }else{
        $wpdb->query("INSERT INTO `ts_redirects` SET `title`='".$new_url."', `new_link`='".$new_url."', `old_link`='".$old_url."'");
      }
  }

  die();
}

