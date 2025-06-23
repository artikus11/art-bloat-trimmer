<?php
/**
 * Class Disable_Embeds
 *
 * @since   2.0.0
 * @package art-bloat-trimmer
 * @source https://wordpress.org/plugins/disable-embeds/
 */

namespace Art\BloatTrimmer\Cleanup_Core;

use Art\BloatTrimmer\Helpers\Utils;
use Art\BloatTrimmer\Interfaces\Init_Hooks_Interface;
use WP_Scripts;

class Disable_Embeds implements Init_Hooks_Interface {

	public function init_hooks(): void {

		add_action( 'init', [ $this, 'disable_embeds' ], PHP_INT_MAX );
	}


	public function disable_embeds() {

		global $wp;

		// Remove the embed query var.
		$wp->public_query_vars = array_diff( $wp->public_query_vars, [
			'embed',
		] );

		// Remove the oembed/1.0/embed REST route.
		add_filter( 'rest_endpoints', [ $this, 'remove_embed_endpoint' ] );

		// Disable handling of internal embeds in oembed/1.0/proxy REST route.
		add_filter( 'oembed_response_data', [ $this, 'filter_oembed_response_data' ] );

		// Turn off oEmbed auto discovery.
		add_filter( 'embed_oembed_discover', '__return_false' );

		// Don't filter oEmbed results.
		remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

		// Remove oEmbed discovery links.
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

		// Remove oEmbed-specific JavaScript from the front-end and back-end.
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		add_filter( 'tiny_mce_plugins', [ $this, 'tiny_mce_plugin' ] );

		// Remove all embeds rewrite rules.
		add_filter( 'rewrite_rules_array', [ $this, 'disable_embeds_rewrites' ] );

		// Remove filter of the oEmbed result before any HTTP requests are made.
		remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );

		// Load block editor JavaScript.
		add_action( 'enqueue_block_editor_assets', [ $this, 'disable_embed_variation' ] );

		// Remove wp-embed dependency of wp-edit-post script handle.
		add_action( 'wp_default_scripts', [ $this, 'remove_script_dependencies' ] );
	}


	public function disable_embed_variation() {

		$deps = [ 'wp-blocks', 'wp-dom-ready' ];

		global $pagenow;

		if ( 'widgets.php' === $pagenow ) {
			$deps[] = 'wp-edit-widgets';
		} elseif ( 'site-editor.php' === $pagenow ) {
			$deps[] = 'wp-edit-site';
		} else {
			$deps[] = 'wp-edit-post';
		}

		wp_enqueue_script(
			sprintf( '%s-disable-embed-variation', Utils::get_plugin_prefix() ),
			sprintf( '%s/assets/js/%s-disable-embed-variation.js', Utils::get_plugin_url(), Utils::get_plugin_prefix() ),
			$deps,
			Utils::get_plugin_version(),
			false
		);
	}


	/**
	 * Removes the oembed/1.0/embed REST route.
	 *
	 * @param  array $endpoints Registered REST API endpoints.
	 *
	 * @return array Filtered REST API endpoints.
	 * @since 1.4.0
	 */
	public function remove_embed_endpoint( $endpoints ): array {

		unset( $endpoints['/oembed/1.0/embed'] );

		return $endpoints;
	}


	/**
	 * Disables sending internal oEmbed response data in proxy endpoint.
	 *
	 * @param  array $data The response data.
	 *
	 * @return array|false Response data or false if in a REST API context.
	 * @since 1.4.0
	 */
	public function filter_oembed_response_data( $data ) {

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return false;
		}

		return $data;
	}


	public function tiny_mce_plugin( $plugins ) {

		return array_diff( $plugins, [ 'wpembed' ] );
	}


	/**
	 * Remove all rewrite rules related to embeds.
	 *
	 * @param  array $rules WordPress rewrite rules.
	 *
	 * @return array Rewrite rules without embeds rules.
	 * @since 1.2.0
	 */
	public function disable_embeds_rewrites( $rules ) {

		foreach ( $rules as $rule => $rewrite ) {
			if ( false !== strpos( $rewrite, 'embed=true' ) ) {
				unset( $rules[ $rule ] );
			}
		}

		return $rules;
	}


	/**
	 * Removes wp-embed dependency of core packages.
	 *
	 * @param  WP_Scripts $scripts WP_Scripts instance, passed by reference.
	 *
	 * @since 1.4.0
	 */
	public function remove_script_dependencies( WP_Scripts $scripts ) {

		if ( ! empty( $scripts->registered['wp-edit-post'] ) ) {
			$scripts->registered['wp-edit-post']->deps = array_diff(
				$scripts->registered['wp-edit-post']->deps,
				[ 'wp-embed' ]
			);
		}
	}
}
