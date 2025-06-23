<?php

namespace Art\BloatTrimmer\Admin;

use Art\BloatTrimmer\Helpers\Condition;
use Art\BloatTrimmer\Helpers\Utils;

class Settings {

	protected Options $wposa;


	protected Utils $utils;


	public ?array $widgets = [];


	protected string $section_id = '';


	protected array $support_plugins;


	protected Condition $condition;


	public function __construct( $wposa, $utils, $condition ) {

		$this->wposa     = $wposa;
		$this->utils     = $utils;
		$this->condition = $condition;

		$this->support_plugins = [
			'woocommerce/woocommerce.php',
			'seo-by-rank-math/rank-math.php',
			'wordpress-seo/wp-seo.php',
		];

		$this->init_hooks();
	}


	public function init_hooks(): void {

		add_action( 'init', [ $this, 'section' ], 110 );
		add_action( 'widgets_init', [ $this, 'set_active_widgets' ], 99 );
	}


	public function section(): void {}


	/**
	 * Получение активных виджетов перед сохранением
	 *
	 * @return void
	 */
	public function set_active_widgets(): void {

		global $wp_widget_factory;
		$this->set_widgets( $wp_widget_factory->widgets );
	}


	/**
	 * @return array|null
	 */
	public function get_widgets(): ?array {

		return $this->widgets;
	}


	/**
	 * @param  array|null $widgets
	 */
	public function set_widgets( ?array $widgets ): void {

		$this->widgets = $widgets;
	}


	/**
	 * @return array[]
	 */
	protected function get_option_widgets(): array {

		$widgets_check = [
			'select_all' => [
				'label'      => 'Выбрать все',
				'class'      => '',
				'attributes' => [
					'data-select-all' => 'remove',
				],
			],
		];

		foreach ( $this->get_widgets() as $key => $widget ) {

			$is_active_widget = is_active_widget( false, false, $widget->id_base, true ) ? ' (активен)' : '';

			$widgets_check[ $key ] = [
				'label'      => $widget->name . $is_active_widget,
				'class'      => 'checkbox-' . $widget->option_name,
				'attributes' => [
					'data-selectable' => 'remove',
				],
			];
		}

		return $widgets_check;
	}


	/**
	 * @return bool
	 */
	protected function is_active_plugins(): bool {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		foreach ( $this->support_plugins as $plugin ) {
			if ( is_plugin_active( $plugin ) ) {
				return true;
			}
		}

		return false;
	}
}
