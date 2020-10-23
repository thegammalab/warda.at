<div class="modal fade general_modal" id="login_popup" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
          <h2>JETZT REGISTRIEREN UND VORTEILE ERLEBEN</h2>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="popup_login_section">
                <?php if(isset($_GET["login"]) && $_GET["login"]=="failed"){ ?>
                  <div class="alert alert-danger">Tut uns leid, dein Login ist nicht korrekt, versuche es erneut</div>
                <?php  } ?>
              <?php wp_login_form(array(
                'label_username' => __( 'Username oder Emailadresse' ),
                'label_password' => __( 'Passwort' ),
                'label_remember' => __( 'Angemeldet bleiben' ),
                "label_log_in"=>"Einloggen"
                )); ?>
              <hr />
              <p>Hast du noch kein Konto? <a href="#" id="trigger_popup_signup_section">Klick hier um ein Konto zu erstellen..</a></p>
            </div>
            <div id="popup_signup_section" style="display:none">
              <?=do_shortcode('[gravityform id="1" title="false" description="false" ajax="true"]'); ?>
              <hr />
              <p>Haben Sie bereits ein Konto? <a href="#" id="trigger_popup_login_section">Klick hier um dich einzuloggen.</a></p>
              <p>Haben Sie Ihr Passwort vergessen? <a href="<?=get_bloginfo("url");?>/my-account/lost-password/">Klicken Sie hier, um es zurückzusetzen.</a></p>

              <?php if(isset($_GET["signup"]) && $_GET["signup"]=="failed"){ ?>
                  <div class="alert alert-danger">Es gab einen Fehler bei Ihrer Eingabe, bitte überprüfen Sie es nochmal.</div>
              <?php  } ?>
            </div>
            <script>
              jQuery(document).ready(function(){
                jQuery("#trigger_popup_signup_section").click(function(){
                  jQuery("#popup_login_section").slideUp();
                  jQuery("#popup_signup_section").slideDown();
                });
                jQuery("#trigger_popup_login_section, #trigger_popup_login_section2").click(function(){
                  jQuery("#popup_login_section").slideDown();
                  jQuery("#popup_signup_section").slideUp();
                });

                jQuery(".register_link").unbind("click").click(function(){
                  jQuery("#popup_login_section").hide();
                  jQuery("#popup_signup_section").show();
                  jQuery("#login_popup").modal();
                });
                jQuery(".login_link").unbind("click").click(function(){
                  jQuery("#popup_login_section").show();
                  jQuery("#popup_signup_section").hide();
                  jQuery("#login_popup").modal();
                });

                <?php if(isset($_GET["login"]) && $_GET["login"]=="failed" && !get_current_user_id()){ ?>
                  jQuery("#login_popup").modal();
                <?php } ?>
              });
            </script>
        </div>
    </div>
  </div>
</div>