<?php
/**
 * Class Disable_Feed
 *
 * @since   2.0.0
 * @package art-bloat-trimmer
 * @source https://wordpress.org/plugins/disable-feeds/
 */

namespace Art\BloatTrimmer\Cleanup_Core;

class Disable_Xml_Rpc {

	public function init_hooks(): void {

		add_filter( 'template_redirect', [ $this, 'remove_x_pingback_headers' ] );
		add_filter( 'wp_headers', [ $this, 'remove_x_pingback' ] );

		// Remove RSD link from head
		remove_action( 'wp_head', 'rsd_link' );

		// Disable xmlrcp/pingback
		add_filter( 'xmlrpc_enabled', '__return_false' );
		add_filter( 'pre_update_option_enable_xmlrpc', '__return_false' );
		add_filter( 'pre_option_enable_xmlrpc', '__return_zero' );
		add_filter( 'pings_open', '__return_false' );

		// Force to uncheck pingbck and trackback options
		add_filter( 'pre_option_default_ping_status', '__return_zero' );

		add_filter( 'xmlrpc_methods', '__return_empty_array' );
	}


	public function remove_x_pingback_headers() {

		if ( function_exists( 'header_remove' ) ) {
			header_remove( 'X-Pingback' );
			header_remove( 'Server' );
		}
	}


	/**
	 * Remove X-Pingback
	 * https://github.com/nickyurov/
	 */
	public function remove_x_pingback( $headers ) {

		unset( $headers['X-Pingback'] );

		return $headers;
	}
}
