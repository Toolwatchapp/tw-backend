Omega WordPress Theme
---------------------
Omega is a parent theme for WordPress and Omega child

Created by Hence Wijaya, https://themehall.com


Install
-------
1. Upload the omega folder via FTP to your wp-content/themes/ directory.
2. Go to your WordPress dashboard and select Appearance.
3. Be sure to activate the Omega child theme, and not the Omega parent theme.


License
-------
Omega WordPress Theme, Copyright (C) 2013 themehall.com
Omega WordPress Theme is licensed under the GPL.

Omega WordPress Theme, Copyright 2012 Joe Smith
Ginger is distributed under the terms of the GNU GPL

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see .

Omega WordPress Theme is derived from Hybrid Core, Copyright Justin Tadlock, justintadlock.com.
Hybrid Core is distributed under the terms of the GNU GPL

Omega WordPress Theme incorporates code from Genesis Framework, Copyright Copyblogger Media LLC, copyblogger.com.
Genesis Framework is distributed under the terms of the GNU GPL

Omega WordPress Theme incorporates code from Underscores WordPress Theme, Copyright 2013 Automattic, Inc.
Underscores WordPress Theme is distributed under the terms of the GNU GPL

Omega WordPress Theme bundles the following third-party resources:

HTML5 Shiv v3.6 stable, Copyright @afarkas @jdalton @jon_neal @rem
HTML5 Shiv are dual licensed under the MIT or GPL Version 2 licenses
Source: https://code.google.com/p/html5shiv/

TGM Plugin Activation, Copyright (c) 2012, Thomas Griffin
TGM Plugin Activation is distributed under the terms of the GNU GPL
Source: https://github.com/thomasgriffin/TGM-Plugin-Activation

jQuery Superfish Menu, Copyright (c) 2013 Joel Birch
jQuery Superfish Menu is distributed under the terms of the GNU GPL & MIT
Source: http://users.tpg.com.au/j_birch/plugins/superfish/

TinyNav, Copyright (c) 2011-2012 Viljami Salminen.
TinyNav is licensed under the MIT license
Source: http://tinynav.viljamis.com

Changelog
---------

1.1.3
- update TGM_Plugin_Activation class

1.1.2
- add placeholder attribute to Omega_Customize_Control_Textarea class
- add nofollow to footer link

1.1.1
- add add_theme_support( "title-tag" )
- fix custom_css sanitation

1.1.0
- fix omega_attr_entry_content schema property
- remove shortcode from theme
- remove get the image theme support, please use this instead https://wordpress.org/plugins/get-the-image/
- add menu.js

1.0.8
- h1 for logo on homepage

1.0.7
- add alt to logo image
- fix featured image layout for thumbnail

1.0.6
- add omega_before_loop action
- add favicon
- fixed blog layout option

1.0.5
- add omega_after_loop action

1.0.4
- fixed child theme info

1.0.3
- add single post Prev Next links

1.0.0
- update CSS and markup structure for accessibility
- add customizer options

0.9.10
- remove page-full-width.php

0.9.9
- fix pagination style css
- bring back the Layout Options
- Add compatibility with WordPress 3.9

0.9.8
- fix featured image issue from 0.9.6 update

0.9.7
- fix featured image issue from 0.9.6 update

0.9.6
- Replace layout meta box with templates
- move theme options to omega options plugin

0.9.5
- add validation to theme option
- replace do_atomic with do_action (back to basic)

0.9.4
- remove if class_exists( 'Omega' )
- remove about meta box
- add bbpress compatibility
- add Family Child theme

0.9.3
- Removed HTML Allowed Tags in WordPress Comment Section (done)
- fix theme setting issue 
- add do_action( 'omega_content' ); // omega_content

0.9.2
- fixed duplicate tag

0.9.1
- Add 2 child themes
- files cleaned up
- add featured widgets

0.9.0
- upgrade to HybridCore 2
- forking HybridCore 2

0.8.1   2013-10-24
- update credit link
- add mobile child theme

0.8.0   2013-10-22
- add header and footer actions
- add option to prevent Page Scroll When Clicking the More Link

0.7.1   2013-10-09
- fixed jetpack style
- add custom and church child themes
- hide admin bar for non super admin

0.7.0   2013-10-06
- Updated Omega hook filters 
- updated 404.php

0.6.0 	2013-09-20
- removed header_left and header_right action
- add template part files
- break down comments.php into template part

0.5.2 	2013-09-18
- added entry_author shortcode filter

0.5.1 	2013-09-17
- fixed markup validation error


0.5.0 	2013-09-08
- simplified header action hook
- fixed omega_footer_insert
- apply filter for entry byline and entry meta
- added omega wrap

0.4.1	2013-08-21
- removed entry_header hook
- fixed comment setting bug

0.4.0	2013-08-20
- add disqus compatibility
- add logo upload to Customizer > Branding
- move custom footer to Customize > Global Settings  (theme_mod)


0.3.6	2013-08-16
- update default setting
- add help tab
- add child theme page
- upgrade Hybrid Core to 1.6
- add more link option
- add Post Navigation Option
- move omega child theme to wordpress.org/themes/omega-child

0.3.5	2013-08-14
- add widget-wrap and entry-wrap
- update languages files
- fixed featured image setting

0.3.4	2013-08-12
- simplified CSS
- add footer widgets
- fixed featured image

0.3.3	2013-08-10
- add custom css
- fixed theme layout customizer

0.3.2	2013-08-10
- disabled omega_meta_template
- add archive theme setting

0.3.1	2013-08-08
- add more actions and filters
- fixed Meta Title

0.3.0	2013-08-03
- add sample omega child theme
- cleaned up files and functions
- add schema.org markup
- Introduced omega actions

0.2.4	2013-07-31
- moved array argument inside wp_nav_menu() - header.php
- moved font include to style.css
- add sidebar / page layout options

0.1.0 - 0.2.3	2013-07-28
- concept changes