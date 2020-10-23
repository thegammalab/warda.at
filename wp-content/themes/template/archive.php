<?php
if (isset($_GET["post_type"])) {
  $post_type = $_GET["post_type"];
} else {
  $elem = (get_queried_object());
  $post_type = get_query_var('post_type');
}
if (file_exists(dirname(__FILE__) . "/views/posts/" . $post_type . "/content-list.php")) {
  get_template_part('views/posts/' . $post_type . "/content", "list");
} else {
  get_template_part('views/posts/defaults/content', 'list');
}
