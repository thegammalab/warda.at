<?php
if ( ! class_exists('Warda_func') ){

    class Warda_func {

        public function __construct() {
            add_action( 'wp_ajax_warda_load_podcasts',array( $this, 'load_podcasts' ) );
            add_action( 'wp_ajax_nopriv_warda_load_podcasts',array( $this, 'load_podcasts' ) );

            add_action( 'wp_ajax_warda_filter_results',array( $this, 'filter_results' ) );
            add_action( 'wp_ajax_nopriv_warda_filter_results',array( $this, 'filter_results' ) );
            
            add_action( 'wp_ajax_warda_filter_results_events',array( $this, 'filter_results_events' ) );
            add_action( 'wp_ajax_nopriv_warda_filter_results_events',array( $this, 'filter_results_events' ) );
            
            add_action( 'wp_ajax_warda_filter_results_photos',array( $this, 'filter_results_photos' ) );
            add_action( 'wp_ajax_nopriv_warda_filter_results_photos',array( $this, 'filter_results_photos' ) );
            
            add_action( 'wp_ajax_warda_join_sweepstakes',array( $this, 'join_sweepstakes' ) );
            add_action( 'wp_ajax_nopriv_warda_join_sweepstakes',array( $this, 'join_sweepstakes' ) );

            add_action( 'gform_user_registered', array( $this,'add_custom_user_meta'), 10, 4 );
        }
        
        function add_custom_user_meta( $user_id, $feed, $entry, $user_pass ) {
            wp_set_password($entry[24],$user_id);
        }

        function join_sweepstakes(){
            $sweepstakes_id = $_REQUEST["sweepstakes_id"];
            $uid = get_current_user_id();
            $user = get_user_by("id",$uid);

            $participants = get_post_meta($sweepstakes_id,"participants",true);
            if(!$participants){
                $participants = 0;
            }

            $exists = 0;
            for($i=0;$i<$participants;$i++){
                if(get_post_meta($sweepstakes_id,"participants_".$i."_user",true)==$uid){
                    $exists = 1;
                }
            }

            if(!$exists){
                update_post_meta($sweepstakes_id,"participants_".$participants."_user",$uid);
                update_post_meta($sweepstakes_id,"participants_".$participants."_name",apply_filters("tdf_get_display_name",$uid));
                update_post_meta($sweepstakes_id,"participants_".$participants."_email",$user->user_email);
                update_post_meta($sweepstakes_id,"participants_".$participants."_date",date("m/d/Y g:i a"));

                update_post_meta($sweepstakes_id,"participants",($participants+1));
                echo "added";
            }else{
                echo "exists";
            }
            
            die();
        }

        function load_podcasts(){
            $post_type = "podcasts";
            $per_page = 5;
            $page = 1;

            if(isset($_REQUEST["post_type"])){
                $post_type = $_REQUEST["post_type"];
            }
            if(isset($_REQUEST["per_page"])){
                $per_page = $_REQUEST["per_page"];
            }
            if(isset($_REQUEST["page"])){
                $page = $_REQUEST["page"];
            }
            $color = "purple";

            $posts = new TDF_Posts_Model;
            $args = array("post_type"=>$post_type,"page"=>$page,"per_page"=>$per_page);

            $args["post_template"] = locate_template("/views/posts/podcasts/content-item.php");
            $args["no_results_html"] = '<script>jQuery("#load_more_button").slideUp();</script>';
            $results = $posts->get($args);
            echo $results["output"];
            die();
        }


        function filter_results_photos(){
            header("Content-type: text/html; charset=UTF-8");

            $post_type = "post";
            $per_page = 10;
            $page = 1;
            $color = "yellow";

            if(isset($_REQUEST["post_type"])){
                $post_type = $_REQUEST["post_type"];
            }
            if(isset($_REQUEST["per_page"])){
                $per_page = $_REQUEST["per_page"];
            }
            if(isset($_REQUEST["page"])){
                $page = $_REQUEST["page"];
            }
            $color = "purple";

            $posts = new TDF_Posts_Model;
            $args = array("post_type"=>$post_type,"page"=>$page,"per_page"=>$per_page,"order"=>"meta_desc_the_date");

            $args["return_ids"] = 1;
            $args["post_template"] = locate_template("/views/posts/".$post_type."/ajax_load_item.php");
            $args["no_results_html"] = '<div class="col-12 py-6 text-center"><h3>Sorry, no results</h3></div>';

            if(isset($_REQUEST["args"]["search"])){
                $args["search"] = $_REQUEST["args"]["search"];
            }
            if(isset($_REQUEST["args"]["search"]["date"])){
                $the_date = $_REQUEST["args"]["search"]["date"];
                if($the_date=="today"){
                    $args["search"]["meta_the_date_more"] = strtotime(date("Ymd"));
                    $args["search"]["meta_the_date_less"] = strtotime(date("Ymd"))+24*3600;
                }elseif($the_date=="yesterday"){
                    $args["search"]["meta_the_date_more"] = strtotime(date("Ymd"))-24*3600;
                    $args["search"]["meta_the_date_less"] = strtotime(date("Ymd"))-2*24*3600;
                }elseif($the_date=="tomorrow"){
                    $args["search"]["meta_the_date_more"] = strtotime(date("Ymd"))+24*3600;
                    $args["search"]["meta_the_date_less"] = strtotime(date("Ymd"))+2*24*3600;
                }elseif($the_date=="last_weekend"){
                    $args["search"]["meta_the_date_more"] = strtotime(date("Ymd"))-(date("N")+2)*24*3600;
                    $args["search"]["meta_the_date_less"] = strtotime(date("Ymd"))-(date("N")-1)*24*3600;
                }elseif($the_date=="weekend"){
                    $args["search"]["meta_the_date_more"] = strtotime(date("Ymd"))+(5-date("N"))*24*3600;
                    $args["search"]["meta_the_date_less"] = strtotime(date("Ymd"))+(8-date("N"))*24*3600;
                }elseif($the_date=="this_week"){
                    $args["search"]["meta_the_date_more"] = strtotime(date("Ymd"))+(8-date("N"))*24*3600;
                    $args["search"]["meta_the_date_less"] = strtotime(date("Ymd"))+(15-date("N"))*24*3600;
                }elseif($the_date=="next_week"){
                    $args["search"]["meta_the_date_more"] = time();
                    $args["search"]["meta_the_date_less"] = strtotime(date("Ymd"))+(8-date("N"))*24*3600;
                }elseif($the_date=="select"){
                    $args["search"]["meta_the_date_more"] = strtotime($_REQUEST["args"]["search"]["the_date"]);
                    $args["search"]["meta_the_date_less"] = strtotime($_REQUEST["args"]["search"]["the_date"])+24*3600;
                }
            }


            $filters = "";
            foreach($args["search"] as $f=>$v){
                if($f=="key" && $v!=""){
                    $filters .= '<li><a href="#">Keyword: '.$v.'</a><a href="#" class="clear_keyword"><img src="'.get_bloginfo("template_directory").'/assets/images/close-icon-'.$color.'.svg" alt=""></a></li>';
                }elseif(substr($f,0,4)=="tax_"){
                    if(is_array($v)){
                        foreach($v as $v1){
                            $term = get_term_by("id",$v1,substr($f,4));
                            $filters .= '<li><a href="#">'.$term->name.'</a><a href="#" class="clear_tax" data-id="'.substr($f,4).'_'.$v1.'"><img src="'.get_bloginfo("template_directory").'/assets/images/close-icon-'.$color.'.svg" alt=""></a></li>';
                        }
                    }else{
                        $term = get_term_by("id",$v,substr($f,4));
                        $filters .= '<li><a href="#">'.$term->name.'</a><a href="#" class="clear_tax" data-id="'.substr($f,4).'_'.$v.'"><img src="'.get_bloginfo("template_directory").'/assets/images/close-icon-'.$color.'.svg" alt=""></a></li>';
                    }

                }
            }
            $results = $posts->get($args);

            ob_start();
            $date = "";

            if(count($results["items"])){
                foreach($results["items"] as $post_id){
                    $item = apply_filters("tdf_get_single",$post_id);

                    if($item["meta_the_date"] && date("Ymd",$item["meta_the_date"])!=$date){
                        $date = date("Ymd",$item["meta_the_date"]);
                        echo '<div class="col-12 mt-4"><div class="date_bar"><span>ALLE FOTOS VOM</span><h3>'.utf8_encode(strftime("%A, %e. %B %Y",($item["meta_the_date"]))).'</h3></div></div>';
                    }

                    echo '<div class="col-12">';
                    include(locate_template("/views/posts/photos/content-item-wide.php")); 
                    echo '</div>';
                }
            }elseif($page==1){
              echo '<div class="col-12 text-center py-5"><h3>Sorry, keine Treffer</h3></div>';
            }
            if(count($results["items"])<$per_page){
                echo '<script>jQuery(document).ready(function(){jQuery("#load_more_button").slideUp();});</script>';
            }else{
                echo '<script>jQuery(document).ready(function(){jQuery("#load_more_button").slideDown();});</script>';
            }
            $output = ob_get_contents();
            ob_end_clean();

            if($page==1){
                echo json_encode(array("results"=>$output,"filters"=>$filters));
            }else{
                echo json_encode(array("results"=>$output));
            }

            die();
        }

        function filter_results_events(){
            $per_page = 10;
            $page = 1;
            $color = "yellow";

            if(isset($_REQUEST["post_type"])){
                $post_type = $_REQUEST["post_type"];
            }
            if(isset($_REQUEST["per_page"])){
                $per_page = $_REQUEST["per_page"];
            }
            if(isset($_REQUEST["page"])){
                $page = $_REQUEST["page"];
            }
            $color = "purple";

            $posts = new TDF_Posts_Model;
            $args = array("post_type"=>$post_type,"page"=>$page,"per_page"=>$per_page,"order"=>"meta_asc_the_date");

            $args["return_ids"] = 1;
            $args["post_template"] = locate_template("/views/posts/".$post_type."/ajax_load_item.php");
            $args["no_results_html"] = '<div class="col-12 py-6 text-center"><h3>Sorry, no results</h3></div>';

            if(isset($_REQUEST["args"]["search"])){
                $args["search"] = $_REQUEST["args"]["search"];
            }
            $args["search"]["meta_the_date_more"] = time();
            if(isset($_REQUEST["args"]["search"]["date"])){
                $the_date = $_REQUEST["args"]["search"]["date"];
                if($the_date=="today"){
                    $args["search"]["meta_the_date_more"] = strtotime(date("Ymd"));
                    $args["search"]["meta_the_date_less"] = strtotime(date("Ymd"))+24*3600;
                }elseif($the_date=="yesterday"){
                    $args["search"]["meta_the_date_more"] = strtotime(date("Ymd"))-24*3600;
                    $args["search"]["meta_the_date_less"] = strtotime(date("Ymd"))-2*24*3600;
                }elseif($the_date=="tomorrow"){
                    $args["search"]["meta_the_date_more"] = strtotime(date("Ymd"))+24*3600;
                    $args["search"]["meta_the_date_less"] = strtotime(date("Ymd"))+2*24*3600;
                }elseif($the_date=="last_weekend"){
                    $args["search"]["meta_the_date_more"] = strtotime(date("Ymd"))-(date("N")+2)*24*3600;
                    $args["search"]["meta_the_date_less"] = strtotime(date("Ymd"))-(date("N")-1)*24*3600;
                }elseif($the_date=="weekend"){
                    $args["search"]["meta_the_date_more"] = strtotime(date("Ymd"))+(5-date("N"))*24*3600;
                    $args["search"]["meta_the_date_less"] = strtotime(date("Ymd"))+(8-date("N"))*24*3600;
                }elseif($the_date=="this_week"){
                    $args["search"]["meta_the_date_more"] = strtotime(date("Ymd"))+(8-date("N"))*24*3600;
                    $args["search"]["meta_the_date_less"] = strtotime(date("Ymd"))+(15-date("N"))*24*3600;
                }elseif($the_date=="next_week"){
                    $args["search"]["meta_the_date_more"] = time();
                    $args["search"]["meta_the_date_less"] = strtotime(date("Ymd"))+(8-date("N"))*24*3600;
                }elseif($the_date=="select"){
                    $args["search"]["meta_the_date_more"] = strtotime($_REQUEST["args"]["search"]["the_date"]);
                    $args["search"]["meta_the_date_less"] = strtotime($_REQUEST["args"]["search"]["the_date"])+24*3600;
                }
            }

            $filters = "";
            foreach($args["search"] as $f=>$v){
                if($f=="key" && $v!=""){
                    $filters .= '<li><a href="#">Keyword: '.$v.'</a><a href="#" class="clear_keyword"><img src="'.get_bloginfo("template_directory").'/assets/images/close-icon-'.$color.'.svg" alt=""></a></li>';
                }elseif(substr($f,0,4)=="tax_"){
                    if(is_array($v)){
                        foreach($v as $v1){
                            $term = get_term_by("id",$v1,substr($f,4));
                            $filters .= '<li><a href="#">'.$term->name.'</a><a href="#" class="clear_tax" data-id="'.substr($f,4).'_'.$v1.'"><img src="'.get_bloginfo("template_directory").'/assets/images/close-icon-'.$color.'.svg" alt=""></a></li>';
                        }
                    }else{
                        $term = get_term_by("id",$v,substr($f,4));
                        $filters .= '<li><a href="#">'.$term->name.'</a><a href="#" class="clear_tax" data-id="'.substr($f,4).'_'.$v.'"><img src="'.get_bloginfo("template_directory").'/assets/images/close-icon-'.$color.'.svg" alt=""></a></li>';
                    }

                }
            }
            $results = $posts->get($args);
            ob_start();
            $date = "";
            if(count($results["items"])){
                foreach($results["items"] as $post_id){
                    $item = apply_filters("tdf_get_single",$post_id);

                    if($item["meta_the_date"]!=$date){
                        $date = $item["meta_the_date"];
                        echo '<div class="col-12 mt-4"><div class="date_bar"><span>ALLE FOTOS VOM</span><h3>'.strftime("%A, %e. %B %Y",($item["meta_the_date"])).'</h3></div></div>';
                    }
                    echo '<div class="col-12">';
                    include(locate_template("views/posts/events/wide_item.php")); 
                    echo '</div>';
                }
            }elseif($page==1){
              echo '<div class="col-12 text-center py-5"><h3>Sorry, keine Treffer</h3></div>';
            }
            if(count($results["items"])<$per_page){
                echo '<script>jQuery(document).ready(function(){jQuery("#load_more_button").slideUp();});</script>';
            }else{
                echo '<script>jQuery(document).ready(function(){jQuery("#load_more_button").slideDown();});</script>';
            }
            $output = ob_get_contents();
            ob_end_clean();

            if($page==1){
                echo json_encode(array("results"=>$output,"filters"=>$filters));

            }else{
                echo json_encode(array("more_results"=>$output));

            }

            die();
        }

        function filter_results(){
            $post_type = "post";
            $per_page = 12;
            $page = 1;
            $color = "yellow";

            if(isset($_REQUEST["post_type"])){
                $post_type = $_REQUEST["post_type"];
            }
            if(isset($_REQUEST["per_page"])){
                $per_page = $_REQUEST["per_page"];
            }
            if(isset($_REQUEST["page"])){
                $page = $_REQUEST["page"];
            }

            if($post_type=="vouchers"){
                $color = "blue";
            }
            if($post_type=="videos"){
                $color = "fuchsia";
            }
            if($post_type=="photos"){
                $color = "purple";
            }
            if($post_type=="events"){
                $color = "green";
            }


            $posts = new TDF_Posts_Model;
            $args = array("post_type"=>$post_type,"page"=>$page,"per_page"=>$per_page);

            $args["post_template"] = locate_template("/views/posts/".$post_type."/ajax_load_item.php");
            if($page==0 || $page==1){
            $args["no_results_html"] = '<div class="col-12 py-6 text-center"><h3>Sorry, no results</h3></div>';

            }else{
            $args["no_results_html"] = '<script>jQuery("#load_more_button").slideUp();</script>';

            }

            if(isset($_REQUEST["args"]["search"])){
                $args["search"] = $_REQUEST["args"]["search"];
            }

            $filters = "";
            foreach($args["search"] as $f=>$v){
                if($f=="key" && $v!=""){
                    $filters .= '<li><a href="#">Keyword: '.$v.'</a><a href="#" class="clear_keyword"><img src="'.get_bloginfo("template_directory").'/assets/images/close-icon-'.$color.'.svg" alt=""></a></li>';
                }elseif(substr($f,0,4)=="tax_"){
                    if(is_array($v)){
                        foreach($v as $v1){
                            $term = get_term_by("id",$v1,substr($f,4));
                            $filters .= '<li><a href="#">'.$term->name.'</a><a href="#" class="clear_tax" data-id="'.substr($f,4).'_'.$v1.'"><img src="'.get_bloginfo("template_directory").'/assets/images/close-icon-'.$color.'.svg" alt=""></a></li>';
                        }
                    }else{
                        $term = get_term_by("id",$v,substr($f,4));
                        $filters .= '<li><a href="#">'.$term->name.'</a><a href="#" class="clear_tax" data-id="'.substr($f,4).'_'.$v.'"><img src="'.get_bloginfo("template_directory").'/assets/images/close-icon-'.$color.'.svg" alt=""></a></li>';
                    }

                }
            }
            $results = $posts->get($args);
            if(count($results["items"])!=$per_page){
                $results["output"] = $results["output"].'<script>jQuery("#load_more_button").slideUp();</script>';
            }

            echo json_encode(array("results"=>$results["output"],"filters"=>$filters));
            die();
        }
    }

    new Warda_func();
}
?>