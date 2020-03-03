<?php

use UKMNorge\Wordpress\Nyhet;

$ID_ARRANGOR = get_blog_id_from_url( UKM_HOSTNAME, '/arrangor/');
$TWIGdata['image_path'] = '//arrangor.ukm.no/';

require_once('UKM/Autoloader.php');
require_once('WPOO/WPOO/Post.php');
require_once('WPOO/WPOO/Author.php');

$num_on_frontpage = 8;
switch_to_blog($ID_ARRANGOR);
if(isset($_GET['post'])) {
	$posts = query_posts( 'p='.$_GET['post'] );
	$post = $posts[0];
	the_post();
	$wpoo_post = new WPOO_Post( $post );
	$wpoo_post->arrangor = new Nyhet( $ID_ARRANGOR, $post->ID );
    $TWIGdata['post'] = $wpoo_post;
    
	$user = wp_get_current_user();
	$TWIGdata['current_user_name'] = empty( $user->display_name ) ? $user->user_login : $user->display_name;
} else {
	$page = isset($_GET['pagination']) ? $_GET['pagination'] : 1;
	$limit = isset($_GET['limit']) ? $_GET['limit'] : $num_on_frontpage;
	if ($page > 1) {
		$limit = 12;
		$offset = $limit*($page-1)-$num_on_frontpage; // 6 stk på forsiden
	    $post_query = (isset($POST_QUERY) ? $POST_QUERY.'&' : '' ). 'post_status=publish&posts_per_page='.$limit.'&offset='.$offset.'&paged='.$page;
	}
	else {	 
	    $post_query = (isset($POST_QUERY) ? $POST_QUERY.'&' : '' ). 'post_status=publish&posts_per_page='.$limit.'&paged='.$page;
    }
    
	#$posts = query_posts( (isset($POST_QUERY) ? $POST_QUERY.'&' : '' ). 'post_status=publish&posts_per_page='.$limit.'&paged='.$page );
	$posts = query_posts($post_query);
	$TWIGdata['page'] = $page;
	
	if(sizeof($posts) == $limit)
		$TWIGdata['pagination_next'] = $page+1;
	if($page > 1)
		$TWIGdata['pagination_prev'] = $page-1;
	
	global $post;
	foreach( $posts as $key => $post ) {
		the_post();
		$wpoo_post = new WPOO_Post( $post );
		$wpoo_post->arrangor = new Nyhet( $ID_ARRANGOR, $post->ID );
		$TWIGdata['news'][] = $wpoo_post;
	}
}
restore_current_blog();