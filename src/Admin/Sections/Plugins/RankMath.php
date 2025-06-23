<?php

namespace Art\BloatTrimmer\Admin\Sections\Plugins;

use Art\BloatTrimmer\Admin\Sections\Plugins;

class RankMath extends Plugins {

	protected function fields(): void {

		if ( ! $this->condition->is_rank_math_active() ) {
			return;
		}

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'   => 'rank_math_heading',
				'type' => 'title',
				'name' => 'RankMath',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'rank_math_disable_ads',
				'type'    => 'switch',
				'name'    => 'Отключить рекламные блоки',
				'default' => 'off',
				'desc'    => 'Отключает рекламу платной версии',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'rank_math_auto_update_send_email',
				'type'    => 'switch',
				'name'    => 'Отключить отправку писем',
				'default' => 'off',
				'desc'    => 'Отключает отправку присем при автоматическим обновлении плагина',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'rank_math_disable_admin_footer_text',
				'type'    => 'switch',
				'name'    => 'Отключить сообщение в подвале',
				'default' => 'off',
				'desc'    => 'Отключает вывод сообщения о плагине в подвале',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'rank_math_disable_comments',
				'type'    => 'switch',
				'name'    => 'Отключить комментарии на фронте',
				'default' => 'off',
				'desc'    => 'Отключение комментариев плагина на фронте',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'rank_math_disable_columns',
				'type'    => 'switch',
				'name'    => 'Отключить колонки',
				'default' => 'off',
				'desc'    => 'Отключает колонку SEO детали в литинге записей',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'rank_math_disable_filter',
				'type'    => 'switch',
				'name'    => 'Отключить фильтр',
				'default' => 'off',
				'desc'    => 'Отключает в литинге записей фильтр по SEO оценке',
			]
		);
	}
}
