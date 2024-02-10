<?php
/**
 * Class Cleanup_Head
 *
 * @since   2.0.0
 * @package art-cleaner
 * @source https://wp-kama.ru/hook/wp_head#udalenie-funktsij-wp-iz-wp_head
 */

namespace Art\Cleaner\Cleanup_Core;

use Art\Cleaner\Admin\Options;

class Cleanup_Head {

	public function init_hooks(): void {

		if ( 'yes' === Options::get( 'cleanup_head_generator', 'head' ) ) {
			$this->cleanup_generator();
		}

		if ( 'yes' === Options::get( 'cleanup_head_shortlink', 'head' ) ) {
			$this->cleanup_shortlink();
		}

		if ( 'yes' === Options::get( 'cleanup_head_wp_json', 'head' ) ) {
			$this->cleanup_wp_json();
		}

		if ( 'yes' === Options::get( 'cleanup_head_rsd_link', 'head' ) ) {
			remove_action( 'wp_head', 'rsd_link' );
		}
	}


	/**
	 * Remove WP version
	 */
	public function cleanup_generator() {

		remove_action( 'wp_head', 'wp_generator' );
		add_filter( 'the_generator', '__return_empty_string' );
	}


	/**
	 * Remove Shortlink
	 */
	public function cleanup_shortlink() {

		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
		remove_action( 'template_redirect', 'wp_shortlink_header', 11, 0 );
	}


	/**
	 * Remove REST API links
	 */
	public function cleanup_wp_json() {

		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );

		remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
		remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
		remove_action( 'template_redirect', 'rest_output_link_header', 11 );
	}
}
