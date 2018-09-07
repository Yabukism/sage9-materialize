# Frequently Asked Questions

### Q1: I've set the overrides but the templates are not changing, why?
Please make sure you are using the correct version of the plugin. Sage requires version 2, Roots 7.0.0 requires version 1 and anything earlier requires 0.9.1. All three are available to be downloaded from Gumroad.

When using `6.4.0` please ensure the `Roots_Wrapping` class and helper functions (found in `lib/utils.php` or `lib/wrapper.php`) matches the class below.

```php
<?php
/**
 * Theme wrapper
 *
 * @link http://scribu.net/wordpress/theme-wrappers.html
 */
function roots_template_path() {
  return Roots_Wrapping::$main_template;
}

function roots_sidebar_path() {
  return new Roots_Wrapping('templates/sidebar.php');
}

class Roots_Wrapping {
  // Stores the full path to the main template file
  static $main_template;

  // Stores the base name of the template file; e.g. 'page' for 'page.php' etc.
  static $base;

  public function __construct($template='base.php') {
    $this->slug = basename($template, '.php');
    $this->templates = array($template);
    
    if (self::$base) {
      $str = substr($template, 0, -4);
      array_unshift($this->templates, sprintf($str . '-%s.php', self::$base));
    }
  }
  
  public function __toString() {
    $this->templates = apply_filters('roots/wrap_' . $this->slug, $this->templates);
    return locate_template($this->templates);
  }
  
  static function wrap($main) {
    self::$main_template = $main;
    self::$base = basename(self::$main_template, '.php');

    if (self::$base === 'index') {
      self::$base = false;
    }
    
    return new Roots_Wrapping();
  }
}
add_filter('template_include', array('Roots_Wrapping', 'wrap'), 99);
?>
```

### Q2: How can I customise the plugin title, the help text or the (no override) option?
The text used in the plugin can now be customised with `apply_filters`, as per the examples below;

```php
<?php
add_filter('rwo_title', 'rwo_title_custom');
function rwo_title_custom() {
  return 'Select Base Template'; // Replaces 'Roots Wrapper Override'
}

add_filter('rwo_field', 'rwo_field_custom');
function rwo_field_custom() {
  return '(apply default)'; // Replaces '(no override)'
}

add_filter('rwo_help', 'rwo_help_custom');
function rwo_help_custom() {
  return 'This is the new help text'; // Replaces 'Set to (no override) to allow the wrappers to apply the default template.'
}

add_filter('rwo_sidebar_title', 'rwo_sidebar_title_custom');
function rwo_sidebar_title_custom() {
  return 'This is the new help text'; // Replaces 'Display Sidebar'
}

add_filter('rwo_sidebar_show', 'rwo_sidebar_show_custom');
function rwo_sidebar_show_custom() {
  return 'This is the new help text'; // Replaces 'Show'
}

add_filter('rwo_sidebar_hide', 'rwo_sidebar_hide_custom');
function rwo_sidebar_hide_custom() {
  return 'This is the new help text'; // Replaces 'Hide'
}

add_filter('rwo_sidebar_field', 'rwo_sidebar_field_custom');
function rwo_sidebar_field_custom() {
  return 'This is the new help text'; // Replaces '(no override)'
}

add_filter('rwo_sidebar_help', 'rwo_sidebar_help_custom');
function rwo_sidebar_help_custom() {
  return 'This is the new help text'; // Replaces 'Requires compatible base template.'
}
?>
```

Thanks to [@iagdotme](http://twitter.com/iagdotme) for the original suggestion.

### Q3: I've just read Q2, surely there is a better way to customise the text?
Yes there is. You can pass an array that is deciphered via list.

```php
<?php
add_filter('rwo_text', 'rwo_text_custom');
function rwo_text_custom() {
  return array(
    'Roots Wrapper Override Custom',
    '(no override custom)',
    'Set to (no override custom) to allow the wrappers to apply the default template.'
  );
}

add_filter('rwo_sidebar_text', 'rwo_sidebar_text_custom');
function rwo_text_custom() {
  return array(
    'Display Sidebar Custom',
    'Shown',
    'Hidden',
    '(no override custom)',
    'Compatible base template is required'
  );
}
?>
```

### Q4: Can I control templates for more than just the base and sidebar?
This plugin is a singleton, meaning that it can only be initiated once. But you can filter which overrides are applied by placing the following in your `lib/custom.php`:

```php
<?php 
add_filter('rwo_overrides', 'roots_wrapper_overrides_custom'); // Initiate with custom settings.
function roots_wrapper_overrides_custom($templates) {
  $base      = array('prefix' => 'base', 'subdir' => null); // Define all your templates here.
  $sidebar   = array('prefix' => 'sidebar', 'subdir' => 'templates');
  $example   = array('prefix' => 'example', 'subdir' => 'exampledir');

  $overrides = array($base, $sidebar, $example); // All overrides.

  return $overrides;
}
?>
```

To use `exampledir/example.php` in a template you then need to include it via the `Roots_Wrapping` or `SageWrapping` class. This ensures the filters neccessary to enable the override are correctly applied:

```php
<?php include new Roots_Wrapping('exampledir/example.php'); ?>
```

```php
<?php include new Roots\Sage\Wrapper\SageWrapping('exampledir/example.php'); ?>
```

### Q5: Can I change the templates on a per post type basis?
Yes you can and there's even a helper function you can use to determine the post type. Follow the procedure below:

```php
<?php 
add_filter('rwo_overrides', 'roots_wrapper_overrides_custom'); // Initiate with custom settings.
function roots_wrapper_overrides_custom($templates) {
  $base      = array('prefix' => 'base', 'subdir' => null); // Define all your templates here.
  $sidebar   = array('prefix' => 'sidebar', 'subdir' => 'templates');
  $example   = array('prefix' => 'example', 'subdir' => 'exampledir');

  $overrides = array($base, $sidebar, $example); // All overrides.

  $post_type = Roots_Wrapper_Override::postType(); // Get the post_type.

  if ($post_type === 'page') return array($base, $example);
  if ($post_type === 'custom_post_type') return array($sidebar, $example);
  
  return $overrides;
}
?>
```

### Q6: Your screenshots showed nice names instead of filenames, how can I do that?
The nice names are supported using the WordPress `get_file_data` function, it's the same function that's used by custom page templates. An example for a sidebar template is below:

```php
<?php
/*
Sidebar Template: Custom media sidebar
*/
?>
```

### Q7: I've selected a sidebar template, but it's still not being shown, why?
There are two things that control the sidebar being displayed in a Roots or Sage theme. The base file must contain the following code to ensure the sidebar is loaded dynamically, instead of being hard-coded:

```php
<?php if (roots_display_sidebar()) : ?>
  <aside class="sidebar <?php echo roots_sidebar_class(); ?>" role="complementary">
    <?php include roots_sidebar_path(); ?>
  </aside><!-- /.sidebar -->
<?php endif; ?>
```
In Sage you will need to use the following:

```php
<?php if (Config\display_sidebar()) : ?>
  <aside class="sidebar" role="complementary">
    <?php include Wrapper\sidebar_path(); ?>
  </aside><!-- /.sidebar -->
<?php endif; ?>
```

When using Sage, make sure you namespace correctly:

```php
namespace Roots\Sage;

use Roots\Sage\Config;
use Roots\Sage\Wrapper;
```

The second requirement is that the `roots/display_sidebar` or `sage/display_sidebar` must also return true. You can either set this using the guide [here](http://roots.io/the-roots-sidebar/) or from version 1.0.0 you can override the sidebar display using the form provided. For pre-7.0.0 versions you will also need to make sure the filter in `lib/config.php` is `roots/display_sidebar`, or use `sage/display_sidebar` if you are using Sage.

### Q8: I already have some wrapper filters set up, will they stop working?
This plugin is compatible with manual filtering of the `roots/wrap_*` and `sage/wrap_*` filters. Just make sure not to remove the default template from the end of the `$templates` array, otherwise the override will fail. To avoid this scenario use `array_unshift($templates, $new_template);` instead. If you want to override the override, then use a filter priority of `101` or above: `add_filter('sage\wrap_base', 'override_the_override', 101);` or `add_filter('roots\wrap_base', 'override_the_override', 101);` when using Roots.

### Q9: I'm having issues, is there some king of debug mode?
Yes. Debug will automatically be enabled if `WP_DEBUG` is set to true. To enable it manually please add the following to your `lib/custom.php`: `add_filter('rwo_debug', '__return_true');`

### Q10: I'm still having issues, can you help?
Please check for issues on the [Roots Discourse Forum](http://discourse.roots.io/) or add a new post there if you still need help. This plugin was designed to be used by developers and it's been priced aggressively low as it shouldn't need much, if any, support. Please consider this before asking for support or installing the plugin on a new production site without paying. Bug reports are always useful though and are highly appreciated.

Copyright (C) 2013 [Roots](http://roots.io/) and Nick Fox. All rights reserved.
