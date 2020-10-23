<script>
jQuery(document).ready(function(){
    function update_results(){
        jQuery("#page_no").val(1);
 
        jQuery.ajax({
          url: "<?= admin_url("admin-ajax.php"); ?>?action=warda_filter_results<?=$post_filter;?>",
          method: "GET",
          data: jQuery(".filters").serialize()
        }).done(function(data) {
            var obj = JSON.parse(data);
            jQuery(".date_bar, .events_wide").addClass("loaded");
            jQuery("#results_div").html(obj.results);
            jQuery(".date_bar, .events_wide").each(function(){
                if(!jQuery(this).is(".loaded")){
                    jQuery(this).hide().slideDown();
                }
            })
            jQuery([document.documentElement, document.body]).animate({
                scrollTop: 0
            }, 500);
            jQuery("#selected_filter_list .filter_list").html(obj.filters);
            max_mar = jQuery(".sticky_sidebar").parent().height() - jQuery(".sticky_sidebar").outerHeight();
        });
    }

    function load_more_results(){
        var new_page_no = parseInt(jQuery("#page_no").val())+1;
        jQuery("#page_no").val(new_page_no);

        jQuery.ajax({
          url: "<?= admin_url("admin-ajax.php"); ?>?action=warda_filter_results<?=$post_filter;?>",
          method: "GET",
          data: jQuery(".filters").serialize()
        }).done(function(data) {
            var obj = JSON.parse(data);
            jQuery(".date_bar, .events_wide").addClass("loaded");
            jQuery("#load_more_button").removeClass(".disabled");
            jQuery([document.documentElement, document.body]).animate({
                scrollTop: jQuery(".events_wide:last").offset().top
            }, 500);
            jQuery("#results_div").append(obj.more_results);
            jQuery(".date_bar, .events_wide").each(function(){
                if(!jQuery(this).is(".loaded")){
                    jQuery(this).hide().slideDown();
                }
            })
            max_mar = jQuery(".sticky_sidebar").parent().height() - jQuery(".sticky_sidebar").outerHeight();
        });
    }

    jQuery("#load_more_button").unbind("click").click(function(e){
        e.preventDefault();
        if(!jQuery("#load_more_button").is(".disabled")){
            jQuery("#load_more_button").addClass(".disabled");
            load_more_results();
        }
        return false;
    })

    jQuery(".filters").unbind("submit").submit(function(e){
        e.preventDefault();
        update_results();
        return false;
    })

    jQuery(".filters input").change(function(){
        update_results();
    });

    jQuery("#search_but_left").click(function(){
        update_results();
    });

    jQuery("#search_but_right").click(function(){
        jQuery("#search_input_left").val(jQuery("#search_input_right").val());
        update_results();
    });
    
    jQuery("#search_input_right").unbind('keypress').on('keypress',function(e) {
            if(e.which == 13) {
                jQuery("#search_input_left").val(jQuery("#search_input_right").val());
                update_results();
            }
        });

    setInterval(function(){
        
        jQuery(".clear_keyword").unbind("click").click(function(){
            jQuery(".search_key").val("");
            update_results();
        });
        jQuery(".clear_tax").unbind("click").click(function(){
            console.log("#"+jQuery(this).attr("data-id"));
            jQuery("#"+jQuery(this).attr("data-id")).prop("checked",false);
            update_results();
        });
    },500);
    
});

</script>