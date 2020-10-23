<footer style="background: #000;">
  <div class="container">
    <div class="row">
        
        <div class="col-md-12">
          <div class="row">
            <div class="brand_col col-lg-3">
              <a href="<?php echo get_bloginfo("url"); ?>" title="Warda" id="footer-logo">
                <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/logo_yellow.png" alt="Warda logo" />
              </a>
              <p>@ 2020 Warda Network GmbH.<br/> All rights reserved.</p>
            </div>
            <div class="menu_footer col-lg-9">
              <div class="row justify-content-center">
                <div class="menu-1 col-sm-6 col-md-4">
                  <span class="footer-title d-block mb-3">Entdecke</span>
                  
                  <?php
                  if (has_nav_menu('footer_menu_1')) :
                    wp_nav_menu(array('theme_location' => 'footer_menu_1', 'menu_class' => 'footer_menu_1', "depth" => 2));
                  endif;
                  ?>
                </div>
                <div class="menu-2 col-sm-6 col-md-4">
                  <span class="footer-title d-block mb-3">Magazin</span>
                  <?php
                  if (has_nav_menu('footer_menu_2')) :
                    wp_nav_menu(array('theme_location' => 'footer_menu_2', 'menu_class' => 'footer_menu_2', "depth" => 2));
                  endif;
                  ?>
                </div>
                <div class="menu-3 col-md-4">
                  <span class="footer-title d-block mb-3">Information</span>
                  <?php
                  if (has_nav_menu('footer_menu_3')) :
                    wp_nav_menu(array('theme_location' => 'footer_menu_3', 'menu_class' => 'footer_menu_3', "depth" => 2));
                  endif;
                  ?>
                  <div class="footer_socials_wrapper">
                   <ul class="footer_socials">
                    <?php if($lnk = get_option("options_facebook_link")){ ?>
                      <li><a href="<?=$lnk;?>" class="social_link fb" target="_blank"></a></li>
                    <?php } ?>
                    <?php if($lnk = get_option("options_instagram_link")){ ?>
                      <li><a href="<?=$lnk;?>" class="social_link ig" target="_blank"></a></li>
                    <?php } ?>
                    <?php if($lnk = get_option("options_tiktok_link")){ ?>
                      <li><a href="<?=$lnk;?>" class="social_link tt" target="_blank"></a></li>
                    <?php } ?>
                    <?php if($lnk = get_option("options_spotify_link")){ ?>
                      <li><a href="<?=$lnk;?>" class="social_link sp" target="_blank"></a></li>
                    <?php } ?>
                    <?php if($lnk = get_option("options_podcasts_link")){ ?>
                      <li><a href="<?=$lnk;?>" class="social_link ap" target="_blank"></a></li>
                    <?php } ?>
                    <?php if($lnk = get_option("options_youtube_link")){ ?>
                      <li><a href="<?=$lnk;?>" class="social_link yt" target="_blank"></a></li>
                    <?php } ?>
                  </ul>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    
  </div>
</footer>
