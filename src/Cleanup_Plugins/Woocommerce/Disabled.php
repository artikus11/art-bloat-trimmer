<?php
/**
 * Class Disabled
 *
 * @since   2.0.0
 * @package art-bloat-trimmer/src/Cleanup_Plugins/Woocommerce
 */

namespace Art\BloatTrimmer\Cleanup_Plugins\Woocommerce;

use Art\BloatTrimmer\Admin\Options;

class Disabled {

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

		//Отключение верхней панели в админке на товарах
		add_filter( 'woocommerce_navigation_is_connected_page', '__return_false' );

		//Отключение верхней панели в админке на странице статуса
		add_filter( 'woocommerce_navigation_pages_with_tabs', function ( $tabs ) {

			unset( $tabs['wc-status'] );

			return $tabs;
		} );
	}


	public function feature_config_disabled_all( $feature ): array {

		return array_fill_keys( array_keys( $feature ), false );
	}


	public function feature_config_disabled_selected(): array {

		return [
			// Панель акивности. default:true
			'activity-panels'                         => $this->has_activity_panels(),

			// Аналитика. default:true
			'analytics'                               => $this->has_analytics(),

			// Отклбчает редактор товаров совсем, даже если включена опция Попробуйте новый редактор товаров (product). Если false то новый редактор будет пытаться загрудить но не загрузиться. default:true
			'product-block-editor'                    => $this->has_product_block_editor(),

			// Вместо стандартного списка товаров WordPress (wp-admin/edit.php?post_type=product) используется современный интерфейс с возможностью фильтрации, поиска и сортировки. Не работает, видимо на будущее заложено (product). default:false
			'product-data-views'                      => $this->has_product_block_editor(),

			// Включает экспериметальные блоки wp-content/plugins/woocommerce/src/Blocks/BlockTypesController.php:503. default:false
			'experimental-blocks'                     => $this->has_fse(),

			// Отвечает за шаблон страницы "Скоро открытие" с формой подписки на рассылку (Coming Soon Newsletter Template). Непонятный функционал, похоже заложено на будущее. default:false
			'coming-soon-newsletter-template'         => $this->has_fse(),

			// Перенос купонов, не работает если отключен Маркетинг. default:true
			'coupons'                                 => $this->has_marketing(),

			// Инструмент улучшения процесса первоначальной настройки магазина (onboarding). default:true
			'core-profiler'                           => $this->has_onboarding(),

			//Эта функция предоставляет инструменты для быстрой настройки внешнего вида и функциональности вашего магазина, особенно в процессе первоначальной настройки (onboarding). Похоже что работает только с темами FSE. default:true
			'customize-store'                         => $this->has_onboarding(),

			//Это инструмент, который помогает WooCommerce собирать отзывы от пользователей, пользователям будут показываться запросы на оценку усилий после выполнения определённых задач (onboarding). default:true
			'customer-effort-score-tracks'            => $this->has_onboarding(),

			// Импорт товаров при первоначальной настройке магазина (onboarding). default:true
			'import-products-task'                    => $this->has_onboarding(),

			// Будет доступна возможность создания и управления образцами товаров. Хз как это работает, видимо на будущее заложено. default:true
			'experimental-fashion-sample-products'    => $this->has_onboarding(),

			// Это экспериментальная функция, которая помогает автоматически настраивать параметры доставки для новых магазинов (onboarding). default:true
			'shipping-smart-defaults'                 => $this->has_onboarding(),

			// Интерактивный гид, который помогает новым пользователям разобраться в настройках доставки в WooCommerce (onboarding). default:true
			'shipping-setting-tour'                   => $this->has_onboarding(),

			//Главная страница WooCommerce Admin. Раздел обзор. default:true
			'homescreen'                              => $this->has_homescreen(),

			// Раздел маркетинга. default:true
			'marketing'                               => $this->has_marketing(),

			// Минификация скриптов. Хз как это работает. default:false
			'minified-js'                             => true,

			// Связана с баннером мобильного приложения, который отображается в административной панели WooCommerce. default:true
			'mobile-app-banner'                       => $this->has_onboarding(),

			// Первоначальная настройка магазина (Onboarding). default:true
			'onboarding'                              => $this->has_onboarding(),

			// Первоначальная настройка магазина (Onboarding). default:true
			'onboarding-tasks'                        => $this->has_onboarding(),

			// Экспериментальная функциия, которая предоставляет полную композируемость паттернов (product). default:true
			'pattern-toolkit-full-composability'      => $this->has_product_block_editor(),

			// Модальное окно, которое появляется перед публикацией товара. Работет если включено создание товаров через гутенберг (product). default:false
			'product-pre-publish-modal'               => $this->has_product_block_editor(),

			// Поддержка произвольных полей при создании товара в Гутенберг. Хотя не понял как там это работает (product). default:true
			'product-custom-fields'                   => $this->has_product_block_editor(),

			// Уведомления, которые WooCommerce получает с серверов WooCommerce.com. default:true
			'remote-inbox-notifications'              => $this->has_marketing(),

			// Рекламная хрень. WooCommerce получает список рекомендуемых бесплатных расширений с серверов WooCommerce.com и отображает их в административной панели. default:true
			'remote-free-extensions'                  => $this->has_marketing(),

			// Рекомендации по платёжным шлюзам (payments). default:true
			'payment-gateway-suggestions'             => $this->has_payments(),

			// Интеграция с платформой Printful. Непонятно то ли это метод доставки, то ли какой то серви печать на товарах. default:true
			'printful'                                => false,

			// Непонятно что это, при включении фаталы вываливаются. default:false
			'settings'                                => false,

			// Вроде как баннер об интеграциях с доставками. Скорее всего предназначено для плагина WooCommerce Shipping. default:true
			'shipping-label-banner'                   => false,

			// Управляет доступностью функционала подписок. Скорее всего предназначено для плагина WooCommerce Subscriptions. default:true
			'subscriptions'                           => false,

			// Вроде как  управляет доступностью уведомлений о состоянии магазина, но где конкретно это все видно непонятно. default:true
			'store-alerts'                            => false,

			// Временные уведомления. Скорее всего снеки, типа Настройки сохранены. default:true
			'transient-notices'                       => false,

			// Приветственный баннер для мобильного приложения WooCommerce (Onboarding). default:true
			'woo-mobile-welcome'                      => $this->has_onboarding(),

			// Реклама WooCommerce Payments — встроенного платёжного решения от WooCommerce (payments). default:true
			'wc-pay-promotion'                        => $this->has_payments(),

			// Приветственная страница WooCommerce Payments (payments). default:true
			'wc-pay-welcome-page'                     => $this->has_payments(),

			// Асинхронная загрузка категорий ри редактировании товара. Работает в любом редакторе. Рестом категории загружаются и выводять по типу как в гутенберге (product). default:false
			'async-product-editor-category-field'     => true,

			// Фуннкционал Видимость сайта. default:true
			'launch-your-store'                       => $this->has_launch_your_store(),

			// Судя по всему это включение функционала темплейтов товаров под новый редактор. Толком не работает (product). default:false
			'product-editor-template-system'          => $this->has_product_block_editor(),

			// Функционал позволяет создавать предварительно настроенные шаблоны магазинов. Хз как это работает, скорее всего предназначено под FSE. default:false
			'blueprint'                               => $this->has_fse(),

			// Настройки способов оплаты на реакте (payments). default:true
			'reactify-classic-payments-settings'      => $this->has_payments(),

			// Вроде как инструмента для управления очередями задач (job queues). default:false
			'use-wp-horizon'                          => $this->has_fse(),

			// Функционал предоставляет пошаговый интерфейс (stepper layout) для выбора опций товара (например, атрибутов, вариаций) перед добавлением в корзину. Для блока корзины. default:false
			'add-to-cart-with-options-stepper-layout' => $this->has_fse(),

			// Заменяет стандартную кнопку "Добавить в корзину" на блок Gutenberg. Работает скорее всего в FSE. default:false
			'blockified-add-to-cart'                  => $this->has_fse(),
		];
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
}
