<?php
/**
 * By writing this as a class, we can easily add aditional overrides as Roots grows or changes. We can also have a cleaner
 * naming convention and can avoid repeating ourself as much as possible. We also have greater control over how, when and
 * what we can call our functions.
 */
class Roots_Wrapper_Override {
  private static $_single; // Our class is a singleton, meaning it can only be initiated once.
  private static $plugin; // We'll use this for the plugin dir...
  private static $theme; // ...this for the theme dir...
  private static $overrides; // ...and this to hold the overrides.
  private static $title; // You can filter this to rename the plugin...
  private static $field; // ...filter the (no override) field text here...
  private static $help; // ...and customise the help text.
  private static $debug; // Enable debug.
  private static $level; // Minimum user level.
  private $error; // New error helper, which cannot be static.

  function __construct($overrides = array()) {
    self::$overrides = $overrides;
    if (!is_admin()) {
      if (!isset(self::$_single)) {
        return add_action('wp', array($this, 'addFilters'));
      } else {
        return; // We only need the filter when in the blog.
      }
    }

    self::$level = apply_filters('rwo_level', 'manage_options'); // Allow admins to filter required level.
    if (!current_user_can(self::$level)) return; // Exit if the user level isn't up to scratch.

    self::$debug = (defined('WP_DEBUG') && WP_DEBUG) ? true : apply_filters('rwo_debug', false);
    $this->error = $this->errors(); // Check for errors, including the singleton variable.

    if ($this->error) {
      if (is_wp_error($this->error)) {
        return add_action('admin_notices', array($this, 'displayError'));
      } else {
        return;
      }
    }

    self::$_single   = $this; // No errors, so we set up our singleton variable.
    self::$plugin    = dirname(dirname(__FILE__)); // Save our plugin dir for later.
    self::$overrides = $overrides; // and setup our default overrides.
    self::$theme     = trailingslashit(get_stylesheet_directory()); // All the admin only stuff.

    add_action('add_meta_boxes', array($this, 'addMetaBox'));
    add_action('post_updated', array($this, 'saveMeta'));
  }

  /**
   * We need to add our meta box to the admin pages. This only needs to be done once but at the right time.
   */
  public function addMetaBox() {
    $post_type = $this->postType(); // If there is no post type, we are not editing or adding posts...
    if (!$post_type) return; // ...so let's exit here.

    $override_help_text = array(
      apply_filters('rwo_title', __('Roots Wrapper Override', 'roots')),
      apply_filters('rwo_field', __('(no override)', 'roots')),
      apply_filters('rwo_help', __('Set to ' . self::$field . ' to allow the wrappers to apply the default template. ', 'roots'))
    );

    list(self::$title, self::$field, self::$help) =  apply_filters('rwo_text', $override_help_text);

    add_meta_box('roots-rwo-meta', self::$title, array($this, 'addMetaForm'), $post_type, 'side', 'high');
  }

  /**
   * Add the standard elements of our meta box. Then add the fields for each override.
   */
  public function addMetaForm($post) {
    wp_nonce_field('save_roots_rwo_meta', '_roots_rwo_nonce'); // It's good practice to always include a nonce.
    echo '<p>' . self::$help . '</p>';

    $this->filterOverrides(); // Update Overrides
    if (!self::$overrides || !is_array(self::$overrides)) return;

    foreach (self::$overrides as $override) {
      $this->addForm($post->ID, $override['prefix'], $override['subdir']);
    }

    $sidebar_override  = get_post_meta($post->ID, '_roots_rwo_override_sidebar', true); 
    $sidebar_help_text = array(
      apply_filters('rwo_sidebar_title', __('Display Sidebar', 'roots')),
      apply_filters('rwo_sidebar_show', __('Show', 'roots')),
      apply_filters('rwo_sidebar_hide', __('Hide', 'roots')),
      apply_filters('rwo_sidebar_field', __('(no override)', 'roots')),
      apply_filters('rwo_sidebar_help', __('Requires compatible base template.', 'roots'))
    );

    list($description, $show, $hide, $default, $help) =  apply_filters('rwo_sidebar_text', $sidebar_help_text);
    include(self::$plugin . '/templates/sidebar.php');
  }

  /**
   * Add our select fields, one for each override. Eventually we'll move our html to a separate template.
   */
  private function addForm($post_id, $prefix, $subdir = null) {
    $file   = get_post_meta($post_id, '_roots_rwo_' . $prefix, true);
    $file   = ($file === '' || !file_exists(self::$theme . $file)) ? 'none' : $file; // Ignore empty strings or missing files.
    $subdir = ($subdir !== null) ? trailingslashit($subdir) : null;

    include(self::$plugin . '/templates/form.php');
  }

  /**
   * This function will return a list of the available templates. We use it to help create our
   * select options and to check that the chosen files are valid.
   */
  private function templates($prefix, $subdir = null) {
    $templates = array();
    $dir       = self::$theme . $subdir;
    $files     = glob("$dir/{$prefix}*.php", GLOB_NOSORT);

    foreach ($files as $file) {
      $filename    = basename($file);
      $templates[] = $subdir . $filename;
    }

    return $templates;
  }

  /**
   * This function uses "$prefix Template: Custom" to enable our templates to have user friendly
   * names within PHP comments. It works exactly like custom page templates. It also sets the text we use when we
   * want to delete the override meta.
   */
  private function name($file, $prefix, $subdir = null) {
    if ($file === 'none') return self::$field;

    $name = get_file_data(self::$theme . $file, array($prefix . ' template'));
    $name = (current($name)) ? current($name) : basename($file);

    return $name;
  }

  /**
   * This function runs through the array of templates produced by $this->templates and reformats them as options.
   */
  private function values($current, $prefix, $subdir = null) {
    $options = $this->templates($prefix, $subdir);
    $format  = '<option value="%s">%s</option>';

    if (($key = array_search($current, $options)) !== false) unset($options[$key]);

    foreach ($options as $option) {
      printf($format, $option, $this->name($option, $prefix, $subdir));
    }
  }

  /**
   * Check everything is in order before calling for the meta to be updated for each override.
   */
  public function saveMeta($id) {
    if (!isset($_POST['_roots_rwo_nonce']) || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!wp_verify_nonce($_POST['_roots_rwo_nonce'], 'save_roots_rwo_meta')) return $this->fatalError('nonce');
    if (!current_user_can('edit_post', $id)) return $this->fatalError('permissions');

    $this->sidebarOverrides($id); // Save sidebar override

    $this->filterOverrides(); // Update Overrides
    if (!self::$overrides || !is_array(self::$overrides)) return;

    foreach (self::$overrides as $override) {
      $this->saveMetaField($id, $override['prefix']);
    }
  }

  /**
   * This is where we actually update the meta for each field, with a few more file specific checks.
   */
  private function saveMetaField($id, $prefix) {
    $file   = '_roots_rwo_' . $prefix;
    $folder = '_roots_rwo_folder_' . $prefix;

    if (!isset($_POST[$file])) return $this->fatalError('form', $prefix);

    $form   = $_POST[$file];
    $folder = (isset($_POST[$folder])) ? $_POST[$folder] : null;

    $validate = validate_file($form, $this->templates($prefix, $folder));
    if ($form !== 'none' && $validate !== 0) return $this->fatalError('validation');

    if ($form === 'none') {
      delete_post_meta($id, $file);
    } else {
      update_post_meta($id, $file, $form);
    }
  }

  /**
   * Update the sidebar override setting. Priviliges and nonce already checked.
   */
  private function sidebarOverrides($id) {
    $allowed = array('show', 'hide', 'none');
    $setting = $_POST['_roots_rwo_override_sidebar'];

    if(isset($setting) && in_array($setting, $allowed)) {
      if ($setting == 'none') {
        delete_post_meta($id, '_roots_rwo_override_sidebar');
      } else {
        update_post_meta($id, '_roots_rwo_override_sidebar',  $setting);
      }
    }
  }

  /**
   * We need to add our filters to make any use of the meta data. All our filters conform to the same format, so it's easy
   * to loop through the overrides, and add a filter for each.
   */
  public function addFilters() {
    $this->filterOverrides(); // Update Overrides
    if (!self::$overrides || !is_array(self::$overrides) || !is_singular()) return;

    foreach (self::$overrides as $override) {
      add_filter('sage/wrap_' . $override['prefix'], array($this, 'override'), 100);
    }

    add_filter('sage/display_sidebar', array($this, 'overrideSidebar'), 100);
  }

  /**
   * We only need one filter function because this plugin is predicated on a very strict naming convention. It'll add our
   * meta data at the start of the array of preferred templates. We could simply output the template we want,
   * but that will break things if templates are renamed, deleted or are modified by a later filter. Let's hope everyone
   * else employs the same logic or we could run into trouble.
   */
  public function override($templates) {
    $type     = end($templates); // the end of the array contains the default template...
    $type     = basename($type, '.php'); //... but we only need the prefix to know which filter we're filtering.
    $template = get_post_meta(get_queried_object_id(), '_roots_rwo_' . $type, true);

    if ($template !== '') array_unshift($templates, $template); // No point adding a blank template to the front of the queue.

    return $templates;
  }

  /**
   * We only need one filter function because this plugin is predicated on a very strict naming convention. It'll add our
   * meta data at the start of the array of preferred templates. We could simply output the template we want,
   * but that will break things if templates are renamed, deleted or are modified by a later filter. Let's hope everyone
   * else employs the same logic or we could run into trouble.
   */
  public function overrideSidebar($show) {
   $sidebar = get_post_meta(get_the_ID(), '_roots_rwo_override_sidebar', true);
   if (!$sidebar) return $show;
   return ($sidebar == 'show');
  }

  /**
   * Update overrides with apply_filters.
   */
  private function filterOverrides() {
    return self::$overrides = apply_filters('rwo_overrides', self::$overrides);
  }

  /**
   * Helper function to get current post type before get_current_screen is available.
   * This is useful if you want to customise which fields are displayed on a per post type basis.
   */
  public function postType() {
    if (isset($_GET['post'])) return get_post_type($_GET['post']);
    if (isset($_GET['post_type'])) return sanitize_key($_GET['post_type']);
    global $post;
    if (isset($post->post_type)) return $post->post_type;
    return false;
  }

  /**
   * Display error within the WordPress Dashboard.
   */
  public function displayError() {
    return printf('<div class="error"><p>%s</p></div>', $this->error->get_error_message());
  }

  /**
   * Fatal error messages.
   */
  public function fatalError($case, $arg = false) {
    if (!self::$debug) return;

    switch ($case) {
      case ('nonce') :
        $msg   = sprintf(__('%s: Sorry, the nonce provided has failed to verify.', 'roots'), get_class($this));
        $error = new WP_Error('nonce', $msg);
        break;
      case ('permissions') :
        $msg   = sprintf(__('%s: Sorry, there has been a permissions error. Please contact your administrator.', 'roots'), get_class($this));
        $error = new WP_Error('permissions', $msg);
        break;
      case ('form') :
        $msg   = sprintf(__('%s: Sorry, the correct form is not present for "' . $arg . '*-.php".', 'roots'), get_class($this));
        $error = new WP_Error('form', $msg);
        break;
      case ('validation') :
        $msg   = sprintf(__('%s: Sorry, file validation has failed.','roots'), get_class($this));
        $error = new WP_Error('validation', $msg);
        break;
      default :
        $msg   = sprintf(__('Sorry, there was a fatal error with %s, please try again', 'roots'), get_class($this));
        $error = new WP_Error('unknown', $msg);
    }
    return wp_die($error);
  }

  /**
   * If these requirements are not met, we exit the class and do nothing, or if self::$debug is true, and we're in the dashboard
   * we print out an error message. This ensures we won't end up disrupting users reading the blog or admins who don't care.
   */
  private function errors() {
    switch (true) {
      case (isset(self::$_single)) :
        $msg   = sprintf(__('%s is a singleton class and cannot be initiated twice.','roots'), get_class($this));
        $error = new WP_Error('singleton', $msg);
        break;
      case (did_action('init') === 0) :
        $msg   = sprintf(__('%s has been initiated too soon. Please use the init action.','roots'), get_class($this));
        $error = new WP_Error('initation', $msg);
        break;
      case (!class_exists('Roots\Sage\Wrapper\SageWrapping')) :
        $msg   = sprintf(__('%s requires the Roots Theme Wrapper to function.','roots'), get_class($this));
        $error = new WP_Error('dependency', $msg);
        break;
      default :
        return false; // Confirms no errors.
    }
    if (is_wp_error($error) && self::$debug) {
      return $error;
    } else {
      return true; // Only display errors if self::$debug is true.
    }
  }
}

?>
