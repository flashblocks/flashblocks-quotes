<?php
/**
 * Registers the flashblocks_quote custom post type and quote_category taxonomy.
 *
 * @package FlashblocksQuotes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the flashblocks_quote post type.
 */
function flashblocks_register_quote_post_type() {
	$labels = array(
		'name'                  => _x( 'Quotes', 'post type general name', 'flashblocks-quotes' ),
		'singular_name'         => _x( 'Quote', 'post type singular name', 'flashblocks-quotes' ),
		'add_new'               => __( 'Add New', 'flashblocks-quotes' ),
		'add_new_item'          => __( 'Add New Quote', 'flashblocks-quotes' ),
		'edit_item'             => __( 'Edit Quote', 'flashblocks-quotes' ),
		'new_item'              => __( 'New Quote', 'flashblocks-quotes' ),
		'view_item'             => __( 'View Quote', 'flashblocks-quotes' ),
		'view_items'            => __( 'View Quotes', 'flashblocks-quotes' ),
		'search_items'          => __( 'Search Quotes', 'flashblocks-quotes' ),
		'not_found'             => __( 'No quotes found.', 'flashblocks-quotes' ),
		'not_found_in_trash'    => __( 'No quotes found in Trash.', 'flashblocks-quotes' ),
		'all_items'             => __( 'All Quotes', 'flashblocks-quotes' ),
		'archives'              => __( 'Quote Archives', 'flashblocks-quotes' ),
		'attributes'            => __( 'Quote Attributes', 'flashblocks-quotes' ),
		'insert_into_item'      => __( 'Insert into quote', 'flashblocks-quotes' ),
		'uploaded_to_this_item' => __( 'Uploaded to this quote', 'flashblocks-quotes' ),
		'menu_name'             => _x( 'Quotes', 'admin menu', 'flashblocks-quotes' ),
	);

	register_post_type(
		'flashblocks_quote',
		array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'rest_base'           => 'flashblocks-quotes',
			'menu_icon'           => 'dashicons-format-quote',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions' ),
			'taxonomies'          => array( 'flashblocks_quote_category' ),
			'has_archive'         => false,
			'rewrite'             => array( 'slug' => 'quotes', 'with_front' => false ),
			'show_in_nav_menus'   => false,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		)
	);
}
add_action( 'init', 'flashblocks_register_quote_post_type' );

/**
 * Registers the flashblocks_quote_category taxonomy.
 */
function flashblocks_register_quote_category_taxonomy() {
	$labels = array(
		'name'              => _x( 'Quote Categories', 'taxonomy general name', 'flashblocks-quotes' ),
		'singular_name'     => _x( 'Quote Category', 'taxonomy singular name', 'flashblocks-quotes' ),
		'search_items'      => __( 'Search Quote Categories', 'flashblocks-quotes' ),
		'all_items'         => __( 'All Quote Categories', 'flashblocks-quotes' ),
		'parent_item'       => __( 'Parent Quote Category', 'flashblocks-quotes' ),
		'parent_item_colon' => __( 'Parent Quote Category:', 'flashblocks-quotes' ),
		'edit_item'         => __( 'Edit Quote Category', 'flashblocks-quotes' ),
		'update_item'       => __( 'Update Quote Category', 'flashblocks-quotes' ),
		'add_new_item'      => __( 'Add New Quote Category', 'flashblocks-quotes' ),
		'new_item_name'     => __( 'New Quote Category Name', 'flashblocks-quotes' ),
		'menu_name'         => __( 'Categories', 'flashblocks-quotes' ),
		'not_found'         => __( 'No quote categories found.', 'flashblocks-quotes' ),
	);

	register_taxonomy(
		'flashblocks_quote_category',
		'flashblocks_quote',
		array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'rest_base'         => 'flashblocks-quote-categories',
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'rewrite'           => array( 'slug' => 'quote-category', 'with_front' => false ),
			'show_ui'           => true,
		)
	);
}
add_action( 'init', 'flashblocks_register_quote_category_taxonomy' );
