<?php
/**
 * Class Cleanup_Bar
 *
 * @since   2.0.0
 * @package art-bloat-trimmer
 */

namespace Art\BloatTrimmer\Cleanup_Core;

use Art\BloatTrimmer\Interfaces\Init_Hooks_Interface;

class Cleanup_Bar implements Init_Hooks_Interface {

	public function init_hooks(): void {

		add_action( 'admin_bar_menu', [ $this, 'admin_bar' ], PHP_INT_MAX );
	}


	/**
	 * @param  \WP_Admin_Bar $wp_admin_bar
	 *
	 * @return void
	 */
	public function admin_bar( \WP_Admin_Bar $wp_admin_bar ): void {

		$nodes = $wp_admin_bar->get_nodes();

		$allowed_nodes = apply_filters(
			'abt_allowed_nodes_admin_bar',
			[
				'user-actions',
				'user-info',
				'edit-profile',
				'my-account',
				'logout',
				'top-secondary',
				'site-name',
				'view-site',
				'customize',
				'edit',
				'tm-suspend',
				'tm-view',
				'languages',
				'wp-rocket',
				'rocket-settings',
				'purge-all',
				'ppurge-url',
				'query-monitor',
				'query-monitor-placeholder',
				'view',
			]
		);

		foreach ( $nodes as $node_key => $node ) {
			if ( ! in_array( $node_key, $allowed_nodes, true ) ) {
				$wp_admin_bar->remove_node( $node_key );
			}
		}
	}
}
