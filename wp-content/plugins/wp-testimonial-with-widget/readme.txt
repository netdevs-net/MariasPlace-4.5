=== Testimonial Grid and Testimonial Slider plus Carousel with Rotator Widget ===
Contributors: wponlinesupport, anoopranawat, pratik-jain, piyushpatel123, ridhimashukla, patelketan
Tags: testimonial, Testimonial, testimonials, Testimonials, widget,  Best testimonial slider, Responsive testimonial slider, client testimonial slider, easy testimonial slider, testimonials with widget, wordpress testimonial with widget, testimonial rotator, testimonial slider, Testimonial slider , testimonial with shortcode, client testimonial, client, customer, quote, shortcodes
Requires at least: 4.0
Tested up to: 6.0
Stable tag: 3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A quick, easy way to add and display responsive, clean client's testimonial on your website using a shortcode or a widget. Also work with Gutenberg shortcode block. 

== Description ==

[Explore Testimonials Features](https://demo.essentialplugin.com/testimonial-demo/?utm_source=WP&utm_medium=Testimonials&utm_campaign=Read-Me) | [Annual or Lifetime Bundle Deal](https://www.essentialplugin.com/pricing/?utm_source=WP&utm_medium=Testimonials&utm_campaign=Read-Me)

[WP Testimonials with rotator widget](https://www.essentialplugin.com/wordpress-plugin/wp-testimonial-with-widget/?utm_source=WP&utm_medium=Testimonials&utm_campaign=Read-Me) plugin is very helpful to add interesting testimonials of your client's.

Testimonials are a significant part of your website design. WP testimonial with rotator widget enables you to add and display multiple testimonials on your website along with posting the author info, images, pictures, jobs, URL, position, etc. Make your customer recommendations, your ladder to success with this WP plugin.

**Also work with Gutenberg shortcode block.** 

= This testimonial plugin contain two shortcode =

<code>[sp_testimonials] and [sp_testimonials_slider]</code>

Where you can display testimonial in list view, in grid view and slider testimonial with responsive.

= Features =
[youtube https://www.youtube.com/watch?v=m_ULWQ57qfI]

**Please make sure that Permalink link should not be "/testimonial" Otherwise all your Testimonials will go to archive page. You can give it other name like "/testimonials, /our-testimonial, /what-our-say etc"**

This plugin creates a testimonial and a testimonial rotator/testimonial slider custom post type, complete with WordPress admin fields for adding testimonials. It includes a Widget and Shortcode to display the testimonials.

= Here is the Testimonial shortcode example =

<code>[sp_testimonials]</code>

If you want to display Testimonial by category then use this short code 

<code>[sp_testimonials  category="category_ID"]</code>

If you want to display Testimonial using slider then use this short code
 
<code>[sp_testimonials_slider limit="2" slides_column="2"  slides_scroll="2" dots="false" arrows="false" autoplay="true" autoplay_interval="100" speed="5000" ]</code>

= Shortcode Examples =

<code>
1. Simple list/Grid view
[sp_testimonials] OR [sp_testimonials per_row="2"]

2. Slider (per row one/per row two)
[sp_testimonials_slider] OR [sp_testimonials_slider slides_column="2"]</code>

= Use Following Testimonial parameters with shortcode =
<code>[sp_testimonials]</code>

* **limit:**
[sp_testimonials limit="5"] ( i.e. Display 5 testimonials on your website )
* **design:**
[sp_testimonials design="design-1"] ( i.e. Select the design for testimonial. Values are design-1, design-2, design-3, design-4 )
* **Grid:**
[sp_testimonials per_row="2"]( i.e. Display your testimonials by Grid view )
* **orderby:**
[sp_testimonials orderby="title"] ( i.e. Order your testimonials by "title" OR "post_date" OR "none" OR "name" OR "rand" OR "ID" )
* **order:**
[sp_testimonials order="ASC"] ( i.e. Order your testimonials by "ASC" OR "DESC" )
* **Display by category**
[sp_testimonials  category="category_ID"] ( i.e. Display testimonials by their category ID )
* **Display client:**
[sp_testimonials display_client="false"] ( Display Client name OR: You can use "true" OR "false")
* **Display job title:**
[sp_testimonials display_job="false"] ( Display Client job title : You can use "true" OR "false")
* **Display company name:**
[sp_testimonials display_company="false"] ( Display Client company name : You can use "true" OR "false")
* **Display avatar:**
[sp_testimonials display_avatar="false"] ( Display Client avatar : You can use "true" OR "false")
* **Avatar size and style:**
[sp_testimonials size="150" image_style="square"] (Set size of Client avatar and style - square, circle )
* **Display Quotes:**
[sp_testimonials display_quotes="false"] ( Display Quotes: You can use "true" OR "false")

= Use Following Testimonial Slider parameters with shortcode =
<code>[sp_testimonials_slider]</code>

* **Slide columns for testimonial rotator:**
[sp_testimonials_slider slides_column="2"] (Display no of columns in testimonial rotator )
* **design:**
[sp_testimonials_slider design="design-1"] ( i.e. Select the design for testimonial. Values are design-1, design-2, design-3, design-4 )
* **Number of testimonial slides at a time:**
[sp_testimonials_slider slides_scroll="2"] (Controls number of testimonial rotate at a time)
* **Pagination and arrows:**
[sp_testimonials_slider dots="false" arrows="false"]
* **Autoplay and Autoplay Interval:**
[sp_testimonials_slider autoplay="true" autoplay_interval="100"]
* **Testimonials Slide Speed:**
[sp_testimonials_slider speed="3000"]
* **limit:**
[sp_testimonials_slider limit="5"] ( i.e. Display 5 testimonials on your website )
* **orderby:**
[sp_testimonials_slider orderby="title"] (i.e. Order your testimonials by "title" OR "post_date" OR "none" OR "name" OR "rand" OR "ID" )
* **order:**
[sp_testimonials_slider order="ASC"] ( i.e. Order your testimonials by "ASC" OR "DESC" )
* **id:**
[sp_testimonials_slider id="testimonail_id"] ( i.e. Display testimonials by their ID )
* **Display  by category**
[sp_testimonials_slider  category="category_ID"] ( i.e. Display testimonials by their category ID )
* **Display client:**
[sp_testimonials_slider display_client="false"] ( Display Client name OR: You can use "true" OR "false")
* **Display job title:**
[sp_testimonials_slider display_job="false"] ( Display Client job title : You can use "true" OR "false")
* **Display company name:**
[sp_testimonials_slider display_company="false"] ( Display Client company name : You can use "true" OR "false")
* **Display avatar:**
[sp_testimonials_slider display_avatar="false"] ( Display Client avatar : You can use "true" OR "false")
* **Avatar size and style:**
[sp_testimonials_slider size="150" image_style="square"] (Set size of Client avatar and style - square, circle )
* **Display Quotes:**
[sp_testimonials_slider display_quotes="false"] ( Display Quotes: You can use "true" OR "false")
* **Adaptive Height:**
[sp_testimonials_slider adaptive_height="false"] ( Allow slider to use Adaptive Height )

= Here is Template code =
<code><?php echo do_shortcode('[sp_testimonials]'); ?> </code>
<code><?php echo do_shortcode('[sp_testimonials_slider]'); ?> </code>

= Available fields : =
* Title
* Testimonials Content
* Job Title
* Company
* Website URL
* Picture

= New Features include: =
* Added Gutenberg block support.
* Added 4 New Designs.
* Also work with Gutenberg shortcode block. 
* Display Testimonial categories wise.
* Display Testimonial on home page with limit <code>[sp_testimonials limit="1" ]</code> 
* Adding a Random Testimonial to Your Page.
* Responsive.
* Display testimonials using an easy testimonial widget.
* Add Client image.


Best Testimonial Slider and Showcase plugin for WordPress website. It is a developer and user-friendly testimonial plugin that facilitates easy management of customer testimonials. Since it is fully responsive, it ensures that your testimonials appear in beautiful layouts on all devices.

= Pro Features include: =
> <strong>Premium Version</strong><br>
>
> * Added 15 New Designs.
> * Testimonial front-end form.
> * Star rating added.
> * Gutenberg Block Support.
> * Template overriding feature support.
> * Visual Composer / WPBakery Page Builder Supports.
> * Display testimonials using 15 testimonial widget designs.
> * Display Testimonial categories wise.
>
> [Explore Testimonials Features](https://www.essentialplugin.com/wordpress-plugin/wp-testimonial-with-widget/?utm_source=WP&utm_medium=Testimonials&utm_campaign=Read-Me)
>

= Why Use Testimonials? =
* The web has made it easier for consumers to get recommendations not only from friends, but to see secure, verified Testimonial from people all over the world.
* Testimonials help potential customers get to know that you are a credible business.
* Testimonials, when used effectively, are a great tool to increase conversions rates on your website!

= Privacy & Policy =
* We have also opt-in e-mail selection , once you download the plugin , so that we can inform you and nurture you about products and its features.

== Installation ==

1. Upload the 'WP Testimonial Plugin with Widget' folder to the '/wp-content/plugins/' directory.
2. Activate the "WP Testimonial Plugin with Widget" list plugin through the 'Plugins' menu in WordPress.
3. Add a new page and add this short code 
<code>[sp_testimonials]</code>
4. If you want to display Testimonial using slider then use this short code 
<code>[sp_testimonials_slider]</code>
5. Here is Template code 
<code><?php echo do_shortcode('[sp_testimonials]'); ?> </code>
6. If you want to display Testimonial using slider then use this template code
<code><?php echo do_shortcode('[sp_testimonials_slider]'); ?> </code>

= How to install : =
[youtube https://www.youtube.com/watch?v=gUIp0rCNsHg]  

== Screenshots ==

1. Simple list view
2. Grid view
3. Slider (per row one)
4. Slider (per row two)
5. all Testimonials
6. Creating testimonials (admin view)
7. Widget Setting
8. Also work with Gutenberg shortcode block. 

== Changelog ==

= 3.1 (24, June 2022) =
* [*] New - Added "rtl" support in testimonial slider shortcode.
* [+] New - Added Elementor, SiteOrigin, and Beaver page builder native support.
* [+] New - Added Divi page builder and Fusion Page Builder (Avada) native support.
* [*] Update - Use escaping and sanitize functions for better security.
* [*] Update - Removed some unwanted code and files.

= 3.0.8 (18, May 2022) =
* [*] Tested up to: 6.0

= 3.0.7 (28, March 2022) =
* [+] Added demo link.
* [-] Removed some unwanted code and files.

= 3.0.6 (11, Feb 2022) =
* [-] Removed some unwanted code and files.

= 3.0.5 (04, Feb 2022) =
* [*] Tested up to: 5.9 
* [*] Solved Gutenberg wp-editor widget issue.

= 3.0.4.1 (15, Dec 2021) =
* [*] Minor fix.

= 3.0.4 (12, Nov 2021) =
* [*] Fix - Resolve Gutenberg WP-Editor script related issue. 
* [*] Update - Add some text and links in readme.txt file.

= 3.0.3 (15, Sep 2021) =
* [*] Tested up to: 5.8.1
* [*] Updated demo link

= 3.0.1 (19, Aug 2021) =
* [*] Tested up to: 5.8
* [*] Updated language file and JSON file.

= 3.0 (17, Aug 2021) =
* [*] Updated all external links.
* [*] Tweak - Code optimization and performance improvements.

= 2.6.3 (31, May 2021) =
* [*] Added - https link in our analytics code to avoid browser security warning.
* [*] Tested up to: 5.7.2
* [*] Tweak - Code optimization and performance improvements.

= 2.6.2 (05, April 2021) =
* [*] Resolved minor taxonomy sting translation issue.
* [*] Check compatibility to WordPress version 5.7.

= 2.6.1 (08, Dec 2020) =
* [*] Tested up to WP 5.6
* [*] Fixed the design number 3 and 4 if image not added.

= 2.6 (27, Oct 2020) =
* [+] New - Click to copy the shortcode from the getting started page.
* [*] Update - Regular plugin maintenance. Updated readme.txt file.
* [*] Added - Added our other Popular Plugins under WP Testimonials --> Install Our Popular Plugins From. This will help you to save your time during creating a website.
* [*] jQuery( document ).ready(function($) is replaced with function( $ ) to solve the issue with 3rd party plugin and theme js error.

= 2.5.1 (14 July, 2020) =
* [*] Follow WordPress Detailed Plugin Guidelines for Offload Media and Analytics Code.

= 2.5 (07 July, 2020) =
* [+] New - Added Gutenberg block support. Now use plugin easily with Gutenberg!
* [+] New - Added 'align' and 'extra_class' parameter for shortcodes. Now all shortcodes are support twenty-ninteent and twenty-twenty theme Gutenberg block align and additional class feature.
* [*] Tweak - Code optimization and performance improvements.
* [*] Template File - Main design file has been updated. If you have override template file then verify with latest copy.

= 2.4.3 (27 Dec, 2019) =
* [*] Updated features list.
* [*] Fixed arrow:active box-shadow issue.
* [*] Fixed link outline issue.

= 2.4.2 (22 Nov, 2019) =
* [*] Fixed : Fixed a very small margin issue in design.

= 2.4.1 (22 August, 2019) =
* [*] Fixed : Fixed the arrow display issue in widget testimonial design-4.

= 2.4 (13 August, 2019) =
* [*] Updated : Updated complete code structure of the plugin.
* [+] Added : Added better security with esc_url() and esc_html() in the design files.
* [*] Updated: orderby and order shortcode paremeter are now working fine without any issue.
* [+] Added : New file added wtwp-public.js and JS code removed from shortcode files.
* [*] Updated : wp-widget-testimonials.php file complete code updated.
* [-] Removed : Removed unwanted functions.
* [*] Note : Clear your cache if your are using any caching plugin after you update this plugin.

= 2.3.1 (10 Jan, 2019) =
* [*] Update Opt-in flow.

= 2.3 (08 Dec, 2018) =
* [*] Tested with WordPress 5.0 and Gutenberg.
* [*] Fixed some CSS issues.

= 2.2.8 (06 June, 2018) =
* [*] Follow some WordPress Detailed Plugin Guidelines.

= 2.2.7 (14-5-2018) =
* [*] Fixed deprecated function issue.

= 2.2.6 (31-8-2017) =
* [*] Fixed slider navigation style issue.

= 2.2.5 (21-7-2017) =
* [+] Added better structure for all files.
* [+] Added adaptive_height="false" OR  adaptive_height="true" new shortcode parameter for slider and widget slider.
* [+] Added wtwp_ prefix to all CSS classes to avoid conflict with other plugins and theme.
* [+] Added latest slick.js file.
* [+] adaptive height option in widget.
* [*] Fixed all CSS issues.
* [*] Design-4 now also added in the widget.

= 2.2.4 =
* Fixed some CSS issues.

= 2.2.3 =
* Fixed some CSS issues.
* Resolved multiple slider jQuery conflict issue.

= 2.2.2 =
* Added 'display_quotes' parameter in shortcode to show and hide emphasized tag.

= 2.2.1 =
* Fixed some bug
* Removed 'display_url' and added as a href link.
* Added 15 Pro Designs.

= 2.2 =
* Fixed some bug.

= 2.1 =
* Fixed some CSS bug and designs.
* Display more then 1 testimonial post in widget.

= 2.0.1 =
* Fixed some CSS bug and designs.
* Display more then 1 testimonial post in widget.

= 2.0 =
* Added 4 themes designs.
* Removed radio button to select the theme.
* Just use the shortcode with design parameters i.e. design="design-1"

= 1.1.1 =
* Added 2 themes designs.

= 1.1 =
* Added new Testimonial slider parameters i.e. autoplay, autoplayInterval, speed.

= 1.0 =
* Initial release.
* Adds custom post type.