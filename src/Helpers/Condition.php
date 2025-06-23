<?php

namespace Art\BloatTrimmer\Helpers;

class Condition {

	/**
	 * Determines if current admin page is a post edit or new post screen.
	 *
	 * @param  string|null $mode Optional. Specify 'edit' for edit pages only,
	 *                           'new' for new post pages only, or null for both.
	 *
	 * @return bool True if current page matches the specified mode.
	 */
	public static function is_edit_page( string $mode = null ): bool {

		global $pagenow;

		if ( ! is_admin() ) {
			return false;
		}

		$edit_pages = [ 'post.php' ];
		$new_pages  = [ 'post-new.php' ];

		if ( 'edit' === $mode ) {
			return in_array( $pagenow, $edit_pages, true );
		}

		if ( 'new' === $mode ) {
			return in_array( $pagenow, $new_pages, true );
		}

		return in_array( $pagenow, array_merge( $edit_pages, $new_pages ), true );
	}


	public static function is_woocommerce_active(): bool {

		if ( class_exists( 'WC' ) ) {
			return true;
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return true;
		}

		return false;
	}


	public static function is_rank_math_active(): bool {

		if ( class_exists( 'RankMath' ) ) {
			return true;
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active( 'seo-by-rank-math/rank-math.php' ) ) {
			return true;
		}

		return false;
	}


	public static function is_yoast_active(): bool {

		if ( class_exists( 'Yoast\WP\SEO\Main' ) ) {
			return true;
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
			return true;
		}

		return false;
	}
}
