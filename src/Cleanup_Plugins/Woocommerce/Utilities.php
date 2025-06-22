<?php

namespace Art\BloatTrimmer\Cleanup_Plugins\Woocommerce;

use Art\BloatTrimmer\Utils;

class Utilities {

	public static function clear_order_notes( int $limit = 0 ): string {

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$query = "DELETE p FROM {$wpdb->prefix}comments p
                 JOIN {$wpdb->prefix}posts pm on p.comment_post_ID = pm.ID
                 WHERE comment_type = 'order_note' 
                 AND post_status IN ('wc-completed', 'wc-cancelled')";

		if ( $limit > 0 ) {
			$query .= " LIMIT {$limit}";
		}

		$result = absint( $wpdb->query( $query ) );

		wp_cache_flush();

		return sprintf(
			'Успешно удалено: %d %s',
			$result,
			self::get_label_translation( 'notes', $result )
		);
	}


	public static function clear_scheduler_actions( int $limit = 0 ): string {

		global $wpdb;

		$query = "DELETE FROM {$wpdb->prefix}actionscheduler_actions
                 WHERE `status` IN ('canceled', 'failed', 'complete')";

		if ( $limit > 0 ) {
			$query .= " LIMIT {$limit}";
		}

		$result = absint( $wpdb->query( $query ) );

		wp_cache_flush();

		return sprintf(
			'Успешно удалено: %d %s',
			$result,
			self::get_label_translation( 'actions', $result )
		);
	}


	public static function clear_scheduler_actions_logs( int $limit = 0 ): string {

		global $wpdb;

		if ( $limit > 0 ) {

			$result = $wpdb->query(
				"DELETE FROM `{$wpdb->prefix}actionscheduler_logs` 
                 WHERE action_id IN (
                     SELECT action_id FROM `{$wpdb->prefix}actionscheduler_logs` 
                     LIMIT {$limit}
                 )"
			);

			wp_cache_flush();

			return sprintf(
				'Успешно удалено: %d %s',
				$result,
				self::get_label_translation( 'logs', $result )
			);
		} else {

			$wpdb->query( "TRUNCATE `{$wpdb->prefix}actionscheduler_logs`" );

			wp_cache_flush();

			return 'Журнал задач успешно очищен.';
		}
	}


	public static function get_count_select_order_notes(): string {

		global $wpdb;

		$count = $wpdb->get_var(
			"SELECT COUNT(*)
             FROM {$wpdb->prefix}comments p
             JOIN {$wpdb->prefix}posts pm on p.comment_post_ID = pm.ID
             WHERE comment_type = 'order_note' 
             AND post_status IN ('wc-completed', 'wc-cancelled')"
		);

		return absint( $count );
	}


	public static function get_count_select_scheduler_actions(): string {

		global $wpdb;

		$count = $wpdb->get_var(
			"SELECT COUNT(*)
             FROM {$wpdb->prefix}actionscheduler_actions
             WHERE `status` IN ('canceled', 'failed', 'complete')"
		);

		return absint( $count );
	}


	public static function get_count_select_scheduler_actions_logs(): string {

		global $wpdb;

		$count = $wpdb->get_var(
			"SELECT COUNT(*)
             FROM `{$wpdb->prefix}actionscheduler_logs`"
		);

		return absint( $count );
	}


	public static function select_order_notes(): string {

		$result = self::get_count_select_order_notes();

		return sprintf(
			'Получено: %d %s',
			$result,
			self::get_label_translation( 'notes', $result )
		);
	}


	public static function select_scheduler_actions(): string {

		$result = self::get_count_select_scheduler_actions();

		return sprintf(
			'Получено: %d %s',
			$result,
			self::get_label_translation( 'actions', $result )
		);
	}


	public static function select_scheduler_actions_logs(): string {

		$result = self::get_count_select_scheduler_actions_logs();

		return sprintf(
			'Получено: %d %s',
			$result,
			self::get_label_translation( 'logs', $result )
		);
	}


	public static function get_label_translation( string $label, int $count = null ): string {

		$labels = [
			'actions' => [ 'задача', 'задачи', 'задач' ],
			'logs'    => [ 'лог', 'лога', 'логов' ],
			'notes'   => [ 'заметка', 'заметки', 'заметок' ],
		];

		if ( ! isset( $labels[ $label ] ) ) {
			return $label;
		}

		if ( $count === null ) {
			return $labels[ $label ][1];
		}

		return Utils::plural_form( $count, $labels[ $label ] );
	}
}
