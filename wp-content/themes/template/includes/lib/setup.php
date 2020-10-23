<?php
if (!class_exists('TDF_Setup')) {

  class TDF_Setup
  {

    public function __construct()
    { }

    /* POST TYPE FUNCTIONS */

    function register_post_types($types)
    {
      foreach ($types as $type) {
        if (is_array($type)) {
          if (isset($type[1])) {
            $args = $type[1];
          } else {
            $args = array();
          }
          $this->register_post_type($type[0], $args);
        } else {
          $this->register_post_type($type);
        }
      }
    }

    function register_post_type($name, $args = array())
    {
      if ($args["name"]) {
        $label_plural = $args["name"];
      } else {
        $label_plural = str_replace("_", " ", ucfirst($name));
      }
      if ($args["singular_name"]) {
        $label_singular = $args["singular_name"];
      } else {
        $label_singular = str_replace("_", " ", ucfirst($name));
      }
      if (isset($args["position"])) {
        $position = $args["position"];
      } else {
        $position = 3;
      }
      if (isset($args["slug"])) {
        $slug = $args["slug"];
      } else {
        $slug = $name;
      }
      register_post_type(
        $name,
        array(
          'labels' => array(
            'name' => __($label_plural),
            'singular_name' => __($label_singular)
          ),
          'public' => true,
          'show_ui' => true,
          'show_in_menu' => true,
          'supports' => array('title', 'editor', "thumbnail", "author", "custom-fields", "comments", "excerpt"),
          'rewrite' => array('slug' => $slug, 'with_front' => true),
          'has_archive' => true,
          'menu_position' => $position
        )
      );
    }

    /* TAXONOMY FUNCTIONS */

    function register_taxonomies($types)
    {
      foreach ($types as $type) {
        if (isset($type[2])) {
          $args = $type[2];
        } else {
          $args = array();
        }
        $this->register_taxonomy($type[0], $type[1], $args);
      }
    }

    function register_taxonomy($name, $post_types, $args = array())
    {
      if (isset($args["name"])) {
        $label_plural = $args["name"];
      } else {
        $label_plural = str_replace("_", " ", ucfirst($name)) . "s";
      }
      if (isset($args["singular_name"])) {
        $label_singular = $args["singular_name"];
      } else {
        $label_singular = str_replace("_", " ", ucfirst($name));
      }
      if (isset($args["slug"])) {
        $slug = $args["slug"];
      } else {
        $slug = $name;
      }
      $labels = array(
        'name' => $label_plural,
        'singular_name' => $label_singular,
        'search_items' => 'Search ' . $label_plural,
        'popular_items' => 'Popular ' . $label_plural,
        'all_items' => 'All ' . $label_plural,
        'parent_item' => 'Parent ' . $label_singular,
        'edit_item' => 'Edit ' . $label_plural,
        'update_item' => 'Update ' . $label_singular,
        'add_new_item' => 'Add New ' . $label_singular,
        'new_item_name' => 'New ' . $label_singular,
        'separate_items_with_commas' => 'Separate ' . $label_plural . ' with commas',
        'add_or_remove_items' => 'Add or remove ' . $label_plural,
        'choose_from_most_used' => 'Choose from most used ' . $label_plural
      );

      if (isset($args["tag_type"])) {
        $hier = false;
      } else {
        $hier = true;
      }

      $tax_args = array(
        'label' => $label_singular,
        'labels' => $labels,
        'public' => true,
        'hierarchical' => $hier,
        'show_ui' => true,
        'show_in_nav_menus' => true,
        'args' => array('orderby' => 'term_order'),
        'rewrite' => array('slug' => $slug, 'with_front' => false),
        'query_var' => true
      );

      foreach ($post_types as $post_type) {
        $tax_post_types = get_option("tax_" . $name);
        if (!isset($tax_post_types)) {
          $new_tax_post_types = array("post_types" => array(), "default" => "");
        }

        $new_tax_post_types = array("post_types" => array(), "default" => "");
        if (!is_array($tax_post_types["post_types"])) {
          if ($tax_post_types["post_types"]) {
            $new_tax_post_types["post_types"] = array($tax_post_types["post_types"]);
          }
        } else {
          $new_tax_post_types["post_types"] = $tax_post_types["post_types"];
        }
        $new_tax_post_types["post_types"][] = $post_type;
        if (!isset($tax_post_types["default"])) {
          $new_tax_post_types["default"] = $post_type;
        } else {
          $new_tax_post_types["default"] = $tax_post_types["default"];
        }
        $new_tax_post_types["post_types"] = array_unique($new_tax_post_types["post_types"]);
        update_option("tax_" . $name, $new_tax_post_types);
      }

      register_taxonomy($name, $post_types, $tax_args);
    }

    function add_taxonomy($tax, $post_type)
    {
      if (is_array($post_type)) {
        foreach ($post_type as $type) {
          register_taxonomy_for_object_type($tax, $type);
        }
      } else {
        register_taxonomy_for_object_type($tax, $post_type);
      }
    }

    /* MENUS FUNCTIONS */

    function register_menus($types)
    {
      foreach ($types as $type) {
        if (is_array($type)) {
          if (isset($type[1])) {
            $args = $type[1];
          } else {
            $args = array();
          }
          $this->add_menu($type[0], $args);
        } else {
          $this->add_menu($type);
        }
      }
    }

    function add_menu($id, $args = "")
    {
      if (isset($args["description"])) {
        $label = $args["description"];
      } else {
        $label = str_replace("_", " ", ucfirst($id));
      }
      register_nav_menu($id, $label);
    }

    /* SIDEBAR FUNCTIONS */

    function register_sidebars($types)
    {
      foreach ($types as $type) {
        if (is_array($type)) {
          if (isset($type[1])) {
            $args = $type[1];
          } else {
            $args = array();
          }
          $this->add_widget_area($type[0], $args);
        } else {
          $this->add_widget_area($type);
        }
      }
    }

    function add_widget_area($id, $args = "")
    {
      if (isset($args["name"])) {
        $label = $args["name"];
      } else {
        $label = str_replace("_", " ", ucfirst($id));
      }
      if (isset($args["description"])) {
        $description = $args["description"];
      } else {
        $description = "Widgets in this area will show in the " . str_replace("_", " ", ucfirst($id));
      }
      register_sidebar(array(
        'name' => $label,
        'id' => $id,
        'description' => $description,
        'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="inner_body">',
        'after_widget' => '</div></div>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>'
      ));
    }

    /* POST FIELD FUNCTIONS */

    function register_post_fields($types)
    {
      foreach ($types as $f => $args) {

        $this->register_post_fields_box($f, $args);
      }
    }

    function register_post_fields_box($name, $args)
    {
      $fields = array();
      $field_children = array();

      foreach ($args["fields"] as $fid => $val) {
        if (isset($val["name"])) {
          $the_name = $val["name"];
        } else {
          $the_name = $fid;
        }

        if (isset($val["type"])) {
          $the_type = $val["type"];
        } else {
          $the_type = "text";
        }
        if (isset($val["values"])) {
          $the_values = $val["values"];
        } else {
          $the_values = array();
        }
        if (isset($val["fields"])) {
          $field_children[$fid] = $val["fields"];
        }
        $field_array = array(
          'key' => $fid,
          'label' => $the_name,
          'name' => $fid,
          'type' => $the_type,
          'choices' => $the_values,
        );
        foreach ($val as $f => $v) {
          if ($f != "fields" && $f != "values" && $f != "type" && $f != "name") {
            $field_array[$f] = $v;
          }
        }
        $fields[] = $field_array;
        $this->register_post_field($fid, $the_name, $the_type, $the_values);
      }

      if (isset($args["position"])) {
        $position = $args["name"];
      } else {
        $position = "normal";
      }

      if (isset($args["post_types"])) {
        foreach ($args["post_types"] as $post_type) {
          if (function_exists("acf_add_local_field_group")) {
            acf_add_local_field_group(array(
              'key' => 'group_' . $name . "_" . $post_type,
              'title' => $args["name"],
              'position' => $position,
              'fields' => $fields,
              'location' => array(
                array(
                  array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => $post_type,
                  ),
                ),
              ),
            ));
          }
        }
      }
      if (isset($args["page"])) {
        if (function_exists("acf_add_local_field_group")) {
          acf_add_local_field_group(array(
            'key' => 'group_' . $name,
            'title' => $args["name"],
            'position' => $position,
            'fields' => $fields,
            'location' => array(
              array(
                array(
                  'param' => 'page',
                  'operator' => '==',
                  'value' => $args["page"],
                ),
              ),
            ),
          ));
        }
      }
      if (isset($args["taxonomies"])) {
        foreach ($args["taxonomies"] as $tax) {
          if (function_exists("acf_add_local_field_group")) {
            acf_add_local_field_group(array(
              'key' => 'group_' . $name . "_" . $tax,
              'title' => $args["name"],
              'position' => $position,
              'fields' => $fields,
              'location' => array(
                array(
                  array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => $tax,
                  ),
                ),
              ),
            ));
          }
        }
      }

      if (count($field_children)) {
        foreach ($field_children as $f => $subfield) {
          foreach ($subfield as $sf => $sv) {
            $child_array = array(
              'key' => $f . "_" . $sf,
              'parent' => $f
            );
            foreach ($sv as $a => $b) {
              $child_array[$a] = $b;
            }
            if (function_exists("acf_add_local_field")) {
              acf_add_local_field($child_array);
            }
          }
        }
      }
    }

    function register_post_field($name, $label, $type = "text", $values = array())
    {

      $options = array(
        "label" => $label,
        "type" => $type,
        "values" => $values
      );
      update_option('custom_post_' . $name, json_encode($options));
    }

    /* POST FIELD FUNCTIONS */

    function register_user_fields($types)
    {
      foreach ($types as $f => $args) {
        $this->register_user_fields_box($f, $args);
      }
    }

    function register_user_fields_box($name, $args)
    {
      $fields = array();
      foreach ($args["fields"] as $fid => $val) {
        if (isset($val["name"])) {
          $the_name = $val["name"];
        } else {
          $the_name = $fid;
        }
        if (isset($val["type"])) {
          $the_type = $val["type"];
        } else {
          $the_type = "text";
        }
        if (isset($val["values"])) {
          $the_values = $val["values"];
        } else {
          $the_values = array();
        }
        $fields[] = array(
          'key' => $fid,
          'label' => $the_name,
          'name' => $fid,
          'type' => $the_type,
          'choices' => $the_values
        );
        $this->register_user_field($fid, $the_name, $the_type, $the_values);
      }

      if (isset($args["user_roles"])) {
        if (is_array($args["user_roles"])) {
          foreach ($args["user_roles"] as $user_role) {
            if (function_exists("acf_add_local_field_group")) {
              acf_add_local_field_group(array(
                'key' => 'group_' . $name,
                'title' => $args["name"],
                'fields' => $fields,
                'location' => array(
                  array(
                    array(
                      'param' => 'user_role',
                      'operator' => '==',
                      'value' => $user_role,
                    ),
                  ),
                ),
              ));
            }
          }
        } else {
          if (function_exists("acf_add_local_field_group")) {
            acf_add_local_field_group(array(
              'key' => 'group_' . $name,
              'title' => $args["name"],
              'fields' => $fields,
              'location' => array(
                array(
                  array(
                    'param' => 'user_role',
                    'operator' => '==',
                    'value' => $args["user_roles"],
                  ),
                ),
              ),
            ));
          }
        }
      } else {
        if (function_exists("acf_add_local_field_group")) {
          acf_add_local_field_group(array(
            'key' => 'group_' . $name,
            'title' => $args["name"],
            'fields' => $fields,
            'location' => array(
              array(
                array(
                  'param' => 'user_role',
                  'operator' => '==',
                  'value' => "all",
                ),
              ),
            ),
          ));
        }
      }
    }

    function register_user_field($name, $label, $type = "text", $values = array())
    {
      $options = array(
        "label" => $label,
        "type" => $type,
        "values" => $values
      );
      update_option('custom_user_' . $name, json_encode($options));
    }

    /* MISC VARIABLES */

    function register_variables($types)
    {
      foreach ($types as $f => $v) {
        $this->register_varaible($f, $v);
      }
    }

    function register_varaible($name, $values)
    {
      update_option("tdf_" . $name, $values);
    }

    /* THEME SETTINGS VARIABLES */

    function register_theme_variables($types)
    {
      foreach ($types as $f => $args) {
        $this->register_theme_variable($f, $args);
      }
    }

    function register_theme_variable($name, $args)
    {
      $fields = array();
      foreach ($args["fields"] as $fid => $val) {
        $field_array = array(
          'key' => $fid,
          'name' => $fid,
        );
        foreach ($val as $f => $v) {
          if ($f == "name") {
            $field_array["label"] = $v;
          } elseif ($f == "values") {
            $field_array["choices"] = $v;
          } else {
            $field_array[$f] = $v;
          }
        }
        $fields[] = $field_array;
      }
      if (function_exists("acf_add_local_field_group")) {
        acf_add_local_field_group(array(
          'key' => 'group_' . $name,
          'title' => $args["name"],
          'fields' => $fields,
          'location' => array(
            array(
              array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-theme-settings',
              ),
            ),
          ),
        ));
      }
    }
  }

  new TDF_Setup;
}
