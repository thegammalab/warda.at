<li class="col-md-3 col-sm-4 col-6 no-padding img_max_width" data-id="<?php echo $file["id"]; ?>" data-file="<?php echo $file["file"]; ?>">
  <div class="gallery_item">
    <div class="gallery_img">
      <img src="<?php echo $file["src"].'?rand=' . rand(1000, 9999); ?>" />
    </div>
    <div class="gallery_actions">
      <div class="row">
        <div class="col-6 text-left"><a href="#" data-toggle="modal" data-target="#edit_media_<?php echo $file["id"]; ?>">edit</a></div>
        <div class="col-6 text-right"><a href="#" class="del_img_link" data-id="<?php echo $file["id"]; ?>">delete</a></div>
      </div>
    </div>
    <input type="hidden" name="post_attach[]" value="<?php echo $file["id"]; ?>" />
    <script>set_gallery_actions();</script>
    <div class="modal fade" id="edit_media_<?php echo $file["id"]; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Update Image Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
          <div class="modal-body">
            <div class="media_title_form">
              <div class="form-group row">
                <label for="inputPassword" class="col-sm-4 col-form-label">Title</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control media_title" placeholder="" value="<?php echo $file["title"]; ?>" />
                </div>
              </div>
              <div class="form-group row">
                <label for="inputPassword" class="col-sm-4 col-form-label">Description</label>
                <div class="col-sm-8">
                  <textarea class="form-control media_description" placeholder=""><?php echo $file["description"]; ?></textarea>
                </div>
              </div>
              <hr />
              <div class="text-right">
                <button type="button" class="btn color2_button d-inline-block w-auto" data-dismiss="modal">Close</button>
                <button type="button" class="btn color1_button d-inline-block w-auto save_media_title" data-id="<?php echo $file["id"]; ?>">Save</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</li>
