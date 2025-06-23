<?php

namespace Art\BloatTrimmer\Admin\Sections;

use Art\BloatTrimmer\Admin\Settings;

class Plugins extends Settings {

	protected string $section_id = 'plugins';


	protected string $field_prefix;


	private static bool $section_called = false;


	/**
	 * @return void
	 */
	public function section(): void {

		if ( ! self::$section_called ) {
			self::$section_called = true;

			$this->wposa->add_section(
				[
					'id'        => $this->section_id,
					'title'     => '',
					'title_nav' => 'Плагины',
				]
			);
		}

		if ( ! empty( $this->is_active_plugins() ) ) {
			$this->fields();
		} else {
			$this->message();
		}
	}


	protected function message() {

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'   => 'plugins_message',
				'type' => 'title',
				'name' => 'Нет активных плагинов',
				'desc' => 'На текущий момент плагин поддержваются плагины: WooCommerce, SEO Rank Math, Yoast SEO. Настройки появяться автоматически при активации поддерживаемых плагинов.',
			]
		);
	}


	/**
	 * @return void
	 */
	protected function fields(): void {}
}
