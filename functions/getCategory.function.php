<?php

function getCategory( $slug ) {
	switch_to_blog( get_blog_id_from_url( UKM_HOSTNAME, '/arrangor/') );
	$category = get_category_by_slug( $slug );
	restore_current_blog();
	return $category;	
}