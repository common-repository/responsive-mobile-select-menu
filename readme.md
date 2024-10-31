# Responsive Mobile Select Menu - WordPress Plugin #

**Author URI:** https://www.saskialund.de

**Contributors:** Jyria

**Donate link:** https://www.saskialund.de/donate/

**Tags:** responsive, menu, select, drop down, dropdown, mobile menu

**Requires at least:** 4.6

**Tested up to:** 6.6.9

**Requires PHP:** 7.4

**Stable tag:** 1.1.4

**License:** GPLv2 or later

**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

The Responsive Mobile Select Menu plugin automatically turns any WordPress Menu into a select box / dropdown on mobile devices.

## Description ##

One common UI paradigm for navigation menus with responsive design is to display a select box (form element) for mobile devices. 
This plugin allows you to turn your WordPress menu into a select box below a browser viewport width of your choice.

This is the successor plugin of former, unfortunately abandoned Responsive Select Menu by sevenspark.
It has been made translation-ready, PHP 7.4 ready and its code has been rewritten and restructured to work with WordPress 5.x.x

** Features **

* Takes up less screen real estate on mobile devices
* Easier navigation for touch screens
* Works automatically - no need to add extra PHP code

Please find a live demo of the plugin here: [https://responsive-select-menu.saskialund.de/](https://responsive-select-menu.saskialund.de/)

## Installation ##

This section describes how to install the plugin and get it working.

1. Upload the plugin zip through your WordPress admin
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Navigate to Appearance > Responsive Select to configure your menu


## Frequently Asked Questions ##

** Do I have to have a "Navigate" item as my first item **

You can change the name of this item, but it has to exist.  Otherwise, you won't be able to navigate to the first item in the menu
if you're not using the "Show currently selected item" - even if you have that option enabled, the issue would still exist on pages not 
in the menu.

** It doesn't work **

If your theme creates a menu the standard way with wp_nav_menu, it should work.



## Screenshots ##

1. Responsive select menu on the iPhone/iPod Touch
2. Responsive select menu Control Panel

## Changelog ##

**1.1.4**
* readme update and versioning
* Compatibility with WordPress 6.6.9

**1.1.3**
* readme update and versioning
* Compatibility with WordPress 6.4.x

**1.1.2**
* readme update and versioning
* typo correction regarding compatibility with WordPress 6.3.1

**1.1.1**
* readme update and versioning
* Compatibility with PHP 8.0 and 8.1

**1.1.0**
* readme update and versioning
* Compatibility with WordPress version 5.6.x and upcoming 5.7

**1.0.5**
* readme update and versioning
* Feature: added accessibility aria code (thanks @haefele for asking)
* Compatibility with PHP 7.4

**1.0.4**
* readme update and versioning

**1.0.3**
* Fix: under certain circumstances settings weren't saving - refactored

**1.0.2**
* Fix: under certain circumstances settings weren't saving.

**1.0.1**
* readme update and versioning

**1.0.0**

* Initial version
* Forked from Responsive Select Menu Plugin 1.7 which is 4 years old and unfortunately abandoned by its developer :(
* Added Translation Readyness
* Added German formal and informal translation files
* Compatibility with PHP 7.3
* Bugfix due to PHP 7.3 debug_log warnings
* Removed Ads and external hotlinked images from ControlPanel