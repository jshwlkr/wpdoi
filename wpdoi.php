<?php

/**
 * WP-DOI
 *
 * @package           WPDOI
 * @version           1.0.0
 * @author            Joshua Walker
 * @copyright         2019 Joshua Walker
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       WP-DOI
 * Plugin URI:        https://github.com/jshwlkr/wpdoi
 * Description:       Add DOI metadata to a WordPress post.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Joshua Walker
 * Author URI:        https://example.com
 * Text Domain:       wpdoi
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://github.com/jshwlkr/wpdoi
 * GitHub Plugin URI: jshwlkr/wpdoi
 * Primary Branch:    main
 */

// TODO: Check if not gutenberg

if ( ! defined( 'ABSPATH' ) ) {
    wp_die();
}

/**
 * Registers the meta key with WordPress.
 *
 * @return void
 */
function wpdoi_register_meta() {

    $object_type = 'post';

    $args = array(
        'type'         => 'string',
        'description'  => 'A meta key associated with post views.',
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
		plugins_url( 'wpdoi-sidebar.js', __FILE__ ),
		array(
			'wp-plugins',
			'wp-edit-post',
			'wp-element',
			'wp-components'
		)
	);

}

add_action( 'init', 'wpdoi_assets');

function wpdoi_enqueue() {
	wp_enqueue_script( 'plugin-sidebar-js' );
}

add_action( 'enqueue_block_editor_assets', 'wpdoi_enqueue' );


/**
 * https://github.com/WordPress/gutenberg-examples/tree/trunk/plugin-sidebar
 */


// TODO: https://developer.wordpress.org/reference/functions/register_uninstall_hook/


	/**
	 * Check if Gutenberg is active.
	 * Must be used not earlier than plugins_loaded action fired.
	 * https://gist.github.com/mihdan/8ba1a70d8598460421177c7d31202908
	 *
	 * @return bool
	 */
	private function is_gutenberg_active() {
		$gutenberg    = false;
		$block_editor = false;

		if ( has_filter( 'replace_editor', 'gutenberg_init' ) ) {
			// Gutenberg is installed and activated.
			$gutenberg = true;
		}

		if ( version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' ) ) {
			// Block editor.
			$block_editor = true;
		}

		if ( ! $gutenberg && ! $block_editor ) {
			return false;
		}

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( ! is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
			return true;
		}

		$use_block_editor = ( get_option( 'classic-editor-replace' ) === 'no-replace' );

		return $use_block_editor;
	}
