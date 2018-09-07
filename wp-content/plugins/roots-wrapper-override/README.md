# Roots Wrapper Override
A WordPress plugin that allows you to override the Roots wrapper templates from the WordPress dashboard. Supports the base and sidebar templates by default, with additional overrides only an array away (see **Extending**).

Requires the [Roots](http://www.roots.io) theme and wrapper to function. 

Fully supported in `7.0.0` or higher. Support for `6.4.0` and higher can easily be added by updating the `Roots_Wrapping` class and filters. See the FAQ for details.

## Installation
Unzip and place into the `plugins` directory of your Roots based theme and activate. 

When using `6.4.0` please ensure the `Roots_Wrapping` class and helper functions (found in `lib/utils.php` or `lib/wrapper.php`) matches the class in **FAQ.md**. You will also need to change the filter in `lib/config.php` to `roots/display_sidebar`.

## Usage
The templates available to select are shown in a meta box drop down list. Simply select the desired template and update to save. The template is saved as custom meta and, as its name suggests, will override the template selected by the `Roots_Wrapping` class.

To deselect a template, set to `(no override)` and save or remove the template file.

You can also select whether to manually show or hide the sidebar.

### Naming Templates
Template filenames must begin with the same prefix as the default template i.e. all `base` templates must begin `base-` and all `sidebar` templates `sidebar-`. If you want to give your templates nice names, please see **FAQ.md**.

## Extending
Please see **FAQ.md** for how you can extend this plugin.

## Uninstallation
Deactivate the plugin to disable the overrides. Presently, the custom meta fields, `_roots_rwo_$prefix`, are not deleted with deactivation. This will assist with troubleshooting and prevent resetting the custom templates upon accidental deactivation.

## License and Support
As with most WordPress themes, plugins and templates, the PHP source code of this plugin is licensed under the [GPLv2 or later](http://www.gnu.org/licenses/gpl-2.0.html). 

Unless otherwise stated, all other code, including but not limited to, any Javascript, jQuery, CSS, Markdown and this plugin's support documentation is Copyright (C) 2013 [Roots](http://roots.io/) and Nick Fox. All rights reserved. 

If you would like to redistribute this plugin under the terms of the GPLv2, please remove any Copyrighted materials, including this README.md and FAQ.md and provide your own in their place.

Please note, that unless you have purchased this plugin through its official [Roots page](http://roots.io/plugins/roots-wrapper-override/) or an authorized vendor, support is provided on a voluntary basis only. For those who have purchased this plugin, installation support will be provided for one domain, so please purchase a copy for each production site the plugin will be used on. The author should be contacted within 30 days of purchase for any assistance.

For multisite support, or for a discount on multiple purchases (5+ copies), please contact the author.