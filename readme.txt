=== Plugin Name ===
Contributors: osdwebdev
Tags: wordpress, social media, facebook, twitter, pinterest, linkedIn, google plus, google, email, osd, social media sharing, share buttons, sharing buttons, jetpack sharing, jetpack, custom sharing icons
Requires at least: 3.4
Tested up to: 4.1
Stable tag: 3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

OSD Social Media Sharing is extremely light weight and does not require external libraries to share your WordPress content on social media sites.

== Description ==

OSD Social Media Sharing gives the ability to add sharing buttons anywhere on your site with a shortcode, or automatically generate them based on post type. This plugin is very similar to Jet Pack sharing without all of the bloat and external requests. Place sharing in sidebars / widget areas using the WordPress text widget and the OSD Social Media Sharing shortcode. Place the buttons in any content field with the shortcode as well. Simple administration can be found under the settings tab including Drag and Drop re-ordering of the buttons, and the ability to add your own sharing icons. Remove the icons on a per post basis using the check box in the right sidebar of the edit post screen. This plugin does not hook into the content filter for post type based display!! this means that the sharing buttons will only appear ONE time on your pages, not all over the place like some other plugins.

Shortcode:
[osd_social_media_sharing]

== Installation ==

1. Upload osd-social-media-sharing directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to the general settings tab, and then OSD Sharing settings
4. Save your options
5. Add the shortcode, or rely on post type
6. Start sharing!

== Frequently Asked Questions ==

= Soon to come? =

Yes, as users ask us questions.

== Screenshots ==

1. Plugin in action on the page
2. Administration page under Settings
3. The metabox on the edit post screen


== Changelog ==

= 3.2 =
* OSD Social Media Sharing now prevents the offical Pinterest JS from hijacking the link/picture
* Fixed bug where opened windows were all the same size
* General bug fixes

= 3.1 =
* Added some shortcodes to the custom url
* [page]  this is the current url (the page that the user would share)
* [title]  this is the current page title

= 3.0 =
* THIS CHANGE WILL REQUIRE YOU TO RE-SAVE YOUR SETTINGS IN THE ADMIN SCREEN!!!!
* SOME CLASSES HAVE CHANGED, SO YOUR CUSTOM STYLING MAY BE EFFECTED!!!
* Changed icons to png from svg for better cross browser support
* Upgraded plugin to allow custom link / icon pairs

= 2.3.0 =
* Removed console logging in admin screens
* Fixed broken image linking in admin screens
* Updated image script in admin screens

= 2.2.0 =
* Added page url to email button
* Added ability to change the default send to mail address

= 2.1.3 =
* Fixed minor styling bug where shortcode and default placing of osd social media looked different

= 2.1.2 =
* Changed link hrefs to help remove 302 and 301 redirects

= 2.1.1 =
* Fixed broken images in settings pages
* Fixed Pinterest image picker showing up if there are no images
* Fixed JS error bug
* Fixed icon ordering issues in settings pages

= 2.1.0 =
* Fixed a styling issue with IE (icons were spaced too far apart due to IE rendering error)
* Streamlined icons into one SVG sprite (only one network request now)!

= 2.0.2 =
* General bug fixes

= 2.0.1 =
* Fixing Pinterest image picker styling

= 2.0 =
* Added Pinterest image picker while maintaining no external library loading
* Corrected the open in same window mode
* Improved default styling

= 1.1 =
* Fixed bug with quick edit overwriting plugin settings

= 1.0 =
* Initial creation

== Upgrade Notice ==

= 3.1 =
* Added some shortcodes to the custom url
* [page]  this is the current url (the page that the user would share)
* [title]  this is the current page title

= 3.0.1 =
* Upgrade bug fixes
* YOU MUST DE-ACTIVATE AND THEN ACTIVATE THE PLUGIN TO HAVE A SUCCESSFUL UPGRAGE TO 3.0

= 3.0 =
* THIS UPGRADE WILL REQUIRE YOU TO RESET YOUR SETTINGS!!!!
* SOME CLASSES HAVE CHANGED, SO YOUR CUSTOM STYLING MAY BE EFFECTED!!!
* Added the ability to enter custom sharing links / icons

= 1.1 =
* Fixed bug with quick edit overwriting plugin settings

= 1.0 =
Start Sharing!

== A brief Feature List ==

1. Sharing buttons can be used in the Text Widget provided by WordPress
2. Sharing buttons can be used in the WordPress wysiwyg (any where that runs the content filter)
3. Sharing buttons can be automatically loaded based on post type (page, post, custom content types work too!)
4. Lightweight
5. Shortcode usage allows for flexibility
6. Simple, rewritable styling
7. Ability to upload your own Icons!
8. Remove the icons on a per post basis with a check box in the edit post screen
9. Does not hook into the content filter for the post type!
10. Ability to change pinterest icon

Link to plugin page [Wordpress plugin page](http://wordpress.org/plugins/osd-social-media-sharing "Link").

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"