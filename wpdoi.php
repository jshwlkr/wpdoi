<?php

/**
 * WP-DOI
 *
 * @wordpress-plugin
 * plugin Name:       WP-DOI
 * plugin URI:        https://github.com/jshwlkr/wpdoi
 * description:       Add DOI metadata to a WordPress post.
 * version: 1.0.2
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Joshua Walker
 * Author URI:        https://jshwlkr.info
 * Text Domain:       wpdoi
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Update URI:        https://github.com/jshwlkr/wpdoi
 * GitHub Plugin URI: jshwlkr/wpdoi
 * Release Asset: 	  true
 * Primary Branch:    main
 *
 **
 * Resources:
 * https://github.com/WordPress/gutenberg-examples/tree/trunk/plugin-sidebar/
 *
 */

// TODO: Check if not gutenberg
// TODO: https://developer.wordpress.org/reference/functions/register_uninstall_hook/
// TODO: ability to add to more than just posts?

if ( ! defined( 'ABSPATH' ) ) {
    wp_die();
}

require_once ABSPATH . '/wp-admin/includes/screen.php';

/**
 * Registers the meta key with WordPress.
 *
 * @return void
 */
function wpdoi_register_meta() {

    $object_type = 'post';

    $args = array(
        'type'         => 'string',
        'description'  => 'A meta key associated with post views, meant to contain a DOI.',
        'single'       => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field'
    );

    register_meta( $object_type, 'wpdoi_doi', $args );

}

add_action( 'init', 'wpdoi_register_meta');

function wpdoi_assets() {

	wp_register_script(
		'wpdoi-sidebar-js',
		plugins_url( 'admin/js/wpdoi-sidebar.js', __FILE__ ),
		array(
			'wp-plugins',
			'wp-edit-post',
			'wp-element',
			'wp-components'
		)
	);

	wp_register_style(
		'wpdoi-sidebar-css',
		plugins_url( 'admin/css/wpdoi-sidebar.css', __FILE__ )
	);

}

add_action( 'init', 'wpdoi_assets');

function wpdoi_enqueue() {
	wp_enqueue_script( 'wpdoi-sidebar-js' );
	wp_enqueue_style( 'wpdoi-sidebar-css' );

}

add_action( 'enqueue_block_editor_assets', 'wpdoi_enqueue' );


function wpdoi_meta() {

	$ID = get_the_ID();

	if ( ! wpdoi_is_admin() && metadata_exists( 'post', $ID, 'wpdoi_doi' ) ) {

        add_filter( 'language_attributes', 'wpdoi_xml_namespaces' );
		add_action( 'wp_head', 'wpdoi_dublin_core', 0 );

    }
}

add_action('wp','wpdoi_meta');

function wpdoi_xml_namespaces( $output ) {

	$dc = 'xmlns:dc="http://purl.org/dc/terms/"';
	$doi = 'xmlns:doi="http://dx.doi.org/"';

	if ( stripos( $output, $dc ) === false ) {
		$output .= ' ' . $dc;
	}

	if ( stripos( $output, $doi ) === false ) {
		$output .= ' ' . $doi;
	}

    return $output;

}

function wpdoi_dublin_core() {

	$ID = get_the_ID();
	$DOI = sanitize_text_field( get_post_meta( $ID, 'wpdoi_doi', true ) );
	//TODO: check meta first
	//TODO: sprintf?
	echo '<meta name="dc.identifier" content="doi:' . $DOI . '">';
	echo '<meta name="DOI" content="' . $DOI . '">';
	echo '<meta name="citation_doi" content="' . $DOI . '">';
}


function wpdoi_is_admin() {

	if ( is_admin() ) {
		return true;
	}

    if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
        return true;
    }

    $current_screen = get_current_screen();
    if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
        return true;
    }

    return false;
}

add_action( 'admin_enqueue_scripts', 'wpdoi_is_admin' );
