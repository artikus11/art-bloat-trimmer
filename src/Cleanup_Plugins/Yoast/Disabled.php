<?php

namespace Art\BloatTrimmer\Cleanup_Plugins\Yoast;

use Art\BloatTrimmer\Admin\Options;

class Disabled {

	public function init_hooks(): void {

		if ( 'on' === Options::get( 'yoast_disable_ads', 'plugins', 'off' ) ) {
			define( 'WPSEO_PREMIUM_FILE', true );
		}

		if ( 'on' === Options::get( 'yoast_disable_submenu_pages', 'plugins', 'off' ) ) {
			add_action( 'admin_menu', [ $this, 'remove_admin_addon_submenu' ], 999 );
		}

		if ( 'on' === Options::get( 'yoast_remove_head_comment', 'plugins', 'off' ) ) {
			add_filter( 'wpseo_debug_markers', '__return_false' );
		}

		if ( 'on' === Options::get( 'yoast_disable_filters_columns', 'plugins', 'off' ) ) {
			add_filter( 'wpseo_use_page_analysis', '__return_false' );
			add_filter( 'wpseo_link_count_post_types', '__return_empty_array' );
		}

		add_filter( 'wpseo_metabox_prio', function () {

			return 'low';
		} );
	}


	public function remove_admin_addon_submenu(): void {

		remove_submenu_page( 'wpseo_dashboard', 'wpseo_page_academy' ); // Академия
		remove_submenu_page( 'wpseo_dashboard', 'wpseo_licenses' ); // Обнолвения
		remove_submenu_page( 'wpseo_dashboard', 'wpseo_workouts' ); // Тренажеры
		remove_submenu_page( 'wpseo_dashboard', 'wpseo_page_support' ); // Поддержка
		remove_submenu_page( 'wpseo_dashboard', 'wpseo_integrations' ); // Интеграции
		remove_submenu_page( 'wpseo_dashboard', 'wpseo_redirects' ); // Редиректы
	}
}
