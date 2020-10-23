=== ShortPixel Adaptive Images ===
Contributors: ShortPixel
Tags: adaptive images, responsive images, resize images, scale images, cdn, optimize images, compress images, on the fly, webp, lazy load
Requires at least: 3.3.0
Tested up to: 5.5.1
Requires PHP: 5.6.40
Stable tag: 2.0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Start serving properly sized, smart cropped & optimized images from our CDN with a click; On the fly convert WebP support.

== Description ==

**An easy to use plugin that can help you solve within minutes all your website’s image-related problems.**

Display properly sized, smartly cropped and optimized images on your website; Images are processed on the fly and served from our CDN, in the next-gen WebP format, if the browser supports it.

= Do I need this plugin? =
If you have a WordPress website with images then the answer is: most probably yes!
Did you ever test your website with tools like <a href="https://developers.google.com/speed/pagespeed/insights/" target="_blank">PageSpeed Insights</a> or <a href="https://gtmetrix.com/">GTmetrix</a> and received complains about images not being properly sized or being too large? Or that you should use "next-gen" images like WebP? Or that the website should "defer offscreen images"?
ShortPixel Adaptive Images comes to the rescue and resolves your site's image-related problems in no time.

= What are the benefits? =

[vimeo https://vimeo.com/407181635 ]

Imagine that you could have all your image-related website problems solved with a simple click, wouldn't that be great?
Usually the images are the biggest resource on a website page. With just one click, ShortPixel Adaptive Images replaces all your website's pics with properly sized, smartly-cropped, optimized images and offloads them on to the ShortPixel's global CDN.
And for more Google love the plugin serves <a href="https://en.wikipedia.org/wiki/WebP">WebP</A> images to the right browsers auto-magically!

= What are the features? =
* same visual quality but smaller images thanks to ShortPixel algorithms
* smart cropping - <a href="https://shortpixel.helpscoutdocs.com/article/182-what-is-smart-cropping" target="_blank">see an example</a>
* serve only appropriately sized images depending on the visitor's viewport
* lazy load support
* automatically serves WebP images to browsers that support this format. Animated Gifs are supported too!
* caching and serving from a global CDN
* all major image galleries, sliders, page builders are supported
* SVG place holders
* support for JPEG, PNG, GIF, TIFF, BMP
* convert to WebP on the fly.
* traffic is not counted

= Do I need an account to test this plugin? =
No, just go ahead and install then activate it on your WordPress website and you’ll automatically receive 500 image optimization credits.

= How much does it cost? =
When using ShortPixel Adaptive Images, only the image optimization <a href="https://shortpixel.helpscoutdocs.com/article/96-how-are-the-credits-counted">credits are counted</a>. That means the CDN traffic is not metered (considering a fair usage). The free tier receives 100 image optimization credits, paid plans start at $4.99 and both <a href="https://shortpixel.com/pricing-one-time">one-time</a> and <a href="https://shortpixel.com/pricing">monthly</a> plans are available.
Even better: if you already use <a href="https://wordpress.org/plugins/shortpixel-image-optimiser/">ShortPixel Image Optimizer</a> then you can use the same credits with ShortPixel Adaptive Images!

= How does this work? =
Different visitors have different devices (laptop, mobile phone, tablet) each with its own screen resolution. ShortPixel AI considers the device's resolution and then serves the right sized image for each placeholder.
Let's consider a webpage with a single 640x480 pixels image.
When viewed from a laptop the image will retain it 640x480px size but it will be optimized and served from a CDN.
When the same webpage is viewed from a mobile phone, the image will be resized (for example) to 300x225px, optimized and served from CDN.
This way, neither time nor bandwidth will be wasted by visitors.
Please note that the first time the call for a specific image is made to our servers, the original image will be served temporarily.

**Other plugins by ShortPixel**

* Image optimization & compression for all the images on your site, including WebP delivery - <a href="https://wordpress.org/plugins/shortpixel-image-optimiser/" target="_blank">ShortPixel Image Optimizer</a> 
* Easily replace images or files in Media Library - <a href="https://wordpress.org/plugins/enable-media-replace/" target="_blank">Enable Media Replace</a>
* Regenerate thumbnails plugin compatible with the other ShortPixel plugins - <a href="https://wordpress.org/plugins/regenerate-thumbnails-advanced/" target="_blank">reGenerate Thumbnails Advanced</a>
* Make sure you don't have huge images in your Media Library - <a href="https://wordpress.org/plugins/resize-image-after-upload/" target="_blank">Resize Image After Upload</a>

== Frequently Asked Questions ==

= What happens when the quota is exceeded? =

In your WP dashboard you'll be warned when your quota is about to be exhausted and also when it was exceeded. The already optimized and cached images will still be served from our CDN for up to 30 days.
The images that weren't already optimized will be served directly from your website.

= What Content Delivery Network (CDN) do you use? =

ShortPixel Adaptive Images uses <a href="https://www.stackpath.com/">STACKPATH</a> to offload the images - a global CDN with <a href="https://www.stackpath.com/platform/network/">45 edge locations</a> around the world.
Both free and paid plans use the same CDN with the same number of locations.
You can independently check out how StackPath CDN compares to other CDN providers <a href="https://www.cdnperf.com/">here</a> (wordlwide) and <a href="https://www.cdnperf.com/#!performance,North%20America">here</a> (North America).

= Can I use a different CDN? =

Sure. <a href="https://shortpixel.helpscoutdocs.com/article/180-can-i-use-a-different-cdn-with-shortpixel-adaptive-images">Here</a> you can see how to configure it with Cloudflare and <a href="https://shortpixel.helpscoutdocs.com/article/200-setup-your-stackpath-account-so-that-it-can-work-with-shortpixel-adaptive-images-api">here</a>’s how to configure it with STACKPATH.
If you need further assistance please <a href="https://shortpixel.com/contact">contact us</a>

= What happens if I deactivate the plugin? =
You can stop using the SPAI whenever you want but this means your site will suddenly become slower.
Basically, your website will revert to the original, un-optimized images served from your server.

= Are there different image optimization levels available? =
Yes, you can compress images as Lossy, Glossy or Lossless.
You can find out more about each optimization level <a href="https://shortpixel.helpscoutdocs.com/article/11-lossy-glossy-or-lossless-which-one-is-the-best-for-me">here</a> or can run some free tests to optimize images <a href="https://shortpixel.com/online-image-compression">here</a>

= I already used ShortPixel Image Optimizer, can I also use this? =
Certainly!

= What is the difference between this plugin and ShortPixel Image Optimizer =
You can see <a href="https://shortpixel.helpscoutdocs.com/article/179-shortpixel-adaptive-images-vs-shortpixel-image-optimizer">here</a> the differences between the two services.

= Where can I optimize my images? There's nothing on my admin panel. =
SPAI works differently than a regular image optimizer. <a href="https://help.shortpixel.com/article/132-how-shortpixel-adaptive-images-work" target="_blank">Here's</a> what it does.

= How can I make sure that the plugin is working well? =
You have more information about this <a href="https://help.shortpixel.com/article/240-is-shortpixel-adaptive-images-working-well-on-my-website" target="_blank">here</a>.

= I want to start using the plugin, what should I do? =
The exact instructions for this are available <a href="https://help.shortpixel.com/article/231-step-by-step-guide-to-install-and-use-shortpixel-adaptive-images-spai" target="_blank">here</a>.

= My images are getting redirected from cdn.shortpixel.ai, why? =
Have a look at <a href="https://help.shortpixel.com/article/148-why-are-my-images-redirected-from-cdn-shortpixel-ai" target="_blank">this article</a>.

= SPAI is not working well, I'm having some issues. =
Please check the following things: 
1) Make sure your domain <a href="https://help.shortpixel.com/article/94-how-to-associate-a-domain-to-my-account" target="_blank">is associated to your account</a>;
2) Make sure you have enough credits available in your account;
3) Have a look at <a href="https://help.shortpixel.com/article/220-i-installed-shortpixel-adaptive-images-but-it-doesnt-seem-to-work" target="_blank">this article</a>;
4) Take a look at <a href="https://help.shortpixel.com/category/307-shortpixel-adaptive-images" target="_blank">our knowledge base</a>.

If nothing seems to work, please <a href="https://shortpixel.com/contact" target="_blank">contact us</a>.

== For developers ==

If in Media Library there are main images which end in the usual thumbnail size suffix (eg. -100x100) please set in wp-config.php this:

    define('SPAI_FILENAME_RESOLUTION_UNSAFE', true);

If you need to do a post-processing in JavaScript after the image/tag gets updated by ShortPixel AI, you can add a callback like this:

    jQuery( document ).ready(function() {
        ShortPixelAI.registerCallback('element-updated', function(elm){
            // elm is the jQuery object, elm[0] is the tag
            console.log("element updated: " + elm.prop('nodeName'));
            });
    });

For changing the original URL of the image, that is detected by ShortPixel, use this filter that receives the original URL:

    add_filter('shortpixel/ai/originalUrl', 'my_function');

Sometimes, when the option to crop the images is active, SPAI thinks it's not safe to crop an image, but you want to crop it anyway. Please add this attribute to force the cropping:

    <img data-spai-crop="true" ....

ShortPixel Adaptive Images triggers a JS event after processing the HTML page: spai-body-handled and an event after each processed DOM mutation, if at least one URL was replaced: spai-block-handled



== Screenshots ==

1. Example site metrics on PageSpeed Insights before: Low

2. Example site metrics on PageSpeed Insights after: Good

3. Example site metrics on GTMetrix before: F score

4. Example site metrics on GTMetrix after: B score

5. Main settings page

6. Advanced settings page

== Changelog ==
= 2.0.9 =
Release date: October 22nd 2020
* Fix: the integration with W3 Total Cache wasn't properly working;
* Fix: in certain cases an image was cropped in a smaller size after being present in a not cropped larger size;
* Fix: workaround for Swift Performance's bug that was making changes in JSON variables thus breaking SPAI's JS code;
* Fix: certain images were being displayed twice on devices with JS deactivated;
* Fix: a division by zero notice was sometimes thrown, which is now gone;
* Fix: in certain situations the plugin was trying to calculate the image size for css files;
* Fix: when NextGen Gallery was active, some of its admin screens were reloading because of a JS library clash;
* Language: 0 new strings added, 0 updated, 0 fuzzed, and 0 obsoleted.

= 2.0.8 =
Release date: October 8th 2020
* Fix: there was a typo in the ai.js file;
* Fix: the fatal error for declaring `Psr\Cache\CacheException` is now fixed for good;
* Fix: Modula Creative Gallery now works with SPAI's lazy loading;
* Fix: for the NextGen lightbox sizing problem, also cache the `get_image_size` calls;
* Fix: the integration with WP Fastest Cache works correctly now;
* Language: 0 new strings added, 0 updated, 0 fuzzed, and 0 obsoleted.

= 2.0.7 =
Release date: October 6th 2020
* New: option to force crop an image using a custom property `data-spai-crop`;
* New: added JS events `spai-body-handled`, `spai-element-handled` and `spai-block-handled`;
* Tweak: also parse input type image;
* Compat: added `rs-bg-elem` for slider-revolution;
* Compat: integrate Divi's custom attribute `data-et-multi-view`;
* Fix: URLs from IMG `src` are now correctly parsed, even if there's an extra space at the end;
* Fix: sometimes a fatal error was thrown when one of the plugin dependencies was declared by another plugin;
* Language: 0 new strings added, 0 updated, 0 fuzzed, and 0 obsoleted.

= 2.0.6 =
Release date: September 15th 2020
* New: Added a filter for the original url: 'shortpixel/ai/originalUrl';
* New: Integrated with The Grid plugin;
* Fix: parse tags attributes that have spaces around the '=' sign;
* Fix: parse correctly cases when data-thumb is a HTML;
* Fix: displaying the associated domain in multisite installations;
* Fix: comma seprated excluding selectors are properly added via JS too in the plugin settings;
* Tweak: hide login button and account e-mail for sub-accounts;
* Language: 0 new strings added, 6 updated, 0 fuzzied, and 0 obsoleted.

= 2.0.5 =
Release date: August 20th 2020
* Fix: a better method to parse the html tags' attributes in order to avoid potential errors;
* Fix: removed HelpScout and Quriobot in order to comply with the directory plugins guidelines; added Support and FAQ links instead;
* Fix: excluding selectors can now be added comma separated in the plugin settings;
* Fix: only HTML and JSON will be parsed from now on, other types or malformatted content will be ignored;
* Fix: updated the user capability needed to access the settings, from `install_plugins` to `manage_options`;
* Fix: compatibility with Oxygen Builder;
* Fix: missing Lity library on some pages;
* Fix: conflict with WooCommerce Variation Swatches plugin;
* Fix: some unminified 3rd party JS files were throwing console errors in wp-admin;
* Fix: the login link in the plugin settings now logs the user directly on the ShortPixel site;
* Fix: minor fixes for certain notifications and hooks used by the plugin;
* Compat: deactivate the default lazy loading introduced in WordPress 5.5 when the plugin JS lazy loading is active;
* Compat: added notification for WP Optimize's CSS Merge functionality, which can break the CSS in some cases;
* Language: 28 new strings added, 14 updated, 0 fuzzied, and 3 obsoleted.

= 2.0.4 =
Release date: July 30th 2020
* Fix: A notice is now displayed when the Combine JS option is activated in LiteSpeed plugin, until a proper integration will be ready;
* Fix: A fatal error was thrown in certain situations when Elementor Pro plugin was active;
* Fix: Compatibility with Slider Revolution;
* Fix: Front-checker tool didn't properly work in case the user name had special chars;
* Fix: Added a notification that blocks the on-boarding wizard in case WebP delivery is active on ShortPixel Image Optimizer, as it was causing conflcts;
* Fix: `background:url` links will now be replaced even if they have a space after the opening bracket;
* Tweak: CSS files with `<link rel=prefetch>` links will also be replaced;
* Tweak: The Image Checker Tool strings can now be translated as well;
* Tweak: Minor CSS improvements in the notifications;
* Language: 79 new strings added, 0 updated, 0 fuzzied, and 3 obsoleted.

= 2.0.3 =
Release date: July 17th 2020
* Fix: certain exclusions were not kept when upgrading from 1.x to 2.x;
* Fix: some PHP warnings & errors that were showing up in some very specific cases;
* Fix: minor refactoring in some parts of the code;
* Language: 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted.

= 2.0.2 =
Release date: July 14th 2020
* Fix: Various situations when the plugin wasn't properly working with Elementor Pro;
* Fix: For the on-boarding message regarding available credits;
* Fix: WooCommerce product images were no longer displayed when looged in, in certain situations;
* Fix: In certain viewport settings the optimized images weren't visible;
* Language: 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted.

= 2.0.1 =
Release date: July 9th 2020
* Fix: on certain PHP 7.0.x versions there was a fatal error that is now fixed;
* Fix: the integration with WP Fastest Cache will now check if all methods used are properly defined;
* Fix: the deactivation pop-up wasn't working properly in some cases;
* Fix: width/height attributes being larger on real 1x1 images;
* Fix: SVG files can now be properly excluded with the Image Checker Tool;
* Language: 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted.

= 2.0.0 =
Release date: July 8th 2020
* New: Onboarding wizard, which guides the user through the plugin settings and features, including before and after GTMetrix test, in order to check the performance imprrovement;
* New: Front-end tool to detect what are the best settings for the given site, as part of the onboarding wizard;
* New: Settings are now reorganized and with a new layout. All settings are now stored in one serialized option inside the DB;
* New: Account status box on the settings page;
* New: Browser native lazy-loading support;
* New: Option to add NOSCRIPT fallback for the image tags, when using the SRC replace method;
* New: ImageChecker tool on the front-end (while logged in), to exclude images from optimization, lazy-loading or resizing and also to refresh an image on the CDN;
* New: Handle image swapping on hover; Until now, the image displayed on hovering another image wasn't handled by the plugin;
* New: Option to serve SVG files from ShortPixel's CDN;
* New: Option to replace images from JS blocks with optimized and properly scaled ones, served from the CDN;
* New: Added integrations with caching plugins: W3 Total Cache, Swift Performance Pro, WP Fastest Cache; The image URL's are now replaced directly in the minified CSS files handled by these plugins;
* New: Added a deactivation pop-up that includes the possibility to downgrade the settings to the 1.x.x version;
* Tweak: Improved the integration with Elementor plugin (Simple Image Lightbox);
* Tweak: You can now select the image types for which WebP will be delivered to supporting browsers;
* Tweak: Improved ShortPixel support integration, which now allows easy searching inside the plugin documentation, straight from the settings page;
* Fix: Issues related to indexing of the images by Google and other bots;
* Language: 305 new strings added, 0 updated, 1 fuzzied, and 122 obsoleted

= 1.8.9 =
Release date: June 9th 2020
* Fix JSON not properly replaced background-image, when the only background images are from JSON (MyListings theme);
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted.

= 1.8.8 =
Release date: May 28th 2020
* Fix some roundings when calculating image size;
* Fix PHP Warning: Invalid argument supplied for foreach() in url-tools.class.php line 284;
* Added update notices functionality in order to announce the upcoming discontinuation of support for PHP < 5.6;
* Language – 6 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted.

= 1.8.7 =
Release date: 27th April 2020
* Fix for smooth lazy loading (fade-in);
* Fix for srcsets with different aspect ratio images;
* Fix: remove jQuery's Deferred() from WebP check, as it was generating JS errors in certain situations with WP Rocket enabled;
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.8.6 =
Release date: 20th April 2020
* Fix slow query based on GUID on wp_posts
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.8.5 =
Release date: 13th April 2020
* Fix wrong replace of srcset in some cases after the metadata changes
* Fix integration with Oxygen builder when using code blocks
* Support Elementor's data-settings in &lt;header&gt; tag
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.8.4 =
Release date: 16th March 2020
* Fix background-images with white space before the closing paranthesis
* Fix isFullPseudoSrc
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.8.3 =
Release date: 12th March 2020
* Fix replacing images when NextGen active
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.8.2 =
Release date: 5th March 2020
* Fix images having URLs without protocol (//mysite.com)
* Handle multiple background-image's in the same style attribute
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.8.1 =
Release date: 3rd March 2020
* W3C compliant inline image placeholders
* Integrate with the Content Views plugin.
* Fix wrong absolute URL calculation for relative image URLs and page URLs not ending in /
* More precise size determination for fractional width and height, using getComputedStyle instead of jQuery.
* Remove the obsolete setting External meta because now the inline image placeholders are W3C compliant.
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 2 obsoleted

= 1.8.0 =
Release date: 11th February 2020
* Integrate with WP Rocket's CSS cache in order to replace the image URLs there.
* Add preconnect header.
* Integrate with Lovely 2 theme.
* Integrate with My Listings theme.
* Parse divs data-thumbnail and data-options too.
* Fixed: 'path' not defined notice when validating some URLs
* Fixed: parsing encoded HTML inside JSON blocks inside HTML.
* Fixed: warning meta['file'] undefined in some circumstances.
* Fixed: replacing the generated stats graph of JetPack.
* Language – 2 new strings added, 1 updated, 0 fuzzied, and 0 obsoleted

= 1.7.5 =
Release date: 2nd January 2020
* Don't replace lazily inside structured data
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.7.4 =
Release date: 30th December 2019
* Fix PHP Warning: preg_match() on JSON AJAX in some circumstances
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.7.3 =
Release date: 22nd December 2019
* Replace inside application/json scripts
* Fixed: style blocks extracting based on regex failing on specific large blocks
* Fixed: AJAX calls returning a primitive which is valid JSON
* Fixed: exclude properly from srcset based on URL
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.7.2 =
Release date: 17th December 2019
* Fix handling lazy URLs on HTML tags that are present only on JSON data.
* Properly exclude by path URLs from srcset
* Fix wrong handling of non-image URLs in JSON AJAX in some situations ( video src )
* Language – 3 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.7.1 =
Release date: 13th December 2019
* Fix JS typo.

= 1.7.0 =
Release date: 12th December 2019
* Add a JS callback that is called after each tag is changed
* Better handling of HTML properties inside JSON calls.
* Fixed: not matching URLs having three bytes characters in JSON objects.
* Fixed: srcset replacement when the first item doesn't have the size indicator.

= 1.6.1 =

Release date: 27th November 2019
* Fixed: wrong URLs on srcset with excluded image in some circumstances.
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.6.0 =

Release date: 25th November 2019
* Add option to replace lazily in JSON AJAX calls.
* Compatibility with CROWD 2 theme.
* Compatibility with Gravity Forms.
* Compatibility with Stack theme.
* Fixed: srcset not being parsed and integrated properly on SRC method when the URL is from Media Library.
* Fixed: use the same type of quote for SPAI's own attributes as the one of the URL - some JSON encoded content doesn't escape both types.
* Fixed: if the srcset is already parsed, don't try to parse again.
* Fixed: content coming later in AJAX doesn't have the tags on record to be parsed. Send next time.
* Fixed: PHP warning when specifying a selector without tag before the #id.
* Language – 1 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.5.2 =

Release date: 12th November 2019
* Fixed: calculating the image size when there are several paddings involved - another case.
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.5.1 =

Release date: 11th November 2019
* Integrate with WP Grid Builder.
* Integrate with Smart Slider 3.
* Rescale all lazy-loaded backgrounds.
* Don't add size parameters to SVGs.
* Fixed: handling of the WooCommerce product variations.
* Fixed: catch JS exceptions caused by wrong regexes configured by users in settings.
* Fixed: iPhone page reloaded caused by the CSS files parsing by JS.
* Fixed: calculating the image size when there are several paddings involved.
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.5.0 =

Release date: 5th November 2019
* Button to clear the .css cache in Advanced settings.
* Update width and height attributes of the img tag when modifying the src
* Replace eagerly in style blocks
* Integrate with Jupiter theme's slider that uses JSON-encoded image attributes.
* Integrate with galleries that use the media-gallery-link attribute.
* Add index.html with noindex in each folder for sites which don't implement proper access rules to the plugin folder.
* More explicit errors when the plugin can't connect to ShortPixel to check credits.
* Fix broken retina @2x shortpixel logo on notices
* Language – 6 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.4.1 =

Release date: 25th October 2019
* Make the external metadata optional with default in-placeholder
* Fixed: broken regex which was not catching some backgrounds.
* Language – 3 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.4.0 =

Release date: 23rd October 2019
* Replace image URLs inside CSS files too, minify the CSS files and serve them from CDN.
* Add JSON parsing support option.
* Use eager URLs when replacing inside &lt;noscript&gt; tags.
* Serve SVG files from CDN too.
* Integrate with WP Bakery's data-ultimate-bg attribute.
* Integrate with Slider Revolution's lazyload in slider.
* Compatibility with Oxygen Builder's a hrefs.
* Fix srcset's images heights on BOTH mode.
* Notify conflict with Divi Toolbox version < 1.4.2.
* Fix replacing background images for some sliders: Revolution Slider, Glow Pro's Swiper slider and Optimizer PRO's frontpage slider.
* Fix bug on iPhone: delays in rendering the inline placeholders delay the initial parsing and conflicts with mutations.
* Settings advanced tab now remains active after save if previously selected.
* Language – 7 new strings added, 2 updated, 0 fuzzied, and 0 obsoleted

= 1.3.6 =

Release date: 30th September 2019
* Fix replacing background images when delimited by &quot;
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.3.5 =

Release date: 23rd September 2019
* For `<img>`'s, take the largest image from src and srcset if srcset is present
* Compatibility with Slider Revolution
* Compatibility with Oxygen Builder
* Notify incompatibility with Divi Toolbox's "Custom Post Meta" option.
* Fixed: images not loaded on first page load on some iPhones due to the fact that DOMLoaded event is triggered before the `<img>`'s inline src's are parsed.
* Fixed: not replacing divs data-thumbs in some circumstances
* Language – 7 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.3.4 =

Release date: 14th September 2019
* fix replacing images in `<img data-src>` tags
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.3.3 =

Release date: 12th September 2019
* Fix SRCSET parsing
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.3.2 =

Release date: 11th September 2019
* Fix IE problem and DOM syntax errors due to the non-standard data:image
* If an image is resized to a specific size and later on in the same page the same image needs a smaller size, use again the previously resized image.
* Fix CSS backgrounds regex in some cases
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.3.1 =

Release date: 10th September 2019
* Better integration with Modula
* Fixed: background regex in some cases
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.3.0 =

Release date: 9th September 2019
* Add option to cap backgrounds to a max width
* Add option to lazy-load backgrounds and limit their width to device width
* Improve performance of front-end JS by parsing only tags that were affected on back-end.
* Better handling for cropped images if crop option set.
* Keep EXIF option.
* Fixed: wrong API url on multisites
* Fixed: catastrophic backtracking on pages with huge ( > 1M ) CDATA blocks
* Fixed: background images in inline style not caught properly in some cases
* Language – 15 new strings added, 1 updated, 0 fuzzied, and 0 obsoleted

= 1.2.6 =

Release date: 28th August 2019

* Improve the main image regex in order to catch some malformed cases
* Replace also link rel="icon" in the header
* Fix warning strpos needle is empty
* Be able to find file on disk based on urlencoded name from URL, for images with spaces in the name (try with urldecode too).
* Language – 0 new strings added, 0 updated, 0 fuzzied, and 0 obsoleted

= 1.2.5 =

Release date: 17th July 2019

* improve the load time of images displayed on DOM changes (as menus for instance)
* code refactoring in preparation of DOM Parser
* Fix JS error settings not an object
* Fix some replacing issues when URLs contain encoded characters.
* Fix replacing urls when pages under edit in some builders (Thrive Architect, Avia among them)

= 1.2.4 =

Release date: 3rd July 2019

* Fix bug in span background-image
* Compatibility with Ginger – EU Cookie Law plugin
* Parse also `<section>`'s backgrounds
* Fix bug when parsing some background-images containing spaces

= 1.2.3 =

Release date: 20th June 2019

* Add help links and Support beacon
* Compatibility with Thrive Architect and Avia Layout Builder
* Fix problem with sites having the WP install in a subfolder (site_url vs. home_url)
* Fix notice on sites with older Autoptimize versions
* Skip the `<a>` tags when determining the size of an element recursively, based on parent size
* Fix: background images of spans
* Refactoring in preparation for DOM Parse

= 1.2.2 =

Release date: 7th June 2019

* Fix for image URLs containing &'s
* fix for eagerly loaded background-image URLs not containing the protocol (//some.site/img.jpg)

= 1.2.1 =

Release date: 6th June 2019

* Fix JS not triggering when DOMContentLoaded was fired before the JS load

= 1.2.0 =

Release date: 4th June 2019

* Integrate with Viba Portfolio
* Integrate with the Elementor paralax section
* Work around random jQuery not loaded error due to jQuery bug (https://github.com/jquery/jquery/issues/3271)
* Don't lazy-load the images set on backgrounds in `<style>` blocks.
* Move ai.min.js to footer
* Fix exclude pattern matching when class defined without quotes (`<div class=myclass>`)

= 1.1.3 =

Release date: 30th May 2019

* Fix JS issues on iPhone 6s
* Make Elementor External CSS warning dismissable
* Fix exclude regexes added on Windows and having \r\n at the end.
* Fix replacing images that are not in Media Library but directly in wp_content

= 1.1.2 =

Release date: 29th May 2019

* Thrive Architect preview compatibility
* Parse also the `<amp-img>` tag
* Fix not parsing AJAX in some circumstances
* Fix compatibility with Safari in some cases when ai.min.js is loaded later (async)
* Fix translations by adding load_plugin_textdomain

= 1.1.1 =

Release date: 27th May 2019

* Retina displays - properly take into account pixel ratio when resizing images.
* Fix feed-back loop on MutationObserver caused by some jQuery versions which set id as a hack to implement qSA thus trigger a mutation
* Parse also the .css files in browser - in order to catch some optimization plugins (like Swift Performance) which extract the inline CSS to external .css resources
* Notify if Elementor has the option to use External File for CSS Print Method because it conflicts with replacing background-image's

= 1.1.0 =

Release date: 23rd May 2019

* option to exclude images based on URL parts or patterns
* option to either do or do not the fade-in effect when lazy-loading
* fix for urls starting with '//'
* fix for urls starting with '../' even if the page is in the root of the site ( https://example.com/../pictures/pic1.jpg )

= 1.0.3 =

Release date: 20th May 2019

* fix replacing background image on elements not initially visible
* MSIE fixes: String.startsWith polyfill, fix IntersectionExplorer polyfill, handle cases when element.parentNode.children is undefined ( IE magic:) )
* Fix compatibility with WooCommerce's magnifier lens when using the fade-in effect of the lazy-loaded images.

= 1.0.2 =

Release date: 16th May 2019

* integrate Avada - notify to deactivate the lazy-loading of Avada

= 1.0.1 =

Release date: 10th May 2019

* better handling of the excludes by tag ID
* do not replace the images src if plugin's JS script was dequeued (like on logins or coming soon pages).
* check if the URL has host before, in order to prevent some warnings.

= 1.0.0 =

Release date: 8th May 2019

* alert when quota is low or exhausted.
* fade-in effect for lazy-loaded images
* replace also the background CSS shorthand
* do not replace the unsupported image types (like SVG) in backgrounds either

= 0.9.6 =

Release date: 25th April 2019

* updates of the verification of Autoptimize's setting for image optimization after changes in version 2.5.0.

= 0.9.5 =

Release date: 25th April 2019

* fix JS error on Firefox

= 0.9.4 =

Release date: 23rd April 2019

* Parse the CSS `<style>` blocks for background-image rules and replace them
* Smarter replace for background-image rules - cover cases when there is also a gradient
* Alert for double compression when ShortPixel Image Optimization is present has the same lossy setting
* Alert for conflict when Autoptimize has the option to deliver images using ShortPixel's service.
* Make sure it doesn't replace the URL of any image type (by extension) which is not supported
* Exclude the AMP endpoints from replacing
* fix bug for the Elementor gallery which was replacing other links having common CSS class

= 0.9.3 =

Release date: 4th March 2019

* Integrate galleries: Foo Gallery, Envira, Modula, Elementor, Essential add-ons for Elementor, Everest, default WordPress gallery
* Integrate with WP Bakery's Testimonial widget
* activate the integrations only if the respective plugins are active (also for existing NextGen integration)
* use the '+' separator for optimization params, which integrates better with some plugins which parse the srcset and get puzzled by the comma.
* display a notice about conflicts with other lazy-loading plugins.

= 0.9.2 =

Release date: 13th February 2019

* exclude from parsing the `<script>` and `<![CDATA[` sections
* honour the GET parameter PageSpeed=off used by some third parties as the Divi Builder
* add settings link in the plugins list
* lazy-load the images referred by inline background-image CSS
* Fixed: image src's without quotes followed immediately by >, URLs containing non-encoded UTF8, inline background-image URL with &quot; etc

= 0.9.1 =

Release date: 30th January 2019

* handle `<div data-src="...">`
* handle &nbsp;'s, &quot;'s in background-image CSS
* handle images with empty href
* handle more cases of hrefs without quotes

= 0.9.0 =

Release date: 23rd January 2019

* Use the Babel generated replacement for the async/await WebP code
* parse the background-image inline style
* check also if the element is :visible
* add to settings a list of excludes which leaves the URL as it is
* use svg instead of gif for the inline image replacement, for better compatibility with Safari
* use minified javascript for front-end
* fixed: IntersectionObserver on Safari

= 0.8.0 =

Release date: 9th December 2018

* WebP support

= 0.7.2 =

Release date: 28th October 2018

* add MutationObserver polyfill
* add alert that plugin is in beta

= 0.7.1 =

Release date: 7th October 2018

* Fix performance problems when page has many modifications by JS.

= 0.7.0 =

Release date: 3rd November 2018

* added lazy loading of images.

= 0.6.4 =

Release date: 7th October 2018

* add the SRCSET and BOTH (both src and srcset) option
* urlencode the URLS before base64 to circumveit incompatibility with atob on some characters like (R)

== Upgrade Notice ==

= 2.0 =
ShortPixel Adaptive Images version 2.0 is a major upgrade and it comes with some new tools that help you manage the settings and the optimized images. Please note that following this upgrade, the plugin settings will be stored in a different way, therefore please also make a full site backup, update your theme and extensions, and review update best practices before upgrading.
= 2.0:END =
