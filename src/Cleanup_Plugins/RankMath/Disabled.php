<?php
/**
 *
 * Class Disabled
 *
 * @since     2.0.0
 * @package   art-bloat-trimmer
 *
 * @link      https://rankmath.com/kb/filters-hooks-api-developer/
 *
 * @see       https://gist.github.com/timbowen/c5c00667c4c48f8ec3f5706b686d6f00
 * @see       https://github.com/herewithme/wp-filters-extras
 */

namespace Art\BloatTrimmer\Cleanup_Plugins\RankMath;

use Art\BloatTrimmer\Admin\Options;

class Disabled {

	public function init_hooks(): void {

		if ( 'on' === Options::get( 'rank_math_disable_ads', 'plugins', 'off' ) ) {
			// Отключение рекламных баннеров и сообщений
			define( 'RANK_MATH_PRO_FILE', true );

			/**
			 * Remove  Rank Math upgrade notice.
			 */
			add_action( 'wp_loaded', static function () {

				self::remove_filters(
					'admin_notices',
					'RankMathPro\Plugin_Update\Plugin_Update',
					'admin_license_notice',
					20
				);
			} );
		}

		if ( 'on' === Options::get( 'rank_math_auto_update_send_email', 'plugins', 'off' ) ) {
			// Отключить автоматическое обновление уведомлений по электронной почте.
			add_filter( 'rank_math/auto_update_send_email', '__return_false' );
		}

		if ( 'on' === Options::get( 'rank_math_disable_admin_footer_text', 'plugins', 'off' ) ) {
			// Filter: Prevent Rank Math from changing admin_footer_text.
			add_action( 'rank_math/whitelabel', '__return_true' );
		}

		if ( 'on' === Options::get( 'rank_math_disable_comments', 'plugins', 'off' ) ) {
			// Отключение комментариев плагина на фронте
			add_filter( 'rank_math/frontend/remove_credit_notice', '__return_true' );
		}

		// Отключение ссылок rel="next" / rel="prev".
		add_filter( 'rank_math/frontend/disable_adjacent_rel_links', '__return_true' );

		// Filter to remove sitemap credit.
		add_filter( 'rank_math/sitemap/remove_credit', '__return_true' );

		// Filter to remove `rank-math-link` class from the frontend content links
		add_filter( 'rank_math/link/remove_class', '__return_true' );

		// Filter to hide SEO Score
		add_filter( 'rank_math/show_score', '__return_false' );

		// Filter to hide Analytics Stats bar from the frontend
		// add_filter( 'rank_math/analytics/frontend_stats', '__return_false' );

		// Filter to hide the Email Reporting Options
		// add_filter( 'rank_math/analytics/hide_email_report_options', '__return_true' );

		// Change the Rank Math Meta Box Priority
		add_filter( 'rank_math/metabox/priority', static function () {

			return 'low';
		} );

		if ( 'on' === Options::get( 'rank_math_disable_columns', 'plugins', 'off' ) ) {
			add_action( 'wp_loaded', static function () {

				// Column score on list tables
				self::remove_filters(
					'admin_init',
					'RankMath\Admin\Post_Columns',
					'init',
					10
				);
			} );
		}

		if ( 'on' === Options::get( 'rank_math_disable_filter', 'plugins', 'off' ) ) {
			add_action( 'wp_loaded', static function () {

				// Filter score on list tables
				self::remove_filters(
					'admin_init',
					'RankMath\Admin\Post_Filters',
					'init',
					10
				);
			} );
		}
	}


	/**
	 * @param  string $hook_name
	 * @param  string $class_name
	 * @param  string $method_name
	 * @param  int    $priority
	 *
	 * @source https://github.com/herewithme/wp-filters-extras
	 *
	 * @return false
	 */
	public static function remove_filters( string $hook_name = '', string $class_name = '', string $method_name = '', int $priority = 0 ): bool {

		global $wp_filter;

		// Take only filters on right hook name and priority
		if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
			return false;
		}

		// Loop on filters registered
		foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {

			// Test if filter is an array ! (always for class/method)
			if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {

				// Test if object is a class, class and method is equal to param !
				if (
					is_object( $filter_array['function'][0] )
					&& get_class( $filter_array['function'][0] )
					&& get_class( $filter_array['function'][0] ) === $class_name
					&& $filter_array['function'][1] === $method_name
				) {
					// Test for WordPress >= 4.7 WP_Hook class (https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/)
					if ( is_a( $wp_filter[ $hook_name ], 'WP_Hook' ) ) {
						unset( $wp_filter[ $hook_name ]->callbacks[ $priority ][ $unique_id ] );
					} else {
						unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
					}
				}
			}
		}

		return false;
	}
}
