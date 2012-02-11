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
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('view-site');
}
add_action( 'wp_before_admin_bar_render', 'wp_modify_admin_bar' );

// enable shortcode parsing in widget_text
add_filter('widget_text', 'do_shortcode');