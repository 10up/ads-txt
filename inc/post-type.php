<?php

namespace Adstxt;

/**
 * Register the `adstxt` custom post type.
 *
 * @return void
 */
function register() {
	register_post_type(
		'adstxt', array(
			'labels'           => array(
				'name'          => esc_html_x( 'Ads.txt', 'post type general name', 'ads-txt' ),
				'singular_name' => esc_html_x( 'Ads.txt', 'post type singular name', 'ads-txt' ),
			),
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
				'edit_others_posts'      => 'customize',
				'edit_post'              => 'customize',
				'edit_posts'             => 'customize',
				'edit_private_posts'     => 'customize',
				'edit_published_posts'   => 'customize',
				'publish_posts'          => 'customize',
				'read'                   => 'read',
				'read_post'              => 'customize',
				'read_private_posts'     => 'customize',
			),
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\register' );
