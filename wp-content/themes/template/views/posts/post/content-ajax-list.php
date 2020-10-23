<?php
foreach($results["items"] as $i=>$item){
    if($i%10==0 || $i%10==9){
    if($results["items"][$i]){
        echo '<div class="col-md-6">';
        include(locate_template("/views/posts/post/content-item-big.php")); 
        echo '</div>';
    }
    }else{
    if($i%10==1 || $i%10==5){
        echo '<div class="col-md-6"><div class="row">';
    }
    
    if($results["items"][$i]){
        echo '<div class="col-md-6">';
        include(locate_template("/views/posts/post/content-item-small.php")); 
        echo '</div>';
    }

    if($i%10==4 || $i%10==8){
        echo '</div></div>';
    }
    }
}

if($i%5!=4 && $i%10!=8 && $i%10!=0 && $i%10!=9){
    echo '</div></div>';
}
