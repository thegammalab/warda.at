<?php
if ( ! class_exists('TDF_Comments_Model') ){

  class TDF_Comments_Model {
    function add($args){
      if ($cid = $args["comment_core_id"]) {
        $fields = array("comment_post_ID" => "comment_core_post_id", "comment_author" => "comment_core_author", "comment_author_email" => "comment_core_author_email", "comment_author_url" => "comment_core_author_url", "comment_content" => "comment_core_content");
        $data = array();
        $data["comment_ID"] = $cid;
        foreach ($fields as $f => $v) {
          if ($value = $args[$v]) {
            $data[$f] = $value;
          }
        }
        if ($args["TDF_save_comment"] == "Save as Draft") {
          $data['comment_approved'] = 0;
        } else {
          $data['comment_approved'] = 1;
        }
        wp_update_comment($data);
      } else {
        if ($args["TDF_save_comment"] == "Save as Draft") {
          $app = 0;
        } else {
          $app = 1;
        }
        $data = array(
          'comment_post_ID' => $args["comment_core_post_id"],
          'comment_author' => $args["comment_core_author"],
          'comment_author_email' => $args["comment_core_author_email"],
          'comment_author_url' => $args["comment_core_author_url"],
          'comment_content' => $args["comment_core_content"],
          'user_id' => get_current_user_id(),
          'comment_approved' => $app,
        );
        $cid = wp_insert_comment($data);
      }
      foreach ($args as $f => $v) {
        if (substr($f, 0, 13) == "comment_meta_") {
          $field = substr($f, 13);
          if (is_array($v)) {
            delete_post_meta($cid, $field);
            foreach ($v as $val) {
              add_comment_meta($cid, $field, $val);
            }
          } else {
            update_comment_meta($cid, $field, $v);
          }
        }
      }
      $pid = $args["comment_core_post_id"];
      $comments = TDF_get_comments(array("hide_post_info" => 1, "search" => array("post_id" => array($pid))));
      $ratingsum = 0;
      $ratingsum1 = 0;
      $ratingsum2 = 0;
      $ratingsum3 = 0;
      $ratingsum4 = 0;
      $ratingno = 0;
      foreach ($comments["items"] as $item1) {
        $ratingsum+=$item1["meta_rating"];
        $ratingsum1+=$item1["meta_rating1"];
        $ratingsum2+=$item1["meta_rating2"];
        $ratingsum3+=$item1["meta_rating3"];
        $ratingsum4+=$item1["meta_rating4"];
        $ratingno++;
      }
      update_post_meta($pid, "rating", $ratingsum / $ratingno);
      update_post_meta($pid, "rating1", $ratingsum1 / $ratingno);
      update_post_meta($pid, "rating2", $ratingsum2 / $ratingno);
      update_post_meta($pid, "rating3", $ratingsum3 / $ratingno);
      update_post_meta($pid, "rating4", $ratingsum4 / $ratingno);
      update_post_meta($pid, "rating_count", $ratingno);
    }
    function get($args = array()) {
      /*
      array(
      page: 1,
      per_page: 10,
      hide_post_info: 1
      comment_template: administrator_item.php
      no_results_html: '<p>No results</p>'
      no_results_file: 'no_results.php'
      search: array(
      cid: 15
      post_type: array(post),
      post_id: 15,
      user_id: 15,
      user_email: office@thegammalab.com
      user_roles: array(administrator,subscriber)
      status: approve
      meta_price: 15
      meta_price: array(1,2,3)
      meta_price: array(
      compare: >
      value: 5
      )
      date: mm/dd/yyyy
      date_start: mm/dd/yyyy
      date_end: mm/dd/yyyy
      )
      posts: posts_array()
      order: name_asc/name_desc/id_asc/id_desc/date_asc/date_desc/post_count_asc/post_count_desc/meta_asc_FIELD/meta_desc_FIELD/custom_asc_FUNCTION/custom_desc_FUNCTION
      )
      */
      $query_args = array();
      $output = "";
      if(isset($args["order"])){
      if ($args["order"] == "name_asc") {
        $query_args['orderby'] = 'title';
        $query_args['order'] = 'ASC';
      } elseif ($args["order"] == "name_desc") {
        $query_args['orderby'] = 'title';
        $query_args['order'] = 'DESC';
      } elseif ($args["order"] == "id_asc") {
        $query_args['orderby'] = 'ID';
        $query_args['order'] = 'ASC';
      } elseif ($args["order"] == "id_desc") {
        $query_args['orderby'] = 'ID';
        $query_args['order'] = 'DESC';
      } elseif ($args["order"] == "date_asc") {
        $query_args['orderby'] = '';
        $query_args['order'] = 'ASC';
      } elseif ($args["order"] == "date_desc") {
        $query_args['orderby'] = '';
        $query_args['order'] = 'DESC';
      } elseif (substr($args["order"], 0, 9) == "meta_asc_") {
        $query_args['orderby'] = 'meta_value';
        $query_args['meta_key'] = substr($args["order"], 9);
        $query_args['order'] = 'ASC';
      } elseif (substr($args["order"], 0, 10) == "meta_desc_") {
        $query_args['orderby'] = 'meta_value';
        $query_args['meta_key'] = substr($args["order"], 10);
        $query_args['order'] = 'DESC';
      } elseif (substr($args["order"], 0, 11) == "custom_asc_") {

      } elseif (substr($args["order"], 0, 12) == "custom_desc_") {

      }
    }

      if (isset($args["search"]["date"]) && $date = $args["search"]["date"]) {
        $datestart_time = mktime(0, 0, 0, intval(substr($date, 0, 2)), intval(substr($date, 3, 2)), intval(substr($date, 6, 4)));
        $dateend_time = mktime(23, 59, 0, intval(substr($date, 0, 2)), intval(substr($date, 3, 2)), intval(substr($date, 6, 4)));
      }
      if (isset($args["search"]["date_start"]) && $date = $args["search"]["date_start"]) {
        $datestart_time = mktime(0, 0, 0, intval(substr($date, 0, 2)), intval(substr($date, 3, 2)), intval(substr($date, 6, 4)));
      }
      if (isset($args["search"]["date_end"]) && $date = $args["search"]["date_end"]) {
        $dateend_time = mktime(0, 0, 0, intval(substr($date, 0, 2)), intval(substr($date, 3, 2)), intval(substr($date, 6, 4)));
      }
      if (!isset($datestart_time)) {
        $datestart_time = 0;
      }
      if (!isset($dateend_time)) {
        $dateend_time = time();
      }
      if (isset($args["per_page"])) {
        $per_page = intval($args["per_page"]);
      } else {
        $per_page = 1000;
      }
      if (isset($args["page"])) {
        $page_no = $args["page"] * $per_page;
      } else {
        $page_no = 0;
      }
      $post_status_array = array();
      $post_id_array = array();
      $user_id_array = array();
      $comment_id_array = array();
      foreach ($args["search"] as $f => $v) {
        if (substr($f, 0, 5) == "meta_") {
          $field = substr($f, 5);
          if (!is_array($v)) {
            $query_args["meta_query"][] = array(
              'key' => $field,
              'value' => $v
            );
          } else {
            if (isset($v["compare"])) {
              $query_args["meta_query"][] = array(
                'key' => $field,
                'value' => $v["value"],
                'compare' => $v["compare"]
              );
            } else {
              $query_args["meta_query"][] = array(
                'key' => $field,
                'value' => $v,
                'compare' => "IN"
              );
            }
          }
        } elseif ($f == "status") {
          if (is_array($v)) {
            $post_status_array = $v;
          } else {
            $post_status_array = array($v);
          }
        } elseif ($f == "post_type") {
          if (is_array($v)) {
            $post_type_array = $v;
          } else {
            $post_type_array = array($v);
          }
        } elseif ($f == "post_id") {
          if (is_array($v)) {
            $post_id_array = $v;
          } else {
            $post_id_array = array($v);
          }
          if (count($post_id_array) == 1) {
            $query_args["post_id"] = $post_id_array[0];
          }
        } elseif ($f == "user_id") {
          if (is_array($v)) {
            $user_id_array = $v;
          } else {
            $user_id_array = array($v);
          }
          if (count($user_id_array) == 1) {
            $query_args["user_id"] = $user_id_array[0];
          }
        } elseif ($f == "user_email") {
          $author_email = $v;
        } elseif ($f == "cid") {
          $comment_id_array = $v;
        } else {

        }
      }
      if (isset($args["user_roles"])) {
        $user_list = array();
        foreach ($args["user_roles"] as $role) {
          $user_query = new WP_User_Query(array('role' => $role));
          $authors = $user_query->get_results();
          foreach ($authors as $author) {
            if (empty($user_id_array) || in_array($author->ID, $user_id_array)) {
              $user_list[] = $author->ID;
            }
          }
        }
        $user_id_array = $user_list;
      }
      //print_r($query_args);
      $items = array();
      $comments_query = new WP_Comment_Query;
      if ($comments = $comments_query->query($query_args)) {
        //print_r($comments);
        foreach ($comments as $comment) {
          $item = array();
          $reg_time = strtotime($comment->comment_date);
          $status = wp_get_comment_status($comment->comment_ID);
          $valid = 1;
          if (!$post_status_array || in_array($status, $post_status_array)) {

          } else {
            $valid = 0;
          }
          if (!$user_id_array || in_array($comment->user_id, $user_id_array)) {

          } else {
            $valid = 0;
          }
          if (!$post_id_array || in_array($comment->comment_post_ID, $post_id_array)) {

          } else {
            $valid = 0;
          }
          if (!$post_type_array || in_array(get_post_type($comment->comment_post_ID), $post_type_array)) {

          } else {
            $valid = 0;
          }
          if (!$comment_id_array || in_array($comment->comment_ID, $comment_id_array)) {

          } else {
            $valid = 0;
          }
          //if($reg_time>$datestart_time && $dateend_time>$reg_time){}else{$valid = 0;}
          if ($valid) {
            //$items[]=$comment;
            foreach ($comment as $f => $v) {
              $item[$f] = $v;
            }
            $pid = $comment->comment_post_ID;
            $item["comment_user_id"] = $comment->user_id;
            $item["comment_date"] = strtotime($comment->comment_date);
            $item["comment_status"] = $status;
            $meta = get_comment_meta($comment->comment_ID);
            foreach ($meta as $f => $v) {
              if (count($v) > 1) {
                $item["meta_" . $f] = $v;
              } else {
                $item["meta_" . $f] = $v[0];
              }
            }
            $ratingsum = 0;
            $ratingsum1 = 0;
            $ratingsum2 = 0;
            $ratingsum3 = 0;
            $ratingsum4 = 0;
            $ratingsum5 = 0;
            $ratingno = 0;
            foreach ($item["comments"]["items"] as $item1) {
              if ($item1["meta_rating"]) {
                $ratingsum += $item1["meta_rating"];
                $ratingsum1 += $item1["meta_rating1"];
                $ratingsum2 += $item1["meta_rating2"];
                $ratingsum3 += $item1["meta_rating3"];
                $ratingsum4 += $item1["meta_rating4"];
                $ratingsum5 += $item1["meta_rating5"];
                $ratingno++;
              }
            }
            $item["rating_score"] = round($item["meta_rating"], 2);
            $item["rating_stars"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round($item["meta_rating"] * 20) . '%">&nbsp;</div></div>';
            $item["rating_score1"] = round($item["meta_rating1"], 2);
            $item["rating_stars1"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round($item["meta_rating1"] * 20) . '%">&nbsp;</div></div>';
            $item["rating_score2"] = round($item["meta_rating2"], 2);
            $item["rating_stars2"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round($item["meta_rating2"] * 20) . '%">&nbsp;</div></div>';
            $item["rating_score3"] = round($item["meta_rating3"], 2);
            $item["rating_stars3"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round($item["meta_rating3"] * 20) . '%">&nbsp;</div></div>';
            $item["rating_score4"] = round($item["meta_rating4"], 2);
            $item["rating_stars4"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round($item["meta_rating4"] * 20) . '%">&nbsp;</div></div>';
            $item["rating_score5"] = round($item["meta_rating5"], 2);
            $item["rating_stars5"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round($item["meta_rating5"] * 20) . '%">&nbsp;</div></div>';
            if ($comment->user_id) {
              $info = array("id", "user_login", "user_pass", "user_nicename", "user_email", "user_url", "user_registered", "user_activation_key", "user_status", "display_name", "nickname");
              foreach ($info as $field) {
                $item["author_" . $field] = get_the_author_meta($field, $comment->comment_post_ID);
              }
              $author_meta = get_user_meta($comment->user_id);
              foreach ($author_meta as $f => $v) {
                if (count($v) > 1) {
                  $item["author_" . $f] = $v;
                } else {
                  $item["author_" . $f] = $v[0];
                }
              }
              $item["author_name"] = $item["author_user_nicename"];
              $item["author_link"] = get_author_posts_url($author_id);
              if ($avatar = $author_meta["avatar_id"]) {
                $item["author_avatar_thumbnail"] = wp_get_attachment_image($avatar, 'thumbnail');
                $item["author_avatar_medium"] = wp_get_attachment_image($avatar, 'medium');
                $item["author_avatar_large"] = wp_get_attachment_image($avatar, 'large');
                $item["author_avatar_full"] = wp_get_attachment_image($avatar, 'full');
              }
            } else {
              $item["author_name"] = $comment->comment_author;
              $item["author_email"] = $comment->comment_author_email;
              $item["author_link"] = $comment->comment_author_url;
            }
            if (!$args["hide_post_info"]) {
              $item["post_title"] = get_the_title($pid);
              $item["post_content"] = get_the_content($pid);
              $item["post_excerpt"] = get_the_excerpt($pid);
              $item["post_date"] = get_the_time('U', $pid);
              $post_type = get_post_type($pid);
              $author_id = $post->post_author;
              $post_meta = get_post_meta($pid);
              foreach ($post_meta as $field => $v) {
                $item["post_" . $field] = $v;
              }
              $sizes = array("thumbnail", "medium", "large", "full", 'square_crop', 'tiny_crop', 'smaller_crop', 'small_crop', 'medium_crop', 'large_crop');
              foreach ($sizes as $size) {
                $item["featured_img_info_" . $size] = wp_get_attachment_image_src(get_post_thumbnail_id($pid), $size);
                $item["featured_img_width_" . $size] = $item["featured_img_info_" . $size][1];
                $item["featured_img_height_" . $size] = $item["featured_img_info_" . $size][1];
                $item["featured_img_src_" . $size] = $item["featured_img_info_" . $size][0];
                $item["featured_img_" . $size] = get_the_post_thumbnail($pid, $size);
              }
              $images2 = get_children('post_type=attachment&post_mime_type=image&output=ARRAY_N&orderby=menu_order&order=ASC&post_parent=' . $pid);
              if ($images2) {
                $item["gallery_thumbnail"] = "<ul>";
                $item["gallery_medium"] = "<ul>";
                $item["gallery_large"] = "<ul>";
                $item["gallery_full"] = "<ul>";
                if ($imid = get_post_thumbnail_id($pid)) {
                  $img = array();
                  foreach ($sizes as $size) {
                    $full_link = wp_get_attachment_image_src($imid, "full");
                    $img["img_info_" . $size] = wp_get_attachment_image_src($imid, $size);
                    $img["img_src_" . $size] = $item["featured_img_info_" . $size][0];
                    $img["img_" . $size] = '<a href="' . $full_link[0] . '">' . wp_get_attachment_image($imid, $size, false) . '</a>';
                    $item["gallery_" . $size] .= '<li><a href="' . $full_link[0] . '">' . wp_get_attachment_image($imid, $size, false) . '</a></li>';
                  }
                  $item["gallery_list"][] = $img;
                }
                foreach ($images2 as $image) {
                  if (get_post_thumbnail_id($pid) != $image->ID) {
                    $img = array();
                    foreach ($sizes as $size) {
                      $full_link = wp_get_attachment_image_src($image->ID, "full");
                      $img["img_info_" . $size] = wp_get_attachment_image_src($image->ID, $size);
                      $img["img_src_" . $size] = $item["featured_img_info_" . $size][0];
                      $img["img_" . $size] = '<a href="' . $full_link[0] . '">' . wp_get_attachment_image($image->ID, $size, false) . '</a>';
                      $item["gallery_" . $size] .= '<li><a href="' . $full_link[0] . '">' . wp_get_attachment_image($image->ID, $size, false) . '</a></li>';
                    }
                    $item["gallery_list"][] = $img;
                  }
                }
                $item["gallery_thumbnail"] .= "</ul>";
                $item["gallery_medium"] .= "</ul>";
                $item["gallery_large"] .= "</ul>";
                $item["gallery_full"] .= "</ul>";
              }
              $taxonomies = get_object_taxonomies($post_type, "objects");
              foreach ($taxonomies as $tax => $v) {
                $terms = get_the_terms($pid, $tax);
                if ($terms) {
                  $terms_links_ul = "<ul>";
                  $terms_string_ul = "<ul>";
                  $terms_list = array();
                  $terms_links = array();
                  foreach ($terms as $term) {
                    $terms_list[] = $term->name;
                    $terms_links[] = '<a href="' . get_term_link($term, $tax) . '">' . $term->name . '</a>';
                    $terms_links_ul .= '<li><a href="' . get_term_link($term, $tax) . '">' . $term->name . '</a></li>';
                    $terms_string_ul .= '<li>' . $term->name . '</li>';
                  }
                  $terms_links_ul .= "</ul>";
                  $terms_string_ul .= "</ul>";
                  $item["tax_string_" . $tax] = implode(", ", $terms_list);
                  $item["tax_list_" . $tax] = $terms_string_ul;
                  $item["tax_links_string_" . $tax] = implode(", ", $terms_links);
                  $item["tax_links_list_" . $tax] = $terms_links_ul;
                  $item["tax_array_" . $tax] = $terms;
                } else {
                  $item["tax_string_" . $tax] = "";
                  $item["tax_list_" . $tax] = "";
                  $item["tax_links_string_" . $tax] = array();
                  $item["tax_links_list_" . $tax] = "";
                  $item["tax_array_" . $tax] = array();
                }
              }
            }
            $items[] = $item;
          }
        }
      }
      $items2 = array();
      for ($g = $page_no; $g < ($page_no + $per_page); $g++) {
        if(isset($items[$g])){
        $item = $items[$g];
        if ($item) {
          if ($args["comment_template"]) {
            ob_start();
            include($args["comment_template"]);
            $output .= ob_get_contents();
            ob_end_clean();
          }
          $items2[] = $item;
        }
      }
      }
      if (!count($items2)) {
        if (isset($args["post_template"])) {
          if ($args["no_results_html"]) {
            $output .= $args["no_results_html"];
          } elseif ($args["no_results_file"]) {
            ob_start();
            include($args["no_results_file"]);
            $output .= ob_get_contents();
            ob_end_clean();
          }
        }
      }
      return array(
        "output" => $output,
        "total_posts" => count($items),
        "page_no" => $page_no,
        "per_page" => $per_page,
        "items" => $items2
      );
    }

    function get_add_field($name, $type = "", $args = "") {
      if ($name == "author" || $name == "author_email" || $name == "author_url") {
        echo '<input type="text" name="comment_core_' . $name . '" id="comment_core_' . $name . '" class="' . $args["classes"] . '" />';
      } elseif ($name == "pid") {
        echo '<input type="hidden" name="comment_core_' . $name . '" id="comment_core_' . $name . '" class="' . $args["classes"] . '" value="' . $args["pid"] . '" />';
      } elseif ($name == "content") {
        echo '<textarea name="comment_core_' . $name . '" id="comment_core_' . $name . '" class="' . $args["classes"] . '"></textarea>';
      } elseif ($name == "image") {
        echo '<div class="' . $args["classes"] . '">' . $args["label"] . ' <input type="file" name="core_featured_image" id="core_featured_image" /></div>';
      } elseif (substr($name, 0, 5) == "meta_") {
        $field = substr($name, 5);
        $info = get_option('TDF_custom_comment_' . $field);
        $info = json_decode($info);
        if (!$type) {
          $type = $info->type;
        }
        $vals = $info->values;
        if ($type == "textarea") {
          echo '<textarea name="comment_meta_' . $field . '" id="comment_meta_' . $field . '" class="' . $args["classes"] . '">' . $value . '</textarea>';
        } elseif ($type == "checkbox") {
          echo '<ul id="meta_' . $field . '">';
          foreach ($vals as $f => $v) {
            echo '<li><span><input type="checkbox" name="comment_meta_' . $field . '[]" value="' . $f . '" class="' . $args["classes"] . '" /></span> ' . $v . "</li>";
          }
          echo "</ul>";
        } elseif ($type == "radio") {
          echo '<ul id="tax_' . $field . '">';
          foreach ($vals as $f => $v) {
            echo '<li><span><input type="radio" name="comment_meta_' . $field . '" value="' . $f . '" class="' . $args["classes"] . '" /></span> ' . $v . "</li>";
          }
          echo "</ul>";
        } elseif ($type == "select") {
          echo '<select name="comment_meta_' . $field . '" id="comment_meta_' . $field . '" class="' . $args["classes"] . '">';
          echo '<option value="0"> -- select -- </option>';
          foreach ($vals as $f => $v) {
            echo '<option value="' . $f . '">' . $v . "</option>";
          }
          echo "</select>";
        } else {
          echo '<input type="text" name="comment_meta_' . $field . '" id="comment_meta_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        }
      }
    }

    function get_update_field($name, $cid, $type = "", $args = "") {
      $comment = get_comment($cid);
      if ($name == "author") {
        echo '<input type="text" name="comment_core_' . $name . '" id="comment_core_' . $name . '" value="' . $comment->comment_author . '" class="' . $args["classes"] . '" />';
      } elseif ($name == "author_email") {
        echo '<input type="text" name="comment_core_' . $name . '" id="comment_core_' . $name . '" value="' . $comment->comment_author_email . '" class="' . $args["classes"] . '" />';
      } elseif ($name == "author_url") {
        echo '<input type="text" name="comment_core_' . $name . '" id="comment_core_' . $name . '" value="' . $comment->comment_author_url . '" class="' . $args["classes"] . '" />';
      } elseif ($name == "content") {
        echo '<textarea name="comment_core_' . $name . '" id="comment_core_' . $name . '" class="' . $args["classes"] . '">' . $comment->comment_content . '</textarea>';
      } elseif ($name == "cid") {
        echo '<input type="hidden" name="comment_core_' . $name . '" id="comment_core_' . $name . '" class="' . $args["classes"] . '" value="' . $args["cid"] . '" />';
      } elseif ($name == "image") {
        echo '<div class="' . $args["classes"] . '">' . $args["label"] . ' <input type="file" name="core_featured_image" id="core_featured_image" /></div>';
      } else {
        $field = $name;
        $info = get_option('TDF_custom_comment_' . $field);
        $info = json_decode($info);
        if (!$type) {
          $type = $info->type;
        }
        $vals = $info->values;
        $value = get_comment_meta($cid, $field, true);
        if (count($value1 = explode("|", $value)) > 1) {
          $value = explode("|", $value);
        }
        if ($type == "textarea") {
          echo '<textarea name="comment_meta_' . $field . '" id="comment_meta_' . $field . '" class="' . $args["classes"] . '">' . $value . '</textarea>';
        } elseif ($type == "checkbox") {
          echo '<ul id="meta_' . $field . '">';
          foreach ($vals as $f => $v) {
            if (is_array($value)) {
              if (in_array($v, $value)) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            } else {
              if ($v == $value) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            }
            echo '<li><span><input type="checkbox" name="comment_meta_' . $field . '[]" value="' . $f . '" class="' . $args["classes"] . '" ' . $selected . ' /></span> ' . $v . "</li>";
          }
          echo "</ul>";
        } elseif ($type == "radio") {
          echo '<ul id="tax_' . $field . '">';
          foreach ($vals as $f => $v) {
            if (is_array($value)) {
              if (in_array($v, $value)) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            } else {
              if ($v == $value) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            }
            echo '<li><span><input type="radio" name="comment_meta_' . $field . '" value="' . $f . '" class="' . $args["classes"] . '" ' . $selected . ' /></span> ' . $v . "</li>";
          }
          echo "</ul>";
        } elseif ($type == "select") {
          echo '<select name="comment_meta_' . $field . '" id="comment_meta_' . $field . '" class="' . $args["classes"] . '">';
          echo '<option value="0"> -- select -- </option>';
          foreach ($vals as $f => $v) {
            if (is_array($value)) {
              if (in_array($v, $value)) {
                $selected = 'selected="selected"';
              } else {
                $selected = "";
              }
            } else {
              if ($v == $value) {
                $selected = 'selected="selected"';
              } else {
                $selected = "";
              }
            }
            echo '<option value="' . $f . '" ' . $selected . '>' . $v . "</option>";
          }
          echo "</select>";
        } else {
          echo '<input type="text" name="comment_meta_' . $field . '" id="comment_meta_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        }
      }
    }

    function register_field($name, $label, $type, $values) {
      if (!$type) {
        $type = "text";
      }
      if (!is_array($values)) {
        $values = explode("|", $values);
      }
      foreach ($values as $f => $v) {
        if (is_int($f)) {
          $vals[$f + 1] = $v;
        } else {
          $vals[$f] = $v;
        }
      }
      $options = array(
        "label" => $label,
        "type" => $type,
        "values" => $vals
      );
      update_option('TDF_custom_comment_' . $name, json_encode($options));
    }
  }
}
