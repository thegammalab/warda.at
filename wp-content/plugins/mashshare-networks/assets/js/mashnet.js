jQuery(document).ready( function($) {


    /* Hide Whatsapp button on other devices than iPhones and Androids */
    if(navigator.userAgent.match(/(iPhone)/i) || navigator.userAgent.match(/(Android)/i)){
        $('.mashicon-whatsapp').show(); 
    }
    /* Network sharer scripts */
    $('.mashicon-google').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 350;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');

        window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes'); 
        });
    $('.mashicon-buffer').click( function(e) {
        e.preventDefault();
        console.log("buffer");
        winWidth = 800;
        winHeight = 470;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');

        window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');
        });

    $('body')
        .off('click', '.mashicon-pinterest')
        .on('click', '.mashicon-pinterest', function(e) {
            e.preventDefault();
            console.log('preventDefault:' + e);
            winWidth = 520;
            winHeight = 350;
            var winTop = (screen.height / 2) - (winHeight / 2);
            var winLeft = (screen.width / 2) - (winWidth / 2);
            //var url = $(this).attr('href');
            var url = $(this).attr('data-mashsb-url');
            var pinterest_select = mashnet.pinterest_select;
            if (pinterest_select === '1'){
                console.log('pinterest_select:' + pinterest_select);
                mashnet_load_pinterest(mashnet_get_images(url));

            }else{
                console.log('opening second sharer:' + pinterest_select);
                window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');

            }
        });

    $('.mashicon-linkedin').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 350;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');

        window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');
        });
    $('.mashicon-digg').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 350;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
	//window.open('http://digg.com/submit?phase=2%20&url=' + mashsb.share_url + '&title=' + mashsb.title, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
        window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes'); 
    });
    $('.mashicon-stumbleupon').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 350;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
        /*if (mashsb.singular === '1') {
            window.open('http://www.stumbleupon.com/submit?url=' + encodeURIComponent(mashsb.share_url), 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
        }else{*/
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes'); 
        //}
        });
    $('.mashicon-vk').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 350;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
        //if (mashsb.singular === '1') {
            //window.open('http://vkontakte.ru/share.php?url=' + mashsb.share_url + '&item=' + mashsb.title, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
        /*}else{*/	
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');
        //}
    });
    $('.mashicon-print').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 350;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
	//window.open('http://www.printfriendly.com/print/?url=' + mashsb.share_url + '&item=' + mashsb.title, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
        window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');
    });
    $('.mashicon-reddit').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 820;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
        /*if (mashsb.singular === '1') {
	window.open('http://www.reddit.com/submit?url=' + mashsb.share_url + '&title=' + mashsb.title, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
        }else{*/
        window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');
        //}
        });
    $('.mashicon-delicious').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 350;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
        /*if (mashsb.singular === '1') {
            window.open('https://delicious.com/save?v=5&noui&jump=close&url=' + mashsb.share_url + '&title=' + mashsb.title, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
        }else{*/
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes'); 
        //}
        });
    $('.mashicon-weibo').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 350;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
        /*if (mashsb.singular === '1') {
            window.open('http://service.weibo.com/share/share.php?url=' + mashsb.share_url + '&title=' + mashsb.title, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
        }else{*/
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');
        //}
        });
    $('.mashicon-pocket').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 350;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
        /*if (mashsb.singular === '1') {
	window.open('https://getpocket.com/save?title=' + mashsb.title + '&url=' + mashsb.share_url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
        }else{*/
          	window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');  
        //}
        });
    $('.mashicon-xing').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 350;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
        /*if (mashsb.singular === '1') {
            window.open('https://www.xing.com/social_plugins/share?h=1;url=' + mashsb.share_url + '&title=' + mashsb.title, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
        }else{*/
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');   
        //}
        });
    $('.mashicon-odnoklassniki').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 350;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
        /*if (mashsb.singular === '1') {
            window.open('http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl=' + mashsb.share_url + '&title=' + mashsb.title, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
        }else{
        */
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');     
        //}
        });
    $('.mashicon-managewp').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 350;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
        /*if (mashsb.singular === '1') {
            window.open('http://managewp.org/share/form?url=' + mashsb.share_url + '&title=' + mashsb.title, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
        }else{*/
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');
        //}
        });
    $('.mashicon-tumblr').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 350;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
        /*if (mashsb.singular === '1') {
            window.open('https://www.tumblr.com/share?v=3&u='+ encodeURIComponent(mashsb.share_url) + '&t=' + mashsb.title, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
        }else{*/
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');
        //}
        });
    $('.mashicon-meneame').click( function(e) {
        e.preventDefault();
        winWidth = 520;
        winHeight = 350;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
        /*if (mashsb.singular === '1') {
            window.open('http://www.meneame.net/submit.php?url=' + mashsb.share_url + '&title=' + mashsb.title, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
        }else{*/
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');   
        //}
        });
    $('.mashicon-whatsapp').click( function(e) {
        //e.preventDefault();
        function escapeRegExp(string) {
            return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
        }

        function replaceAll(string, find, replace) {
            return string.replace(new RegExp(escapeRegExp(find), 'g'), replace);
        }
        var title = mashsb.title;
        var url = $(this).attr('href');
        var href = 'whatsapp://send?text=' + replaceAll(title, '+', '%20') + '%20' + mashsb.share_url;
        /*if (mashsb.singular === '1') {
            $(this).attr("href", href); 
        }else{*/
            $(this).attr("href", url); 
        //}
    });
    $('.mashicon-mail').click( function(e) {
        if (typeof mashnet !== 'undefined'){
            var subject = mashnet.subject;
            var body = mashnet.body;
        } else {
            var subject = 'Check out this site: ';
            var body = '';
        }
        
        /*if (mashsb.singular === '1') {
            var href = 'mailto:?subject=' + subject + '&body=' + body + mashsb.share_url;
            $(this).attr("href", href);
        }else{*/
            var href = $(this).attr('href');
            $(this).attr("href", href);
        //}
        $(this).attr('target', '_blank');
    });
    $('.mashicon-yummly').click( function(e) {
        e.preventDefault();
        winWidth = 620;
        winHeight = 447;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');   
    });
    $('.mashicon-frype').click( function(e) {
        e.preventDefault();
        winWidth = 620;
        winHeight = 447;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');   
    });
    $('.mashicon-skype').click( function(e) {
        e.preventDefault();
        winWidth = 620;
        winHeight = 447;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');   
    });
    $('.mashicon-telegram').click( function(e) {
        e.preventDefault();
        winWidth = 620;
        winHeight = 540;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');   
    });
    $('.mashicon-flipboard').click( function(e) {
        e.preventDefault();
        winWidth = 620;
        winHeight = 540;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');   
    });
    $('.mashicon-hackernews').click( function(e) {
        e.preventDefault();
        winWidth = 620;
        winHeight = 540;
        var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
        var url = $(this).attr('href');
            window.open(url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight + ',resizable=yes');   
    });
    
/* Load Pinterest Popup window
 * 
 * @param string html container
 * @returns void
 */
function mashnet_load_pinterest(html) {

        mashnet_load_pinterest_body();
    
        jQuery('.mashnet_pinterest_header').fadeIn(500); 
        jQuery('.mashnet_pinterest_inner').html(html); 
        
            /* Close Pinterest popup*/
    jQuery ('.mashnet_pinterest_close').click( function (e){
            e.preventDefault();
            jQuery ('.mashnet_pinterest_header').hide();
    });
}

/**
 * Load pinterest wrapper
 * 
 * @returns voids
 */
    function mashnet_load_pinterest_body() {
        var winWidth = window.innerWidth;
        var popupWidth = 350;
        var popupHeight = 310;

        /* Load Pinterest popup into body of page */
        if (winWidth <= 330)
            var popupWidth = 310;
        if (winWidth > 400)
            var popupWidth = 390;
        if (winWidth > 500)
            var popupWidth = 490;

        var winTop = (window.innerHeight / 2) - (popupHeight / 2);
        var winLeft = (window.innerWidth / 2) - (popupWidth / 2);
        var struct = '<div class="mashnet_pinterest_header" style="position:fixed;z-index:999999;max-width:' + popupWidth + 'px; margin-left:' + winLeft + 'px;top:' + winTop + 'px;">\n\
                        <div class="mashnet_pinit_wrapper" style="background-color:white;"><span class="mashnet_pin_it">Pin it! </span><span class="mashnet_pinicon"></span> \n\
<div class="mashnet_pinterest_close" style="float:right;"><a href="#">X</a></div></div>\n\
<div class="mashnet_pinterest_inner"></div>\n\
                </div>\n\
                ';

        jQuery('body').append(struct);
    }
    
    
    /* Get all images on site 
     * 
     * @return html
     * */
    function mashnet_get_images(url){
        
    var allImages = jQuery('img').not("[nopin='nopin']");
    var html = '';
    var url = '';
    
    var largeImages = allImages.filter(function(){
    return (jQuery(this).width() > 70) || (jQuery(this).height() > 70)
    })
    for (i = 0; i < largeImages.length; i++){ 
     html += '<li><a target="_blank" id="mashnetPinterestPopup" href="https://pinterest.com/pin/create/button/?url='+encodeURIComponent(window.location.href) +'%2F&media='+largeImages[i].src+'&description='+largeImages[i].alt+'"><img src="' + largeImages[i].src + '"></a></li>';
    }
    return html;
    }
});