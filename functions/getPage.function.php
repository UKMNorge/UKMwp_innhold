<?php

$TWIGdata['image_path'] = '//arrangor.ukm.no/';

function getPage( $page_slug ) {
	global $post;
	require_once('WPOO/WPOO/Post.php');
	require_once('WPOO/WPOO/Author.php');

	switch_to_blog( get_blog_id_from_url( UKM_HOSTNAME, '/arrangor/') );
	
	$post_id = get_page_by_path( $page_slug );
	$post = get_post( $post_id );
	#the_post();
	$wpoo = new WPOO_Post( $post );
	
	restore_current_blog();
	return $wpoo;
}