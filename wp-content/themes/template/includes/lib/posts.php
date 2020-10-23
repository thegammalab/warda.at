<?php
if ( ! class_exists('TDF_Posts_Model') ){

  class TDF_Posts_Model {

    public function __construct() {
    }

    /* GET */

    // function filter_query( $query ) {
    //     $query->query_vars['orderby'] = 'meta_value_num';
    //     $query->query_vars['order'] = 'DESC';
    //     return $query;
    // }

    function get($args = array()){
      /*
      array(
      post_type: array("product","post"),
      page: 1,
      per_page: 10,
      hide_author_info: 1,
      post_template: array(
      product: product_item.php
      post: post_item.php
      )
      no_results_html: '<p>No results</p>'
      no_results_file: 'no_results.php'
      search: array(
      pid: 15
      pid: array(15,16,17)
      tax:
      tax_slug_category: array(aaa,bbb,ccc)
      tax_category: array(1,2,3)
      meta_price: 15
      meta_price: array(1,2,3)
      meta_price: array(
      compare: >
      value: 5
      )
      key: "aaa"
      date: mm/dd/yyyy
      date_start: mm/dd/yyyy
      date_end: mm/dd/yyyy
      author_name: user_nicename
      author: array(1,2,3)
      author_not: array(1,2,3)
      author_role: array(subscriber,author)
      )
      order: title_asc/title_desc/date_asc/date_desc/comments_asc/comments_desc/rand/meta_asc_FIELD/meta_desc_FIELD/custom_asc_FUNCTION/custom_desc_FUNCTION
      )
      */
      $output = "";
      $query_args = $this->generate_query_args($args);

      $items = array();
      // add_action( 'pre_get_posts', array($this,'filter_query'));
      $my_query = new wp_query($query_args);
      // remove_action( 'pre_get_posts', array($this,'filter_query'));
      if (count($my_query->posts)) {
        foreach($my_query->posts as $pid){
          if(intval($pid)>0){
            if($args["return_ids"]){
              $items[] = $pid;

            }else{
              $item = $this->get_the_post($pid);
              $items[] = $item;

              ob_start();
              include($args["post_template"]);
              $output .= ob_get_contents();
              ob_end_clean();
            }
            
            // if(isset($args["post_template"])){
            //   if (is_array($args["post_template"])) {
            //     ob_start();
            //     include($args["post_template"][$post_type]);
            //     $output .= ob_get_contents();
            //     ob_end_clean();
            //   } elseif ($args["post_template"]) {
            //
            //   }
            // }
          }
        }
      } else {
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
      wp_reset_query();

      return array(
        "output" => $output,
        "args" => $args,
        "total_posts" => $my_query->found_posts,
        "query" => $my_query,
        "page_no" => $query_args["paged"]-1,
        "per_page" => $query_args["posts_per_page"],
        "items" => $items
      );
    }

    function get_the_post($pid, $args = array()){
      $item = array();
      if($pid){
        $pid = intval($pid);
        $post = get_post($pid);
        $post_type = get_post_type($pid);
        $item["post_id"] = $pid;
        $item["post_title"] = $post->post_title;
        $item["post_permalink"] = get_permalink($pid);
        $item["post_content"] = apply_filters("the_content", $post->post_content);
        $item["post_excerpt"] = get_the_excerpt($pid);
        $item["post_type"] = $post_type;
        $item["post_date"] = get_the_time('U',$pid);
        $item["author_id"] = $post->post_author;
        $item["price"] = get_post_meta($pid, "_price", true);
        $item["stock"] = get_post_meta($pid, "_stock", true);
        $item["sku"] = get_post_meta($pid, "_sku", true);

        foreach (get_post_meta($pid) as $f => $v) {
          if(get_option('custom_post_' . $f)){
            $options = json_decode(get_option('custom_post_' . $f));
            $vls1 = $options->values;
            $vls = array();
            foreach ($vls1 as $fv => $vv) {
              $vls[$fv] = $vv;
            }
          }
          if (count($v) > 1) {
            $vl_list = array();
            foreach ($v as $vs) {
              if (isset($vls[$vs])) {
                $vl_list[] = $vls[$vs];
              } else {
                $vl_list[] = $vs;
              }
            }
            $item["meta_" . $f] = implode(", ", $vl_list);
            $item["meta_array_" . $f] = $vl_list;
          } else {
            if (isset($vls[($v[0])])){
              $item["meta_" . $f] = $vls[($v[0])];
            } else {
              $item["meta_" . $f] = $v[0];
            }
          }
        }
        // if ($post_type == "product") {
        //   $item["buy_now_button"] = '<a href="' . get_bloginfo("siteurl") . '?add-to-cart=' . $pid . '" rel="nofollow" data-product_id="' . $pid . '" data-product_sku="' . $item["sku"] . '" class="add_to_cart_button button product_type_simple">Add to cart</a>';
        //   $item["buy_now_link"] = '<a href="' . get_bloginfo("siteurl") . '?add-to-cart=' . $pid . '">Add to cart</a>';
        // }
        $author_id = $post->post_author;
        if (!isset($args["hide_author_info"])){
          $info = array("user_login", "user_pass", "user_nicename", "user_email", "user_url", "user_registered", "user_activation_key", "user_status", "display_name", "nickname");
          foreach ($info as $field) {
            $item["author_" . $field] = get_the_author_meta($field);
          }
          $author_meta = get_user_meta($author_id);
          foreach ($author_meta as $f => $v) {
            if (count($v) > 1) {
              $item["author_" . $f] = $v;
            } else {
              $item["author_" . $f] = $v[0];
            }
          }
          
          $user = new TDF_Users_Model;
          $item["author_display_name"] = $user->get_display_name($author_id);

          if(isset($author_meta["avatar_id"])){
            if ($avatar = $author_meta["avatar_id"]) {
              $item["author_avatar_thumbnail"] = wp_get_attachment_image($avatar, 'thumbnail');
              $item["author_avatar_medium"] = wp_get_attachment_image($avatar, 'medium');
              $item["author_avatar_large"] = wp_get_attachment_image($avatar, 'large');
              $item["author_avatar_full"] = wp_get_attachment_image($avatar, 'full');
            }
          }
          $item["author_name"] = $item["author_user_nicename"];
          $item["author_link"] = get_author_posts_url($author_id);
        }
        // $post_meta = get_post_meta($pid);
        // foreach ($post_meta as $field => $v) {
        //   $item["post_" . $field] = $v;
        // }
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
          foreach ($sizes as $size) {
            $item["gallery_".$size] = "<ul>";
          }

          if ($imid = get_post_thumbnail_id($pid)) {
            $img = array();
            foreach ($sizes as $size) {
              $full_link = wp_get_attachment_image_src($imid, "full");
              $img["img_info_" . $size] = wp_get_attachment_image_src($imid, $size);
              $img["img_src_" . $size] = $item["featured_img_info_" . $size][0];
              $img["img_" . $size] = '<a href="' . $full_link[0] . '">' . wp_get_attachment_image($imid, $size, false) . '</a>';
              $item["gallery_" . $size] .= '<li><a rel="gallery_' . $pid . '" href="' . $full_link[0] . '">' . wp_get_attachment_image($imid, $size, false) . '</a></li>';
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
                $item["gallery_" . $size] .= '<li><a rel="gallery_' . $pid . '" href="' . $full_link[0] . '">' . wp_get_attachment_image($image->ID, $size, false) . '</a></li>';
              }
              $item["gallery_list"][] = $img;
            }
          }

          foreach ($sizes as $size) {
            $item["gallery_".$size] = "</ul>";
          }

        }
        $taxonomies = get_object_taxonomies($post_type, "objects");
        foreach ($taxonomies as $tax => $v) {
          $terms = get_the_terms($pid, $tax);
          if (count($terms)) {
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
            $item["tax_string_" . $tax] = implode(" ", $terms_list);
            $item["tax_list_" . $tax] = $terms_string_ul;
            $item["tax_links_string_" . $tax] = implode(" ", $terms_links);
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

        $comments = new TDF_Comments_Model;
        if (isset($args["comment_template"])){
          $item["comments"] = $comments->get(array("comment_template" => $args["comment_template"], "hide_post_info" => 1, "search" => array("post_id" => array($pid))));
        } else {
          $item["comments"] = $comments->get(array("hide_post_info" => 1, "search" => array("post_id" => array($pid))));
        }
        if(count($item["comments"]["items"])){
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
          if($ratingno){
            $item["rating_score"] = round($ratingsum / $ratingno, 2);
            $item["rating_stars"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round(round($ratingsum / $ratingno, 2) * 20) . '%">&nbsp;</div></div>';
            $item["rating_score1"] = round($ratingsum1 / $ratingno, 2);
            $item["rating_stars1"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round(round($ratingsum1 / $ratingno, 2) * 20) . '%">&nbsp;</div></div>';
            $item["rating_score2"] = round($ratingsum2 / $ratingno, 2);
            $item["rating_stars2"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round(round($ratingsum2 / $ratingno, 2) * 20) . '%">&nbsp;</div></div>';
            $item["rating_score3"] = round($ratingsum3 / $ratingno, 2);
            $item["rating_stars3"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round(round($ratingsum3 / $ratingno, 2) * 20) . '%">&nbsp;</div></div>';
            $item["rating_score4"] = round($ratingsum4 / $ratingno, 2);
            $item["rating_stars4"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round(round($ratingsum4 / $ratingno, 2) * 20) . '%">&nbsp;</div></div>';
            $item["rating_score5"] = round($ratingsum5 / $ratingno, 2);
            $item["rating_stars5"] = '<div class="outer_stars"><div class="inner_stars" style="width:' . round(round($ratingsum5 / $ratingno, 2) * 20) . '%">&nbsp;</div></div>';
            $item["rating_count"] = $ratingno;
          }
        }


        $item["date"] = get_the_time('U');

        return $item;
      }
    }

    function generate_search_from_get(){
      $seach_term = get_query_var('term');
      $seach_tax_name = get_query_var('taxonomy');

      $search = array();
      if($seach_term){
        $search["tax_slug_" . $seach_tax_name] = $seach_term;
      }
      foreach($_GET as $f=>$v){
        if(substr($f,0,4)=="tax_"){
          if(!is_array($v)){
            $v = array($v);
          }
          $search["tax_slug_" . substr($f,4)] = $v;
        }
      }
      return $search;
    }

    function generate_query_args($args){
      if (!$args["page"]) {
        $args["page"] = 1;
      }
      if (!$args["per_page"]) {
        $args["per_page"] = 10;
        $per_page = 10;
      } else {
        $per_page = $args["per_page"];
      }
      $query_args = array(
        'post_type' => $args["post_type"],
        'posts_per_page' => $per_page,
        'paged' =>  $args["page"]
      );
      if(isset($args["order"])){
        if ($args["order"] == "title_asc") {
          $query_args['orderby'] = 'title';
          $query_args['order'] = 'ASC';
        } elseif ($args["order"] == "title_desc") {
          $query_args['orderby'] = 'title';
          $query_args['order'] = 'DESC';
        } elseif ($args["order"] == "date_asc") {
          $query_args['orderby'] = 'date';
          $query_args['order'] = 'ASC';
        } elseif ($args["order"] == "date_desc") {
          $query_args['orderby'] = 'date';
          $query_args['order'] = 'DESC';
        } elseif ($args["order"] == "rand") {
          $query_args['orderby'] = 'rand';
        } elseif ($args["order"] == "comments_asc") {
          $query_args['orderby'] = 'comment_count';
          $query_args['order'] = 'ASC';
        } elseif ($args["order"] == "comments_desc") {
          $query_args['orderby'] = 'comment_count';
          $query_args['order'] = 'DESC';
        } elseif (substr($args["order"], 0, 9) == "meta_asc_") {
          $query_args['orderby'] = 'meta_value_num';
          $query_args['meta_key'] = substr($args["order"], 9);
          $query_args['order'] = 'ASC';
        } elseif (substr($args["order"], 0, 10) == "meta_desc_") {
          $query_args['orderby'] = 'meta_value_num';
          $query_args['meta_key'] = substr($args["order"], 10);
          $query_args['order'] = 'DESC';
        } elseif (substr($args["order"], 0, 11) == "custom_asc_") {

        } elseif (substr($args["order"], 0, 12) == "custom_desc_") {

        }
      }
      //print_r($args["search"]);
      if(isset($args["search"])){
        foreach ($args["search"] as $f => $v) {
          if ($v || (is_array($v) && count($v))) {
            if (substr($f, 0, 9) == "tax_slug_") {
              $tax = substr($f, 9);
              if (!is_array($v)) {
                $v = array($v);
              }
              $query_args["tax_query"][] = array(
                'taxonomy' => $tax,
                'field' => 'slug',
                'terms' => $v,
                'operator' => "IN"
              );
            } elseif (substr($f, 0, 4) == "tax_") {
              $tax = substr($f, 4);
              if (!is_array($v)) {
                $v = array($v);
              }
              $query_args["tax_query"][] = array(
                'taxonomy' => $tax,
                'field' => 'id',
                'terms' => $v,
                'compare' => "IN"
              );
            } elseif (substr($f, 0, 5) == "meta_") {
              if ($v || count($v)) {
                $field = substr($f, 5);
                if (!is_array($v)) {
                  if (strpos($field, "_less")) {
                    if ($v) {
                      $field = str_replace("_less", "", $field);
                      $query_args["meta_query"][] = array(
                        'key' => $field,
                        'value' => $v,
                        'type' => 'numeric',
                        'compare' => "<="
                      );
                    }
                  } elseif (strpos($field, "_more")) {
                    if ($v) {
                      $field = str_replace("_more", "", $field);
                      $query_args["meta_query"][] = array(
                        'key' => $field,
                        'value' => $v,
                        'type' => 'numeric',
                        'compare' => ">="
                      );
                    }
                  } else {
                    if ($v) {
                      $query_args["meta_query"][] = array(
                        'key' => $field,
                        'value' => $v,
                      );
                    }
                  }
                } else {
                  if (isset($v["compare"])) {
                    if ($v["value"]) {
                      $query_args["meta_query"][] = array(
                        'key' => $field,
                        'value' => $v["value"],
                        'compare' => $v["compare"]
                      );
                    }
                  } else {
                    if ($v) {
                      $query_args["meta_query"][] = array(
                        'key' => $field,
                        'value' => $v,
                        'compare' => "IN"
                      );
                    }
                  }
                }
              }
            } elseif ($f == "key") {
              $query_args["s"] = $v;
            } elseif ($f == "date") {
              $query_args["date_query"][] = array(
                'year' => intval(substr($v, 6, 4)),
                'month' => intval(substr($v, 0, 2)),
                'day' => intval(substr($v, 3, 2)),
              );
            } elseif ($f == "date_start") {
              $query_args["date_query"] = array(
                'after' => $v,
                'inclusive' => true,
              );
            } elseif ($f == "date_end") {
              $query_args["date_query"] = array(
                'before' => $v,
                'inclusive' => true,
              );
            } elseif ($f == "author_name") {
              $query_args["author_name"] = $v;
            } elseif ($f == "exclude") {
              $query_args["post__not_in"] = $v;
            } elseif ($f == "pid") {
              if (is_array($v)) {
                $query_args["post__in"] = $v;
              } else {
                $query_args["p"] = $v;
              }
              $query_args["post_status"] = "any";
            } elseif ($f == "post_status") {
              $query_args["post_status"] = $v;
            } elseif ($f == "author") {
              $query_args["author__in"] = $v;
            } elseif ($f == "author_not") {
              $query_args["author__not_in"] = $v;
            } elseif ($f == "post_status") {
              $query_args["post_status"] = $v;
            } elseif ($f == "author_role") {
              $user_list = array();
              foreach ($v as $role) {
                $user_query = new WP_User_Query(array('role' => $role));
                $authors = $author_query->get_results();
                foreach ($authors as $author) {
                  $user_list[] = $author->ID;
                }
              }
              $query_args["author__in"] = $user_list;
            } else {

            }
          }
        }
      }
      if(!isset($query_args["post_status"] )){
        $query_args["post_status"] = "publish";
      }
      $query_args['fields'] = 'ids';

      return $query_args;
    }


    /* FIELDS */


    function get_add_field($name, $type = "", $args = array()) {
      ob_start();

      if(isset($args["value"])){
        $value=$args["value"];
      }elseif(isset($_GET["post_meta_".$name])){
        $value=$_GET["post_meta_".$name];
      }elseif(isset($_GET["post_tax_".$name])){
        $value=$_GET["post_tax_".$name];
      }elseif(isset($_GET["post_core_".$name])){
        $value=$_GET["post_core_".$name];
      }else{
        $value="";
      }

      if (substr($name, 0, 4) == "tax_") {
        if (!$type) {
          $type = "select";
        }
        $field = substr($name, 4);
        if ($type == "checkbox") {
          echo '<ul id="tax_' . $field . '">';
          $terms = get_terms($field, 'hide_empty=0');
          foreach ($terms as $term) {
            echo '<li><label><input type="checkbox" class="' . $args["classes"] . '" name="post_tax_' . $field . '[]" value="' . $term->term_id . '" />' . $term->name . "</span></li>";
          }
          echo "</ul>";
        } elseif ($type == "radio") {
          echo '<ul id="tax_' . $field . '">';
          $terms = get_terms($field, 'hide_empty=0');
          foreach ($terms as $term) {
            echo '<li><span><input type="radio" class="' . $args["classes"] . '" name="post_tax_' . $field . '" value="' . $term->term_id . '"/></span> ' . $term->name . "</li>";
          }
          echo "</ul>";
        } elseif ($type == "select") {
          echo '<select name="post_tax_' . $field . '" id="post_tax_' . $field . '" class="' . $args["classes"] . '">';
          echo '<option value="0"> -- select -- </option>';
          $terms = get_terms($field, 'hide_empty=0&parent=0');
          foreach ($terms as $term) {
            $terms1 = get_terms($field, 'hide_empty=0&parent=' . $term->term_id);
            if (count($terms1)) {
              echo ' <optgroup label="' . $term->name . '" data-parent="' . $term->slug . '">';
            } else {
              echo '<option value="' . $term->term_id . '" >' . $term->name . "</option>";
            }
            foreach ($terms1 as $term1) {
              $terms1 = get_terms($field, 'hide_empty=0&parent=' . $term->term_id);
              echo '<option value="' . $term1->term_id . '">' . $term1->name . "</option>";
            }
            if (count($terms1)) {
              echo ' </optgroup>';
            }
          }
          echo "</select>";
        } elseif ($type == "tags") {
          echo '<input type="text" name="post_tax_' . $field . '" id="post_tax_tags_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        }
      }
      if (substr($name, 0, 5) == "meta_") {
        $field = substr($name, 5);
        $info = get_option('custom_post_' . $field);
        if($info){
          $info = json_decode($info);
          if (!$type) {
            $type = $info->type;
          }
          $vals = $info->values;
        }
        if ($type == "textarea") {
          echo '<textarea name="post_meta_' . $field . '" id="post_meta_' . $field . '" class="' . $args["classes"] . '"></textarea>';
        } elseif ($type == "checkbox") {
          echo '<ul id="meta_' . $field . '" class="' . $args["classes"] . '">';
          foreach ($vals as $f => $v) {
            echo '<li><span><input type="checkbox" name="post_meta_' . $field . '[]" value="' . $f . '" /></span>' . $v . "</li>";
          }
          echo "</ul>";
        } elseif ($type == "radio") {
          echo '<ul id="tax_' . $field . '" class="' . $args["classes"] . '" >';
          foreach ($vals as $f => $v) {
            echo '<li><span><input type="radio" name="post_meta_' . $field . '" value="' . $f . '" /></span> ' . $v . "</li>";
          }
          echo "</ul>";
        } elseif ($type == "select") {
          echo '<select name="post_meta_' . $field . '" id="post_meta_' . $field . '" class="' . $args["classes"] . '">';
          echo '<option value="0"> -- select -- </option>';
          foreach ($vals as $f => $v) {
            echo '<option value="' . $f . '">' . $v . "</option>";
          }
          echo "</select>";
        } elseif ($type == "file") {
          echo '<input type="file" name="post_file_' . $field . '" id="post_file_' . $field . '" class="' . $args["classes"] . '" />';
        } else {
          echo '<input type="text" name="post_meta_' . $field . '" id="post_meta_' . $field . '" class="' . $args["classes"] . '" />';
        }
      }
      if ($name == "title") {
        echo '<input type="text" name="post_core_' . $name . '" id="post_core_' . $name . '" class="' . $args["classes"] . '" />';
      }
      if ($name == "excerpt") {
        echo '<textarea name="post_core_' . $name . '" id="post_core_' . $name . '" class="' . $args["classes"] . '"></textarea>';
      }
      if ($name == "content") {
        echo '<textarea name="post_core_content" id="post_core_content" class="' . $args["classes"] . '"></textarea>';
      }
      if ($name == "featured_image") {
        echo '<div class="' . $args["classes"] . '">' . $args["label"] . ' <input type="file" name="post_core_image" id="post_core_image" /></div>';
      }
      if ($name == "image_gallery") {
        $sel = '[data_file="+file.name+"]';
        include(get_stylesheet_directory().'/views/media/scripts.php'); ?>
        <div id="attachment_inputs_post" class="hidden"><ul class="row"></ul></div>
        <div class="dropzone-previews dropzone" id="img_gallery_box" style="clear:both; overflow:auto; margin-bottom:20px;"></div>
        <?php
      }

      $cont = ob_get_contents();
      ob_end_clean();

      return $cont;
    }

    function get_update_field($name, $pid, $type = "", $args = array()) {
      ob_start();

      if (substr($name, 0, 4) == "tax_") {
        if (!$type) {
          $type = "select";
        }
        $field = substr($name, 4);
        $value = array();
        $value_val = array();
        $terms = get_the_terms($pid, $field);
        if(is_array($terms)){
          foreach ($terms as $term) {
            $value[] = $term->term_id;
            $value_val[] = $term->name;
          }
        }
        if ($type == "checkbox") {
          echo '<ul id="tax_' . $field . '">';
          $terms = get_terms($field, 'hide_empty=0');
          foreach ($terms as $term) {
            if (is_array($value)) {
              if (in_array($term->term_id, $value)) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            } else {
              if ($term->term_id == $value) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            }
            echo '<li><label><input type="checkbox" name="post_tax_' . $field . '[]" value="' . $term->term_id . '" ' . $selected . ' class="' . $args["classes"] . '" /> ' . $term->name . "</span></li>";
          }
          echo "</ul>";
        } elseif ($type == "radio") {
          echo '<ul id="tax_' . $field . '" >';
          $terms = get_terms($field, 'hide_empty=0');
          foreach ($terms as $term) {
            if (is_array($value)) {
              if (in_array($term->term_id, $value)) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            } else {
              if ($term->term_id == $value) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            }
            echo '<li><label><input type="radio" name="post_tax_' . $field . '" value="' . $term->term_id . '" ' . $selected . ' class="' . $args["classes"] . '" /> ' . $term->name . "</label></li>";
          }
          echo "</ul>";
        } elseif ($type == "select") {
          echo '<select name="post_tax_' . $field . '" id="post_tax_' . $field . '" class="' . $args["classes"] . '">';
          echo '<option value="0"> -- select -- </option>';
          $terms = get_terms($field, 'hide_empty=0&parent=0');
          foreach ($terms as $term) {
            if (is_array($value)) {
              if (in_array($term->term_id, $value)) {
                $selected = 'selected="selected"';
              } else {
                $selected = "";
              }
            } else {
              if ($term->term_id == $value) {
                $selected = 'selected="selected"';
              } else {
                $selected = "";
              }
            }
            $terms1 = get_terms($field, 'hide_empty=0&parent=' . $term->term_id);
            if (count($terms1)) {
              echo ' <optgroup label="' . $term->name . '" data-parent="' . $term->slug . '">';
            } else {
              echo '<option value="' . $term->term_id . '" ' . $selected . '>' . $term->name . "</option>";
            }
            foreach ($terms1 as $term1) {
              if (is_array($value)) {
                if (in_array($term1->term_id, $value)) {
                  $selected1 = 'selected="selected"';
                } else {
                  $selected1 = "";
                }
              } else {
                if ($term1->term_id == $value) {
                  $selected1 = 'selected="selected"';
                } else {
                  $selected1 = "";
                }
              }
              echo '<option value="' . $term1->term_id . '" ' . $selected1 . '>' . $term1->name . "</option>";
            }
            if (count($terms1)) {
              echo ' </optgroup>';
            }
          }
          echo "</select>";
        } elseif ($type == "tags") {
          echo '<input type="text" name="post_tax_' . $field . '" id="post_tax_' . $field . '" value="' . implode(", ", $value_val) . '" class="' . $args["classes"] . '" />';
        }
      }
      if (substr($name, 0, 5) == "meta_") {
        $field = substr($name, 5);
        $info = get_option('custom_post_' . $field);
        $info = json_decode($info);
        if (!$type) {
          if(isset($info->type)){
            $type = $info->type;
          }
        }
        if(isset($info->values)){
          $vals = $info->values;
        }

        $value = get_post_meta($pid, $field);
        if (count($value) > 1) {
          $value = $value;
        } else {
          if(count($value)==1){
            $value = $value[0];
          }else{
            $value="";
          }
        }
        if (count(explode("|", $value)) > 1) {
          $value = explode("|", $value);
        }
        if ($type == "textarea") {
          echo '<textarea name="post_meta_' . $field . '" id="post_meta_' . $field . '" class="' . $args["classes"] . '">' . $value . '</textarea>';
        } elseif ($type == "checkbox") {
          echo '<ul id="meta_' . $field . '" class="' . $args["classes"] . '" >';
          foreach ($vals as $f => $v) {
            if (is_array($value)) {
              if (in_array($f, $value)) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            } else {
              if ($f == $value) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            }
            echo '<li><label><input type="checkbox" name="post_meta_' . $field . '[]" value="' . $f . '" ' . $selected . ' class="' . $args["classes"] . '"/> ' . $v . "</label></li>";
          }
          echo "</ul>";
        } elseif ($type == "radio") {
          echo '<ul id="post_meta_' . $field . '" class="' . $args["classes"] . '" >';
          foreach ($vals as $f => $v) {
            if (is_array($value)) {
              if (in_array($f, $value)) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            } else {
              if ($f == $value) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            }
            echo '<li><label><input type="radio" name="post_meta_' . $field . '" value="' . $f . '" ' . $selected . ' class="' . $args["classes"] . '"/> ' . $v . "</label></li>";
          }
          echo "</ul>";
        } elseif ($type == "select") {
          echo '<select name="post_meta_' . $field . '" id="post_meta_' . $field . '" class="' . $args["classes"] . '">';
          echo '<option value="0"> -- select -- </option>';
          foreach ($vals as $f => $v) {
            if (is_array($value)) {
              if (in_array($f, $value)) {
                $selected = 'selected="selected"';
              } else {
                $selected = "";
              }
            } else {
              if ($f == $value) {
                $selected = 'selected="selected"';
              } else {
                $selected = "";
              }
            }
            echo '<option value="' . $f . '" ' . $selected . '>' . $v . "</option>";
          }
          echo "</select>";
        } elseif ($type == "file") {
          echo '<input type="file" name="post_file_' . $field . '" id="post_file_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
          echo "<div>Current file: ";
          echo '<a href="' . wp_get_attachment_url($value) . '" target="_blank">' . get_the_title($value) . "</a>";
          echo "</div>";
        } else {
          echo '<input type="text" name="post_meta_' . $field . '" id="post_meta_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        }
      }
      if ($name == "title") {
        echo '<input type="text" name="post_core_' . $name . '" id="post_core_' . $name . '" value="' . get_the_title($pid) . '" class="' . $args["classes"] . '" />';
      }
      if ($name == "excerpt") {
        echo '<textarea name="post_core_' . $name . '" id="post_core_' . $name . '" class="' . $args["classes"] . '">' . get_the_excerpt($pid) . '</textarea>';
      }
      if ($name == "price") {
        $value = get_post_meta($pid, "_price", true);
        echo '<input type="text" name="post_meta_' . $name . '" id="post_meta_' . $name . '" value="' . $value . '" class="' . $args["classes"] . '" />';
      }
      if ($name == "stock") {
        $value = get_post_meta($pid, "_stock", true);
        echo '<input type="text" name="post_meta_' . $name . '" id="post_meta_' . $name . '" value="' . $value . '" class="' . $args["classes"] . '" />';
      }
      if ($name == "content") {
        $page_data = get_page($pid);  //gets all page data
        $content = apply_filters('the_content', $page_data->post_content);
        echo '<textarea name="post_core_content" id="post_core_content" class="' . $args["classes"] . '">' . $content . '</textarea>';
      }
      if ($name == "featured_image") {
        $av = get_post_thumbnail_id($pid);
        echo '<div style="float:left; width:10%; margin-left:0;" class="img_max_width">';
        if (wp_get_attachment_image($av)) {
          echo wp_get_attachment_image($av);
        } else {
          echo "<img alt='image_alt' style='width:100%; height:auto;' src='" . get_bloginfo("template_url") . "/assets/images/defaults/no_user.png' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
        }
        echo '</div><div style="float:right; width:85%;">';
        echo '<label style="text-align:left; width:100%; padding-top:0;">Update the image:</label><br><input type="file" name="post_core_image" id="post_core_image" />';
        echo '</div>';
      }
      if ($name == "image_gallery") {

        ?>
        <?php include(get_stylesheet_directory().'/views/media/scripts.php'); ?>

        <div id="attachment_inputs_post">
          <ul class="row">
            <?php
            $images2 = get_attached_media('image',$pid);

            if (isset($images2)) {
              foreach ($images2 as $im) {
                $src = wp_get_attachment_image_src($im->ID, "thumbnail");
                $media_info = get_post($im->ID);
                $file = array(
                  "id"=>$im->ID,
                  "file"=>basename($src[0]),
                  "src"=>$src[0],
                  "title"=>$media_info->post_title,
                  "description"=>$media_info->post_content,
                );
                include(get_stylesheet_directory().'/views/media/item.php');

              }
            }
            ?>
          </ul>
        </div>
        <div class="dropzone-previews dropzone" id="img_gallery_box" style="clear:both; overflow:auto; margin-bottom:20px;"></div>
        <?php
      }

      $cont = ob_get_contents();
      ob_end_clean();

      return $cont;
    }



    /* PROCESS SAVE */

    function save($post_type, $cid = 0, $args){
      if ($cid) {
        $fields = array("post_content" => "post_core_content", "post_name" => "post_core_name", "post_title" => "post_core_title", "post_status" => "post_core_status", "post_author" => "post_core_author", "post_date" => "post_core_date");
        $data = array();
        $data["ID"] = $cid;
        foreach ($fields as $f => $v) {
          if ($value = $_POST[$v]) {
            $data[$f] = $value;
          }
        }
        $cid = (wp_update_post($data));
      } else {
        $fields = array("post_content" => "post_core_content", "post_name" => "post_core_name", "post_title" => "post_core_title", "post_status" => "post_core_status", "post_author" => "post_core_author", "post_date" => "post_core_date");
        $data = array();
        foreach ($fields as $f => $v) {
          if ($value = $_POST[$v]) {
            $data[$f] = $value;
          }
        }
        $data["post_type"] = $post_type;
        $cid = wp_insert_post($data);
      }

      if(!is_wp_error($cid)){

        if ($_POST["post_attach"]) {

          $attach_list = $_POST["post_attach"];
          $first_aid = $attach_list[0];
          $attach_list = array_unique($attach_list);
          foreach ($attach_list as $aid) {
            wp_update_post(
              array(
                'ID' => $aid,
                'post_parent' => $cid
              )
            );
          }
        }

        if ($file = basename($_FILES["post_core_image"]["name"])) {
          $uploadedfile = $_FILES['post_core_image'];
          $upload_overrides = array('test_form' => false);
          $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
          if ($movefile) {
            $wp_filetype = $movefile['type'];
            $filename = $movefile['file'];
            $wp_upload_dir = wp_upload_dir();
            $attachment = array(
              'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
              'post_mime_type' => $wp_filetype,
              'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
              'post_content' => '',
              'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment($attachment, $filename, $cid);
            $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
            wp_update_attachment_metadata($attach_id, $attach_data);
            set_post_thumbnail($cid, $attach_id);
          }
        }else{
          if(isset($first_aid)){
            set_post_thumbnail($cid, $first_aid);
          }
        }

        foreach ($_FILES as $f => $v) {
          if (substr($f, 0, 11) == "post_media_") {
            $uploadedfile = $_FILES[$f];
            if ($uploadedfile['name']) {
              $upload_overrides = array('test_form' => false);
              $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
              if ($movefile) {
                $wp_filetype = $movefile['type'];
                $filename = $movefile['file'];
                $wp_upload_dir = wp_upload_dir();
                $attachment = array(
                  'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                  'post_mime_type' => $wp_filetype,
                  'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                  'post_content' => '',
                  'post_status' => 'inherit'
                );
                $attach_id = wp_insert_attachment($attachment, $filename, $cid);
                $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                wp_update_attachment_metadata($attach_id, $attach_data);
              }
            }
          }
          if (substr($f, 0, 10) == "post_file_") {
            $field = substr($f, 10);
            $uploadedfile = $_FILES[$f];
            if ($uploadedfile['name']) {
              $upload_overrides = array('test_form' => false);
              $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
              if ($movefile) {
                $wp_filetype = $movefile['type'];
                $filename = $movefile['file'];
                $wp_upload_dir = wp_upload_dir();
                $attachment = array(
                  'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                  'post_mime_type' => $wp_filetype,
                  'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                  'post_content' => '',
                  'post_status' => 'inherit'
                );
                $attach_id = wp_insert_attachment($attachment, $filename);
                $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                wp_update_attachment_metadata($attach_id, $attach_data);
              }
            }
            update_post_meta($cid, $field, $attach_id);
          }
        }
        foreach ($_POST as $f => $v) {
          if (substr($f, 0, 9) == "post_tax_") {
            $field = substr($f, 9);

            $values = array();
            if (is_array($v)) {
              foreach ($v as $vs) {
                $values[] = intval($vs);
              }
            } else {
              $values[] = $v;
            }
            (wp_set_post_terms($cid, $values, $field, false));
          }
          if (substr($f, 0, 10) == "post_meta_") {
            $field = substr($f, 10);
            if ($field == "price") {
              $field = "_price";
            }
            if ($field == "stock") {
              $field = "_stock";
            }
            if (is_array($v)) {
              delete_post_meta($cid, $field);
              foreach ($v as $val) {
                add_post_meta($cid, $field, $val);
              }
              //update_post_meta($cid, $field, $v);
            } else {
              update_post_meta($cid, $field, $v);
            }
          }
        }

      }
      return $cid;

    }
  }
}
