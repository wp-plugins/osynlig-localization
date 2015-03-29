osynlig-wordpress-localization
=====================

Basic Wordpress plugin that makes it possible to change your websites locale.

Installation
  1. Download the plugin here (and name it osynlig-localization) or from wordpress.org and put it in your plugins folder. Activate the plugin.
  2. Add your website translations to a folder named languages in your current theme. Make sure they are named as the locale. For example sv_SE.mo.
  3. Go to "Settings -> Localization" and choose the locale.
  4. Done! Your front-end should now display in the locale you choose (don't forget load_theme_textdomain).