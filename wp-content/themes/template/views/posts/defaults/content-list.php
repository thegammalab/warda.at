<?php
global $wp;
global $curauth;

$curauth = (get_query_var('author_name'));
$auth_user = get_user_by('slug',$curauth);

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
  $results_photos = apply_filters('tdf_get_posts', "photos", get_option("posts_per_page"), $page, $args);

}elseif ($auth_user) {
  $fname = get_user_meta($auth_user->ID,'first_name',true);
  $lname = get_user_meta($auth_user->ID,'last_name',true);
  $author_name = "{$fname} {$lname}";

  $the_title = 'Für <br/> "' . $author_name . '"';
  $author_id = $auth_user->ID;

  $feat_results = apply_filters('tdf_get_posts', "post", 1, 0, array("search" => array("author" => $author_id, "meta__is_ns_featured_post"=>"yes")));
  foreach($feat_results["items"] as $feat_item){
    $featured_ids[]=$feat_item["post_id"];
  }

  $args = array("search" => array("author" => $author_id, "exclude"=>$featured_ids));
  $results = apply_filters('tdf_get_posts', "post", get_option("posts_per_page"), $page, $args);
  $results_photos = apply_filters('tdf_get_posts', "photos", get_option("posts_per_page"), $page, $args);

}

?>
<div class="<?=$the_class; ?>">
    <section class="header_article">
        <div class="container">
            <h1><?=$the_title; ?></h1>
        </div>
    </section>
</div>
<?php if(count($results["items"])){ ?>
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
<?php } ?>

<?php if(count($results_photos["items"])){ ?>
  <section class="homepage_events_photos">
    <div class="container">
      <div class="row half_space">
        
          <?php 
            foreach($results_photos["items"] as $i=>$item){
              if($i%10==1 || $i%10==5){
                echo '<div class="col-md-8 mb-3 double_size">';
              }else{
                echo '<div class="col-md-4 mb-3">';
              }
              include(locate_template("/views/posts/photos/content-item.php")); 
              echo '</div>';
            }
          ?>
        </div>
      </div>
  </section>
<?php } ?>
        