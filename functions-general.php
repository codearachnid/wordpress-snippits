<?php

// enable shortcode parsing in widget_text
add_filter( 'widget_text', 'do_shortcode' );

// remove unnessary <p> tags around images within the_content
function filter_tags_on_images($content){
    return preg_replace( '/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content );
}
add_filter( 'the_content', 'filter_ptags_on_images' );

// remove WP generator tags in head
remove_action( 'wp_head', 'wp_generator' );

// replace WP badging
function replace_wp_generator() { return '<meta name="generator" content="Custom CMS Message" />'; }
add_filter('the_generator', 'replace_wp_generator');

// get_the_time() - makes up for a lack of return the_time functionality
function return_the_time( $d = '' ) { 
	return apply_filters( 'the_time', get_the_time( $d ), $d ); 
}

// display time like twitter
function the_time_like_twitter( $type = 'post' ) {
	$d = 'comment' == $type ? 'get_comment_time' : 'get_post_time';
	return human_time_diff($d('U'), current_time('timestamp')) . " " . __('ago');
}

// check if post's assigned categories are descendants of target categories
function post_is_in_descendant_category( $cats, $_post = null ) {
	foreach ( (array) $cats as $cat ) {
		// get_term_children() accepts integer ID only
		$descendants = get_term_children( (int) $cat, 'category' );
		if ( $descendants && in_category( $descendants, $_post ) )
			return true;
	}
	return false;
}

// set favicon with Gravatar
function gravatar_favicon() {
	$GetTheHash = md5(strtolower(trim(get_bloginfo('admin_email'))));
	return 'http://www.gravatar.com/avatar/' . $GetTheHash . '?s=16';
}
function favicon() {
	echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.gravatar_favicon().'" />';
}
add_action('wp_head', 'favicon');

// notify user when role is changed
function user_role_update( $user_id, $new_role ) {
        $site_url = get_bloginfo( 'wpurl' );
        $user_info = get_userdata( $user_id );
        $to = $user_info->user_email;
        $subject = "Role changed: {$site_url}";
        $message = "Hello {$user_info->display_name} your role has changed on {$site_url} congratulations you are now an {$new_role}.";
        wp_mail( $to, $subject, $message );
}
add_action( 'set_user_role', 'user_role_update', 10, 2);

// get related posts by author
function related_author_posts($atts) {
	global $authordata, $post;
	extract( shortcode_atts( array(
		'return' => false, // if set to true will hand over raw records
		'author' => $authordata->ID,
		'post__not_in' => array( $post->ID ),
		'posts_per_page' => 5
	  ), $atts ) );
    $related_posts = get_posts( array( 'author' => $author, 'post__not_in' => $post__not_in, 'posts_per_page' => $posts_per_page ) );
    if( $return ) {
    	return $related_posts;
    } else {
		$output = '<ul>';
		foreach ( $related_posts as $rel_post ) {
			$output .= '<li><a href="' . get_permalink( $rel_post->ID ) . '">' . apply_filters( 'the_title', $rel_post->post_title, $rel_post->ID ) . '</a></li>';
		}
		$output .= '</ul>';
		return $output;
    }
}

// show query performance in footer comment
function wp_query_performance( $visible = false ) {
    $stat = sprintf(  '%d queries in %.3f seconds, using %.2fMB memory',
        get_num_queries(),
        timer_stop( 0, 3 ),
        memory_get_peak_usage() / 1024 / 1024
        );
    echo $visible ? $stat : "<!-- {$stat} -->" ;
}
add_action( 'wp_footer', 'wp_query_performance', 20 );

// exclude categories from RSS feed
function exclude_categories_from_rss($query) {
    if ($query->is_feed) {
        $query->set('cat','-20,-21,-22'); // hardcode category ids
    }
    return $query;
}
add_filter('pre_get_posts','exclude_categories_from_rss');

// insert wmode parameter into oEmbeds
function insert_wmode_opaque( $html, $url, $args ) {
	if ( strpos( $html, '<param name="movie"' ) !== false )
		$html = preg_replace( '|</param>|', '</param><param name="wmode" value="opaque"></param>', $html, 1 );
	if ( strpos( $html, '<embed' ) !== false )
		$html = str_replace( '<embed', '<embed wmode="opaque"', $html );
	return $html;
}
add_filter( 'oembed_result', 'insert_wmode_opaque', 10, 3 );

// redirect user to referring page after login
if ( (isset($_GET['action']) && $_GET['action'] != 'logout') || (isset($_POST['login_location']) && !empty($_POST['login_location'])) ) {
	add_filter('login_redirect', 'my_login_redirect', 10, 3);
	function my_login_redirect() {
		$location = $_SERVER['HTTP_REFERER'];
		wp_safe_redirect($location);
		exit();
	}
}

// disable self pings with pre_ping hook
function no_self_ping( &$links ) {
    $home = get_option( 'home' );
    foreach ( $links as $l => $link )
        if ( 0 === strpos( $link, $home ) )
            unset($links[$l]);
}
add_action( 'pre_ping', 'no_self_ping' );

// remove l10n.js javascript in WP 3+
function remove_l1on() {
if ( !is_admin() ) {
    wp_deregister_script('l10n');
  }
}
add_action( 'init', 'remove_l1on' );

// register jquery from Google CDN
function goog_cdn_jquery_register() {
if ( !is_admin() ) {
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', ( 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js' ), false, null, true );
    wp_enqueue_script( 'jquery' );
   }
}
add_action( 'init', 'goog_cdn_jquery_register' );

// add login|logout link into menu
function add_login_logout_link($items, $args) {
        $loginoutlink = wp_loginout('index.php', false);
        $items .= '<li>'. $loginoutlink .'</li>';
    return $items;
}
add_filter('wp_nav_menu_items', 'add_login_logout_link', 10, 2);

// strip shortcodes from the_content on homepage 
function remove_shortcode_from_index($content) {
  if ( is_home() ) {
    $content = strip_shortcodes( $content );
  }
  return $content;
}
add_filter('the_content', 'remove_shortcode_from_index');

// nofollow external links only, the_content and the_excerpt
function my_nofollow($content) {
    return preg_replace_callback('/<a[^>]+/', 'my_nofollow_callback', $content);
}
function my_nofollow_callback($matches) {
    $link = $matches[0];
    $site_link = get_bloginfo('url');
    if (strpos($link, 'rel') === false) {
        $link = preg_replace("%(href=\S(?!$site_link))%i", 'rel="nofollow" $1', $link);
    } elseif (preg_match("%href=\S(?!$site_link)%i", $link)) {
        $link = preg_replace('/rel=\S(?!nofollow)\S*/i', 'rel="nofollow"', $link);
    }
    return $link;
}
add_filter('the_content', 'my_nofollow');
add_filter('the_excerpt', 'my_nofollow');

// add file type category within media library
function modify_post_mime_types($post_mime_types) {
    $post_mime_types['application/x-shockwave-flash'] = array(
    	__( 'mimeframe' ), 
    	__('Manage Flash'), 
    	_n_noop( 'Flash <span class="count">(%s)</span>', 'Flash <span class="count">(%s)</span>')
    );
    return $post_mime_types;
}
add_filter('post_mime_types', 'modify_post_mime_types');

// convert URI, www, ftp, and emails into clickable links
add_filter('the_content', 'make_clickable');
add_filter('the_excerpt', 'make_clickable');

// show user last login date
function your_last_login($login) {
    global $user_ID;
    $user = get_userdatabylogin($login);
    update_usermeta($user->ID, 'last_login', current_time('mysql'));
}
function get_last_login($user_id) {
    $last_login = get_user_meta($user_id, 'last_login', true);
    $date_format = get_option('date_format') . ' ' . get_option('time_format');
    $the_last_login = mysql2date($date_format, $last_login, false);
    echo $the_last_login;
}
add_action('wp_login','your_last_login');
	// to display:
	global $userdata;
	get_currentuserinfo();
	echo  'You last logged in:';
	get_last_login($userdata->ID);
	

// add "Read More" to the end of an excerpt
function excerpt_readmore($more) {
	return '... <a href="'. get_permalink($post->ID) . '" class="readmore">' . 'Read More' . '</a>';
}
add_filter('excerpt_more', 'excerpt_readmore');

// redirect to post if single result in category or tag
function redirect_to_post(){
    global $wp_query;
    if( is_archive() && $wp_query->post_count == 1 ){
        the_post();
        $post_url = get_permalink();
        wp_redirect( $post_url );
    }
} add_action('template_redirect', 'redirect_to_post');

// sensor words in comments
function wp_filter_comment($comment) {
	$replace = array(
		// 'WORD TO REPLACE' => 'REPLACE WORD WITH THIS'
		'foobar' => '*****',
		'hate' => 'love',
		'zoom' => '<a href="http://zoom.com">zoom</a>'
	);
	$comment = str_replace(array_keys($replace), $replace, $comment);
	return $comment;
}
add_filter( 'pre_comment_content', 'wp_filter_comment' );

// Google doc's shortcode for non-browser friendly formats
function wps_viewer($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => 'http://',
		"class" => ''
	), $atts));
	return '<a href="http://docs.google.com/viewer?url='.$href.'" class="'.$class.' icon">'.$content.'</a>';
}
add_shortcode("doc", "wps_viewer");
	// USAGE:
	// [doc class="psd" href="http://www.wpsnipp.com/file.psd"]my PSD file name[/doc]
	// [doc class="ai" href="http://www.wpsnipp.com/file.ai"]my AI file name[/doc]
	// [doc class="svg" href="http://www.wpsnipp.com/file.svg"]my SVG file name[/doc]

// enable Gravatar hovercards
function gravatar_hovercards() {
	wp_enqueue_script( 'gprofiles', 'http://s.gravatar.com/js/gprofiles.js', array( 'jquery' ), 'e', true );
}
add_action('wp_enqueue_scripts','gravatar_hovercards');

// if available enable gzip through php
if(extension_loaded("zlib") && (ini_get("output_handler") != "ob_gzhandler"))
   add_action('wp', create_function('', '@ob_end_clean();@ini_set("zlib.output_compression", 1);'));

// extend cookie expiration for logged in users
function logged_in( $expirein ) {
   return 604800; // 1 week in seconds
}
add_filter( 'auth_cookie_expiration', 'logged_in' );

// determine if post is older than (int)
function is_old_post($days = 5) {
    $days = (int) $days;
    $offset = $days*60*60*24;
    if ( get_post_time() < date('U') - $offset )
         return true;
    return false;
 }