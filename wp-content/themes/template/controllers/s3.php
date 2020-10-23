<?php
require(get_stylesheet_directory()."/includes/third_party/aws/aws-autoloader.php");

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\S3\Exception\S3Exception;

if ( ! class_exists('Warda_S3') ){
    class Warda_S3 {
        public function __construct() {
            global $wpdb;
            
            add_filter( 'warda_get_photo_thumbnails', array( $this, 'warda_get_photo_thumbnails' ), 10, 1);
            add_filter( 'warda_get_photo_images', array( $this, 'get_photo_images' ), 10, 1);

            add_filter( 'warda_get_event_thumbnail', array( $this, 'get_event_thumbnail' ), 10, 1);

            if(isset($_GET["action"]) && $_GET["action"]=="import_images_photos"){
                $results = $wpdb->get_results("SELECT * FROM `db_galleries`");
                foreach($results as $item){
                    if($post_id = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='orig_gal_id' AND `meta_value`='".$item->id."'")){
                        echo $post_id."<br>";
                        if(!get_post_meta($post_id,"gallery_images",true)){
                            $this->get_s3_images($post_id);
                        }
                        if(!get_post_meta($post_id,"_thumbnail_id",true)){
                            $this->get_s3_thumbnail($post_id);
                        }
                    }
                }
                die();
            }

        }

        function get_s3_images($post_id){
            $gallery_images = get_post_meta($post_id,"gallery_images",true);
            $count = get_post_meta($post_id,"gallery_images_count",true);
            $orig_gal_id = get_post_meta($post_id,"orig_gal_id",true);      

                $aws_access_key = esc_attr( get_option('s3_browse_aws_access_key') );
                $aws_secret = esc_attr( get_option('s3_browse_aws_secret') );
                $aws_region = esc_attr( get_option('s3_browse_aws_region') );
                $bucket = "assets.warda.at";

                $credentials = new Credentials("$aws_access_key", "$aws_secret");

                //Instantiate the S3 client with your AWS credentials
                $s3Client = S3Client::factory(array(
                    'credentials' => $credentials,
                    'region' => "$aws_region",
                    'version' => 'latest' ));

                $command = $s3Client->getCommand('ListObjects', array(
                    'MaxKeys' => 999,
                    "Bucket" => $bucket,
                    'Prefix'  => 'uploads/galleries/'.$orig_gal_id.'/thumb_',
                ));
                $result = $s3Client->execute($command);
                $files = $result->getPath('Contents');
                update_post_meta($post_id,"gallery_images_count",count($files));

                $images = array();
                foreach($files as $f=>$obj){
                    $images[]=array(
                        "thumb"=>"https://s3.eu-central-1.amazonaws.com/".$bucket."/".$obj["Key"],
                        "image"=>"https://s3.eu-central-1.amazonaws.com/".$bucket."/".str_replace("thumb_","branded_",$obj["Key"]),
                    );
                }

                update_post_meta($post_id,"gallery_images",$images);

                $gallery_images = get_post_meta($post_id,"gallery_images",true);
                $count = get_post_meta($post_id,"gallery_images_count",true);
        }

        function get_event_thumbnail($post_id){

            if(!get_post_meta($post_id,"_thumbnail_id",true)){
                $orig_event_id = get_post_meta($post_id,"orig_event_id",true);   
                // echo "//".$orig_event_id."//";   

                $aws_access_key = esc_attr( get_option('s3_browse_aws_access_key') );
                $aws_secret = esc_attr( get_option('s3_browse_aws_secret') );
                $aws_region = esc_attr( get_option('s3_browse_aws_region') );
                $bucket = "assets.warda.at";

                $credentials = new Credentials("$aws_access_key", "$aws_secret");

                //Instantiate the S3 client with your AWS credentials
                $s3Client = S3Client::factory(array(
                    'credentials' => $credentials,
                    'region' => "$aws_region",
                    'version' => 'latest' ));

                $command = $s3Client->getCommand('ListObjects', array(
                    'MaxKeys' => 999,
                    "Bucket" => $bucket,
                    'Prefix'  => 'uploads/events/'.$orig_event_id.'/',
                ));
                $result = $s3Client->execute($command);
                $files = $result->getPath('Contents');
                
                if(isset($files[0])){
                    $image_url = "https://s3.eu-central-1.amazonaws.com/".$bucket."/".$files[0]["Key"];
                    $curl = curl_init($image_url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $image_data = curl_exec($curl);
                    curl_close($curl);

                    if($image_data){
                        $upload_dir = wp_upload_dir();
                        $filename = basename( $image_url );

                        if ( wp_mkdir_p( $upload_dir['path'] ) ) {
                            $file = $upload_dir['path'] . '/' . $filename;
                        }
                        else {
                            $file = $upload_dir['basedir'] . '/' . $filename;
                        }

                        file_put_contents( $file, $image_data );
                        $wp_filetype = wp_check_filetype( $filename, null );

                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => sanitize_file_name( $filename ),
                            'post_content' => get_post_meta($post_id,"cover_image_credits",true),
                            'post_status' => 'inherit'
                        );

                        $attach_id = wp_insert_attachment( $attachment, $file, $post_id);
                        require_once( ABSPATH . 'wp-admin/includes/image.php' );
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                        wp_update_attachment_metadata( $attach_id, $attach_data );
                        update_post_meta($post_id,"_thumbnail_id",$attach_id);
                    }
                }
            }
        }

        function get_s3_thumbnail($post_id){
            $gallery_images = get_post_meta($post_id,"gallery_images",true);

            $image_url = $gallery_images[0]["image"];

                if(!get_post_meta($post_id,"_thumbnail_id",true)){
                    $curl = curl_init($image_url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $image_data = curl_exec($curl);
                    curl_close($curl);

                    if($image_data){
                        $upload_dir = wp_upload_dir();
                        $filename = basename( $image_url );

                        if ( wp_mkdir_p( $upload_dir['path'] ) ) {
                            $file = $upload_dir['path'] . '/' . $filename;
                        }
                        else {
                            $file = $upload_dir['basedir'] . '/' . $filename;
                        }

                        file_put_contents( $file, $image_data );
                        $wp_filetype = wp_check_filetype( $filename, null );

                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => sanitize_file_name( $filename ),
                            'post_content' => get_post_meta($post_id,"cover_image_credits",true),
                            'post_status' => 'inherit'
                        );

                        $attach_id = wp_insert_attachment( $attachment, $file, $post_id);
                        require_once( ABSPATH . 'wp-admin/includes/image.php' );
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                        wp_update_attachment_metadata( $attach_id, $attach_data );
                        update_post_meta($post_id,"_thumbnail_id",$attach_id);
                    }

                    return $attach_id;
                }

        }
                
        function warda_get_photo_thumbnails($post_id){
            $gallery_images = get_post_meta($post_id,"gallery_images",true);
            $count = get_post_meta($post_id,"gallery_images_count",true);
            
            if(!$gallery_images){
                $this->get_s3_images($post_id);
                $gallery_images = get_post_meta($post_id,"gallery_images",true);
                $count = get_post_meta($post_id,"gallery_images_count",true);
            }

            ob_start();
            echo '<ul class="img_gallery">';
            for($i=0;$i<4;$i++){
                $thumb_im = $gallery_images[$i];
                echo '<li><a href="'.get_the_permalink($post_id).'" class="photos_img"><img src="'.$thumb_im["thumb"].'" /></a></li>';
            } 
            if($count>4){
                echo '<li><a href="'.get_the_permalink($post_id).'" class="img_gallery_more">+'.($count-4).'</a></li>';
            }
            echo '</ul>';

            $cont = ob_get_contents();
            ob_end_clean();
            return $cont;
        }

        function get_photo_images($post_id){
            $gallery_images = get_post_meta($post_id,"gallery_images",true);
            $count = get_post_meta($post_id,"gallery_images_count",true);
            
            if(!$gallery_images){
                $this->get_s3_images($post_id);
                $gallery_images = get_post_meta($post_id,"gallery_images",true);
                $count = get_post_meta($post_id,"gallery_images_count",true);
            }

            ob_start();
            echo '<ul class="img_gallery">';
            foreach($gallery_images as $thumb_im){
                echo '<li><a href="'.get_the_permalink($post_id).'" class="photos_img">'.$thumb_im["thumb"].'</a></li>';
            } 
            echo '<li><a href="'.get_the_permalink($post_id).'" class="img_gallery_more">+'.($count-4).'</a></li>';
            echo '</ul>';

            $cont = ob_get_contents();
            ob_end_clean();
            return $cont;
        }
    }

    new Warda_S3();
}