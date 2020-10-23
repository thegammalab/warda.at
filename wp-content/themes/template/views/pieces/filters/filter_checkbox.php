<div class="filter_box">
              <div class="filter_title">
                <h5><?=$category_name;?></h5>
                <i class="fas fa-chevron-down"></i>
              </div>
              <ul class="filter1">
               <?php
                $terms = get_terms($category_slug,'orderby=count&order=DESC&hide_empty=1');
                foreach($terms as $term){
                ?>
                  <li><input type="checkbox" <?php if((isset($_GET["tax_".$category_slug]) && (is_array($_GET["tax_".$category_slug]) && in_array($term->term_id,$_GET["tax_".$category_slug]) || $_GET["tax_".$category_slug]==$term->term_id)) || (get_query_var("taxonomy")==$category_slug && get_query_var("term")==$term->slug)){echo 'checked="checked"';} ?> name="args[search][tax_<?=$category_slug;?>][]" id="<?=$category_slug;?>_<?=$term->term_id;?>" value="<?=$term->term_id;?>" /><label for="<?=$category_slug;?>_<?=$term->term_id;?>"><?=wp_get_attachment_image(get_term_meta($term->term_id,"image",true));?><?=$term->name;?></label></li>
                <?php } ?>
              </ul>
</div>