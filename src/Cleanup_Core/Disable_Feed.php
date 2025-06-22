<?php
/**
 * Class Disable_Feed
 *
 * @since   2.0.0
 * @package art-bloat-trimmer
 * @source https://wordpress.org/plugins/disable-feeds/
 */

namespace Art\BloatTrimmer\Cleanup_Core;

class Disable_Feed {

	public function init_hooks(): void {

		add_action( 'wp_loaded', [ $this, 'remove_links' ] );
		add_action( 'template_redirect', [ $this, 'filter_feeds' ], 1 );
		add_filter( 'bbp_request', [ $this, 'filter_bbp_feeds' ], 9 );
	}


	public function remove_links(): void {

		remove_action( 'wp_head', 'feed_links', 2 );
		remove_action( 'wp_head', 'feed_links_extra', 3 );
	}


	public function filter_feeds(): void {

		if ( ! is_feed() || is_404() ) {
			return;
		}

		$this->redirect_feed();
	}


	protected function redirect_feed(): void {

		global $wp_rewrite, $wp_query;

		if ( isset( $_GET['feed'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification
			wp_safe_redirect( esc_url_raw( remove_query_arg( 'feed' ) ), 301 );
			exit;
		}

		// WP redirects these anyway, and removing the query var will confuse it thoroughly
		if ( 'old' !== get_query_var( 'feed' ) ) {
			set_query_var( 'feed', '' );
		}

		// Let WP figure out the appropriate redirect URL.
		redirect_canonical();

		// Still here? redirect_canonical failed to redirect, probably because of a filter. Try the hard way.
		$struct = ( ! is_singular() && is_comment_feed() ) ? $wp_rewrite->get_comment_feed_permastruct() : $wp_rewrite->get_feed_permastruct();
		$struct = preg_quote( $struct, '#' );
		$struct = str_replace( '%feed%', '(\w+)?', $struct );
		$struct = preg_replace( '#/+#', '/', $struct );

		$requested_url = sprintf( '%s%s%s',
			is_ssl() ? 'https://' : 'http://',
			! empty( $_SERVER['HTTP_HOST'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '',
			! empty( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : ''
		);

		$new_url = preg_replace( '#' . $struct . '/?$#', '', $requested_url );

		if ( $new_url !== $requested_url ) {
			wp_safe_redirect( $new_url, 301 );
			exit;
		}
	}


	/**
	 * BBPress feed detection sourced from bbp_request_feed_trap() in BBPress Core.
	 *
	 * @param  array $query_vars
	 *
	 * @return array
	 */
	public function filter_bbp_feeds( $query_vars ) {

		// Looking at a feed
		if ( isset( $query_vars['feed'] ) ) {

			// Forum/Topic/Reply Feed
			if ( isset( $query_vars['post_type'] ) ) {

				// Matched post type
				$post_type = false;

				// Post types to check
				$post_types = [
					bbp_get_forum_post_type(),
					bbp_get_topic_post_type(),
					bbp_get_reply_post_type(),
				];

				// Cast query vars as array outside of foreach loop
				$qv_array = (array) $query_vars['post_type'];

				// Check if this query is for a bbPress post type
				foreach ( $post_types as $bbp_pt ) {
					if ( in_array( $bbp_pt, $qv_array, true ) ) {
						$post_type = $bbp_pt;
						break;
					}
				}

				// Looking at a bbPress post type
				if ( ! empty( $post_type ) ) {
					$this->redirect_feed();
				}
			}
			// @todo User profile feeds
		}

		// No feed so continue on
		return $query_vars;
	}
}
