<?php
/**
 * Post Type functionality for Ads.txt.
 *
 * @package Ads_Txt_Manager
 */

namespace Adstxt;

/**
 * Register the `adstxt` custom post type.
 *
 * @return void
 */
function register() {
	$args = array(
		'public'           => false,
		'hierarchical'     => false,
		'rewrite'          => false,
		'query_var'        => false,
		'delete_with_user' => false,
		'supports'         => array( 'revisions' ),
		'map_meta_cap'     => true,
		'capabilities'     => array(
			'create_posts'           => 'customize',
			'delete_others_posts'    => 'customize',
			'delete_post'            => 'customize',
			'delete_posts'           => 'customize',
			'delete_private_posts'   => 'customize',
			'delete_published_posts' => 'customize',
			'edit_others_posts'      => 'edit_others_posts',
			'edit_post'              => 'customize',
			'edit_posts'             => 'customize',
			'edit_private_posts'     => 'customize',
			'edit_published_posts'   => 'edit_published_posts',
			'publish_posts'          => 'customize',
			'read'                   => 'read',
			'read_post'              => 'customize',
			'read_private_posts'     => 'customize',
		),
	);

	register_post_type(
		'adstxt',
		array_merge(
			array(
				'labels' => array(
					'name'          => esc_html_x( 'Ads.txt', 'post type general name', 'ads-txt' ),
					'singular_name' => esc_html_x( 'Ads.txt', 'post type singular name', 'ads-txt' ),
				),
			),
			$args
		)
	);

	register_post_type(
		'app-adstxt',
		array_merge(
			array(
				'labels' => array(
					'name'          => esc_html_x( 'App-ads.txt', 'post type general name', 'ads-txt' ),
					'singular_name' => esc_html_x( 'App-ads.txt', 'post type singular name', 'ads-txt' ),
				),
			),
			$args
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\register' );
