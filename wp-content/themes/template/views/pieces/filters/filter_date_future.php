<div class="filter_box">
              <div class="filter_title">
                <h5><?=$category_name;?></h5>
                <i class="fas fa-chevron-down"></i>
              </div>
              <ul class="filter1" style="max-height:999px;">
               <?php
                $values = array("today"=>"Heute","tomorrow"=>"Morgen","this_week"=>"Diese Woche","next_week"=>"Nächste Woche","select"=>"Datum wählen");
                foreach($values as $f=>$v){
                ?>
                  <li><input type="radio" <?php if(isset($_GET[$category_slug]) && is_array($_GET[$category_slug]) && in_array($f,$_GET[$category_slug])){echo 'checked="checked"';} ?> name="args[search][<?=$category_slug;?>]" id="<?=$category_slug;?>_<?=$f;?>" value="<?=$f;?>" /><label for="<?=$category_slug;?>_<?=$f;?>"><?=$v;?></label><?php if($f=="select"){ ?><input type="date" name="args[search][the_date]" /><?php } ?></li>
                <?php } ?>
              </ul>
</div>