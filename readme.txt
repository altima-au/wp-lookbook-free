=== Altima Lookbook Free for WooCommerce===

Contributors: altima-interactive
Developer link: http://shop.altima.net.au
Tags: lookbook, slider, hotspot, woocommerce
Requires at least: 4.0
Tested up to: 4.4.2
Stable tag: trunk
License: GPL v3
License URI: http://www.gnu.org/licenses/gpl-3.0.en.html

The plug-in allows WooCommerce merchants to create slider/carousel with multiple clickable tag points per slider images.

== Description ==

Altima LookBook Free provides your online store with "Shop the look" experience. First of all it is intended for online retailers who use WooCommerce. 

To view demo visit http://wplookbook.u2.com.ua/ 
Altima Lookbooks Free for WooCommerce provides the next functionality:

1. One slider.
2. Product name, link to product page, price and stock status in hotspot, also any link with text supported
3. Up to five slides .
4. Up to three hotspots per slide.
5. 2 flip and 2 fade transition effects.
6. Default link from each slide allows to define an URL linked with the areas of the slide not related with any hotspots, so effectively you can use Altima Lookbook Free as a simple slider. That default link is also clickable when a slider degrades to a simple gallery mode on small screens
7. Support of responsiveness - lookbook sliders are adaptive and resize according screen resolution
8. Mobile and touchscreens friendly - support of gestures to slide the slides

There is a more advanced version of the plug-in with unlimited number of slides and sliders and other additional features available at http://shop.altima.net.au/wordpress-plug-ins/lookbook-woocommerce-professional.html	

== Requirements ==

1. Installed Wordpress version 4.0 or higher
2. Installed WooCommerce 2.3.0 or higher (Tested up to 2.6.0-dev)

== Installation ==

First download the ZIP file,

1. Log in to your website administrator panel.
2. Go to Plugins page, and add new plugin.
3. Upload ZIP file with Lookbook free
4. Depends on php mode (apache module, cgi, fpm-cgi) yo may need to check write permissions for folder /wp-content/uploads
5. Click `Activate Plugin` button.

You're done!

== Frequently Asked Questions ==

1. What is the difference between Free and Professional versions of Altima Lookbook for WooCommerce?
Professional version allows to create unlimited number of sliders, slides and hotspots and has other additional nice features. You can get it for $49 from http://shop.altima.net.au/wordpress-plug-ins/lookbook-woocommerce-professional.html

2. Can I upgrade from Free to Professional version?
Unfortunately not, it is completely separate plug-ins, so you need to deactivate Free and install Pro version if decide to switch.

== Screenshots ==

1. Lookbook front end sample look
2. Another lookbook front end sample look
3. Third lookbook front end sample lool
4. Admin interface - manage slides
5. Admin interface - adding hotspot

== Changelog ==

= 1.0.1

* Bug fix release (Bug fix release, improvements for subdomain andsub folder installation) (released 02/22/2016)

= 1.0 =

* First public release.

== Wordpress Lookbook Free step by step guide ==

1. Tab "Settings"

    "Uploaded file max size (bytes)" - set limit for max file size for uploading files. For example if you interred 20 000 000 it means - you can upload files with maximum file size up to 20 MB (megabytes)
    
    "Allowed extensions" - string with comma separated file extensions allowed to upload. Example: png,gif,jpg,jpeg
    
    "Disallow hotspots areas overlap" - If "Yes", will disallow hotspots areas overlap.

2. Tab "Lookbook Sliders"

    * 2.1 Edit slider
    
        "Slider Name" - it's required field to identify slider.
    
        "Slider Width (px)" / "Slider Height (px)" - dimensions in pixels for slider, it's required fields.
    
        "Slider Thumbnail Width (px)" / "Slider Thumbnail Height (px)" - dimensions in pixels for slider thumbnails, it's required fields too.
    
        "Transition effect" - you can select one or multiple effects (keep CTRL pressed) from the list.
    
        "Show navigation" - this option activate navigation layer under the pictures - and right arrows and stop/play button.
    
        "Navigation on hover state only" - if "Yes" than navigation appears only if mouse will be over the slider area.
    
        "Show thumbnails" - control to show or hide thumbnails under the slides.
    
        "Deny resize images" - if you select "Yes" pictures will not resize after upload, it's have reason if you uploaded already prepared pictures for slider dimensions, otherwise for good result select option "No" and thumbnails will be resized automatically.
    
        "Pause" - time in milliseconds (1000 - 1s) before pictures will change, required fields.
    
        "Transition duration" - time in milliseconds, sets the duration of transition between the slides.
    
        "Content Before" - text before slider.
    
        "Content After" - text after slider.
    
        "Status" - enable/disable slider publication.
    
        "Show Slide Caption" - showing caption on the slide.
    
        Note: If you change slider or thumbnail dimensions it affects all already uploaded pictures(of course if "Deny resize images" - in "No" state).

    * 2.2 Manage Slides

        On this page placed ordered list of all slides.

        *   2.2.1 Add slide

            "Name" - arbitrary text field, but required.
    
            "Caption" - text for slide caption
    
            "Order" - numeric field to assign the order of slides, lower order means priority to display
    
            "Link" - default url address, for situation when user clicks outside hotspot areas or when the slider degraded to simple gallery. Must start with http://.
    
        "Status" - enable/disable slide in slider.
    
        "Upload file" - field for picture files.

3. Hotspot point

    1. After slide image is uploaded,  "Add Hotspot" button appears below the image 

    2. Click to "Add Hotspot" to open dialog popup window, where you can select one of 2 type hotspot
    
        a - Product page
    
        b - External page

    3. For type "Product page" - you should input id of post, page or product from woocommerce shop plugin. If page/product/post with this id doesn not exist, you will see warning message.
    
    4. For type "External page" - you should input right url link for external page and title for this link.

    5. In edit mode hotpoint is draggable so you can place it on any places on picture.

    6. Also you can resize area around hotspot, it's area that activates hotspot for your website visitors

4. Inserting the Slider

    * 4.1 Inserting the Slider into the Post or Page.

        You should use shortcode. The shortcodes takes the following form:
        `[slider_render slider_id="1"]`

    * 4.2 Inserting the Slider into the template.

        The shortcode can be easily inserted into a template/theme which corresponds to the WordPress standards.
        It can be done by inserting the following shorcode:

        `<?php echo do_shortcode('[slider_render slider_id="1" admin=true]'); ?>`

        For shortcode possible 1 additional boolean parameter admin=true|false,
    
        if true - in slider will output all slides outside of slide parameter "Status".