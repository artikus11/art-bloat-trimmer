<?php
/**
 * Class Disable_Emoji
 *
 * @since   2.0.0
 * @package art-bloat-trimmer
 * @source https://wordpress.org/plugins/disable-emojis/
 */

namespace Art\BloatTrimmer\Cleanup_Core;

class Disable_Emoji {

	public function init_hooks(): void {

		add_action( 'init', [ $this, 'disable_emojis' ] );
	}


	public function disable_emojis(): void {

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter( 'wp_resource_hints', [ $this, 'disable_emojis_remove_dns_prefetch' ], 10, 2 );
	}


	/**
	 * Filter function used to remove the tinymce emoji plugin.
	 *
	 * @param  array $plugins
	 *
	 * @return   array             Difference betwen the two arrays
	 */
	public function disable_emojis_tinymce( $plugins ) {

		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, [ 'wpemoji' ] );
		}

		return [];
	}


	/**
	 * Remove emoji CDN hostname from DNS prefetching hints.
	 *
	 * @param  array  $urls          URLs to print for resource hints.
	 * @param  string $relation_type The relation type the URLs are printed for.
	 *
	 * @return array                 Difference betwen the two arrays.
	 */
	public function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {

		if ( 'dns-prefetch' === $relation_type ) {

			// Strip out any URLs referencing the WordPress.org emoji location
			$emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
			foreach ( $urls as $key => $url ) {
				if ( str_contains( $url, $emoji_svg_url_bit ) ) {
					unset( $urls[ $key ] );
				}
			}
		}

		return $urls;
	}
}
