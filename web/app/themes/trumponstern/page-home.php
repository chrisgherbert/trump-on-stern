<?php
/**
 *  Template Name: Home Page
 */

$context = Timber::get_context();
$post = Timber::get_post($post->ID, 'bermanco\ExtendedTimberClasses\Post');

$context['post'] = $post;

// Set special OG tags for the home page
$context['open_graph'] = array(
	array(
		'key' => 'og:title',
		'value' => get_option('blogname'),
	),
	array(
		'key' => 'og:url',
		'value' => get_option('home'),
	),
	array(
		'key' => 'og:description',
		'value' => get_option('blogdescription'),
	),
	array(
		'key' => 'og:image',
		'value' => get_template_directory_uri() . '/assets/img/trump-on-stern-og-image.png'
	)
);

// Episodes
$context['episodes_grouped_by_year'] = Content\Episode::get_grouped_by_year();

// Tags
$context['tags'] = Timber::get_terms('post_tag');

Timber::render( array( 'page-' . $post->post_name . '.twig', 'page.twig' ), $context, false, TimberLoader::CACHE_NONE );
