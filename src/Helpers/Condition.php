<?php

namespace Art\BloatTrimmer\Helpers;

class Condition {

	/**
	 * Determines if the current page is a post edit or new post page in the WordPress admin.
	 *
	 * @param  string|null $mode Optional. Specify "edit" to check only edit pages,
	 *                           "new" to check only new post pages, or null to check both.
	 *
	 * @return bool True if the current page matches the specified mode, false otherwise.
	 */
	public static function is_edit_page( string $mode = null ): bool {

		if ( ! is_admin() ) {
			return false;
		}

		$screen = get_current_screen();

		if ( ! $screen ) {
			return false;
		}

		$is_edit = 'post' === $screen->base && 'edit.php' === $screen->parent_file;
		$is_new  = 'post' === $screen->base && 'add' === $screen->action;

		if ( 'edit' === $mode ) {
			return $is_edit;
		}

		if ( 'new' === $mode ) {
			return $is_new;
		}

		return $is_edit || $is_new;
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
