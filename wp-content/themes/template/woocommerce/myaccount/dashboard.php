  <div class="breadcrumb_bg">
    <div class="container">

    </div>

  </div>
  <section class="landing_top">
    <div class="container">
  		<?php $uid = get_current_user_id(); ?>
      <h2>Welcome <?=get_user_meta($uid,"first_name",true)." ".get_user_meta($uid,"last_name",true); ?> to</h2>
      <h1>Limited edition </h1>
      <h6>Thank you for joining Limited edition, a special place for the special things in life, <br>
Your subscription now active until aug 6th 2020</h6>
<a href="#" class="btn-primary trigger_menu">start browsing</a>
    </div>
  </section>