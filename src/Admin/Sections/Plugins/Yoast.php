<?php

namespace Art\BloatTrimmer\Admin\Sections\Plugins;

use Art\BloatTrimmer\Admin\Sections\Plugins;

class Yoast extends Plugins {

	protected string $field_prefix = 'yoast_';


	protected function fields(): void {

		if ( ! $this->condition->is_yoast_active() ) {
			return;
		}

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'   => $this->field_prefix . 'heading',
				'type' => 'title',
				'name' => 'Yoast SEO',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => $this->field_prefix . 'disable_ads',
				'type'    => 'switch',
				'name'    => 'Отключить рекламные блоки',
				'default' => 'off',
				'desc'    => 'Отключает рекламу платной версии',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => $this->field_prefix . 'disable_submenu_pages',
				'type'    => 'switch',
				'name'    => 'Отключить подменю',
				'default' => 'off',
				'desc'    => 'Отключает страницы подменю: Академия, Обновления, Тренажеры, Поддержка, Интеграции, Редиректы',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => $this->field_prefix . 'remove_head_comment',
				'type'    => 'switch',
				'name'    => 'Удалить комментарий из секции <head>',
				'default' => 'off',
				'desc'    => sprintf(
					'Удаляет комментарий вида <code>%s</code> в секции %s',
					esc_html( '<!-- This site is optimized with the Yoast SEO plugin -->' ),
					esc_html( '<head>' )
				),
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => $this->field_prefix . 'disable_filters_columns',
				'type'    => 'switch',
				'name'    => 'Отключить фильтры и колонки',
				'default' => 'off',
				'desc'    => 'Отключает колонки SEO детали и фильтры по SEO оценке в литинге записей',
			]
		);
	}
}
