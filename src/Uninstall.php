<?php
/**
 *
 * Class Uninstall
 *
 * Удаление настроек и меты при удалении плагина
 *
 * @since   2.1.0
 * @package art-bloat-trimmer
 */

namespace Art\BloatTrimmer;

class Uninstall {

	/**
	 * Deleting settings when uninstalling the plugin
	 *
	 * @since 2.1.0
	 */
	public static function init(): void {

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		self::remove_options();
	}


	protected static function remove_options(): void {

		global $wpdb;

		//phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->options WHERE option_name LIKE %s",
				sprintf( '%s%s', $wpdb->esc_like( 'abt_' ), '%' )
			)
		);
	}
}
