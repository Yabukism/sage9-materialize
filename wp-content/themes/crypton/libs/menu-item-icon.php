<?php
/**
 * @package nav-menu-icon-fields
 * @version 0.1.0
 */
/*
Plugin Name: Nav Menu Custom Fields
*/

/*
 * Saves new field to postmeta for navigation
 */
add_action('wp_update_nav_menu_item', 'icon_nav_update',10, 3);
function icon_nav_update($menu_id, $menu_item_db_id, $args ) {
    if ( is_array($_REQUEST['menu-item-icon']) ) {
        $icon_value = $_REQUEST['menu-item-icon'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_icon', $icon_value );
    }
}

/*
 * Adds value of new field to $item object that will be passed to     Walker_Nav_Menu_Edit_icon
 */
add_filter( 'wp_setup_nav_menu_item','icon_nav_item' );
function icon_nav_item($menu_item) {
    $menu_item->icon = get_post_meta( $menu_item->ID, '_menu_item_icon', true );
    return $menu_item;
}

add_filter( 'wp_edit_nav_menu_walker', 'icon_nav_edit_walker',10,2 );
function icon_nav_edit_walker($walker,$menu_id) {
    return 'Walker_Nav_Menu_Edit_icon';
}

/**
 * Copied from Walker_Nav_Menu_Edit class in core
 *
 * Create HTML list of nav menu input items.
 *
 * @package WordPress
 * @since 3.0.0
 * @uses Walker_Nav_Menu
 */
class Walker_Nav_Menu_Edit_icon extends Walker_Nav_Menu  {
    /**
     * @see Walker_Nav_Menu::start_lvl()
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     */
    function start_lvl(&$output) {}

    /**
     * @see Walker_Nav_Menu::end_lvl()
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     */
    function end_lvl(&$output) {
    }

    /**
     * @see Walker::start_el()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Menu item data object.
     * @param int $depth Depth of menu item. Used for padding.
     * @param object $args
     */
    function start_el(&$output, $item, $depth, $args) {
        global $_wp_nav_menu_max_depth;
        $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        ob_start();
        $item_id = esc_attr( $item->ID );
        $removed_args = array(
            'action',
            'customlink-tab',
            'edit-menu-item',
            'menu-item',
            'page-tab',
            '_wpnonce',
        );

        $original_title = '';
        if ( 'taxonomy' == $item->type ) {
            $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
            if ( is_wp_error( $original_title ) )
                $original_title = false;
        } elseif ( 'post_type' == $item->type ) {
            $original_object = get_post( $item->object_id );
            $original_title = $original_object->post_title;
        }

        $classes = array(
            'menu-item menu-item-depth-' . $depth,
            'menu-item-' . esc_attr( $item->object ),
            'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
        );

        $title = $item->title;

        if ( ! empty( $item->_invalid ) ) {
            $classes[] = 'menu-item-invalid';
            /* translators: %s: title of menu item which is invalid */
            $title = sprintf( '%s (Invalid)' , $item->title );
        } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
            $classes[] = 'pending';
            /* translators: %s: title of menu item in draft status */
            $title = sprintf( '%s (Pending)', $item->title );
        }

        $title = empty( $item->label ) ? $title : $item->label;

        ?>
    <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
        <dl class="menu-item-bar">
            <dt class="menu-item-handle">
                <span class="item-title"><?php echo esc_html( $title ); ?></span>
                <span class="item-controls">
                    <span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
                    <span class="item-order hide-if-js">
                        <a href="<?php
                        echo wp_nonce_url(
                            add_query_arg(
                                array(
                                    'action' => 'move-up-menu-item',
                                    'menu-item' => $item_id,
                                ),
                                remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                            ),
                            'move-menu_item'
                        );
                        ?>" class="item-move-up"><abbr title="Move up">&#8593;</abbr></a>
                        |
                        <a href="<?php
                        echo wp_nonce_url(
                            add_query_arg(
                                array(
                                    'action' => 'move-down-menu-item',
                                    'menu-item' => $item_id,
                                ),
                                remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                            ),
                            'move-menu_item'
                        );
                        ?>" class="item-move-down"><abbr title="Move down">&#8595;</abbr></a>
                    </span>
                    <a class="item-edit" id="edit-<?php echo $item_id; ?>" title="Edit Menu Item'" href="<?php
                    echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
                    ?>">Edit Menu Item</a>
                </span>
            </dt>
        </dl>

        <div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
            <?php if( 'custom' == $item->type ) : ?>
                <p class="field-url description description-wide">
                    <label for="edit-menu-item-url-<?php echo $item_id; ?>">
                        URL<br />
                        <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
                    </label>
                </p>
            <?php endif; ?>
            <p class="description description-thin">
                <label for="edit-menu-item-title-<?php echo $item_id; ?>">
                    Navigation Label<br />
                    <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
                </label>
            </p>
            <p class="description description-thin">
                <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
                    Title Attribute<br />
                    <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
                </label>
            </p>
            <p class="field-link-target description">
                <label for="edit-menu-item-target-<?php echo $item_id; ?>">
                    <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
                    Open link in a new window/tab
                </label>
            </p>
            <p class="field-css-classes description description-thin">
                <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
                    CSS Classes (optional)<br />
                    <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
                </label>
            </p>
            <p class="field-xfn description description-thin">
                <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
                    Link Relationship (XFN)<br />
                    <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
                </label>
            </p>
            <p class="field-description description description-wide">
                <label for="edit-menu-item-description-<?php echo $item_id; ?>">
                    Description<br />
                    <textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
                    <span class="description">The description will be displayed in the menu if the current theme supports it.</span>
                </label>
            </p>
            <?php
            /*
             * This is the added field
             */
            /* mega menu code
            ?>
            <p class="field-megamenu description description-wide">
                <label for="edit-menu-item-megamenu">
                    <?php _e('Mega Menu'); ?><br/>
                    <select id="edit-menu-item-megamenu-<?php echo $item_id; ?>" class="widefat code edit-menu-item-megamenu" name="menu-item-megamenu[<?php echo $item_id; ?>]" >
                        <option value="0">None</option>
                        <?php $posts = get_posts('post_type=megamenu'); foreach($posts as $post){
                            echo "<option value='{$post->ID}'>{$post->post_title}</option>";
                        } ?>
                    </select>
                </label>
            </p>
            */ ?>
            <p class="field-icon description description-wide">
                <label for="edit-menu-item-icon-<?php echo $item_id; ?>">
                    Menu Icon<br />
                    <select id="edit-menu-item-icon-<?php echo $item_id; ?>" class="widefat code edit-menu-item-icon" name="menu-item-icon[<?php echo $item_id; ?>]" >
                    <?php
                    $icons[''] = 'No Icon';
                    $icons['icon-cloud-download'] = 'Cloud Download';
                    $icons['icon-cloud-upload'] = 'Cloud Upload';
                    $icons['icon-lightbulb'] = 'Lightbulb';
                    $icons['icon-exchange'] = 'Exchange';
                    $icons['icon-bell-alt'] = 'Bell Alt';
                    $icons['icon-file-alt'] = 'File Alt';
                    $icons['icon-beer'] = 'Beer';
                    $icons['icon-coffee'] = 'Coffee';
                    $icons['icon-food'] = 'Food';
                    $icons['icon-fighter-jet'] = 'Fighter Jet';
                    $icons['icon-user-md'] = 'User Md';
                    $icons['icon-stethoscope'] = 'Stethoscope';
                    $icons['icon-suitcase'] = 'Suitcase';
                    $icons['icon-building'] = 'Building';
                    $icons['icon-hospital'] = 'Hospital';
                    $icons['icon-ambulance'] = 'Ambulance';
                    $icons['icon-medkit'] = 'Medkit';
                    $icons['icon-h-sign'] = 'H Sign';
                    $icons['icon-plus-sign-alt'] = 'Plus Sign Alt';
                    $icons['icon-spinner'] = 'Spinner';
                    $icons['icon-angle-left'] = 'Angle Left';
                    $icons['icon-angle-right'] = 'Angle Right';
                    $icons['icon-angle-up'] = 'Angle Up';
                    $icons['icon-angle-down'] = 'Angle Down';
                    $icons['icon-double-angle-left'] = 'Double Angle Left';
                    $icons['icon-double-angle-right'] = 'Double Angle Right';
                    $icons['icon-double-angle-up'] = 'Double Angle Up';
                    $icons['icon-double-angle-down'] = 'Double Angle Down';
                    $icons['icon-circle-blank'] = 'Circle Blank';
                    $icons['icon-circle'] = 'Circle';
                    $icons['icon-desktop'] = 'Desktop';
                    $icons['icon-laptop'] = 'Laptop';
                    $icons['icon-tablet'] = 'Tablet';
                    $icons['icon-mobile-phone'] = 'Mobile Phone';
                    $icons['icon-quote-left'] = 'Quote Left';
                    $icons['icon-quote-right'] = 'Quote Right';
                    $icons['icon-reply'] = 'Reply';
                    $icons['icon-github-alt'] = 'Github Alt';
                    $icons['icon-folder-close-alt'] = 'Folder Close Alt';
                    $icons['icon-folder-open-alt'] = 'Folder Open Alt';
                    $icons['icon-adjust'] = 'Adjust';
                    $icons['icon-asterisk'] = 'Asterisk';
                    $icons['icon-ban-circle'] = 'Ban Circle';
                    $icons['icon-bar-chart'] = 'Bar Chart';
                    $icons['icon-barcode'] = 'Barcode';
                    $icons['icon-beaker'] = 'Beaker';
                    $icons['icon-beer'] = 'Beer';
                    $icons['icon-bell'] = 'Bell';
                    $icons['icon-bell-alt'] = 'Bell Alt';
                    $icons['icon-bolt'] = 'Bolt';
                    $icons['icon-book'] = 'Book';
                    $icons['icon-bookmark'] = 'Bookmark';
                    $icons['icon-bookmark-empty'] = 'Bookmark Empty';
                    $icons['icon-briefcase'] = 'Briefcase';
                    $icons['icon-bullhorn'] = 'Bullhorn';
                    $icons['icon-calendar'] = 'Calendar';
                    $icons['icon-camera'] = 'Camera';
                    $icons['icon-camera-retro'] = 'Camera Retro';
                    $icons['icon-certificate'] = 'Certificate';
                    $icons['icon-check'] = 'Check';
                    $icons['icon-check-empty'] = 'Check Empty';
                    $icons['icon-circle'] = 'Circle';
                    $icons['icon-circle-blank'] = 'Circle Blank';
                    $icons['icon-cloud'] = 'Cloud';
                    $icons['icon-cloud-download'] = 'Cloud Download';
                    $icons['icon-cloud-upload'] = 'Cloud Upload';
                    $icons['icon-coffee'] = 'Coffee';
                    $icons['icon-cog'] = 'Cog';
                    $icons['icon-cogs'] = 'Cogs';
                    $icons['icon-comment'] = 'Comment';
                    $icons['icon-comment-alt'] = 'Comment Alt';
                    $icons['icon-comments'] = 'Comments';
                    $icons['icon-comments-alt'] = 'Comments Alt';
                    $icons['icon-credit-card'] = 'Credit Card';
                    $icons['icon-dashboard'] = 'Dashboard';
                    $icons['icon-desktop'] = 'Desktop';
                    $icons['icon-download'] = 'Download';
                    $icons['icon-download-alt'] = 'Download Alt';
                    $icons['icon-edit'] = 'Edit';
                    $icons['icon-envelope'] = 'Envelope';
                    $icons['icon-envelope-alt'] = 'Envelope Alt';
                    $icons['icon-exchange'] = 'Exchange';
                    $icons['icon-exclamation-sign'] = 'Exclamation Sign';
                    $icons['icon-external-link'] = 'External Link';
                    $icons['icon-eye-close'] = 'Eye Close';
                    $icons['icon-eye-open'] = 'Eye Open';
                    $icons['icon-facetime-video'] = 'Facetime Video';
                    $icons['icon-fighter-jet'] = 'Fighter Jet';
                    $icons['icon-film'] = 'Film';
                    $icons['icon-filter'] = 'Filter';
                    $icons['icon-fire'] = 'Fire';
                    $icons['icon-flag'] = 'Flag';
                    $icons['icon-folder-close'] = 'Folder Close';
                    $icons['icon-folder-open'] = 'Folder Open';
                    $icons['icon-folder-close-alt'] = 'Folder Close Alt';
                    $icons['icon-folder-open-alt'] = 'Folder Open Alt';
                    $icons['icon-food'] = 'Food';
                    $icons['icon-gift'] = 'Gift';
                    $icons['icon-glass'] = 'Glass';
                    $icons['icon-globe'] = 'Globe';
                    $icons['icon-group'] = 'Group';
                    $icons['icon-hdd'] = 'Hdd';
                    $icons['icon-headphones'] = 'Headphones';
                    $icons['icon-heart'] = 'Heart';
                    $icons['icon-heart-empty'] = 'Heart Empty';
                    $icons['icon-home'] = 'Home';
                    $icons['icon-inbox'] = 'Inbox';
                    $icons['icon-info-sign'] = 'Info Sign';
                    $icons['icon-key'] = 'Key';
                    $icons['icon-leaf'] = 'Leaf';
                    $icons['icon-laptop'] = 'Laptop';
                    $icons['icon-legal'] = 'Legal';
                    $icons['icon-lemon'] = 'Lemon';
                    $icons['icon-lightbulb'] = 'Lightbulb';
                    $icons['icon-lock'] = 'Lock';
                    $icons['icon-unlock'] = 'Unlock';
                    $icons['icon-magic'] = 'Magic';
                    $icons['icon-magnet'] = 'Magnet';
                    $icons['icon-map-marker'] = 'Map Marker';
                    $icons['icon-minus'] = 'Minus';
                    $icons['icon-minus-sign'] = 'Minus Sign';
                    $icons['icon-mobile-phone'] = 'Mobile Phone';
                    $icons['icon-money'] = 'Money';
                    $icons['icon-move'] = 'Move';
                    $icons['icon-music'] = 'Music';
                    $icons['icon-off'] = 'Off';
                    $icons['icon-ok'] = 'Ok';
                    $icons['icon-ok-circle'] = 'Ok Circle';
                    $icons['icon-ok-sign'] = 'Ok Sign';
                    $icons['icon-pencil'] = 'Pencil';
                    $icons['icon-picture'] = 'Picture';
                    $icons['icon-plane'] = 'Plane';
                    $icons['icon-plus'] = 'Plus';
                    $icons['icon-plus-sign'] = 'Plus Sign';
                    $icons['icon-print'] = 'Print';
                    $icons['icon-pushpin'] = 'Pushpin';
                    $icons['icon-qrcode'] = 'Qrcode';
                    $icons['icon-question-sign'] = 'Question Sign';
                    $icons['icon-quote-left'] = 'Quote Left';
                    $icons['icon-quote-right'] = 'Quote Right';
                    $icons['icon-random'] = 'Random';
                    $icons['icon-refresh'] = 'Refresh';
                    $icons['icon-remove'] = 'Remove';
                    $icons['icon-remove-circle'] = 'Remove Circle';
                    $icons['icon-remove-sign'] = 'Remove Sign';
                    $icons['icon-reorder'] = 'Reorder';
                    $icons['icon-reply'] = 'Reply';
                    $icons['icon-resize-horizontal'] = 'Resize Horizontal';
                    $icons['icon-resize-vertical'] = 'Resize Vertical';
                    $icons['icon-retweet'] = 'Retweet';
                    $icons['icon-road'] = 'Road';
                    $icons['icon-rss'] = 'Rss';
                    $icons['icon-screenshot'] = 'Screenshot';
                    $icons['icon-search'] = 'Search';
                    $icons['icon-share'] = 'Share';
                    $icons['icon-share-alt'] = 'Share Alt';
                    $icons['icon-shopping-cart'] = 'Shopping Cart';
                    $icons['icon-signal'] = 'Signal';
                    $icons['icon-signin'] = 'Signin';
                    $icons['icon-signout'] = 'Signout';
                    $icons['icon-sitemap'] = 'Sitemap';
                    $icons['icon-sort'] = 'Sort';
                    $icons['icon-sort-down'] = 'Sort Down';
                    $icons['icon-sort-up'] = 'Sort Up';
                    $icons['icon-spinner'] = 'Spinner';
                    $icons['icon-star'] = 'Star';
                    $icons['icon-star-empty'] = 'Star Empty';
                    $icons['icon-star-half'] = 'Star Half';
                    $icons['icon-tablet'] = 'Tablet';
                    $icons['icon-tag'] = 'Tag';
                    $icons['icon-tags'] = 'Tags';
                    $icons['icon-tasks'] = 'Tasks';
                    $icons['icon-thumbs-down'] = 'Thumbs Down';
                    $icons['icon-thumbs-up'] = 'Thumbs Up';
                    $icons['icon-time'] = 'Time';
                    $icons['icon-tint'] = 'Tint';
                    $icons['icon-trash'] = 'Trash';
                    $icons['icon-trophy'] = 'Trophy';
                    $icons['icon-truck'] = 'Truck';
                    $icons['icon-umbrella'] = 'Umbrella';
                    $icons['icon-upload'] = 'Upload';
                    $icons['icon-upload-alt'] = 'Upload Alt';
                    $icons['icon-user'] = 'User';
                    $icons['icon-user-md'] = 'User Md';
                    $icons['icon-volume-off'] = 'Volume Off';
                    $icons['icon-volume-down'] = 'Volume Down';
                    $icons['icon-volume-up'] = 'Volume Up';
                    $icons['icon-warning-sign'] = 'Warning Sign';
                    $icons['icon-wrench'] = 'Wrench';
                    $icons['icon-zoom-in'] = 'Zoom In';
                    $icons['icon-zoom-out'] = 'Zoom Out';
                    $icons['icon-file'] = 'File';
                    $icons['icon-file-alt'] = 'File Alt';
                    $icons['icon-cut'] = 'Cut';
                    $icons['icon-copy'] = 'Copy';
                    $icons['icon-paste'] = 'Paste';
                    $icons['icon-save'] = 'Save';
                    $icons['icon-undo'] = 'Undo';
                    $icons['icon-repeat'] = 'Repeat';
                    $icons['icon-text-height'] = 'Text Height';
                    $icons['icon-text-width'] = 'Text Width';
                    $icons['icon-align-left'] = 'Align Left';
                    $icons['icon-align-center'] = 'Align Center';
                    $icons['icon-align-right'] = 'Align Right';
                    $icons['icon-align-justify'] = 'Align Justify';
                    $icons['icon-indent-left'] = 'Indent Left';
                    $icons['icon-indent-right'] = 'Indent Right';
                    $icons['icon-font'] = 'Font';
                    $icons['icon-bold'] = 'Bold';
                    $icons['icon-italic'] = 'Italic';
                    $icons['icon-strikethrough'] = 'Strikethrough';
                    $icons['icon-underline'] = 'Underline';
                    $icons['icon-link'] = 'Link';
                    $icons['icon-paper-clip'] = 'Paper Clip';
                    $icons['icon-columns'] = 'Columns';
                    $icons['icon-table'] = 'Table';
                    $icons['icon-th-large'] = 'Th Large';
                    $icons['icon-th'] = 'Th';
                    $icons['icon-th-list'] = 'Th List';
                    $icons['icon-list'] = 'List';
                    $icons['icon-list-ol'] = 'List Ol';
                    $icons['icon-list-ul'] = 'List Ul';
                    $icons['icon-list-alt'] = 'List Alt';
                    $icons['icon-angle-left'] = 'Angle Left';
                    $icons['icon-angle-right'] = 'Angle Right';
                    $icons['icon-angle-up'] = 'Angle Up';
                    $icons['icon-angle-down'] = 'Angle Down';
                    $icons['icon-arrow-down'] = 'Arrow Down';
                    $icons['icon-arrow-left'] = 'Arrow Left';
                    $icons['icon-arrow-right'] = 'Arrow Right';
                    $icons['icon-arrow-up'] = 'Arrow Up';
                    $icons['icon-caret-down'] = 'Caret Down';
                    $icons['icon-caret-left'] = 'Caret Left';
                    $icons['icon-caret-right'] = 'Caret Right';
                    $icons['icon-caret-up'] = 'Caret Up';
                    $icons['icon-chevron-down'] = 'Chevron Down';
                    $icons['icon-chevron-left'] = 'Chevron Left';
                    $icons['icon-chevron-right'] = 'Chevron Right';
                    $icons['icon-chevron-up'] = 'Chevron Up';
                    $icons['icon-circle-arrow-down'] = 'Circle Arrow Down';
                    $icons['icon-circle-arrow-left'] = 'Circle Arrow Left';
                    $icons['icon-circle-arrow-right'] = 'Circle Arrow Right';
                    $icons['icon-circle-arrow-up'] = 'Circle Arrow Up';
                    $icons['icon-double-angle-left'] = 'Double Angle Left';
                    $icons['icon-double-angle-right'] = 'Double Angle Right';
                    $icons['icon-double-angle-up'] = 'Double Angle Up';
                    $icons['icon-double-angle-down'] = 'Double Angle Down';
                    $icons['icon-hand-down'] = 'Hand Down';
                    $icons['icon-hand-left'] = 'Hand Left';
                    $icons['icon-hand-right'] = 'Hand Right';
                    $icons['icon-hand-up'] = 'Hand Up';
                    $icons['icon-circle'] = 'Circle';
                    $icons['icon-circle-blank'] = 'Circle Blank';
                    $icons['icon-play-circle'] = 'Play Circle';
                    $icons['icon-play'] = 'Play';
                    $icons['icon-pause'] = 'Pause';
                    $icons['icon-stop'] = 'Stop';
                    $icons['icon-step-backward'] = 'Step Backward';
                    $icons['icon-fast-backward'] = 'Fast Backward';
                    $icons['icon-backward'] = 'Backward';
                    $icons['icon-forward'] = 'Forward';
                    $icons['icon-fast-forward'] = 'Fast Forward';
                    $icons['icon-step-forward'] = 'Step Forward';
                    $icons['icon-eject'] = 'Eject';
                    $icons['icon-fullscreen'] = 'Fullscreen';
                    $icons['icon-resize-full'] = 'Resize Full';
                    $icons['icon-resize-small'] = 'Resize Small';
                    $icons['icon-phone'] = 'Phone';
                    $icons['icon-phone-sign'] = 'Phone Sign';
                    $icons['icon-facebook'] = 'Facebook';
                    $icons['icon-facebook-sign'] = 'Facebook Sign';
                    $icons['icon-twitter'] = 'Twitter';
                    $icons['icon-twitter-sign'] = 'Twitter Sign';
                    $icons['icon-github'] = 'Github';
                    $icons['icon-github-alt'] = 'Github Alt';
                    $icons['icon-github-sign'] = 'Github Sign';
                    $icons['icon-linkedin'] = 'Linkedin';
                    $icons['icon-linkedin-sign'] = 'Linkedin Sign';
                    $icons['icon-pinterest'] = 'Pinterest';
                    $icons['icon-pinterest-sign'] = 'Pinterest Sign';
                    $icons['icon-google-plus'] = 'Google Plus';
                    $icons['icon-google-plus-sign'] = 'Google Plus Sign';
                    $icons['icon-sign-blank'] = 'Sign Blank';
                    $icons['icon-ambulance'] = 'Ambulance';
                    $icons['icon-beaker'] = 'Beaker';
                    $icons['icon-h-sign'] = 'H Sign';
                    $icons['icon-hospital'] = 'Hospital';
                    $icons['icon-medkit'] = 'Medkit';
                    $icons['icon-plus-sign-alt'] = 'Plus Sign Alt';
                    $icons['icon-stethoscope'] = 'Stethoscope';
                    $icons['icon-user-md'] = 'User Md';
                    $data = maybe_unserialize(get_post_meta($post->ID,'wpeden_post_meta', true));
                    if(is_array($data))
                        $icon = $data['icon'];
                    ?>


                        <?php foreach($icons  as $class => $name){ echo "<option value='{$class}' ".selected($class, $item->icon).">{$name}</option>"; } ?>
                    </select>

                </label>
            </p>
            <?php
            /*
             * end added field
             */
            ?>
            <div class="menu-item-actions description-wide submitbox">
                <?php if( 'custom' != $item->type && $original_title !== false ) : ?>
                    <p class="link-to-original">
                        <?php printf( 'Original: %s', '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
                    </p>
                <?php endif; ?>
                <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
                echo wp_nonce_url(
                    add_query_arg(
                        array(
                            'action' => 'delete-menu-item',
                            'menu-item' => $item_id,
                        ),
                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                    ),
                    'delete-menu_item_' . $item_id
                ); ?>">Remove</a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ) );
                ?>#menu-item-settings-<?php echo $item_id; ?>">Cancel</a>
            </div>

            <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
            <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
            <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
            <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
            <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
            <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
        </div><!-- .menu-item-settings-->
        <ul class="menu-item-transport"></ul>
        <?php
        $output .= ob_get_clean();
    }
}

?>