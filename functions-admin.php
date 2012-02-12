<?php

// remove editor support for fields on specific post types
add_action( 'admin_init', 'wp_removed_editable_fields' );
function wp_removed_editable_fields() {
	remove_post_type_support( 'post', 'title' );
}

// add or remove items from WP menu bar
// http://codex.wordpress.org/Function_Reference/add_menu
function wp_modify_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'wp-logo' );
    $wp_admin_bar->remove_menu( 'view-site' );
}
add_action( 'wp_before_admin_bar_render', 'wp_modify_admin_bar' );

// restrict wp-admin access to subscribers
function restrict_access_admin_panel(){
	global $current_user;
	get_currentuserinfo();
	if ($current_user->user_level <  4) {
		wp_redirect( get_bloginfo('url') );
		exit;
	}
}
add_action('admin_init', 'restrict_access_admin_panel', 1);

// disable browser nag/upgrade warnings
function disable_browser_upgrade_warning() {
    remove_meta_box( 'dashboard_browser_nag', 'dashboard', 'normal' );
}
add_action( 'wp_dashboard_setup', 'disable_browser_upgrade_warning' );

// increase default (30) limit on custom fields
add_filter( 'postmeta_form_limit' , 'customfield_limit_increase' );
function customfield_limit_increase( $limit ) {
    $limit = 60;
    return $limit;
}

// disable autosaving in editor screen
function disableAutoSave(){
    wp_deregister_script('autosave');
}
add_action( 'wp_print_scripts', 'disableAutoSave' );

// remove curly quotes
remove_filter('the_content', 'wptexturize');

// replace wp-login.php logo
function wp_badge_login_logo() {
    echo '<style type="text/css">h1 a { background-image:url("path/to/image") !important; }</style>';
}
add_action('login_head', 'wp_badge_login_logo');

// remove custom fields metabox
function remove_default_page_screen_metaboxes() {
       remove_meta_box( 'postcustom','post','normal' );
}
add_action('admin_menu','remove_default_page_screen_metaboxes');

// remove revision list from editor
function remove_revisions_metabox() {
       remove_meta_box( 'revisionsdiv','post','normal' );
}
add_action('admin_menu','remove_revisions_metabox');

// add subscript & superscript to tinymce
function enable_more_buttons($buttons) {
  $buttons[] = 'sub';
  $buttons[] = 'sup';
  return $buttons;
}
add_filter("mce_buttons_3", "enable_more_buttons");

// remove default profile fields from admin
function hide_profile_fields( $contactmethods ) {
	unset($contactmethods['aim']);
	unset($contactmethods['jabber']);
	unset($contactmethods['yim']);
	return $contactmethods;
}
add_filter('user_contactmethods','hide_profile_fields',10,1);

// restrict author (user) to see only authored posts
function posts_for_current_author($query) {
	global $pagenow;
    if( 'edit.php' != $pagenow || !$query->is_admin )
        return $query;
    if( !current_user_can( 'manage_options' ) ) {
       global $user_ID;
       $query->set('author', $user_ID );
     }
     return $query;
}
add_filter('pre_get_posts', 'posts_for_current_author');

// restrict user to only see media they have uploaded
function current_user_files_only( $wp_query ) {
    if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/upload.php' ) !== false ) {
        if ( !current_user_can( 'level_5' ) ) {
            global $current_user;
            $wp_query->set( 'author', $current_user->id );
        }
    }
}
add_filter('parse_query', 'current_user_files_only' );

// disable ability to use captions
add_filter( 'disable_captions', create_function( '$a','return true;' ) );

// remove the theme editor submenu
function remove_editor_menu() {
  remove_action('admin_menu', '_add_themes_utility_last', 101);
}
add_action('_admin_menu', 'remove_editor_menu', 1);

// disable html editor for roles below admin
function disable_html_editor() {
	global $current_user;
	get_currentuserinfo();
	if ($current_user->user_level != 10) {
		echo '<style type="text/css">#editor-toolbar #edButtonHTML, #quicktags {display: none;}</style>';
	}
}
add_filter( 'wp_default_editor', create_function('', 'return "tinymce";') );
add_action( 'admin_head', 'disable_html_editor' );

// remove ability for anyone not "admin" to swap themes
function slt_lock_theme() {
	global $submenu, $userdata;
	get_currentuserinfo();
	if ( $userdata->ID != 1 ) {
		unset( $submenu['themes.php'][5] );
		unset( $submenu['themes.php'][15] );
	}
}
add_action( 'admin_init', 'slt_lock_theme' );

// add a custom message to the login screen
function custom_login_message() {
	$message = '<p class="message">Welcome, please read our <a href="#">terms of service</a> before you register.</p><br />';
	return $message;
}
add_filter('login_message', 'custom_login_message');

// automatically categorize and tag posts when saved
function update_post_terms( $post_id ) {
    if ( $parent = wp_is_post_revision( $post_id ) )
        $post_id = $parent;
    $post = get_post( $post_id );
    if ( $post->post_type != 'post' )
        return;
    // add a tag
    wp_set_post_terms( $post_id, 'new tag', 'post_tag', true );
    // add a category
    $categories = wp_get_post_categories( $post_id );
    $newcat    = get_term_by( 'name', 'Some Category', 'category' );
    array_push( $categories, $newcat->term_id );
    wp_set_post_categories( $post_id, $categories );
}
add_action( 'wp_insert_post', 'update_post_terms' );

// restrict upload file types
function restrict_mime($mimes) {
	$mimes = array(
					'jpg|jpeg|jpe' => 'image/jpeg',
					'gif' => 'image/gif',
	);
	return $mimes;
}
add_filter('upload_mimes','restrict_mime');

// add total members to “Right Now” dashboard widget
function dashboard_wp_user_count() {
    global $wpdb;
    $user_c = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users");
?>
<table>
    <tbody>
       <tr class="first">
          <td class="first b b_pages"><a href="users.php"><? echo $user_c; ?></a></td>
          <td class="t pages"><a href="users.php">Members</a></td>
       </tr>
     </tbody>
</table>
<?php
}
add_action( 'right_now_content_table_end', 'dashboard_wp_user_count');

// remove WP defined admin submenus
function remove_submenus() {
  global $submenu;
  //Dashboard menu
  unset($submenu['index.php'][10]); // Removes Updates
  //Posts menu
  unset($submenu['edit.php'][5]); // Leads to listing of available posts to edit
  unset($submenu['edit.php'][10]); // Add new post
  unset($submenu['edit.php'][15]); // Remove categories
  unset($submenu['edit.php'][16]); // Removes Post Tags
  //Media Menu
  unset($submenu['upload.php'][5]); // View the Media library
  unset($submenu['upload.php'][10]); // Add to Media library
  //Links Menu
  unset($submenu['link-manager.php'][5]); // Link manager
  unset($submenu['link-manager.php'][10]); // Add new link
  unset($submenu['link-manager.php'][15]); // Link Categories
  //Pages Menu
  unset($submenu['edit.php?post_type=page'][5]); // The Pages listing
  unset($submenu['edit.php?post_type=page'][10]); // Add New page
  //Appearance Menu
  unset($submenu['themes.php'][5]); // Removes 'Themes'
  unset($submenu['themes.php'][7]); // Widgets
  unset($submenu['themes.php'][15]); // Removes Theme Installer tab
  //Plugins Menu
  unset($submenu['plugins.php'][5]); // Plugin Manager
  unset($submenu['plugins.php'][10]); // Add New Plugins
  unset($submenu['plugins.php'][15]); // Plugin Editor
  //Users Menu
  unset($submenu['users.php'][5]); // Users list
  unset($submenu['users.php'][10]); // Add new user
  unset($submenu['users.php'][15]); // Edit your profile
  //Tools Menu
  unset($submenu['tools.php'][5]); // Tools area
  unset($submenu['tools.php'][10]); // Import
  unset($submenu['tools.php'][15]); // Export
  unset($submenu['tools.php'][20]); // Upgrade plugins and core files
  //Settings Menu
  unset($submenu['options-general.php'][10]); // General Options
  unset($submenu['options-general.php'][15]); // Writing
  unset($submenu['options-general.php'][20]); // Reading
  unset($submenu['options-general.php'][25]); // Discussion
  unset($submenu['options-general.php'][30]); // Media
  unset($submenu['options-general.php'][35]); // Privacy
  unset($submenu['options-general.php'][40]); // Permalinks
  unset($submenu['options-general.php'][45]); // Misc
}
add_action('admin_menu', 'remove_submenus');