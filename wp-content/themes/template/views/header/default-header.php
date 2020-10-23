<header class="">
  <div class="headerbar_1">
    <div class="headerbar_col_left">
      <button class="navbar-toggler position-relative" type="button" >
        <i></i><i></i><i></i>
      </button>
    </div>
    <a class="navbar-brand position-relative" href="<?php echo get_bloginfo("url"); ?>"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/logo_yellow.png" alt="logo" /></a>
    <div class="headerbar_col_right">
      <ul id="header_social_links">
        <?php if($lnk = get_option("options_instagram_link")){ ?>
          <li><a href="<?=$lnk;?>" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/menu-instagram.svg" /></a></li>
        <?php } ?>
        <?php if($lnk = get_option("options_facebook_link")){ ?>
          <li class="d-none d-md-block"><a href="<?=$lnk;?>" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/menu-facebook.svg" /></a></li>
        <?php } ?>
        <?php if($lnk = get_option("options_tiktok_link")){ ?>
          <li class="d-none d-md-block"><a href="<?=$lnk;?>" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/menu-tiktok.svg" /></a></li>
        <?php } ?>
        <?php if($lnk = get_option("options_spotify_link")){ ?>
          <li class="d-none d-md-block"><a href="<?=$lnk;?>" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/menu-spotify.svg" /></a></li>
        <?php } ?>
        <?php if($lnk = get_option("options_podcasts_link")){ ?>
          <li class="d-none d-md-block"><a href="<?=$lnk;?>" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/menu-podcasts.svg" /></a></li>
        <?php } ?>
        <?php if($lnk = get_option("options_youtube_link")){ ?>
          <li class="d-none d-md-block"><a href="<?=$lnk;?>" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/menu-youtube.svg" /></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div id="main_menu_popup">
    <?php
            if (has_nav_menu('main_menu_header')) :
              wp_nav_menu(array('theme_location' => 'main_menu_header', 'walker' => new TDF_Nav_Walker(), 'menu_class' => 'navbar-nav ', "depth" => 3));
            endif;
          ?>
  </div>
  <div class="headerbar_2">
    <nav class="navbar d-flex align-items-center navbar-expand-md">
        <a href="<?php echo get_bloginfo("url"); ?>" class="menu_btn">War Da. Komme Wieder.</a>
        <div id="main_menu">
          <?php
            if (has_nav_menu('main_menu_header')) :
              wp_nav_menu(array('theme_location' => 'main_menu_header', 'walker' => new TDF_Nav_Walker(), 'menu_class' => 'navbar-nav ', "depth" => 3));
            endif;
          ?>
          <form action="<?=get_bloginfo("url");?>" method="GET" id="header_search">
            <input type="text" name="s" id="search_input" class="form-control" />
            <button type="button" class="search_btn" href="#"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/search.svg" alt="" /></button>
          </form>
        </div>

        <script>
          jQuery(document).ready(function(){
            jQuery("#search_input").click(function(e){
              e.stopPropagation();
              return false;
            });
            jQuery(".search_btn").unbind("click").click(function(e){
              if(!jQuery(this).closest("form").is(".active")){
                jQuery(this).closest("form").addClass("active");
                jQuery("#search_input").focus();
              }else{
                if(jQuery("#search_input").val()){
                  jQuery("#header_search").submit();
                }else{
                  jQuery("#search_input").focus();
                }
              }
              e.stopPropagation();
            });

            setInterval(function(){
              if(jQuery("#menu-header-menu .dropdown-menu.show").length){
                jQuery("#menu-header-menu").addClass("open");
              }else{
                jQuery("#menu-header-menu").removeClass("open");
              }
            },200);
            
            jQuery("body").click(function(e){
              console.log("aaa");
              jQuery("#header_search.active").removeClass("active");
            });
          })
        </script>

        <ul class="login_menu">
          <?php if($uid = get_current_user_id()){ ?>
            <li><a href="#" >Hallo <?=get_user_meta($uid,"first_name",true); ?></a></li>
            <li><a href="<?=wp_logout_url(get_bloginfo("url"));?>">Abmelden</a></li>
          <?php }else{ ?>
            <li><a href="#" class="login_link" data-toggle="modal" data-target="#login_popup" >Anmelden</a></li>
            <li><a href="#" class="register_link" data-toggle="modal" data-target="#login_popup" >Registrieren</a></li>
          <?php } ?>
        </ul>
    </nav>
  </div>
</header>

<?php include(locate_template("views/pieces/login_modal.php"));?>

<script>
jQuery(document).ready(function(){
  jQuery(".navbar-toggler").click(function(){
    jQuery(this).toggleClass("open");
    if(jQuery(this).is(".open")){
      jQuery("#main_menu_popup").addClass("active");
    }else{
      jQuery("#main_menu_popup").removeClass("active");
    }
  })
})
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-18779952-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-18779952-3');
  gtag('set', 'anonymizeIp', true);
</script>