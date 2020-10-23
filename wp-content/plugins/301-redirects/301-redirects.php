<?php
/*
  Plugin Name: 301 Redirects
  Plugin URI: https://wp301redirects.com/
  Description: Easily create & manage redirections.
  Version: 0.5
  Author: WebFactoryLtd
  Author URI: https://www.webfactoryltd.com/

  Copyright 2015 - 2020  WebFactory Ltd  (email: wp301@webfactoryltd.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// this is an include only WP file
if (!defined('ABSPATH')) {
  die;
}

require_once 'controllers.php';

require_once 'wp301/wp301.php';
new wf_wp301(__FILE__, 'settings_page_301-redirects');


function load_301_redirect_assets($adminpage)
{
  if ($adminpage == 'settings_page_301-redirects') {
    wp_enqueue_style('redirect_301_bootstrap_css', plugin_dir_url(__FILE__) . 'lib/bootstrap-3.3.4.css', false, '1.0.0');
    wp_enqueue_style('redirect_301_custom_css', plugin_dir_url(__FILE__) . 'lib/style.css', false, '1.0.0');
  }
} //load_301_redirect_assets


add_action('admin_enqueue_scripts', 'load_301_redirect_assets');
add_action('wp', 'redirects_301_do_redirect', 0, 1);


function redirects_301_do_redirect()
{
  $siteurl = get_bloginfo('url');
  $actual_link = getUrl();
  $all_redirects = $GLOBALS['redirectsplugins']->getAll();

  if ($all_redirects) {
    foreach ($all_redirects as $redirect_id) {
      $la_redirect = $GLOBALS['redirectsplugins']->getFields($redirect_id);
      $la_old_link = str_replace($siteurl, "", $la_redirect['old_link']);
      if ($actual_link == $siteurl . $la_old_link) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Location: ' . $la_redirect['new_link'], true, 301);
        die();
      }
    } // foreach
  }
} // redirects_301_do_redirect


function getUrl()
{
  $url  = @($_SERVER["HTTPS"] != 'on') ? 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] : 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
  return $url;
} // getUrl


function redirects_301_options()
{

  $redirects = new Redirects;

  $savedsuccesfully = false;
  if (isset($_POST['custom_id']) && isset($_POST['delete_custom'])) {
    $custom_id = sanitize_text_field($_POST['custom_id']);
    $redirects->remove($custom_id);
    die();
  }

  if (isset($_POST['links_audit_submit']) && !isset($_POST['delete_custom'])) {

    $redirects->delete();

    if (!empty($_POST['title'])) {
      $redirect_arr = $_POST['title'];
      foreach ($redirect_arr as $key => $redirect_title) {
        $title = sanitize_text_field($redirect_title);
        $section = sanitize_text_field($_POST['section'][$key]);
        $new_link = esc_url($_POST['new_link'][$key]);
        $old_link = esc_url($_POST['old_link'][$key]);
        $redirects->edit($title, $section, $new_link, $old_link);
      }
    }

    $savedsuccesfully = true;
  }
  ?>

  <div class="col-sm-12">
    <h1>301 Redirects</h1><br>

    <?php
    if ($savedsuccesfully) {
      ?>
      <div class="alert alert-success">All redirect rules have been saved.<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>
    <?php
  }
  ?>

    <p>
      Add your old path <code>/old-path-from-site/</code> (don't forget to start with a forward slash) in the old link field, and the new path <code>/new-path-somewhere/</code> in the new link field. If you're redirecting to an external URL add the <code>https://</code> prefix.<br>Name and Section are there for your organization and convenience.
    </p>
    <form action="" method="post">
      <input type="hidden" name="links_audit_submit" value="true">
      <table class="table table-striped table-bordered">
        <tr>
          <td class="col-md-3">Name</td>
          <td class="col-md-3">Section</td>
          <td class="col-md-3">Old URL</td>
          <td class="col-md-3">New URL</td>
        </tr>
        <?php
        $custom_redirects = $redirects->getAll();

        if ($custom_redirects) {
          foreach ($redirects->getAll() as $custom_id) {

            $fields = $redirects->getFields($custom_id);
            ?>
            <tr id="customRow<?php echo $custom_id; ?>">
              <td><input type="text" class="form-control" placeholder="Redirect name (for internal use)" name="title[]" value="<?php echo $fields['title']; ?>" /></td>
              <td><input type="text" class="form-control" placeholder="Section" name="section[]" value="<?php echo $fields['section']; ?>" /></td>
              <td>
                <input placeholder="Old URL" name="old_link[]" class="form-control short-field" value="<?php echo $fields['old_link']; ?>" />
                <a title="Test redirect rule" class="link-icon" target="_blank" href="<?php echo $fields['old_link']; ?>"><span class="dashicons dashicons-external"></span></a>
              </td>
              <td>
                <input type="text" class="form-control new-link short-field" placeholder="New URL" name="new_link[]" value="<?php echo $fields['new_link']; ?>" />
                <a title="Remove redirect rule" class="remove-custom" href="#" data-id="<?php echo $custom_id; ?>"><span class="dashicons dashicons-trash"></span></a>
              </td>
            </tr>
          <?php
        } // foreach
      }
      ?>
        <tr id="addRow">
          <td colspan="10" class="text-left">
            <button type="submit" class="btn btn-default btn-info">Save All</button>&nbsp;&nbsp;&nbsp;
            <a id="addRowBtn" class="btn btn-default" href="#"><span style="vertical-align: middle;" class="dashicons dashicons-plus"></span> Add a New Redirect Rule</a></td>
        </tr>
      </table>
    </form>

    <p>Please <a href="https://wordpress.org/support/plugin/301-redirects/reviews/#new-post" target="_blank">rate the plugin &starf;&starf;&starf;&starf;&starf;</a> to <b>keep it free &amp; maintained</b>. It only takes a minute to rate. Thank you! 👋</p>
  </div><!-- .col-sm-12 -->
  <script>
    jQuery(function() {

      var rowId = 0;

      jQuery('#addRowBtn').on('click', function(e) {

        e.preventDefault();

        var newRow = '<tr id="row' + rowId + '">' +
          '<td><input type="text" class="form-control" placeholder="Redirect name (for internal use)" name="title[]" /></td>' +
          '<td><input type="text" class="form-control" placeholder="Section" name="section[]" /></td>' +
          '<td><input name="old_link[]" class="pull-left form-control" placeholder="Old URL" /></td>' +
          '<td>' +
          '<input type="text" class="form-control new-link short-field" placeholder="New URL" name="new_link[]" /> <a title="Remove redirect rule" class="remove-row" href="#" data-id="' + rowId + '"><span class="dashicons dashicons-trash"></span></a></td>' +
          '</td>' +
          '</tr>';

        jQuery('#addRow').before(newRow);

        rowId++;

        jQuery('.remove-row').on('click', function(e) {
          e.preventDefault();
          var id = jQuery(this).data('id');
          jQuery('#row' + id).fadeOut(function() {
            jQuery(this).remove();
          });
        });

      });

    });

    jQuery(function() {

      jQuery('.remove-custom').on('click', function(e) {

        e.preventDefault();

        var confirmDelete = confirm("Are you sure you want to delete this redirect rule?  This cannot be undone.");

        if (confirmDelete) {

          var id = jQuery(this).data('id');

          jQuery.ajax({
            type: "POST",
            url: '',
            data: {
              delete_custom: 'true',
              custom_id: id
            }

          }).done(function(html) {

            jQuery('#customRow' + id).fadeOut(function() {
              jQuery(this).remove();
            });

          });
        }
      });
    });
  </script>
<?php
} // redirects_301_options

add_action('admin_menu', 'redirects_301');

function redirects_301()
{
  add_options_page('301 Redirects', '301 Redirects', 'manage_options', '301-redirects', 'redirects_301_options');
} //redirects_301
