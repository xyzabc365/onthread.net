<?php
/**
 * Single blog post.
 *
 * @package Flatsome_Child
 */

defined('ABSPATH') || exit;

if (!is_singular('post')) {
	include get_template_directory() . '/single.php';
	return;
}

get_header();

while (have_posts()) :
	the_post();
	$onthread_blog_detail = onthread_get_current_post_blog_detail();
	$onthread_blog_detail_template = locate_template('template-parts/blog-detail.php');

	if ($onthread_blog_detail_template) {
		include $onthread_blog_detail_template;
	}
endwhile;

get_footer();
