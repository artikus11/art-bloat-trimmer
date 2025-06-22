<?php

namespace Art\BloatTrimmer\Admin\Sections;

use Art\BloatTrimmer\Admin\Options;
use Art\BloatTrimmer\Admin\Settings;

class Admin extends Settings {

	protected string $section_id = 'admin';


	/**
	 * @return void
	 */
	public function section(): void {

		$this->wposa->add_section(
			[
				'id'        => $this->section_id,
				'title'     => '',
				'title_nav' => 'Очистка админки',
			]
		);

		$this->fields();
	}


	/**
	 * @return void
	 */
	protected function fields(): void {

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'cleanup_dashboard',
				'type'    => 'switch',
				'name'    => 'Отключить виджеты в консоли',
				'default' => 'off',
				'desc'    => 'Удаление виджетов на странице Консоли. Совсем всех, независимо от установленных плагинов.',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'cleanup_admin_bar',
				'type'    => 'switch',
				'name'    => 'Отключить элементы админбара',
				'default' => 'off',
				'desc'    => 'Отключает элементы админбара вверху админки. <br><strong>Внимание!</strong> Отключаются все элементы, независимо от установленных плагинов, кроме разрешенных.',
			]
		);

		if ( 'on' !== Options::get( 'disable_comments', 'general' ) ) {
			$this->wposa->add_field(
				$this->section_id,
				[
					'id'      => 'cleanup_count_comments',
					'type'    => 'switch',
					'name'    => 'Отключить пересчет количества комментариев',
					'default' => 'off',
					'desc'    => 'Отключает пересчет количества комментариев.',
				]
			);
		}

		if ( 'on' !== Options::get( 'disable_comments', 'general' ) ) {
			$this->wposa->add_field(
				$this->section_id,
				[
					'id'      => 'delete_intermediate_image_sizes',
					'type'    => 'switch',
					'name'    => 'Удалить лишние размеры миниатюр',
					'default' => 'off',
					'desc'    => 'Удаляет штатные размеры миниатюр: <code>medium_large</code>, <code>1536x1536</code>, <code>2048x2048</code>',
				]
			);
		}

		if ( ! wp_use_widgets_block_editor() ) {
			$this->wposa->add_field(
				$this->section_id,
				[
					'id'      => 'cleanup_widgets',
					'type'    => 'multicheck',
					'name'    => 'Отключить виджеты',
					'class'   => 'multicheck-inputs',
					'options' => $this->get_option_widgets(),
					'desc'    => 'Удаление всех виджетов, кроме не выбранных. Выберите виджет который требуется отключить',
				]
			);
		}
	}


	protected function default_widgets(): array {

		$allowed_widgets = [
			'WP_Widget_Text',
			'WP_Widget_Custom_HTML',
			'WP_Widget_Block',
		];

		$default_widgets = [];

		foreach ( $this->get_option_widgets() as $key => $widget ) {
			if ( ! in_array( $key, $allowed_widgets, true ) ) {
				$default_widgets[ $key ] = $key;
			}
		}

		return $default_widgets;
	}
}
