=== Mashshare - Social Networks Add-On ===
Author URL: https://www.mashshare.net
Plugin URL: https://www.mashshare.net
Contributors: renehermi
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: Mashable, Share button, share buttons, Facebook Share button, Twitter Share Button, Social Share, Social buttons, Share, Google+, Twitter, Facebook, Digg, Email, Stumble Upon, Linkedin,+1, add to any, AddThis, addtoany, admin, bookmark, bookmarking, bookmarks, buffer, button, del.icio.us, Digg, e-mail, email, Facebook, facebook like, google, google plus, google plus one, icon, icons, image, images, Like, linkedin, links, lockerz, page, pages, pin, pin it, pinit, pinterest, plugin, plus 1, plus one, Post, posts, Reddit, save, seo, Share, Shareaholic, sharedaddy, sharethis, sharing, shortcode, sidebar, sociable, social, social bookmarking, social bookmarks, statistics, stats, stumbleupon, svg, technorati, tumblr, tweet, twitter, vector, widget, wpmu
Requires at least: 3.1+
Tested up to: 4.8
Stable tag: 2.4.5

Adds more Social networks to Mashshare: Google, Whatsapp, Pinterest, Digg, Linkedin, Reddit, Stumbleupon, Vk, Print, Delicious, Buffer, Weibo, Pocket, Xing, Tumblr, Frype, Skype


== Description == 

> Mashshare Share Buttons shows the total share counts of Facebook and Twitter at a glance 
It puts some beautiful and clean designed Share Buttons on top and end of your posts to get the best most possible social share feedback from your user.
It´s inspired by the Share buttons Mashable is using on his website.

<h3> Mashshare demo </h3>

[Share Buttons](http://www.mashshare.net/?ref=1 "Share-Buttons - Mashable inspired Share Buttons")


This plugin is in active development and will be updated on a regular basis - Please do not rate negative before i tried my best to solve your issue. Thanks buddy!

= Main features Features =

* Performance improvement for your website as no external scripts and count data is loaded
* Privacy protection for your user - No permanent connection to Facebook, Twitter and Google needed for sharing
* High-Performance caching functionality. You decide how often counts are updated.
* All counts will be collected in your database and loaded first from cache. No further database requests than.
* Up to 10.000 free daily requests
* Up to 40.000 free additional daily requests with an api key (Get it free at sharedcount.com)
* Social buttons works with every Theme
* Works on pages and posts
* Automatic embedding or manual via Shortcode into posts and pages
* Simple installation and setup
* Uninstaller: Removes all plugin tables and settings in the WP database
* Service and support by the author
* Periodic updates and improvements. (Feel free to tell me your demand)
* More Share Buttons are coming soon. 

**Shortcodes**

* Use `[mashshare]` anywhere in pages or post's text to show the buttons and total count where you like to at a custom position.
Buttons are shown exactly where you put the shortcode in.
* For manual insertion of the Share Buttons in your template files use the following php code where you want to show your Mash share buttons:`mashsharer();`
Configure the Share buttons sharing function in the settings page of the plugin.
* Change the color and font size of Mashshare directly in the css file `yourwebsite.com/wp-content/mashsharer/assets/mashsharer.css`
* With one of the next updates i will give you the possibility to change color and font-size on the settings page. So you dont have to fiddle around in css files any longer.

= How does it work? =

Mashshare makes use of the great webservice sharedcount.com and periodically checks for the total count 
of all your Facebook and Twitter shares and cumulates them. It than shows the total number beside the Share buttons. 
No need to embed dozens of external slow loading scripts into your website. 
 
= How to install and setup? =
Install it via the admin dashboard and to 'Plugins', click 'Add New' and search the plugins for 'Mashshare'. Install the plugin with 'Install Now'.
After installation goto the settings page Settings->Mashshare and make your changes there.


== Official Site ==
* https://www.mashshare.net

== Installation ==
1. Download the share button plugin "Mashshare" , unzip and place it in your wp-content/plugins/ folder. You can alternatively upload and install it via the WordPress plugin backend.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Select Plugins->Mashshare

== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png
3. screenshot-3.png
3. screenshot-4.png

== Changelog ==

= 2.4.5 =
* Fix: Undefined var

= 2.4.4 =
* New: Add image param to VK button
* New: Compatible to WordPress 4.8

= 2.4.3 =
* Fix: Pinterest image not shared
* Fix: Debug console.log added to mashnet.(min).js
* New: Add new filter mashnet_pinterest_image to overwrite the pinterest image

= 2.4.2 =
* Fix: Pinterest Gallery mode is not showing images sometimes because of using old pinterest url

= 2.4.1 =
* Fix: Mail Button not shown on AMP pages

= 2.4.0 =
* Fix: Double quotes must be escaped in subject and body query string for e-mail share button.
* Fix: Pinterest button fix for dynamically (ajax) loaded pages

= 2.3.9 =
* Fix: pinit.js converts pinterest button into a pinterest native icon. Needs MashShare 3.2.6 or later!

= 2.3.8 =
* Fix: Remove duplicate networks while activation

= 2.3.7 =
* New: Flipboard Button
* New: Hacker News
* Fix: Pinterest Button

= 2.3.6 =
* New: Add Telegram Button

= 2.3.5 =
* Fix: Pinterest description empty on non singular pages

= 2.3.4 =
* Fix: Fatal error on AMP pages when used MashShare 3.0

= 2.3.3 =
* Pinterest image not shown on non singular pages in MashShare 3.0

= 2.3.2 =
* Fix: Fatal error when used on MashShare 3.0.0

= 2.3.1 =
* New: Compatible to MashShare 3.0.0
* New: Use of the new MashShare 3.0.0 Pinterest image
* New: Use of the new MashShare 3.0.0 Pinterest description
* Tweak: GUI Improvement and changed setting labels

= 2.3.0 =
* New: Use the pinterest [nopin='nopin'] attribute

= 2.2.9 =
* Fix: Missing version number leads to infinite update message

= 2.2.8 =
* Fix: Prevent type error: checking with empty($current_networks) instead strlen()
* Fix: Use post title for sharing pinterest image
* Fix: Small typo

= 2.2.7 =
* New: New Pinterest option in Settings->Extensions->Social Network Settings allows to change the pinterest sharing behavior
* Fix: CSS Tweaks (remove list-style from pinterest popup, increase padding-top for pinterest header)

= 2.2.6 =
* New: Skype Share buttton

= 2.2.5 =
* New: Add Frype / Draugiem to the list of supported social networks

= 2.2.4 =
* Fix: Load pinterest html conditionally when pinterest is enabled not working in all circumstances

= 2.2.3 =
* New: Load pinterest html conditionally when pinterest is enabled
* New: Tested up to wp 4.3
* Fix: First time installation is sometimes not installing all new networks.
* Tweak: Change readme.txt description

= 2.2.2 =
* Fix: Fatal error when disabling Mashshare core not together with network add-on

= 2.2.1 =
* New: Pinterest now uses the image alt tag

= 2.2.0 =
* New: Pinterest share all images on a website
* Tweak: Allow resizing of popup windows

= 2.1.9 =
* New: Add yummly.com
* Fix: Make print window resizable in Internet Explorer

= 2.1.8 =
* Fix: Facebook, Twitter and Subscribe button missing after new install of 2.1.7

= 2.1.7 =
* New: mail.ru
* New: line

= 2.1.6 =
* New: Load JS files into footer
* Tested up to WP 4.2

= 2.1.5 =
* New: Use custom share texts and images for all buttons when free Mashshare Add-On "OpenGraph" is installed. 
* Fix: Missing URLencode for shortcode function. Prevents "not found" for tumblr button

= 2.1.4 =
* Fix: FB and TW button are disapearing after updating to 2.1.3

= 2.1.3 =
* Fix: Linkedin, Reddit and Stumbleupon not working under special circumstances (Fix in 2.1.2 was not working as expected)

= 2.1.2 =
* Fix: Linkedin, Reddit and Stumbleupon not working under special circumstances
* Fix: Remove the + character as separating character between singular words in whatsapp and mail sharing

= 2.1.1 =
* New: Complete rewrite of the sharing core. Use of href instead mashsb wp_localize
* Tweak: Change settings header description
* Fix: HTML 5 compliance convert & into &amp;
* Fix: use urlencode for body and subject string in mail

= 2.1.0 =
* Fix: Whatsapp sharing not working on different site constellations

= 2.0.9 =
* Fix: Mail subject and body missing

= 2.0.8 =
* New: Managewp
* New: Odnoklassniki
* New: meneame
* New: Change mail subject and body text via settings

= 2.0.7 =
Fix: remove plus characters from whatsapp sharing 

= 2.0.6 =
Fix: Whatsapp button not shown on mobile devices

= 2.0.5 =
New: Open mail sharing in new window
New: WPMU WordPress multisite compatible
New: Whatsapp Button should be working on newer Android versions (not tested on all devices)
New: Show Whatsapp button only on iPhone and Android


= 2.0.4 =
New: E-Mail button

= 2.0.3 =
Fix: Prevent deactivation of the Add-On when core Plugin Mashshare is updated 
Fix: Some spelling corrections
Fix: change https meta row links
Fix: Remove mashsb_settings_extensions filter
Fix: Prevent permission error when plugin is activated but mashsb is disabled or not installed

= 2.0.2 =
Fix: Pinterest
Fix: Tumblr
Fix: Stumbleupon
Fix: Remove Mail Button
Fix: Remove Flattr Button

= 2.0.1 =
* Fix: registration_hook excluded from class mashshare
* New: Detection if Mashshare is activated or installed

= 2.0.0 = 
* First release