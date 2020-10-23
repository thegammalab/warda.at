<?php
global $wp;
$featured_ids = array();
$page = get_query_var("paged");

if (!$page) {
  $page = 1;
}
if ($key = get_query_var("s")) {
  $the_title = 'Suchergebnisse für "' . $key . '"';

  $feat_results = apply_filters('tdf_get_posts', "post", 1, 0, array("search" => array("key" => $key, "meta__is_ns_featured_post"=>"yes")));
  foreach($feat_results["items"] as $feat_item){
    $featured_ids[]=$feat_item["post_id"];
  }

  $args = array("search" => array("key" => $key, "exclude"=>$featured_ids));
  $results = apply_filters('tdf_get_posts', "post", get_option("posts_per_page"), $page, $args);
}elseif ($author_name = get_query_var("author_name")) {
  $the_title = 'Posts by "' . $author_name . '"';
  $user = get_user_by('slug',$author_name);
  $author_id = $user->ID;

  $feat_results = apply_filters('tdf_get_posts', "post", 1, 0, array("search" => array("author" => $author_id, "meta__is_ns_featured_post"=>"yes")));
  foreach($feat_results["items"] as $feat_item){
    $featured_ids[]=$feat_item["post_id"];
  }

  $args = array("search" => array("author" => $author_id, "exclude"=>$featured_ids));
  $results = apply_filters('tdf_get_posts', "post", get_option("posts_per_page"), $page, $args);
} elseif ($cat_name = get_query_var("category_name")) {
  $the_cat = get_term_by("slug", $cat_name, "category");
  $the_title = $the_cat->name;
  $the_descr = apply_filters("the_content",$the_cat->description);

  if($the_cat->parent){
      $level0 = $the_cat->parent;
  }else{
      $level0 = $the_cat->term_id;
  }
  $the_class = "cat_".get_term_meta($level0,"theme_color",true);

  $feat_results = apply_filters('tdf_get_posts', "post", 1, 0, array("search" => array("tax_category" => $the_cat->term_id, "meta__is_ns_featured_post"=>"yes")));
  foreach($feat_results["items"] as $feat_item){
    $featured_ids[]=$feat_item["post_id"];
  }

  $args = array("search" => array("tax_category" => $the_cat->term_id, "exclude"=>$featured_ids));
  $results = apply_filters('tdf_get_posts', "post", get_option("posts_per_page"), $page, $args);
} else {

  $feat_results = apply_filters('tdf_get_posts', "post", 1, 0, array("search" => array("tax_category" => $the_cat->term_id, "meta__is_ns_featured_post"=>"yes")));
  foreach($feat_results["items"] as $feat_item){
    $featured_ids[]=$feat_item["post_id"];
  }

  $args = array("search" => array("exclude"=>$featured_ids));
  $results = apply_filters('tdf_get_posts', "post", get_option("posts_per_page"), $page, $args);
  $the_title = 'Magazin';
  $the_descr = "";
}

?>

<div class="<?=$the_class; ?>">
    <section class="header_article">
        <div class="container">
            <h1><?=$the_title; ?></h1>
            <?php 
            foreach($feat_results["items"] as $item){
              include(locate_template("/views/posts/post/content-item-top.php")); 
            }
            ?>
        </div>
    </section>
    <section class="content_mag">
      <div class="container">

        <div class="row" id="article_results">
          <?php 
          foreach($results["items"] as $i=>$item){
            if($i%10==0 || $i%10==9){
              if($results["items"][$i]){
                echo '<div class="col-md-6">';
                include(locate_template("/views/posts/post/content-item-big.php")); 
                echo '</div>';
              }
            }else{
              if($i%10==1 || $i%10==5){
                echo '<div class="col-md-6"><div class="row">';
              }
              
              if($results["items"][$i]){
                echo '<div class="col-md-6">';
                include(locate_template("/views/posts/post/content-item-small.php")); 
                echo '</div>';
              }

              if($i%10==4 || $i%10==8){
                echo '</div></div>';
              }
            }
          }
          if($i%5!=4 && $i%10!=8 && $i%10!=0 && $i%10!=9){
          echo '</div></div>';
          }
          ?>
        </div>
        <?php if($results["total_posts"]>get_option("posts_per_page")){ ?>
          <div class="text-center pt-5">
              <button type="button" data-total="<?=$results["total_posts"];?>" data-page="<?=$page;?>" data-perpage="<?=get_option("posts_per_page");?>" id="load_more_button" class="btn-primary load_more">Mehr Laden</button>
          </div>
        <?php } ?>
    </section>
</div>

        
<script>
  jQuery(document).ready(function() {
    jQuery("#load_more_button").click(function() {
      if (!jQuery(this).is(":disabled")) {
        var th = jQuery(this);
        var tot = parseInt(jQuery(this).attr("data-total"));
        var pg = parseInt(jQuery(this).attr("data-page"));
        var perpg = parseInt(jQuery(this).attr("data-perpage"));

        jQuery(this).attr("disabled", "disabled");
        jQuery(".card_box").addClass("loaded");

        jQuery.ajax({
          url: "<?= admin_url("admin-ajax.php"); ?>",
          method: "GET",
          data: {
            action: "get_ajax_post_list",
            post_type: "<?=$results["args"]["post_type"];?>",
            args: '<?=json_encode($args);?>',
            page: pg + 1,
            per_page: perpg
          }
        }).done(function(data) {
          jQuery("#article_results").append(data);
          th.attr("data-page", pg + 1);
          jQuery(".card_box").each(function(){
            if(!jQuery(this).is(".loaded")){
              jQuery(this).hide().slideDown();
            }
          })

          if ((pg + 1) * perpg > tot) {
            th.slideUp();
          } else {
            th.prop("disabled", false);
          }
        });
      }
    });
  });
</script>