<?php
/**
 * Registers custom post meta for the flashblocks_quote post type.
 *
 * Author name and role are stored as post meta.
 * The author photo uses the post's featured image (post thumbnail).
 *
 * @package FlashblocksQuotes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers author meta fields with REST API support so Gutenberg can read/write them.
 */
function flashblocks_register_quote_meta() {
	$auth_callback = function () {
		return current_user_can( 'edit_posts' );
	};

	register_post_meta(
		'flashblocks_quote',
		'_flashblocks_quote_author_name',
		array(
			'type'              => 'string',
			'description'       => __( 'The name of the person being quoted.', 'flashblocks-quotes' ),
			'single'            => true,
			'default'           => '',
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $auth_callback,
		)
	);

	register_post_meta(
		'flashblocks_quote',
		'_flashblocks_quote_author_role',
		array(
			'type'              => 'string',
			'description'       => __( 'The role or title of the person being quoted.', 'flashblocks-quotes' ),
			'single'            => true,
			'default'           => '',
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $auth_callback,
		)
	);
}
add_action( 'init', 'flashblocks_register_quote_meta' );
