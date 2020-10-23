<link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/includes/third_party/dropzone/css/dropzone.css">
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/includes/third_party/dropzone/dropzone.js"></script>

<script>
function set_gallery_actions(){
  jQuery('.del_img_link').unbind("click").click(function () {
    if (confirm("Are you sure you want to delete?") == true) {
      jQuery.ajax({
        url: '<?php echo admin_url("admin-ajax.php"); ?>?action=tdf_delete_image&img_id=' + jQuery(this).attr("data-id"),
        context: document.body
      })
      jQuery(this).closest("li").fadeOut(500,function(){
        jQuery(this).remove();
      });
    }
    return false;
  });

  jQuery( "#attachment_inputs_post>ul" ).sortable({
    placeholder: "ui-state-highlight",
    update: function (event, ui) {
      var ids = ""
        jQuery(this).find("li").each(function(){
          ids += jQuery(this).attr("data-id")+"|";
        })
        jQuery.ajax({
          url: '<?php echo admin_url("admin-ajax.php"); ?>?action=tdf_save_image_order&images=' + ids,
          context: document.body
        })
    },
  });

  jQuery( ".save_media_title" ).click(function(){
    var par = jQuery(this).closest(".media_title_form");
    jQuery.ajax({
      url: '<?php echo admin_url("admin-ajax.php"); ?>?action=tdf_save_image_content',
      data: {img_id: jQuery(this).attr("data-id"), img_title: par.find(".media_title").val(), img_description: par.find(".media_description").val()},
      context: document.body
    })
    jQuery(this).closest(".modal").find(".close").click().trigger("click");
  })
}

Dropzone.autoDiscover = false;

jQuery(document).ready(function () {
  set_gallery_actions();

  jQuery("#img_gallery_box").dropzone({
    url: "<?php echo admin_url("admin-ajax.php"); ?>?action=tdf_upload_image",
    previewsContainer: ".dropzone-previews",
    uploadMultiple: true,
    parallelUploads: 1,
    maxFiles: 100,
    addRemoveLinks: true,
    init: function () {
      this.on("success", function (file, response) {
        jQuery("#attachment_inputs_post ul.row").append(response);
      });
      this.on("removedfile", function (file, response) {
        var rem = jQuery('#attachment_inputs_post li[data-file="' + file.name + '"]').attr("value");
        jQuery('input[data-file="' + file.name + '"]').remove();
        jQuery.ajax({
          url: '<?php echo admin_url("admin-ajax.php"); ?>?action=tdf_delete_image&img_id=' + rem,
          context: document.body
        })
      });
    }
  });
});
</script>
