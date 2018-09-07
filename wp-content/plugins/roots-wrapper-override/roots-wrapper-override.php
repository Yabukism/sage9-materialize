<?php
/*
Plugin Name:  Roots Wrapper Override
Plugin URI:   http://roots.io/plugins/roots-wrapper-override/
Description:  A WordPress plugin that allows you to override the Sage wrapper templates from the WordPress dashboard. Supports the base and sidebar templates by default, with additional overrides only an array away. Requires the <a href="http://roots.io/sage/">Sage</a> theme wrapper.
Version:      2.0.0
Author:       Nick Fox
Author URI:   http://roots.io/
License:      GPL
*/

defined('ABSPATH') or die('A common mistake that people make when trying to design something completely foolproof is to underestimate the ingenuity of complete fools. - Douglas Adams');

/**
 * We could put everything inside the class, but a lot of users are more comfortable customising
 * a function than a class. Hence why we will initiate the class in a function.
 * Each override is setup by an individual array containing two keys: prefix and subdir. You can
 * add any other overrides you like, with the 'rwo_overrides' filter.
 */
function roots_wrapper_override_init() {
  $base      = array('prefix' => 'base', 'subdir' => null); // The prefix simply matches your unique template naming convention.
  $sidebar   = array('prefix' => 'sidebar', 'subdir' => 'templates'); // The subdir is relative to you current theme's directory.
  $overrides = array($base, $sidebar); // We combine these arrays in a multidimensional array, to pass one argument.

  return new Roots_Wrapper_Override($overrides);
}

add_action('init', 'roots_wrapper_override_init');

include_once('src/plugin.php');
