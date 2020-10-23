function SPAI() {}

SPAI.prototype = {
    /*DEBUG*/stop: 100000,
    //*DEBUG*/observedMutations: 0,
    //*DEBUG*/handledMutations: 0,
    //*DEBUG*/parsedMutations: 0,
    //*DEBUG*/modifiedMutations: 0,


    fancyboxId: "",
    fancyboxHooked: "none",
    mutationsCount: 0,
    mutationsList: {},
    timeOutHandle: false,
    mutationsLastProcessed: 0,
    updatedUrlsCount: 0,

    mutationObserver: false,
    intersectionObserver: false,
    intersectionMargin: 500,

    initialized: false,
    bodyHandled: false,
    bodyCount: 0,

    supportsWebP: false,

    urlRegister: [], //keep a register with all URLs and sizes
    callbacks: [],

    sniperOn: false,

    debugInfo: {log: ''},
    loadTs: Date.now(),

    NORESIZE: 1,
    EXCLUDED: 2,
    EAGER: 4
};


SPAI.prototype.init = function(){
    if(typeof window.IntersectionObserver !== 'function') {
        jQuery.getScript(spai_settings.plugin_url + '/assets/js/intersection.min.js?' + spai_settings.version, ShortPixelAI.setupIntersectionObserverAndParse);
    } else {
        ShortPixelAI.setupIntersectionObserverAndParse(); //this also parses the document for the first time
    }
};

SPAI.prototype.record = function(action, type, value) {
    if(spai_settings.debug) {
        switch (action) {
            case 'count':
                if(typeof ShortPixelAI.debugInfo[type] === 'undefined') ShortPixelAI.debugInfo[type] = 0;
                ShortPixelAI.debugInfo[type] += value;
                break;
            case 'log':
            case 'logX':
                if(typeof ShortPixelAI.debugInfo[type] === 'undefined') ShortPixelAI.debugInfo[type] = '';
                ShortPixelAI.debugInfo[type] += (new Date().getTime()) + ' - ' + (action === 'log' ? value : ShortPixelAI.xpath(value)) + '\n';
        }
    }
}

/**
 * New grouped log method
 * @param {*} [data]
 */
SPAI.prototype.gLog = function( data ) {
    if ( arguments.length === 0 ) {
        return;
    }

    var timeAfterLoad = Date.now() - this.loadTs;

    console.groupCollapsed( '🤖 SPAI Debug' );

    console.log( '%cTime after init: %c' + timeAfterLoad + '%c ms', 'font-weight:bold', 'color:#4caf50;font-weight:bold', 'color:inherit;font-weight:bold' );

    for ( var index = 0; index < arguments.length; index++ ) {
        console.log( arguments[ index ] );
    }

    console.groupEnd();
}

SPAI.prototype.log = function(msg) {
    var ms = Date.now() - ShortPixelAI.loadTs;
    var log = ms + 'ms - ' + msg;
    if(ms < 2000) { //TODO remove - this is for puppeteer that doesn't show the first ~1 sec of console output
        ShortPixelAI.debugInfo['log'] += log + '\n';
        return;
    }
    if(ShortPixelAI.debugInfo['log'] !== ''){
        console.log(ShortPixelAI.debugInfo['log']);
        ShortPixelAI.debugInfo['log'] = '';
    }
    console.log(log);
}

SPAI.prototype.handleBody = function(){
    //console.log("handleBody " + ShortPixelAI.bodyCount);
    var theParent = jQuery('body');
    ShortPixelAI.bodyCount = 1; //Yes, there is a concurency problem but as this is only a trick to stop this from relaunching forever in case the placeholder is never loaded, it will work anyway
    try {
        ShortPixelAI.handleUpdatedImageUrls(true, theParent, true, false);
        ShortPixelAI.bodyHandled = true;
        ShortPixelAI.triggerEvent('spai-body-handled', theParent[0] );
        //console.log("body handled " + ShortPixelAI.bodyCount);
    } catch(error) {
        if(error == 'defer_all' && ShortPixelAI.bodyCount < 20) {
            //spai_settings.debug && ShortPixelAI.log("handleBody - DEFER ALL");
            setTimeout(ShortPixelAI.handleBody, 20 * ShortPixelAI.bodyCount );
            ShortPixelAI.bodyCount++;
        } else {
            spai_settings.debug && ShortPixelAI.log("handleBody - error " + error /* + (typeof e.stack !== 'undefined'? ' stack ' + e.stack : 'no stack')*/);
            throw error;
        }
    }
}

/**This was created for iPhone on which the placeholders are not .complete when the DOMLoaded event is triggered, on first page load on that phone.
 * defer_all is thrown by updateImageUrl.
 * @param theParent
 * @param hasMutationObserver
 * @param fromIntersection
 */
SPAI.prototype.handleUpdatedImageUrlsWithRetry = function(initial, theParent, hasMutationObserver, fromIntersection) {
    try {
        ShortPixelAI.handleUpdatedImageUrls(initial, theParent, hasMutationObserver, fromIntersection);
        //console.log("body handled " + ShortPixelAI.bodyCount);
    } catch(error) {
        if(error == 'defer_all' && ShortPixelAI.bodyCount < 30) {
            spai_settings.debug && ShortPixelAI.log("handleUpdatedImageUrlsWRetry - DEFER ALL");
            setTimeout( function() {
                ShortPixelAI.handleUpdatedImageUrls(initial, theParent, hasMutationObserver, fromIntersection);
            }, 20 * ShortPixelAI.bodyCount );
            ShortPixelAI.bodyCount++;
        } else {
            spai_settings.debug && ShortPixelAI.log("handleUpdatedImageUrlsWRetry - error " + error.description);
            throw error;
        }
    }

}

SPAI.prototype.handleUpdatedImageUrls = function(initial, theParent, hasMutationObserver, fromIntersection){
    //*DEBUG*/ShortPixelAI.record('count', 'observedMutations', 1);
    //*DEBUG*/if(ShortPixelAI.observedMutations > ShortPixelAI.stop) return;
    //*DEBUG*/ShortPixelAI.record('count', 'handledMutations', 1);
    //*DEBUG*/var parsed = 0, modified = 0, divModified = 0;
    /*
        if(theParent.is('body')) { //some of the excludes were not caught server side, catch them in browser and replace with the original URL
            for(var i = 0; i < spai_settings.excluded_selectors.length; i++) {
                var selector = spai_settings.excluded_selectors[i];
                jQuery(selector).each(function(elm){
                    var src = elm.attr('src');
                    if(typeof src !== 'undefined' &&  ShortPixelAI.containsPseudoSrc(src) >=0 ) {
                        var data = ShortPixelAI.parsePseudoSrc(elm.attr('href'));
                        elm.attr('src', data.src);
                    }
                });
            }
        }
    */
    if(!initial && !ShortPixelAI.bodyHandled) {
        //spai_settings.debug && ShortPixelAI.log("handleUpdatedImageUrls return 1");
        return; //not called through handleBody and handleBody wasn't yet successfully ran
    }
    if(theParent.is('img,amp-img')) {
        ShortPixelAI.updateImageUrl(theParent, hasMutationObserver, fromIntersection);
        return;
    }

    ShortPixelAI.updatedUrlsCount = 0;
    jQuery('img,amp-img', theParent).each(function(){
        var elm = jQuery(this);
        ShortPixelAI.updateImageUrl(elm, hasMutationObserver, fromIntersection);
    });

    var affectedTags = spai_settings.affected_tags !== '{{SPAI-AFFECTED-TAGS}}' ? JSON.parse(spai_settings.affected_tags)
        : ( typeof spai_affectedTags !== 'undefined' ? JSON.parse(spai_affectedTags) : {});
    //if(fromIntersection && (theParent.is('a') || theParent.is('div') || theParent.is('li') || theParent.is('header'))) { //will handle the div parents only if they're from intersection OR mutation
    if(fromIntersection) { //will handle the div parents only if they're from intersection OR mutation
        for(var tag in affectedTags) {
            if(theParent.is(tag)) {
                ShortPixelAI.updateDivUrl(theParent, hasMutationObserver, fromIntersection);
                break;
            }
        }
    }

    var affectedTagsList = '';
    for(var tag in affectedTags) {
        affectedTagsList += ',' + tag;
    }
    affectedTagsList = affectedTagsList.replace(/^,/, '');
    //jQuery('a,div,li,header,span,section,article', theParent).each(function(){
    jQuery(affectedTagsList, theParent).each(function(){
        //*DEBUG*/parsed = 1;
        var elm = jQuery(this);
        if(elm[0].tagName === 'VIDEO') {
            ShortPixelAI.updateVideoPoster(elm, hasMutationObserver);
        } else {
            ShortPixelAI.updateDivUrl(elm, hasMutationObserver, fromIntersection);
        }
    });

    //Check if integration is active and update lightbox URLs for each supported gallery
    //the media-gallery-link is present in custom solutions
    ShortPixelAI.updateAHrefForIntegration('CORE', theParent, 'a.media-gallery-link');
    //Envira
    ShortPixelAI.updateAHrefForIntegration('envira', theParent, 'a.envira-gallery-link');
    //Modula
    ShortPixelAI.updateAHrefForIntegration('modula', theParent, 'div.modula-gallery a[data-lightbox]');
    //Essential addons for Elementor
    ShortPixelAI.updateAHrefForIntegration('elementor-addons', theParent, 'div.eael-filter-gallery-wrapper a.eael-magnific-link');
    //Elementor
    ShortPixelAI.updateAHrefForIntegration('elementor', theParent, 'a[data-elementor-open-lightbox]');
    //Viba Portfolio
    ShortPixelAI.updateAHrefForIntegration('viba-portfolio', theParent, 'a.viba-portfolio-media-link');
    //Everest gallery - seems that it's not necessary, the url for the lightbox is parsed from the data:image on the lightbox's <img> creation
    //ShortPixelAI.updateAHrefForIntegration('everest', theParent, 'div.eg-each-item a[data-lightbox-type]');
    //WP Bakery Testimonials
    if(spai_settings.active_integrations['wp-bakery']) {
        jQuery('span.dima-testimonial-image', theParent).each(function(){
            ShortPixelAI.updateWpBakeryTestimonial(jQuery(this), hasMutationObserver);
        });
//        jQuery('div[data-ultimate-bg]', theParent).each(function(){
//            ShortPixelAI.updateWpBakeryTestimonial(jQuery(this));
//        });
    }
    if(spai_settings.active_integrations['social-pug']) {
        //Pinterest buttons are created from pseudo-src's (or api URLs with WebP) by Mediavine Grow, restore the original URL
        jQuery('a.dpsp-pin-it-button', theParent).each(function(){
            var elm = jQuery(this);
            var match = false;
            elm.attr('href', elm.attr('href').replace(/media=(data:image\/svg\+xml;.*)&url=/, function(matched, pseudoSrc, pos){
                match = true;
                return 'media=' + ShortPixelAI.parsePseudoSrc(pseudoSrc).src + '&url=';
            }));
            if(!match) {
                var regex = spai_settings.api_url.substr(0, spai_settings.api_url.lastIndexOf('/') + 1).replace('/', '\\/') + '[^\\/]+\\/';
                elm.attr('href', elm.attr('href').replace(new RegExp(regex), ''));
            }
        });
    }
    //Foo gallery
    ShortPixelAI.updateAHrefForIntegration('foo', theParent, 'div.fg-item a.fg-thumb');
    //NextGen
    if(spai_settings.active_integrations.nextgen) {
        //provide the URL to the fancybox (which doesn't understand the data: inline images) before it tries to preload the image.
        jQuery('a.ngg-fancybox, a.ngg-simplelightbox', theParent).each(function(){
            var elm = jQuery(this);
            if(!ShortPixelAI.isFullPseudoSrc(elm.attr('href'))) {
                return;
            }

            var data = ShortPixelAI.parsePseudoSrc(elm.attr('href'));
            elm.attr('href', ShortPixelAI.composeApiUrl(false, data.src, 'DEFER', false));
            if(elm.hasClass('ngg-fancybox')) {
                elm.mousedown(function(){
                    //this will calculate the width when the link is clicked just before fancybox uses the same algorithm to determine the width of the box and to preload the image...
                    ShortPixelAI.fancyboxUpdateWidth(elm);
                    return true;
                });
            }
        });
    }

    if(ShortPixelAI.updatedUrlsCount > 0) {
        spai_settings.debug && ShortPixelAI.log("trigger spai-block-handled event for " + ShortPixelAI.updatedUrlsCount + " URLs on " + theParent[0].tagName);
        ShortPixelAI.triggerEvent('spai-block-handled', theParent[0]);
    }

    //*DEBUG*/ShortPixelAI.parsedMutations += parsed;
    //*DEBUG*/ShortPixelAI.modifiedMutations += Math.max(modified, divModified);
};

SPAI.prototype.updateImageUrl = function(elm, hasMutationObserver, fromIntersection){
    ///*DEBUG*/parsed = 1;

    if (!ShortPixelAI.containsPseudoSrc(elm[0].outerHTML)){
        return;
    }
    if (typeof elm.attr('data-spai-upd') !== 'undefined'){
        return;
    }

    var exclusions = ShortPixelAI.is(elm, ShortPixelAI.EXCLUDED | ShortPixelAI.EAGER | ShortPixelAI.NORESIZE);
    if(spai_settings.native_lazy == '1') {
        exclusions |= ShortPixelAI.EAGER;
    }

    //flag 4 means eager, don't observe eager elements, just replace them right away
    if(!(exclusions & ShortPixelAI.EAGER) && !fromIntersection && !ShortPixelAI.elementInViewport(elm[0], ShortPixelAI.intersectionMargin)) {
        //spai_settings.debug && ShortPixelAI.log("Observing image: " + ShortPixelAI.parsePseudoSrc(elm[0].src).src);
        //will handle this with the intersectionObserver
        ShortPixelAI.intersectionObserver.observe(elm[0]);
        return;
    }

    var w = 0, h = 0, wPad = 0, hPad = 0;
    if((exclusions & (ShortPixelAI.EXCLUDED | ShortPixelAI.NORESIZE)) == 0) { //flags for do not resize and for exclude completely
        try {
            //var sizeInfo = ShortPixelAI.getSizes(elm[0], hasMutationObserver);
            var sizeInfo = ShortPixelAI.getSizesRecursive(elm, hasMutationObserver);
            //TODO if practice proves the need - discrete function for widths: Math.ceil( w / Math.ceil( w / 20 ) ) * Math.ceil( w / 20 )
            w = sizeInfo.width;
            h = sizeInfo.height;
            wPad = Math.round(w + sizeInfo.padding);
            hPad = Math.round(h + sizeInfo.padding_height);
        } catch (err) {
            if(!elm[0].complete) {
                //on iPhone on first page load, the placeholders are not rendered when it gets here, so defer the parsing of the page altogether
                throw 'defer_all';
            }
            if (typeof err.type !== 'undefined' && err.type == 'defer' && hasMutationObserver && !(exclusions & ShortPixelAI.EAGER)) {
                spai_settings.debug && ShortPixelAI.log("Defer " + err.cause + ' ' + ShortPixelAI.parsePseudoSrc(elm[0].src).src);
                // binding the mouseover event on deferred elements (e.g. which were hid on load)
                if ( !elm.is( ':visible' ) && !!spai_settings.hover_handling ) {
                    spai_settings.debug && ShortPixelAI.log( 'Attach mouseover to it' );
                    elm.off( 'mouseover', ShortPixelAI.mouseOverHandler ); //make sure we don't attach several times
                    elm.on( 'mouseover', ShortPixelAI.mouseOverHandler );
                }

                return;
            }
        }
    }

    ShortPixelAI.record('count', 'modifiedImg', 1);
    ShortPixelAI.record('logX', 'modifiedImgURL', elm[0]);
    //*DEBUG*/modified = 1;

    //TODO future dev: clone()/replaceWith()
    //var newElm = elm.clone();

    if(wPad && elm.attr('width') && wPad < elm.attr('width') ) {
        if(elm.attr('height')) {
            //make it proportionally smaller
            elm.attr('height', Math.round(elm.attr('height') * wPad / elm.attr('width')))
        }
        elm.attr('width', wPad);
    }
    else if(hPad && elm.attr('height') && hPad < elm.attr('height')) {
        elm.attr('height', hPad);
    }

    var origData = ShortPixelAI.updateSrc(elm, 'src', w, h, (spai_settings.method == 'src' || spai_settings.method == 'both') && ((exclusions & ShortPixelAI.EXCLUDED) == 0));
    ShortPixelAI.updateSrc(elm, 'data-src', false, false, ((exclusions & ShortPixelAI.EXCLUDED) == 0));
    ShortPixelAI.updateSrc(elm, 'data-large_image', false, false, ((exclusions & ShortPixelAI.EXCLUDED) == 0));
    if(spai_settings.active_integrations.envira) {
        ShortPixelAI.updateSrc(elm, 'data-envira-src', false, false, ((exclusions & ShortPixelAI.EXCLUDED) == 0));
        ShortPixelAI.updateSrc(elm, 'data-safe-src', w, h, ((exclusions & ShortPixelAI.EXCLUDED) == 0));
    }
    if(spai_settings.active_integrations.foo) {
        ShortPixelAI.updateSrc(elm, 'data-src-fg', w, h, ((exclusions & ShortPixelAI.EXCLUDED) == 0));
    }
    if(spai_settings.method == 'src') {
        ShortPixelAI.removeSrcSet(elm);
    } else {
        ShortPixelAI.updateSrcSet(elm, w, origData);
    }
    if(spai_settings.native_lazy == '1') {
        elm.attr('loading', 'lazy');
    }
    //elm.replaceWith(newElm);
    ShortPixelAI.elementUpdated(elm, w);
    elm.off('mouseover', ShortPixelAI.mouseOverHandler);
};

SPAI.prototype.mouseOverHandler = function() {
    var $this = jQuery( this );
    spai_settings.debug && ShortPixelAI.log("Mouseover triggered on " + ShortPixelAI.parsePseudoSrc(this.src).src);

    if ( $this.is( ':visible' ) ) {
        spai_settings.debug && ShortPixelAI.log("updateImageURL");

        var width  = $this.width(),
            height = $this.height();
        ShortPixelAI.updateImageUrl( $this, true, true );
    }
}

SPAI.prototype.updateWpBakeryTestimonial = function(elm, hasMutationObserver) {
    if (typeof elm.attr('data-spai-upd') !== 'undefined'){
        return;
    }
    ShortPixelAI.updateAttr(elm, 'data-element-bg');

    var w = 0, h = 0, sizes = [];
    var exclusions = ShortPixelAI.is(elm, ShortPixelAI.EXCLUDED | ShortPixelAI.NORESIZE | ShortPixelAI.EAGER);
    if((exclusions & (ShortPixelAI.EXCLUDED | ShortPixelAI.NORESIZE)) == 0) { //do not resize and exclude altogether
        try {
            //TODO if practice proves the need - discrete function for widths: Math.ceil( w / Math.ceil( w / 20 ) ) * Math.ceil( w / 20 )
            //sizes = ShortPixelAI.getSizes(elm[0], hasMutationObserver);
            sizes = ShortPixelAI.getSizesRecursive(elm, hasMutationObserver);
            w = sizes.width;
            h = sizes.height;
        } catch (err) {
            if(typeof err.type !== 'undefined' && err.type == 'defer' && hasMutationObserver && !(exclusions & ShortPixelAI.EAGER)) {
                return;
            }
        }
    }
    ShortPixelAI.updateInlineStyle(elm, w, h, (exclusions & ShortPixelAI.EXCLUDED) == 0);
    ShortPixelAI.elementUpdated(elm, w);
};

SPAI.prototype.updateVideoPoster = function(elm, hasMutationObserver) {
    if (typeof elm.attr('data-spai-upd') !== 'undefined'){
        return;
    }
    var w = 0, h = 0, sizes = [];
    var exclusions = ShortPixelAI.is(elm, ShortPixelAI.EXCLUDED | ShortPixelAI.NORESIZE | ShortPixelAI.EAGER);
    if((exclusions & (ShortPixelAI.EXCLUDED | ShortPixelAI.NORESIZE)) == 0) { //do not resize and exclude altogether
        try {
            sizes = ShortPixelAI.getSizesRecursive(elm, hasMutationObserver);
            w = sizes.width;
            h = sizes.height;
        } catch (err) {
            if(typeof err.type !== 'undefined' && err.type == 'defer' && hasMutationObserver && !(exclusions & ShortPixelAI.EAGER)) {
                return;
            }
        }
    }
    ShortPixelAI.updateSrc(elm, 'poster', w, h, (exclusions & ShortPixelAI.EXCLUDED) == 0);
    ShortPixelAI.elementUpdated(elm, w);
};

SPAI.prototype.updateDivUrl = function(elm, hasMutationObserver, fromIntersection) {
    if (typeof elm.attr('data-spai-upd') !== 'undefined'){
        return;
    }
    if(   typeof elm.attr('src') === 'undefined' && typeof elm.attr('data-src') === 'undefined' && typeof elm.attr('data-thumb') === 'undefined'
        && !ShortPixelAI.getBackgroundPseudoImages(elm.attr('style'))) {
        return;
    }
    if(!fromIntersection && !ShortPixelAI.elementInViewport(elm[0], ShortPixelAI.intersectionMargin)) {
        //will handle this with the intersectionObserver
        ShortPixelAI.intersectionObserver.observe(elm[0]);
        return;
    }
    var w = 0, h = 0, sizes = [];
    var exclusions = ShortPixelAI.is(elm, ShortPixelAI.EXCLUDED | ShortPixelAI.NORESIZE | ShortPixelAI.EAGER);
    if((exclusions & (ShortPixelAI.EXCLUDED | ShortPixelAI.NORESIZE)) == 0) {
        try {
            //TODO if practice proves the need - discrete function for widths: Math.ceil( w / Math.ceil( w / 20 ) ) * Math.ceil( w / 20 )
            //sizes = ShortPixelAI.getSizes(elm[0], hasMutationObserver);
            sizes = ShortPixelAI.getSizesRecursive(elm, hasMutationObserver);
            w = sizes.width;
            h = sizes.height;
        } catch (err) {
            if(typeof err.type !== 'undefined' && err.type == 'defer' && hasMutationObserver && !(exclusions & ShortPixelAI.EAGER)) {
                return;
            }
        }
    }
    ShortPixelAI.updateSrc(elm, 'src', w, h, ((exclusions & ShortPixelAI.EXCLUDED) == 0));
    ShortPixelAI.updateSrc(elm, 'data-src', w, h, ((exclusions & ShortPixelAI.EXCLUDED) == 0));
    //*DEBUG*/divModified =
    ShortPixelAI.updateSrc(elm, 'data-thumb', false, false, ((exclusions & ShortPixelAI.EXCLUDED) == 0));
    //*DEBUG*/? 1 : 0;
    //ShortPixelAI.updateInlineStyle(elm, w, Math.ceil(sizes.height), true);
    ShortPixelAI.updateInlineStyle(elm, w, h, ((exclusions & ShortPixelAI.EXCLUDED) == 0));
    ShortPixelAI.elementUpdated(elm, w);
};

SPAI.prototype.updateAHref = function(elm, hasMutationObserver, fromIntersection) {
    ShortPixelAI.updateAttr(elm, 'href');
};

SPAI.prototype.updateAttr = function(elm, attr) {
    if (typeof elm.attr('data-spai-upd') !== 'undefined'){
        return;
    }
    if( typeof elm.attr(attr) === 'undefined' ) {
        return;
    }
    var data = ShortPixelAI.updateSrc(elm, attr, window.screen.availWidth, window.screen.availHeight, !ShortPixelAI.is(elm, ShortPixelAI.EXCLUDED), true);
    ShortPixelAI.elementUpdated(elm, data.newWidth);
};

SPAI.prototype.is = function(elm, types) {
    var excluded = 0;
    if(types | ShortPixelAI.EAGER) {
        for(var i = 0; i < spai_settings.eager_selectors.length; i++) { //.elementor-section-stretched img.size-full
            var selector = spai_settings.eager_selectors[i];
            try {if(elm.is(selector)) excluded |= ShortPixelAI.EAGER;} catch (xc){spai_settings.debug && ShortPixelAI.log("eager:" + xc.message)} //we don't bother about wrong selectors at this stage
        }
    }

    if(types | ShortPixelAI.EXCLUDED) {
        for(var i = 0; i < spai_settings.excluded_selectors.length; i++) { //.elementor-section-stretched img.size-full
            var selector = spai_settings.excluded_selectors[i];
            try {if(elm.is(selector)) excluded |= ShortPixelAI.EXCLUDED;} catch (xc){spai_settings.debug && ShortPixelAI.log("excluded:" + xc.message)}
        }
    }

    if(types | ShortPixelAI.NORESIZE) {
        for(var i = 0; i < spai_settings.noresize_selectors.length; i++) { //.elementor-section-stretched img.size-full
            var selector = spai_settings.noresize_selectors[i];
            try {if(elm.is(selector)) excluded |= ShortPixelAI.NORESIZE;} catch (xc){spai_settings.debug && ShortPixelAI.log("noresize:" + xc.message)}
        }
    }
    return excluded;
};

SPAI.prototype.updateAHrefForIntegration = function(integration, theParent, query) {
    if(integration == 'CORE' || spai_settings.active_integrations[integration]) {
        jQuery(query, theParent).each(function(){
            var elm = jQuery(this);
            ShortPixelAI.updateAHref(elm);
        });
    }
};

SPAI.prototype.setupDOMChangeObserver = function() {
    //setup DOM change observer
    //TODO vezi de ce nu merge la localadventurer pe versiunea polyfill MutationObserver - caruselul de sus
    //TODO adauga optiune in settings sa nu foloseasca polyfill si sa inlocuiasca din prima (pentru browserele vechi - daca polyfill-ul lui MutationObserver nu se descurca)
    //TODO disconnect my mutationObserver when I make changes. (https://stackoverflow.com/questions/44736209/mutationobserver-ignore-a-dom-action)
    ShortPixelAI.mutationObserver = new MutationObserver(function(mutations) {
        if(ShortPixelAI.sniperOn) return;
        mutations.forEach(function(mutation) {
            //*DEBUG*/ console.log("Mutation type: " + mutation.type + " target Id: " + jQuery(mutation.target).attr("id") + " target: " + jQuery(mutation.target).html());
            if(mutation.type === 'attributes' && mutation.attributeName === 'id') {
                // a hack to mitigate the fact that the jQuery .init() method is triggering DOM modifications. Happens in jQuery 1.12.4 in the Sizzle function. Comment of jQuery Devs:
                // qSA works strangely on Element-rooted queries
                // We can work around this by specifying an extra ID on the root
                // and working up from there (Thanks to Andrew Dupont for the technique)
                // IE 8 doesn't work on object elements
                return;
            }

            //new nodes added by JS
            if(mutation.addedNodes.length) {
                //*DEBUG*/ console.log(mutation.addedNodes[0]);
                for(var i = 0; i < mutation.addedNodes.length; i++) {
                    //TODO if practice proves necessary: window.requestIdleCallback()?
                    try {
                        ShortPixelAI.handleUpdatedImageUrls(false, jQuery(mutation.addedNodes[i]), true, false);
                    }catch(error) {
                        if (error == 'defer_all') { //defer_all means the images are not ready.
                            setTimeout(ShortPixelAI.handleBody, 20 * ShortPixelAI.bodyCount);
                        } else {
                            throw error; //not ours
                        }
                    }
                }
            }

            //attributes changes
            if(mutation.type == 'attributes') {
                var attrClass = mutation.target.getAttribute('class');
                attrClass = (typeof attrClass === 'undefined' || attrClass === null) ? '' : attrClass;
                if(mutation.target.nodeName === 'BODY' && ShortPixelAI.containsPseudoSrc(attrClass) > 0) {
                    //this is because the body seems to become a zombie and fires mutations at will when under Developer Console
                    return;
                }
                if(jQuery(mutation.target).attr('id') == 'fancybox-wrap' && ShortPixelAI.fancyboxId != ShortPixelAI.fancyboxHooked) {
                    //NextGen specific (which uses fancybox for displaying a gallery slideshow popup)
                    ShortPixelAI.hookIntoFancybox(mutation.target);
                } else {
                    if(ShortPixelAI.timeOutHandle) {
                        clearTimeout(ShortPixelAI.timeOutHandle);
                        if((new Date()).getTime() - ShortPixelAI.mutationsLastProcessed > 100) {
                            ShortPixelAI.processMutations();
                        }
                    }
                    else {
                        ShortPixelAI.mutationsLastProcessed = (new Date()).getTime();
                    }
                    /*DEBUG*/ShortPixelAI.observedMutations++;

                    //images having width 0 are deferred for further replacement, so keep a list of mutations and analyze them with a delay (setTimeout)
                    ShortPixelAI.mutationsList[ShortPixelAI.xpath(mutation.target)] = {target: mutation.target, time: (new Date).getTime()};
                    ShortPixelAI.timeOutHandle = setTimeout(ShortPixelAI.processMutations, 50);
                }
            }
        });
    });
    var target = document.querySelector('body');
    var config = { attributes: true, childList: true, subtree: true, characterData: true }
    ShortPixelAI.mutationObserver.observe(target, config);
};

SPAI.prototype.processMutations = function() {
    //TODO if practice proves necessary: window.requestIdleCallback()?
    var mutationsLeft = 0;
    for(var mutationTarget in ShortPixelAI.mutationsList) {
        var mutationTargetJQ = jQuery(ShortPixelAI.mutationsList[mutationTarget].target);
        if(ShortPixelAI.mutationsList[mutationTarget].time + 50 > (new Date).getTime()) {
            //mutations having less than 50ms of age, don't process them yet as they might not be ready - for example a jQuery animate.
            mutationsLeft++;
            continue;
        }
        var outerHTML = mutationTargetJQ[0].outerHTML;
        if (mutationTargetJQ.length && ShortPixelAI.containsPseudoSrc(outerHTML) > 0) { //Previously: 'src="data:image/gif;u=') > 0) {
            //console.log(" PROCESS MUTATIONS " + mutationTarget);
            //Changed fromIntersection to false to load the modifications images lazily too.
            //TODO TEST well (ref.: HS 986527864)
            ShortPixelAI.handleUpdatedImageUrlsWithRetry(false, mutationTargetJQ, true, false);
            if (outerHTML.indexOf('background') > 0) {
                ShortPixelAI.updateInlineStyle(mutationTargetJQ, false, false, !ShortPixelAI.is(mutationTargetJQ, ShortPixelAI.EXCLUDED));
            }
        }
        delete ShortPixelAI.mutationsList[mutationTarget];
    }
    ShortPixelAI.mutationsLastProcessed = (new Date()).getTime();
    if(mutationsLeft > 0) {
        ShortPixelAI.timeOutHandle = setTimeout(ShortPixelAI.processMutations, 50);
    }
}

SPAI.prototype.setupIntersectionObserverAndParse = function() {
    var options = {
        rootMargin: ShortPixelAI.intersectionMargin + 'px',
        threshold: 0
    };
    ShortPixelAI.intersectionObserver = new IntersectionObserver(function(entries, observer){
        //spai_settings.debug && ShortPixelAI.log("Intersection Observer called, scroll: " + (ShortPixelAI.getScroll().join(', ')));
        entries.forEach(function(entry) {
            if(entry.isIntersecting) {
                var elm = jQuery(entry.target);
                //spai_settings.debug && ShortPixelAI.log(elm[0].nodeName + " is intersecting - " + ShortPixelAI.parsePseudoSrc(elm[0].src).src);
                ShortPixelAI.handleUpdatedImageUrlsWithRetry(false, elm, true, true);
                if (entry.target.outerHTML.indexOf('background') > 0) {
                    ShortPixelAI.updateInlineStyle(elm, false, false, !ShortPixelAI.is(elm, ShortPixelAI.EXCLUDED));
                }
                observer.unobserve(entry.target);
                ShortPixelAI.triggerEvent('spai-element-handled', entry.target);
            } else if (spai_settings.debug){
                var elm = jQuery(entry.target);
                ShortPixelAI.log(elm[0].nodeName + " is NOT intersecting - " + ShortPixelAI.parsePseudoSrc(elm[0].src).src);
            }
        });
    }, options);

    jQuery("style").each(function () {
        var e = jQuery(this);
        try  {
            var t = ShortPixelAI.replaceBackgroundPseudoSrc(e.html());
            if(t.replaced) {
                e.html(t.text);
            }
        } catch (ie) {
            spai_settings.debug && ShortPixelAI.log('error ' + ie.description);
        }
    });
    //initial parse of the document
    //style blocks, wherever they might be
    jQuery('style').each(function(){
//      jQuery('style:not([data-spai-upd])').each(function(){
        var elm = jQuery(this);
        //var css = elm.html();
        try  {
            var result = ShortPixelAI.replaceBackgroundPseudoSrc(elm.html());
            if(result.replaced) {
                elm.html(result.text);
            }
        } catch (ierror) {
            //on Internet Explorer jQuery throws undefined function 'replace' for some style tags.
            spai_settings.debug && ShortPixelAI.log('Error parsing styles: ' + ierror.description + ' (STYLE TAG: ' + elm.html() + ')');
        }
    });

    //check the stylesheets, some optimizers (for example Swift Performance) extracts the inline CSS into .css files
    if(!navigator.platform || !/iPad|iPhone|iPod/.test(navigator.platform)) { //but NOT on iPhones, it breaks the page
        for (var styleSheet in document.styleSheets) {
            var style = document.styleSheets[styleSheet];
            try {
                for (var ruleKey in style.rules) {
                    var rule = style.rules[ruleKey];
                    if (typeof rule.cssText !== 'undefined' && ShortPixelAI.containsPseudoSrc(rule.cssText) > 0) {
                        var result = ShortPixelAI.replaceBackgroundPseudoSrc(rule.cssText);
                        if (result.replaced) {
                            rule.cssText = result.text;
                            document.styleSheets[styleSheet].removeRule(ruleKey);
                            document.styleSheets[styleSheet].insertRule(result.text, ruleKey);
                        }
                    }
                }
            } catch (dex) {
                //sometimes it throws this exception:
                //DOMException: Failed to read the 'rules' property from 'CSSStyleSheet': Cannot access rules at CSSStyleSheet.invokeGetter
                //console.log(dex.message);
            }
        }
    }
    //body
    ShortPixelAI.handleBody();

    //setup the mutation observer here too, because if the IntersectionObserver polyfill is needed, it should be done after that one is loaded.
    if(typeof window.MutationObserver !== 'function') {
        jQuery.getScript(spai_settings.plugin_url + '/assets/js/MutationObserver.min.js?' + spai_settings.version, ShortPixelAI.setupDOMChangeObserver);
    } else {
        ShortPixelAI.setupDOMChangeObserver();
    }
};

SPAI.prototype.replaceBackgroundPseudoSrc = function(text){
    var replaced = false;
    //regexps are identical, need to duplicate them because the first will use is internal pointer to replace all
    text.replace(        /background(-image|)\s*:([^;]*[,\s]|\s*)url\(['"]?(data:image\/svg\+xml[^'"\)]*?)(['"]?)\)/gm, function(item){
        var oneMatcher = /background(-image|)\s*:([^;]*[,\s]|\s*)url\(['"]?(data:image\/svg\+xml[^'"\)]*?)(['"]?)\)/m;
        var match = oneMatcher.exec(item);
        var parsed = ShortPixelAI.parsePseudoSrc(match[3]);
        //devicePixelRatio is applied in composeApiUrl
        var screenWidth = window.screen.width;
        var setMaxWidth = spai_settings.backgrounds_max_width ? spai_settings.backgrounds_max_width : 99999;
        var newSrc = ShortPixelAI.composeApiUrl(false, parsed.src, Math.min(parsed.origWidth, screenWidth, setMaxWidth), false);
        text = text.replace(match[3], newSrc);
        replaced = true;
    });
    return {text: text, replaced: replaced};
};

//TODO sa luam de la WP Rocket versiunea mai versatila?
SPAI.prototype.elementInViewport = function(el, threshold) {
    if(!( el.offsetWidth || el.offsetHeight || el.getClientRects().length )) {
        return false;
    }
    var rect = el.getBoundingClientRect();

    return (
        rect.bottom + threshold    >= 0
        && rect.right + threshold   >= 0
        && rect.top - threshold <= (window.innerHeight || document.documentElement.clientHeight)
        && rect.left - threshold <= (window.innerWidth || document.documentElement.clientWidth)
    );
};

SPAI.prototype.hookIntoFancybox = function(theParent){
    if(ShortPixelAI.fancyboxId.length == 0 || ShortPixelAI.fancyboxHooked !== 'none') {
        return;
    }
    //console.log("HookIntoFancybox");
    var theOverlay = jQuery(theParent);
    var elm = jQuery('a#fancybox-right', theOverlay);
    elm.mousedown(function(e){
        var newId = ShortPixelAI.fancyboxChangeId(1);
        //console.log("right " + newId);
        var nextElm = jQuery('div#' + newId + " a.ngg-fancybox");
        if(nextElm.length) {
            ShortPixelAI.fancyboxUpdateWidth(nextElm);
        }
    });
    var elm = jQuery('a#fancybox-left', theOverlay);
    elm.mousedown(function(e){
        var newId = ShortPixelAI.fancyboxChangeId(-1);
        //console.log("left " + newId);
        var prevElm = jQuery('div#' + newId + " a.ngg-fancybox");
        if(prevElm.length) {
            ShortPixelAI.fancyboxUpdateWidth(prevElm);
        }
    });
    ShortPixelAI.fancyboxHooked = ShortPixelAI.fancyboxId;
};

SPAI.prototype.fancyboxChangeId = function(delta) {
    var parts = ShortPixelAI.fancyboxId.match(/(.*)([0-9]+)$/);
    return parts[1] + (parseInt(parts[2]) + delta);
};

SPAI.prototype.composeApiUrl = function(doRegister, src, w, h) {
    if(!src.match(/^http[s]{0,1}:\/\/|^\/\//)) {
        if(src.startsWith('/')) {
            if(typeof ShortPixelAI.aHref === 'undefined') {
                ShortPixelAI.aHref = document.createElement( 'a' );
            }
            ShortPixelAI.aHref.href = spai_settings.site_url;
            src = ShortPixelAI.aHref.protocol + "//" + ShortPixelAI.aHref.hostname + src;
        } else {
            var href = window.location.href.split('#')[0].split('?')[0]; //get rid of hash and query string
            if(!href.endsWith('/')) {
                //fix the problem of relative paths to paths not ending in '/' - remove the last base path item
                var hrefp = href.split('/');
                hrefp.pop();
                href = hrefp.join('/') + '/';
            }
            src = href + src;
            if(src.indexOf('..') > 0) {
                //normalize the URL
                var l = document.createElement("a");
                l.href = src;
                src = l.protocol + "//" + l.hostname + (l.pathname.startsWith('/') ? '' : '/') + l.pathname + l.search + l.hash;

            }
        }
    }

    //get the image extension
    var extensionRegEx = /(?:\.([^.\/\?]+))(?:$|\?.*)/;
    extensionRegEx.lastIndex = 0;
    var extensionMatches = extensionRegEx.exec( src );
    var ext = typeof extensionMatches === 'object' && extensionMatches !== null && typeof extensionMatches[ 1 ] === 'string' && extensionMatches[ 1 ] !== '' ? extensionMatches[ 1 ] : 'jpg';
    ext = ext === 'jpeg' ? 'jpg' : ext;
    if ( ext === 'svg' ) {
        w = h = 0; // no need to add size parameters to a SVG...
    }

    if(w > 1 && w < 99999) {
        var pixelRatio = (typeof window.devicePixelRatio === 'undefined') ? 1 : window.devicePixelRatio;

        //TODO if practice proves the need - discrete function for widths: Math.ceil( w / Math.ceil( w / 20 ) ) * Math.ceil( w / 20 )

        w = Math.round(w * pixelRatio);
        h = h ? Math.round(h * pixelRatio) : undefined;
        //use a register to keep all the SRCs already resized to a specific sizes, if it's already there with a larger width, then use that width, if not add/update it.
        if(ShortPixelAI.urlRegister[src] === undefined || ShortPixelAI.urlRegister[src].width < w ) {
            if(doRegister) { //only the img src's are registered as the others might not get loaded...
                ShortPixelAI.urlRegister[src] = {width: w, height: h};
            }
        } else if (   !ShortPixelAI.urlRegister[src].height && !h
                   || !!ShortPixelAI.urlRegister[src].height && !!h && (Math.abs(1.0 - w * ShortPixelAI.urlRegister[src].height / h / ShortPixelAI.urlRegister[src].width ) < 0.005))  { //same aspect ratio
            h = ShortPixelAI.urlRegister[src].height;
            w = ShortPixelAI.urlRegister[src].width;
        }

        var apiUrl = spai_settings.api_url.replace( "%WIDTH%", "" + w + (h ? "+h_" + h : ""));
    }
    else {
        var apiUrl = spai_settings.api_url.replace( "w_%WIDTH%" + spai_settings.sep, '' );
        apiUrl = apiUrl.replace( "w_%WIDTH%", '' ); //maybe it's the last param, no separator...
    }

    switch ( ext ) {
        case 'svg':
            apiUrl = spai_settings.serve_svg ? ( spai_settings.api_short_url + '/' + src ) : src;
            break;
        case 'png':
            apiUrl = apiUrl + ( ShortPixelAI.supportsWebP && spai_settings.extensions_to_webp.png ? spai_settings.sep + 'to_webp' : '' ) + '/' + src;
            break;
        case 'jpg':
            apiUrl = apiUrl + ( ShortPixelAI.supportsWebP && spai_settings.extensions_to_webp.jpg ? spai_settings.sep + 'to_webp' : '' ) + '/' + src;
            break;
        case 'gif':
            apiUrl = apiUrl + ( ShortPixelAI.supportsWebP && spai_settings.extensions_to_webp.gif ? spai_settings.sep + 'to_webp' : '' ) + '/' + src;
            break;
        default: //just to be sure...
            apiUrl = apiUrl + ( ShortPixelAI.supportsWebP? spai_settings.sep + 'to_webp' : '' ) + '/' + src;
            break;
    }

    return apiUrl;
};

SPAI.prototype.isFullPseudoSrc = function(pseudoSrc) {
    //return pseudoSrc.indexOf('data:image/gif;u=') >= 0;
    return this.parsePseudoSrc(pseudoSrc).full;
};

SPAI.prototype.containsPseudoSrc = function(pseudoSrc) {
    //return pseudoSrc.indexOf('data:image/gif;u=') >= 0;
    return pseudoSrc.indexOf('data:image/svg+xml;') >= 0;
};

/**
 * New implementation
 *
 * @param {string} pseudoSrc
 * @returns {{origHeight: number, src: boolean, origWidth: number, full: boolean}}
 */
SPAI.prototype.parsePseudoSrc = function( pseudoSrc ) {
    var prepared     = {
            src        : false,
            origWidth  : 0,
            origHeight : 0,
            full       : false
        },
        base64RegExp = /([A-Za-z0-9+/]{4})*([A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{2}==)?$/;

    if(typeof pseudoSrc === 'undefined' || !this.containsPseudoSrc(pseudoSrc)) {
        return prepared;
    }

    var $svgElement, svgDecoded, svgEncoded;
    pseudoSrc = pseudoSrc.trim();

    if ( typeof pseudoSrc === 'string' ) {
        svgEncoded = pseudoSrc.match( base64RegExp );
        svgEncoded = svgEncoded.length > 0 ? svgEncoded[ 0 ] : undefined;

        if ( typeof svgEncoded === 'string' ) {
            svgDecoded = atob( svgEncoded );
        }
    }

    try {
        if ( typeof svgDecoded === 'string' ) {
            $svgElement = jQuery( svgDecoded );
        }
    }
    catch ( x ) {
        spai_settings.debug && ShortPixelAI.log( 'svgDecoded: ' + svgDecoded );
        return prepared;
    }

    if ( $svgElement instanceof jQuery ) {
        var metaData = {
            url    : $svgElement.data( 'u' ),
            width  : $svgElement.data( 'w' ),
            height : $svgElement.data( 'h' )
        };

        prepared.src = metaData.url === undefined || metaData.url === '' ? prepared.src : ShortPixelAI.urldecode( metaData.url );

        if ( prepared.src !== '' && typeof prepared.src === 'string' ) {
            if ( prepared.src.lastIndexOf( '//', 0 ) === 0 ) {
                // if the url doesn't have the protocol, use the current one
                prepared.src = window.location.protocol + prepared.src;
            }
        }

        prepared.origWidth = metaData.width === undefined || metaData.width === '' ? prepared.origWidth : metaData.width;
        prepared.origHeight = metaData.height === undefined || metaData.height === '' ? prepared.origHeight : metaData.height;
        prepared.full = typeof prepared.src === 'string' && prepared.src !== '';
    }

    return prepared;
};

/**
 * OLD IMPLEMENTATION
 *
 * @param pseudoSrc
 * @returns {{origHeight: number, src: boolean, origWidth: number, full: number}|boolean}
 *
SPAI.prototype.parsePseudoSrc = function(pseudoSrc) {
    var src = false;
    var origWidth = 0, origHeight = 0, full = false;
    var full = ShortPixelAI.isFullPseudoSrc(pseudoSrc) ? 1 : 0;

    if(full) {
        var parts = pseudoSrc.split(',');
        if(parts.length == 2) {
            full = true;
            pseudoSrc = parts[0];
            var subparts = pseudoSrc.split(';');
            subparts.shift();
        } else {
            return false;
        }
    } else {
        var subparts = pseudoSrc.split(';');
    }
    if(subparts[0].indexOf('u=') == 0) {
        src = ShortPixelAI.urldecode(atob(subparts[0].substring(2)));
        if(src.lastIndexOf('//', 0) == 0) {
            //if the url doesn't have the protocol, use the current one
            src = window.location.protocol + src;
        }
    }
    if(subparts.length >= 2 + full) {// if full we have the last part: base64 so count one more
        var p2 = subparts[1].split('=');
        if(p2.length == 2 && p2[0] == 'w') {
            origWidth = p2[1];
        } else {
            origWidth = 99999;
        }
    }
    if(subparts.length >= 3 + full) {
        var p2 = subparts[2].split('=');
        if(p2.length == 2 && p2[0] == 'h') {
            origHeight = p2[1];
        } else {
            origHeight = 99999;
        }
    }
    return { src: src, origWidth: origWidth, origHeight: origHeight, full: full};
};
*/

SPAI.prototype.updateSrc = function(elm, attr, w, h, isApi, maxHeight) {
    var pseudoSrc = elm.attr('data-spai-' + attr + '-meta');
    if(typeof pseudoSrc === 'undefined') {
        var pseudoSrc = elm.attr(attr);
        if(typeof pseudoSrc === 'undefined') {
            return false;
        }
    }
    var data = ShortPixelAI.parsePseudoSrc(pseudoSrc);
    var src = data ? data.src : false;
    if(!src) {
        return false;
    }

    data.crop = false;
    var forceCrop = elm.attr('data-spai-crop');
    if( (typeof forceCrop !== 'undefined') && (forceCrop === 'true')) {
        data.crop = true;
    }

    if(typeof maxHeight === 'undefined' || !maxHeight) {
        //make sure that if the image container has an imposed (min)height, it's taken into account
        if( h > data.origHeight * w / data.origWidth ) {
            if(spai_settings.crop === "1") {
                data.crop = true;
            } else {
                w = Math.round(data.origWidth * h / data.origHeight);
            }
        }
    } else {
        //make sure that if the image container has an imposed (max)height, it's taken into account
        if( h < data.origHeight * w / data.origWidth ) {
            w = Math.round(data.origWidth * h / data.origHeight);
        }
    }
    data.newWidth = data.origWidth > 1 ? Math.min(data.origWidth, w) : w;
    var newSrc = isApi ? ShortPixelAI.composeApiUrl(attr == 'src' && elm.is('img'), src, data.newWidth, data.crop ? h : false) : src;


    //load images and wait until they've succesfully loaded
    elm.attr(attr, newSrc);
    elm.removeAttr('data-spai-' + attr + '-meta');

    return data;
};

SPAI.prototype.updateSrcSet = function(elm, w, origData) {
    var srcSet = elm.attr('srcset');
    var sizes = elm.attr('sizes');
    var updated = ShortPixelAI.parsePseudoSrcSet(srcSet, sizes, w, origData);
    if(updated.srcSet.length) {
        elm.attr('srcset', updated.srcSet);
    }
    if(updated.sizes.length) {
        elm.attr('sizes', updated.sizes);
    }
};

SPAI.prototype.parsePseudoSrcSet = function(srcSet, sizes, w, origData) {
    var newSrcSet = '';
    var newSizes = '';
    if(srcSet) {
        var srcList = srcSet.match(/[^\s,][^\s]+(\s+[0-9wpx]+\s*,?|\s*,|\s*$)/g); //split(", ");
        for(var i = 0; i < srcList.length; i++) {
            var item = srcList[i].replace(/,$/, '').trim();
            var newItem = '';
            var itemParts = item.split(/\s+/);
            if(this.isFullPseudoSrc(itemParts[0])) {
                var itemParts = item.split(/\s+/);
                if(itemParts.length >= 2) {
                    var itemData = ShortPixelAI.parsePseudoSrc(itemParts[0]);
                    if(itemData.src) {
                        newItem = ShortPixelAI.composeApiUrl(false, itemData.src, false, false) + " " + itemParts[1];
                    }
                    if(w == parseInt(itemParts[1])) {
                        origData = false; //no need to add the original as it's already in the srcset
                    }
                    else if(origData && w < parseInt(itemParts[1])) {
                        newSrcSet += ShortPixelAI.composeApiUrl(false, origData.src, w, false) + " " + w + 'w,';
                        origData = false;
                    }
                }
            }
            if(!newItem.length) {
                newItem = item;
            }
            newSrcSet += newItem + ', ';
        }
        newSrcSet = newSrcSet.replace(/,+\s+$/, '');
    }
    else if (origData && (spai_settings.method == 'srcset') && w < origData.origWidth * 0.9) {
        newSrcSet = ShortPixelAI.composeApiUrl(false, origData.src, w, false) + " " + w + 'w, ' + origData.src + " " + origData.origWidth + "w";
        newSizes = Math.round(100 * w / origData.origWidth) + "vw, 100vw";
    }
    return {srcSet: newSrcSet, sizes: newSizes};
}

SPAI.prototype.removeSrcSet = function(elm) {
    var srcSet = elm.attr('srcset');
    if(typeof srcSet !== 'undefined' && srcSet.length) {
        elm.attr('srcset', '');
        elm.attr('sizes', '');
    }
};

SPAI.prototype.updateInlineStyle = function(elm, w, h, isApi) {
    var style = elm.attr('style');
    var pseudoSrc = ShortPixelAI.getBackgroundPseudoImages(elm.attr('style'));
    var affectedData = [];

    if ( !pseudoSrc ) return;

    for ( var index = 0; index < pseudoSrc.length; index++ ) {
        var data = ShortPixelAI.parsePseudoSrc(pseudoSrc[index]);
        var src = data ? data.src : false;

        if(src){
            //remove the " from beginning and end, happens when the original URL is surrounded by &quot;
            while(src.charAt(0) == '"'){
                src = src.substring(1);
            }
            while(src.charAt(src.length-1)=='"') {
                src = src.substring(0,src.length-1);
            }
        } else {
            return false;
        }

        //devicePixelRatio is applied in composeApiUrl
        var screenWidth = window.screen.width;
        var setMaxWidth = spai_settings.backgrounds_max_width ? spai_settings.backgrounds_max_width : 99999;
        var origWidth = data.origWidth > 0 ? data.origWidth : 99999;
        var cappedWidth = Math.min(origWidth , screenWidth, w ? w : 99999, setMaxWidth);
        var screenHeight = window.screen.height;
        var origHeight = data.origHeight > 0 ? data.origHeight : 99999;
        //if no original height, then it doesn't make sense to calculate the capped height as we can't determine the aspect ratio
        var cappedHeight = origHeight < 99999 ? Math.min(origHeight , screenHeight, h ? h : 99999) : 99999;

        //background-size si background-position
        /*    var sizeCss = elm.css('background-size');
			if(sizeCss !== 'auto') {
				//we need to determine the scaling introduced by CSS
			}
			var posCss = elm.css('background-position');
			if(posCss !== '0% 0%' && false) { //TODO

			}
		*/
        var newSrc = isApi ? ShortPixelAI.composeApiUrl(false, src, cappedWidth < 99999 ? cappedWidth: false,
            (!!spai_settings.crop) && cappedHeight < 99999 ? cappedHeight: false) : src;
        elm.attr('style', style.replace(pseudoSrc[ index ], newSrc));

        // getting current styles again after pass
        style = elm.attr( 'style' );

        affectedData.push( data );
    }

    return affectedData.length > 0 ? affectedData : false;
};

/**
 * Method parses inline styles and returns an array of urls of placeholders to be replaced
 * Otherwise returns false
 *
 * @param {string} style Inline styles
 * @returns {boolean|array}
 */
SPAI.prototype.getBackgroundPseudoImages = function( style ) {
    if ( typeof style === 'undefined' || style.indexOf( 'background' ) < 0 ) {
        return false;
    }
    var regExp        = /(background-image|background)\s*:([^;]*[,\s]|\s*)url\(['"]?([^'"\)]*?)(['"]?)\)/gm,
        matches,
        pseudoSources = [];

    while ( ( matches = regExp.exec( style ) ) !== null ) {
        if ( !matches || matches.length < 3 ) {
            return false;
        }

        if ( matches[ 3 ].indexOf( 'data:image' ) >= 0 ) {
            pseudoSources.push( matches[ 3 ] );
        }
    }

    return pseudoSources.length > 0 ? pseudoSources : false;
};

SPAI.prototype.urldecode = function(str) {
    return decodeURIComponent((str + '').replace(/\+/g, '%20'));
};

/**
 * New version of getSizesRecursive, no recursive needed, no jQuery
 * TODO remove, it seems less effective than the recursive version - for example it doesn't work properly for the MyListings theme's backgrounds.
 * @param elm
 * @param deferHidden
 * @returns {{status: string, width: number, height: number, padding: number, padding_height: number}}
SPAI.prototype.getSizes = function(elm, deferHidden) {
    var style = getComputedStyle(elm);
    if ( deferHidden && !( elm.offsetWidth || elm.offsetHeight || elm.getClientRects().length ) && ( style.visibility !== 'visible' || style.display === 'none' || style.opacity < 0.02 ) ) {
        throw { type : 'defer', cause : 'invisible' };
    }
    var containerWidth = elm.clientWidth;
    var optWidth = elm.attributes.width ? elm.attributes.width.value : 'auto';
    var optHeight = elm.attributes.height ? elm.attributes.height.value : 'auto';
    var containerHeight = optWidth === 'auto' || optHeight === 'auto' || optWidth === '100%' || optHeight === '100%' ? elm.clientHeight : parseFloat(optHeight / optWidth * containerWidth);
    containerWidth = parseFloat(containerWidth);
    containerHeight = parseFloat(containerHeight);
    if (isNaN(containerHeight)) {
        containerHeight = 0;
    }
    var ret = {
        status: 'success',
        width: containerWidth - ShortPixelAI.percent2px(style['padding-left'], containerWidth) - ShortPixelAI.percent2px(style['padding-right'], containerWidth),
        height: containerHeight - ShortPixelAI.percent2px(style['padding-top'], containerHeight) - ShortPixelAI.percent2px(style['padding-bottom'], containerHeight),
        padding: 0,
        padding_height: 0
    };
    return ret;
}
*/

/**
 * @type {{width, padding}}
 */
SPAI.prototype.getSizesRecursive = function(elm, deferHidden) {
    if(!elm.is(':visible') && deferHidden) {
        throw {type: 'defer', cause: 'invisible'};
    }
    var computedStyle = window.getComputedStyle(elm[0]);
    var width = computedStyle['width'];
    var height = computedStyle['height'];
    var w = parseFloat(width);
    var h = parseFloat(height);
    if(width == '0px' && elm[0].nodeName !== 'A') {
        //will need to delay the URL replacement as the element will probably be rendered by JS later on...
        //but skip <a>'s because these haven't got any size
        throw {type: 'defer', cause: 'width 0'};
    }
    if(width.slice(-1) == '%') {
        if(typeof elm.parent() === 'undefined') return {width: -1};
        var parentSizes = ShortPixelAI.getSizesRecursive(elm.parent(), deferHidden);
        if(parentSizes == -1) return {width: -1, padding: 0};
        w = parentSizes.width * w / 100;
        if(height.slice(-1) == '%') {
            h = parentSizes.height * h / 100;
        }
    }
    else if(w <= 1) {
        if(elm[0].tagName === 'IMG' && typeof elm.attr('width') !== "undefined" && typeof elm.attr('height') !== "undefined") {
            w = parseInt(elm.attr('width'));
            h = parseInt(elm.attr('height'));
        } else {
            if(typeof elm.parent() === 'undefined') return {width: -1, padding: 0};
            var parentSizes = ShortPixelAI.getSizesRecursive(elm.parent(), deferHidden);
            if(parentSizes.width == -1) return {width: -1, padding: 0};
            w = parentSizes.width;
            h = parentSizes.height;
        }
        w -= ShortPixelAI.percent2px(computedStyle['margin-left'], w) + ShortPixelAI.percent2px(computedStyle['margin-right'], w);
        h -= ShortPixelAI.percent2px(computedStyle['margin-top'], h) + ShortPixelAI.percent2px(computedStyle['margin-bottom'], h);
    }
    var pw = ShortPixelAI.percent2px(computedStyle['padding-left'], w) + ShortPixelAI.percent2px(computedStyle['padding-right'], w)
        + ShortPixelAI.percent2px(computedStyle['border-left-width'], w) + ShortPixelAI.percent2px(computedStyle['border-right-width'], w);
    var ph = ShortPixelAI.percent2px(computedStyle['padding-top'], h) + ShortPixelAI.percent2px(computedStyle['padding-bottom'], h)
        + ShortPixelAI.percent2px(computedStyle['border-top-width'], h) + ShortPixelAI.percent2px(computedStyle['border-bottom-width'], h);
    //h = Math.round(h);
    return {
        status: 'success',
        width: w - pw,
        height: h - ph,
        padding: pw,
        padding_height: ph,
    }
};

/**
 * if data is % then use the width to calculate its equivalent in px
 * @param data - the CSS string (200px, 30%)
 * @param width - the element width
 * @returns px equivalent of data
 */
SPAI.prototype.percent2px = function(data, width){
    return (data.slice(-1) == '%' ? width * parseFloat(data) / 100 : parseFloat(data))
};

//this is reverse engineered from jQuery.fancybox...
SPAI.prototype.fancyboxUpdateWidth = function(elm) {
    //TODO de ce se afiseaza imaginile mai mici?
    //debugger;
    var fancyParams = jQuery.extend({}, jQuery.fn.fancybox.defaults, typeof elm.data("fancybox") == "undefined" ? {} : elm.data("fancybox"));
    var viewport = [jQuery(window).width() - fancyParams.margin * 2, jQuery(window).height() - fancyParams.margin * 2, jQuery(document).scrollLeft() + fancyParams.margin, jQuery(document).scrollTop() + fancyParams.margin];
    var k = fancyParams.padding * 2;

    var maxWidth = viewport[0] - k;
    var maxHeight = viewport[1] - k;
    var aspectRatio = fancyParams.width / fancyParams.height;
    var screenRatio = maxWidth / maxHeight;

    var width = 0;
    var height = 0;
    if(aspectRatio > screenRatio) {
        width = maxWidth;
    } else {
        height = maxHeight;
        width = Math.round(maxHeight * aspectRatio);
    }

    /*		var width = fancyParams.width.toString().indexOf("%") > -1 ? parseInt(viewport[0] * parseFloat(fancyParams.width) / 100, 10)
                    : maxWidth;
        var height = fancyParams.height.toString().indexOf("%") > -1 ? parseInt(a[1] * parseFloat(fancyParams.height) / 100, 10)
            : maxHeight;

        if (fancyParams.autoScale && (width > viewport[0] || height > viewport[1])) {
            if (width > viewport[0]) {
                width = viewport[0];
            }
            if (height > viewport[1]) {
                width = parseInt((viewport[1] - k) * g + k, 10)
            }
        }
    */
    //use rounded widths, what is below 700 rounds up to multiples of 50, what is above to multiples of 100
    width = width < 700 ? Math.floor((width + 49) / 50) * 50 : Math.floor((width + 99) / 100) * 100;
    var href = elm.attr('href');
    if( href.indexOf('w_DEFER') > 0) {
        var newHref = href.replace('w_DEFER', 'w_' + width);
        //console.log('replace DEFER: ' + newHref);
        elm.attr('href', newHref);
    }
    else {
        var matches = href.match(/\/w_([0-9]+),._/g);
        if (matches !== null && matches[2] < width) {
            var newHref = href.replace(/\/w_[0-9]+,/, '/w_' + width + ',');
            //console.log('replace ' + href + ' with ' + newHref);
            elm.attr('href', newHref);
        } else {
            return;
        }
    }
    ShortPixelAI.fancyboxId = elm.parent().parent().attr('id');
};

SPAI.prototype.xpath = function(el) {
    if (typeof el == "string") return document.evaluate(el, document, null, 0, null);
    if (!el || el.nodeType != 1) return '';
    if (el.id) return "//*[@id='" + el.id + "']";
    var sames = [];
    try {
        sames = (el.parentNode === null || typeof el.parentNode.children === 'undefined' ? [] : [].filter.call(el.parentNode.children, function (x) { return x.tagName == el.tagName }))
    } catch(err) {
        //console.log(err.message);
    }
    return (el.parentNode === null ? '' : ShortPixelAI.xpath(el.parentNode) + '/') + el.tagName.toLowerCase() + (sames.length > 1 ? '['+([].indexOf.call(sames, el)+1)+']' : '')
};

/*SPAI.prototype.identifyImage = function() {
    document.getElementsByTagName("body")[0].style.cursor = "url('" + spai_settings.sniper + "'), auto";
}*/

SPAI.prototype.registerCallback = function(when, callback) {
    ShortPixelAI.callbacks[when] = callback;
}

SPAI.prototype.elementUpdated = function(elm, w) {
    elm.attr('data-spai-upd', Math.round(w));
    ShortPixelAI.updatedUrlsCount++;
    if(typeof ShortPixelAI.callbacks['element-updated'] !== 'undefined') {
        ShortPixelAI.callbacks['element-updated'](elm);
    }
}

SPAI.prototype.triggerEvent = function(name, elem) {
    const event = document.createEvent('Event');
    event.initEvent(name, true, true);
    spai_settings.debug && console.log("Event " + name + " triggered on " + elem.tagName)
    elem.dispatchEvent(event);
}

/* //used only for debug
SPAI.prototype.getScroll = function() {
    if (window.pageYOffset != undefined) {
        return [pageXOffset, pageYOffset];
    } else {
        var sx, sy, d = document,
            r = d.documentElement,
            b = d.body;
        sx = r.scrollLeft || b.scrollLeft || 0;
        sy = r.scrollTop || b.scrollTop || 0;
        return [sx, sy];
    }
}
*/

//Polyfill for MSIE
if (!String.prototype.startsWith) {
    String.prototype.startsWith = function(searchString, position){
        position = position || 0;
        return this.substr(position, searchString.length) === searchString;
    };
}

var shortPixelAIonDOMLoadedTimeout = false;
var shortPixelAIonDOMLoadedCounter = 0;
window.ShortPixelAI = new SPAI();

function shortPixelAIonDOMLoaded() {
    if(ShortPixelAI.initialized) return;
    if(typeof spai_settings === "undefined") {
        if(shortPixelAIonDOMLoadedCounter > 50) {
            return;
        }
        clearTimeout(shortPixelAIonDOMLoadedTimeout);
        shortPixelAIonDOMLoadedTimeout = setTimeout(shortPixelAIonDOMLoaded, shortPixelAIonDOMLoadedCounter > 20 ? 30 : 10);
        shortPixelAIonDOMLoadedCounter++;
        return;
    }

    //the excluded_paths can contain URLs so they are base64 encoded in order to pass our own JS parser :)
    spai_settings.excluded_paths = spai_settings.excluded_paths.map(atob);

    if(spai_settings.native_lazy != '1') {
        //detect if it's a bot, in which case force native lazy loading
        var botPattern = "(googlebot\/|Googlebot-Mobile|Googlebot-Image|Google favicon|Mediapartners-Google|bingbot|slurp|java|wget|curl|Commons-HttpClient|Python-urllib|libwww|httpunit|nutch|phpcrawl|msnbot|jyxobot|FAST-WebCrawler|FAST Enterprise Crawler|biglotron|teoma|convera|seekbot|gigablast|exabot|ngbot|ia_archiver|GingerCrawler|webmon |httrack|webcrawler|grub.org|UsineNouvelleCrawler|antibot|netresearchserver|speedy|fluffy|bibnum.bnf|findlink|msrbot|panscient|yacybot|AISearchBot|IOI|ips-agent|tagoobot|MJ12bot|dotbot|woriobot|yanga|buzzbot|mlbot|yandexbot|purebot|Linguee Bot|Voyager|CyberPatrol|voilabot|baiduspider|citeseerxbot|spbot|twengabot|postrank|turnitinbot|scribdbot|page2rss|sitebot|linkdex|Adidxbot|blekkobot|ezooms|dotbot|Mail.RU_Bot|discobot|heritrix|findthatfile|europarchive.org|NerdByNature.Bot|sistrix crawler|ahrefsbot|Aboundex|domaincrawler|wbsearchbot|summify|ccbot|edisterbot|seznambot|ec2linkfinder|gslfbot|aihitbot|intelium_bot|facebookexternalhit|yeti|RetrevoPageAnalyzer|lb-spider|sogou|lssbot|careerbot|wotbox|wocbot|ichiro|DuckDuckBot|lssrocketcrawler|drupact|webcompanycrawler|acoonbot|openindexspider|gnam gnam spider|web-archive-net.com.bot|backlinkcrawler|coccoc|integromedb|content crawler spider|toplistbot|seokicks-robot|it2media-domain-crawler|ip-web-crawler.com|siteexplorer.info|elisabot|proximic|changedetection|blexbot|arabot|WeSEE:Search|niki-bot|CrystalSemanticsBot|rogerbot|360Spider|psbot|InterfaxScanBot|Lipperhey SEO Service|CC Metadata Scaper|g00g1e.net|GrapeshotCrawler|urlappendbot|brainobot|fr-crawler|binlar|SimpleCrawler|Livelapbot|Twitterbot|cXensebot|smtbot|bnf.fr_bot|A6-Indexer|ADmantX|Facebot|Twitterbot|OrangeBot|memorybot|AdvBot|MegaIndex|SemanticScholarBot|ltx71|nerdybot|xovibot|BUbiNG|Qwantify|archive.org_bot|Applebot|TweetmemeBot|crawler4j|findxbot|SemrushBot|yoozBot|lipperhey|y!j-asr|Domain Re-Animator Bot|AddThis)";
        var re = new RegExp(botPattern, 'i');
        var userAgent = navigator.userAgent;
        if (re.test(userAgent)) {
            spai_settings.native_lazy = '1';
        }
    } else if (!('loading' in document.createElement('img'))) {
        spai_settings.native_lazy = ''; // if the browser doesn't support native lazy loading, use the JS approach
    }

    ShortPixelAI.initialized = true;

    //detect if the browser supports WebP
    if (spai_settings.webp == '1' && self.createImageBitmap) {
        var hasWebP = (function() {
            var images = {
                basic: "data:image/webp;base64,UklGRjIAAABXRUJQVlA4ICYAAACyAgCdASoCAAEALmk0mk0iIiIiIgBoSygABc6zbAAA/v56QAAAAA==",
                lossless: "data:image/webp;base64,UklGRh4AAABXRUJQVlA4TBEAAAAvAQAAAAfQ//73v/+BiOh/AAA="
            };

            return function(feature) {
                function Deferred(){
                    this._done = [];
                    this._fail = [];
                }
                Deferred.prototype = {
                    execute: function(list, args){
                        var i = list.length;

                        // convert arguments to an array
                        // so they can be sent to the
                        // callbacks via the apply method
                        args = Array.prototype.slice.call(args);

                        while(i--) list[i].apply(null, args);
                    },
                    promise: function(){
                        return this;
                    },
                    resolve: function(){
                        this.execute(this._done, arguments);
                    },
                    reject: function(){
                        this.execute(this._fail, arguments);
                    },
                    then: function(doneFilter, failFilter) {
                        this._done.push(doneFilter);
                        this._fail.push(failFilter);
                    },
                    done: function(callback){
                        this._done.push(callback);
                        return this;
                    },
                    fail: function(callback){
                        this._fail.push(callback);
                        return this;
                    }
                }

                var deferred = new Deferred();

                var image = new Image();
                image.onload = function() {
                    if(this.width === 2 && this.height === 1) {
                        deferred.resolve();
                    } else {
                        deferred.reject();
                    }
                };
                image.onerror = deferred.reject;
                image.src = images[feature || "basic"];

                return deferred.promise();
            }
        })();

        //can also call hasWebP('lossless') to check if the newer lossless WebP is supported
        hasWebP().then(
            function(){
                ShortPixelAI.supportsWebP = true;
                ShortPixelAI.init();
            },
            function(){
                ShortPixelAI.init();
            });
    } else {
        ShortPixelAI.init();
    }

    //if the sniper icon is present in the admin bar, activate it
    if(document.getElementById('shortpixel_ai_sniper') !== null) {
        document.getElementById('shortpixel_ai_sniper').setAttribute('onclick', 'SpaiSniper(1);return false;');
    }
}

//jQuery(document).ready(function () {
if(document.readyState === 'loading') {
    document.addEventListener("DOMContentLoaded", function() {
        shortPixelAIonDOMLoaded();
    });
} else {
    shortPixelAIonDOMLoaded();
}
/*
jQuery(document).ready(function(){
	//detect if the browser supports WebP
    if (self.createImageBitmap) {
        async function supportsWebp() {

            const webpData = 'data:image/webp;base64,UklGRh4AAABXRUJQVlA4TBEAAAAvAAAAAAfQ//73v/+BiOh/AAA=';
            const blob = await fetch(webpData).then(r => r.blob());
            return createImageBitmap(blob).then(() => true, () => false);
        }

        (async () => {
            if(await supportsWebp()) {
                ShortPixelAI.supportsWebP = true;
            }
            ShortPixelAI.init();
        })();
    } else {
        ShortPixelAI.init();
    }
});
*/
