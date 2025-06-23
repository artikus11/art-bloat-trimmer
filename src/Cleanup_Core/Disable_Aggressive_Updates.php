<?php
/**
 * Class Disable_Aggressive_Updates
 *
 * @since   2.0.0
 * @package art-bloat-trimmer
 * @source https://wp-kama.ru/id_8514/uskoryaem-adminku-wordpress-otklyuchaem-proverki-obnovlenij.html
 */

namespace Art\BloatTrimmer\Cleanup_Core;

use Art\BloatTrimmer\Interfaces\Init_Hooks_Interface;

class Disable_Aggressive_Updates implements Init_Hooks_Interface {

	public function init_hooks(): void {

		/**
		 *  Отключим проверку обновлений при любом заходе в админку.
		 */
		remove_action( 'admin_init', '_maybe_update_core' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_themes' );

		/**
		 * Отключим проверку обновлений при заходе на специальную страницу в админке.
		 */
		remove_action( 'load-plugins.php', 'wp_update_plugins' );
		remove_action( 'load-themes.php', 'wp_update_themes' );

		/**
		 * Оставим принудительную проверку при заходе на страницу обновлений.
		 */
		remove_action( 'load-update-core.php', 'wp_update_plugins' );
		remove_action( 'load-update-core.php', 'wp_update_themes' );

		/**
		 * Отключим проверку на версию php.
		 */
		add_filter( 'pre_site_transient_php_check_' . md5( PHP_VERSION ), '__return_empty_array' );

		$this->check_browser();
	}


	/**
	 * Отключим проверку необходимости обновить браузер в консоли - мы всегда юзаем топовые браузеры!
	 * эта проверка происходит раз в неделю...
	 *
	 * @see https://wp-kama.ru/function/wp_check_browser_version
	 */
	protected function check_browser(): void {

		if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$user_agent = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );

			add_filter( 'pre_site_transient_browser_' . md5( $user_agent ), '__return_empty_array' );
		}
	}
}
