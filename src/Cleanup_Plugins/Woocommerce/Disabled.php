<?php
/**
 * Class Disabled
 *
 * @since   2.0.0
 * @package art-bloat-trimmer/src/Cleanup_Plugins/Woocommerce
 */

namespace Art\BloatTrimmer\Cleanup_Plugins\Woocommerce;

use Art\BloatTrimmer\Admin\Options;
use Art\BloatTrimmer\Interfaces\Init_Hooks_Interface;

class Disabled implements Init_Hooks_Interface {

	public function init_hooks(): void {

		if ( 'on' === Options::get( 'woocommerce_disable_feature', 'plugins', 'off' ) ) {
			$this->disable_feature();
		} else {
			add_filter( 'woocommerce_admin_get_feature_config', [ $this, 'feature_config_disabled_selected' ], 1000, 1 );
		}

		if ( 'on' === Options::get( 'woocommerce_disable_admin_menu', 'plugins', 'off' ) ) {
			add_action( 'admin_menu', [ $this, 'remove_admin_addon_submenu' ], 999 );
			add_action( 'admin_menu', [ $this, 'remove_admin_addon_submenu_conditionals' ], 999 );
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'only_wc_admin_app' ], 19 );

		add_action( 'admin_menu', [ $this, 'remove_admin_addon_submenu_reviews' ], 999 );
	}


	/**
	 * @return void
	 */
	protected function disable_feature(): void {

		add_action( 'admin_menu', [ $this, 'remove_all_woocommerce_submenu' ], 999 );

		// уведомление о расширениях
		add_filter( 'woocommerce_helper_suppress_admin_notices', '__return_true' );

		add_filter( 'woocommerce_admin_disabled', '__return_true' );

		add_filter( 'woocommerce_admin_get_feature_config', [ $this, 'feature_config_disabled_all' ], 1000, 1 );

		// Отключение верхней панели в админке на товарах
		add_filter( 'woocommerce_navigation_is_connected_page', '__return_false' );

		// Отключение верхней панели в админке на странице статуса
		add_filter( 'woocommerce_navigation_pages_with_tabs', function ( $tabs ) {

			unset( $tabs['wc-status'] );

			return $tabs;
		} );
	}


	public function feature_config_disabled_all( $feature ): array {

		return array_fill_keys( array_keys( $feature ), false );
	}


	public function only_wc_admin_app(): void {

		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		$screen_current = [
			'woocommerce_page_wc-settings',
			'woocommerce_page_wc-admin',
		];

		if ( in_array( $screen_id, $screen_current, true ) ) {
			wp_enqueue_style( 'wc-components' );
			wp_enqueue_style( 'wc-admin-app' );
			wp_add_inline_style( 'wc-admin-app', '#wpbody { margin-top: 0;}' );
		}
	}


	public function remove_admin_addon_submenu(): void {

		remove_submenu_page( 'woocommerce', 'wc-admin' );
		remove_submenu_page( 'woocommerce', 'wc-addons' );
		remove_submenu_page( 'woocommerce', 'wc-reports' );
		remove_submenu_page( 'woocommerce', 'wc-addons&section=helper' );
		remove_submenu_page( 'woocommerce', 'wc-admin&path=/extensions' );
		remove_menu_page( 'admin.php?page=wc-settings&tab=checkout' ); // Отключение ссылки на Платежи
	}


	public function remove_all_woocommerce_submenu(): void {

		remove_submenu_page( 'woocommerce', 'wc-admin' );
		remove_submenu_page( 'woocommerce', 'wc-addons' );
		remove_submenu_page( 'woocommerce', 'wc-reports' );
		remove_submenu_page( 'woocommerce', 'wc-addons&section=helper' );
		remove_submenu_page( 'woocommerce', 'wc-admin&path=/extensions' );
		remove_menu_page( 'admin.php?page=wc-settings&tab=checkout&from=PAYMENTS_MENU_ITEM' ); // Отключение ссылки на Платежи
	}


	public function remove_admin_addon_submenu_conditionals(): void {

		if ( 'no' === get_option( 'woocommerce_show_marketplace_suggestions' ) ) {
			remove_submenu_page( 'woocommerce', 'wc-admin&path=/extensions' );
		}

		if ( 'no' === get_option( 'woocommerce_enable_reviews' ) ) {
			remove_submenu_page( 'edit.php?post_type=product', 'product-reviews' );
		}
	}


	public function remove_admin_addon_submenu_reviews(): void {

		if ( 'no' === get_option( 'woocommerce_enable_reviews' ) ) {
			remove_submenu_page( 'edit.php?post_type=product', 'product-reviews' );
		}
	}


	protected function has_launch_your_store(): bool {

		$option = Options::get( 'woocommerce_disable_launch_your_store', 'plugins', 'off' );

		if ( 'on' === $option ) {
			update_option( 'woocommerce_coming_soon', 'no' );

			return false;
		}

		return true;
	}


	protected function has_analytics(): bool {

		$option = Options::get( 'woocommerce_disable_analytics', 'plugins', 'off' );

		if ( 'on' === $option ) {
			update_option( 'woocommerce_analytics_enabled', 'no' );

			return false;
		}

		update_option( 'woocommerce_analytics_enabled', 'yes' );

		return true;
	}


	protected function has_activity_panels(): bool {

		$option = Options::get( 'woocommerce_disable_activity_panels', 'plugins', 'off' );

		if ( 'on' === $option ) {
			return false;
		}

		return true;
	}


	protected function has_product_block_editor(): bool {

		$option = Options::get( 'woocommerce_disable_product_editor', 'plugins', 'off' );

		if ( 'on' === $option ) {
			return false;
		}

		return true;
	}


	protected function has_onboarding(): bool {

		$option = Options::get( 'woocommerce_disable_onboarding', 'plugins', 'off' );

		if ( 'on' === $option ) {
			return false;
		}

		return true;
	}


	protected function has_homescreen(): bool {

		$option = Options::get( 'woocommerce_disable_homescreen', 'plugins', 'off' );

		$enable = true;

		if ( 'on' === $option ) {
			$enable = false;
		}

		if ( $this->has_analytics() ) {
			$enable = true;
		}

		return $enable;
	}


	protected function has_payments(): bool {

		$option = Options::get( 'woocommerce_disable_payments', 'plugins', 'off' );

		if ( 'on' === $option ) {
			return false;
		}

		return true;
	}


	protected function has_marketing(): bool {

		$option = Options::get( 'woocommerce_disable_marketing', 'plugins', 'off' );

		if ( 'on' === $option ) {
			return false;
		}

		return true;
	}


	protected function has_fse(): bool {

		$option = Options::get( 'woocommerce_disable_fse', 'plugins', 'off' );

		if ( 'on' === $option ) {
			return false;
		}

		return true;
	}


	/**
	 * Returns an array of feature configurations with their current status.
	 * Each feature is represented by a key-value pair, where the key is the feature identifier,
	 * and the value indicates whether the feature is enabled or disabled.
	 *
	 * Feature descriptions:
	 *
	 * - activity-panels
	 *   Панель активности WooCommerce.
	 *
	 * @default true
	 *
	 * - product-block-editor
	 *   Отключает редактор товаров полностью, даже если включена опция "Попробуйте новый редактор товаров".
	 *   Если false, то новый редактор будет пытаться загрузиться, но не загрузится.
	 * @default true
	 *
	 * - reactify-classic-payments-settings
	 *   Настройки способов оплаты на React.js.
	 * @default true
	 *
	 * - coming-soon-newsletter-template
	 *   Отвечает за шаблон страницы "Скоро открытие" с формой подписки на рассылку.
	 *   Непонятный функционал, похоже заложено на будущее.
	 * @default false
	 *
	 * - coupons
	 *   Перенос купонов, не работает если отключен Маркетинг.
	 * @default true
	 *
	 * - core-profiler
	 *   Инструмент улучшения процесса первоначальной настройки магазина (onboarding).
	 * @default true
	 *
	 * - customize-store
	 *   Эта функция предоставляет инструменты для быстрой настройки внешнего вида и функциональности вашего магазина,
	 *   особенно в процессе первоначальной настройки (onboarding). Похоже что работает только с темами FSE.
	 * @default true
	 *
	 * - customer-effort-score-tracks
	 *   Это инструмент, который помогает WooCommerce собирать отзывы от пользователей,
	 *   пользователям будут показываться запросы на оценку усилий после выполнения определённых задач (onboarding).
	 * @default true
	 *
	 * - import-products-task
	 *   Импорт товаров при первоначальной настройке магазина (onboarding).
	 * @default true
	 *
	 * - experimental-fashion-sample-products
	 *   Будет доступна возможность создания и управления образцами товаров.
	 *   Хз как это работает, видимо на будущее заложено.
	 * @default true
	 *
	 * - shipping-smart-defaults
	 *   Это экспериментальная функция, которая помогает автоматически настраивать параметры доставки для новых магазинов (onboarding).
	 * @default true
	 *
	 * - shipping-setting-tour
	 *   Интерактивный гид, который помогает новым пользователям разобраться в настройках доставки в WooCommerce (onboarding).
	 * @default true
	 *
	 * - homescreen
	 *   Главная страница WooCommerce Admin. Раздел обзор.
	 * @default true
	 *
	 * - marketing
	 *   Раздел маркетинга.
	 * @default true
	 *
	 * - minified-js
	 *   Минификация скриптов. Хз как это работает.
	 * @default false
	 *
	 * - mobile-app-banner
	 *   Связана с баннером мобильного приложения, который отображается в административной панели WooCommerce.
	 * @default true
	 *
	 * - onboarding
	 *   Первоначальная настройка магазина (Onboarding).
	 * @default true
	 *
	 * - onboarding-tasks
	 *   Первоначальная настройка магазина (Onboarding).
	 * @default true
	 *
	 * - pattern-toolkit-full-composability
	 *   Экспериментальная функция, которая предоставляет полную композируемость паттернов (product).
	 * @default true
	 *
	 * - product-pre-publish-modal
	 *   Модальное окно, которое появляется перед публикацией товара.
	 *   Работает если включено создание товаров через гутенберг (product).
	 * @default false
	 *
	 * - product-custom-fields
	 *   Поддержка произвольных полей при создании товара в Гутенберг.
	 *   Хотя не понял как там это работает (product).
	 * @default true
	 *
	 * - remote-inbox-notifications
	 *   Уведомления, которые WooCommerce получает с серверов WooCommerce.com.
	 * @default true
	 *
	 * - remote-free-extensions
	 *   Рекламная хрень. WooCommerce получает список рекомендуемых бесплатных расширений с серверов WooCommerce.com
	 *   и отображает их в административной панели.
	 * @default true
	 *
	 * - payment-gateway-suggestions
	 *   Рекомендации по платёжным шлюзам (payments).
	 * @default true
	 *
	 * - printful
	 *   Интеграция с платформой Printful. Непонятно то ли это метод доставки, то ли какой-то сервис печати на товарах.
	 * @default true
	 *
	 * - settings
	 *   Непонятно что это, при включении фаталы вываливаются.
	 * @default false
	 *
	 * - shipping-label-banner
	 *   Вроде как баннер об интеграциях с доставками. Скорее всего предназначено для плагина WooCommerce Shipping.
	 * @default true
	 *
	 * - subscriptions
	 *   Управляет доступностью функционала подписок. Скорее всего предназначено для плагина WooCommerce Subscriptions.
	 * @default true
	 *
	 * - store-alerts
	 *   Вроде как управляет доступностью уведомлений о состоянии магазина, но где конкретно это все видно непонятно.
	 * @default true
	 *
	 * - transient-notices
	 *   Временные уведомления. Скорее всего снеки, типа "Настройки сохранены".
	 * @default true
	 *
	 * - woo-mobile-welcome
	 *   Приветственный баннер для мобильного приложения WooCommerce (Onboarding).
	 * @default true
	 *
	 * - wc-pay-promotion
	 *   Реклама WooCommerce Payments — встроенного платёжного решения от WooCommerce (payments).
	 * @default true
	 *
	 * - wc-pay-welcome-page
	 *   Приветственная страница WooCommerce Payments (payments).
	 * @default true
	 *
	 * - async-product-editor-category-field
	 *   Асинхронная загрузка категорий при редактировании товара.
	 *   Работает в любом редакторе. Категории загружаются и выводятся по типу как в гутенберге (product).
	 * @default false
	 *
	 * - launch-your-store
	 *   Функционал "Видимость сайта".
	 * @default true
	 *
	 * - product-editor-template-system
	 *   Судя по всему это включение функционала темплейтов товаров под новый редактор.
	 *   Толком не работает (product).
	 * @default false
	 *
	 * - blueprint
	 *   Функционал позволяет создавать предварительно настроенные шаблоны магазинов.
	 *   Хз как это работает, скорее всего предназначено под FSE.
	 * @default false
	 *
	 * - use-wp-horizon
	 *   Вроде как инструмента для управления очередями задач (job queues).
	 * @default false
	 *
	 * - add-to-cart-with-options-stepper-layout
	 *   Функционал предоставляет пошаговый интерфейс (stepper layout) для выбора опций товара
	 *   (например, атрибутов, вариаций) перед добавлением в корзину. Для блока корзины.
	 * @default false
	 *
	 * - blockified-add-to-cart
	 *   Заменяет стандартную кнопку "Добавить в корзину" на блок Gutenberg.
	 *   Работает скорее всего в FSE.
	 * @default false
	 */
	public function feature_config_disabled_selected(): array {

		return [
			'activity-panels'                         => $this->has_activity_panels(),
			'analytics'                               => $this->has_analytics(),
			'product-block-editor'                    => $this->has_product_block_editor(),
			'product-data-views'                      => $this->has_product_block_editor(),
			'experimental-blocks'                     => $this->has_fse(),
			'coming-soon-newsletter-template'         => $this->has_fse(),
			'coupons'                                 => $this->has_marketing(),
			'core-profiler'                           => $this->has_onboarding(),
			'customize-store'                         => $this->has_onboarding(),
			'customer-effort-score-tracks'            => $this->has_onboarding(),
			'import-products-task'                    => $this->has_onboarding(),
			'experimental-fashion-sample-products'    => $this->has_onboarding(),
			'shipping-smart-defaults'                 => $this->has_onboarding(),
			'shipping-setting-tour'                   => $this->has_onboarding(),
			'homescreen'                              => $this->has_homescreen(),
			'marketing'                               => $this->has_marketing(),
			'minified-js'                             => true,
			'mobile-app-banner'                       => $this->has_onboarding(),
			'onboarding'                              => $this->has_onboarding(),
			'onboarding-tasks'                        => $this->has_onboarding(),
			'pattern-toolkit-full-composability'      => $this->has_product_block_editor(),
			'product-pre-publish-modal'               => $this->has_product_block_editor(),
			'product-custom-fields'                   => $this->has_product_block_editor(),
			'remote-inbox-notifications'              => $this->has_marketing(),
			'remote-free-extensions'                  => $this->has_marketing(),
			'payment-gateway-suggestions'             => $this->has_payments(),
			'printful'                                => false,
			'settings'                                => false,
			'shipping-label-banner'                   => false,
			'subscriptions'                           => false,
			'store-alerts'                            => false,
			'transient-notices'                       => false,
			'woo-mobile-welcome'                      => $this->has_onboarding(),
			'wc-pay-promotion'                        => $this->has_payments(),
			'wc-pay-welcome-page'                     => $this->has_payments(),
			'async-product-editor-category-field'     => true,
			'launch-your-store'                       => $this->has_launch_your_store(),
			'product-editor-template-system'          => $this->has_product_block_editor(),
			'blueprint'                               => $this->has_fse(),
			'reactify-classic-payments-settings'      => $this->has_payments(),
			'use-wp-horizon'                          => $this->has_fse(),
			'add-to-cart-with-options-stepper-layout' => $this->has_fse(),
			'blockified-add-to-cart'                  => $this->has_fse(),
		];
	}
}
