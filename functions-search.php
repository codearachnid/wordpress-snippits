<?php

// rewrite search results slug to /search/term
function search_url_rewrite_rule() {
	if ( is_search() && ! empty($_GET[ 's' ] ) ) {
		wp_redirect( home_url( '/search/' ) . urlencode(get_query_var( 's' ) ) );
		exit();
	}
}
add_action( 'template_redirect', 'search_url_rewrite_rule' );

// redirect search results to signl page if only result is found
function single_result() {
    if (is_search()) {
        global $wp_query;
        if ($wp_query->post_count == 1) {
            wp_redirect( get_permalink( $wp_query->posts['0']->ID ) );
        }
    }
}
add_action('template_redirect', 'single_result');

// filter search results by post type
function search_posts_filter( $query ){
    if ($query->is_search){
        $query->set('post_type',array('post','custom_post_type1', 'custom_post_type2'));
    }
    return $query;
}
add_filter('pre_get_posts','search_posts_filter');

// limit WP search to title field
function wp_search_by_title_only( $search, &$wp_query )
{
    global $wpdb;
    if ( empty( $search ) )
        return $search; // skip processing - no search term in query
    $q = $wp_query->query_vars;
    $n = ! empty( $q['exact'] ) ? '' : '%';
    $search =
    $searchand = '';
    foreach ( (array) $q['search_terms'] as $term ) {
        $term = esc_sql( like_escape( $term ) );
        $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
        $searchand = ' AND ';
    }
    if ( ! empty( $search ) ) {
        $search = " AND ({$search}) ";
        if ( ! is_user_logged_in() )
            $search .= " AND ($wpdb->posts.post_password = '') ";
    }
    return $search;
}
add_filter( 'posts_search', 'wp_search_by_title_only', 500, 2 );

// customize search WHERE BY date/title filter
function filter_where($where = '') {
	global $sdate, $edate, $ename;
	if ($ename != null) {
		$where .= " AND post_title LIKE '%" . $ename . "%'";
	}
	if ($sdate != null) {
		$where .= " AND post_date >= '" . date('Y-m-d', strtotime($sdate)) . "'";
	}
	if ($edate != null) {
		$where .= " AND post_date <= '" . date('Y-m-d', strtotime($edate ." +1 day")) . "'";
	}
	return $where;
}
function filter_by_date($where = '') {
	global $fcstart, $fcend;
	if ($fcstart != null) {
		$where .= " AND post_date >= '" . date('Y-m-d', $fcstart) . "'";
	}
	if ($fcend != null) {
		$where .= " AND post_date <= '" . date('Y-m-d', $fcend ." +1 day") . "'";
	}
	return $where;
}
function filter_by_today($where = '') {
	$where .= " AND post_date >= '" . date('Y-m-d') . "'";
	return $where;
}

// SETUP filter vars
$fcstart = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
$fcend = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');
$sdate = isset($_GET['sdate']) && $_GET['sdate'] != '' ? $_GET['sdate'] : date('Y-m-d');
$edate = isset($_GET['edate']) ? $_GET['edate'] : null;
$eorderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'date';
$eorder = isset($_GET['eorder']) ? $_GET['eorder'] : 'ASC';
$elimit = isset($_GET['elimit']) ? $_GET['elimit'] : get_option('posts_per_page');
$ename = isset($_GET['ename']) && $_GET['ename'] != 'Event Search' ? $_GET['ename'] : null;
$etype = isset($_GET['etype']) ? $_GET['etype'] : get_category_by_slug('classes')->term_id;
$pagetype = isset($_GET['page_type']) ? $_GET['page_type'] : '';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

add_filter('posts_where', 'filter_where');
query_posts(array(
	'cat' => $etype,
	'paged'=>$paged,
	'post_type' => 'tribe_events',
	'orderby' => $eorderby,
	'order' => $eorder,
	'posts_per_page' => $elimit,
	'post_status' => array('published')
));
remove_filter('posts_where', 'filter_where');