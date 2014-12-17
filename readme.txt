=== Rising Sign Calculator plugin for WordPress ===
Contributors: isabel104
Author: Isabel Castillo
Author URI: http://isabelcastillo.com
License: GNU GPL Version 2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Rising Sign Calculator plugin adds a widget to WordPress that lets visitors calculate their Ascendant by entering their birth date, birth time, and birth city.  You can also add the Rising Sign Calculator to any page or post with a shortcode.

The calculator gives the Ascendant degree, minute, and second. You have the option to enter custom interpretations for the 12 rising signs.

Rising Sign Calculator uses the Swiss Ephemeris to get the longitude of the Ascendant. Information about the Swiss Ephemeris can be found at http://www.astro.com/swisseph/swephinfo_e.htm


**Support**

[Documentation and Instruction Guides](http://isabelcastillo.com/docs/category/rising-sign-calculator-wordpress-plugin)


If you purchased this software at http://isabelcastillo.com, you can get support at [http://isabelcastillo.com/support/](http://isabelcastillo.com/support/)


== Installation ==

1. Log in to you WordPress dashboard.
2. Go to 'Plugins -> Add New'
3. Click 'Upload', then upload the plugin file that you purchased.
4. Activate the plugin by clicking "Activate".
5. Now you are ready to use the plugin.
6. The Rising Sign Calculator widget will be available in `Appearance -> Widgets`
7. To use the widget, drag the widget to a sidebar widget area like you would any other widget.
8. To use the calculator on a page or post instead of a widget, paste this shortcode into the page or post:
     `[risingcalc]`
9. To write your own custom interpretation for each rising sign, go to 'Settings -> Rising Sign Calculator' from your WordPress admin dashboard.



== Extra Notes ==

**Requirements**

WordPress 3.3.1+,  
PHP 5.x+,  



**Included Files**

`rising-sign-calculator.php
rsc.css
dmf-widget.php
license.txt
readme.txt

images/aquarius.png
images/aries.png
images/cancer.png
images/capricorn.png
images/gemini.png
images/leo.png
images/libra.png
images/pisces.png
images/sagittarius.png
images/scorpio.png
images/taurus.png
images/virgo.png
images/ui-anim_16x16.gif

includes/ajax-tz-offset.php
includes/EDD_SL_Plugin_Updater.php
includes/rsc.js
includes/rsc-widget.php
includes/rtl.css

sweph/isabelse
sweph/sepl_18.se1

languages/rsc-es_ES.mo
languages/rsc-es_ES.po
languages/rsc.pot`


== Changelog ==

= 1.3.2 =
* Fix: Shortcode was called incorrectly.
* Maintenance: Removed some PHP notices.
* Maintenance: Updated plugin URI.

= 1.3.1 =
* Bug fix: license status option was not being updated upon license check on options page.
* Bug fix: sanitize_license function call had typo.
* Maintenance: updated .pot file and all language files.
* Tweak: scroll to top of widget, not page, upon form submit.

= 1.3 = 
* New: Version updates will be available directly in your WordPress dashboard.
* Bug fix: updated Geonames web services api url for city search.
* Bug fix: scroll to top upon form submit.
* Tweak: localized numbers.
* Maintenance: updated language files.

= 1.2.9 =
* Bug fix: custom interpretations were not showing for rtl languages.
* New: added rtl.css

= 1.2.8 =
* Bug fix: error in 1 string of translation file which included quotes.

= 1.2.7 = 
* New: added .pot localization template file.
* New: added Spanish translation.
* Bug fix: added missing languages directory.
* Bug fix: localized city list

= 1.2.6 = 
* Bug fix: some strings were not being translated with i18N localization.
* Tested for WP 3.8 compatibility.
* Adapted style for Twentyfourteen WP theme

= 1.2.5 = 
* Performance fix: was making too many ajax requests per second to calculate time offset.

= 1.2.4 = 
* Fix: Form was not validating for cities with GMT offset of '0' (cities such as London).

= 1.2.3 = 
* Fix: Form would not submit due to nonce clashing with cache. Removed since not needed. See http://kovshenin.com/2012/nonces-on-the-front-end-is-a-bad-idea/

= 1.2.2 = 
* Tweak: added pointer cursor to submit button.
* Tweak: removed unecessary $imgdir, functions, and fixed typo in file list.

= 1.2 = 
* New: set chmod automatically.

= 1.1 =
* Fix: shortcode appearance error.

= 1.0: May 15, 2013 =
* Initial release.