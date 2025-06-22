<?php

namespace Art\BloatTrimmer\Admin\Sections;

use Art\BloatTrimmer\Admin\Settings;

class General extends Settings {

	protected string $section_id = 'general';


	/**
	 * @return void
	 */
	public function section(): void {

		$this->wposa->add_section(
			[
				'id'        => $this->section_id,
				'title'     => '',
				'title_nav' => 'Основные',
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
				'id'      => 'disable_aggressive_updates',
				'type'    => 'switch',
				'name'    => 'Отключение агрессивных обновлений',
				'default' => 'off',
				'desc'    => 'Проверки новых версий при генерации страницы PHP отправляет HTTP запрос, а точнее 3 запроса: ядро, темы, плагины. Если есть платные плагины, то на каждый плагин обычно еще один свой запрос. При HTTP запросе в PHP генерация страницы зависает пока каждый запрос не получит результат, а на каждый запрос уходит в среднем 0,3 - 1 секунда. Вот и получается, что страница виснет на 2-4 секунды. <a href="https://wp-kama.ru/id_8514/uskoryaem-adminku-wordpress-otklyuchaem-proverki-obnovlenij.html" target="_blank">Подробнее</a>',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'disable_emoji',
				'type'    => 'switch',
				'name'    => 'Отключение Emoji',
				'default' => 'off',
				'desc'    => 'C версии 4.2. в wordPress появились эти самые Emoji - смайлики, эмоции. Если не используются на сайте, то можно выключить',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'disable_feed',
				'type'    => 'switch',
				'name'    => 'Отключение RSS Feed',
				'default' => 'off',
				'desc'    => 'Отключает все каналы RSS/Atom/RDF на вашем сайте и ставит редирект со всех RSS-лент.',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'disable_embeds',
				'type'    => 'switch',
				'name'    => 'Отключение Embeds',
				'default' => 'off',
				'desc'    => 'Отключает возможность вставки ссылок с контенте с других сайтов',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'disable_xml',
				'type'    => 'switch',
				'name'    => 'Отключение XML RPC',
				'default' => 'off',
				'desc'    => 'Отключает XML RPC',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'disable_comments',
				'type'    => 'switch',
				'name'    => 'Отключение комментариев',
				'default' => 'off',
				'desc'    => '<strong>Вниманние! </strong>Полностью отключает камментарии на сайте, в том числе и отзывы Woocommerce, если они активны',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'autoremove_attachments',
				'type'    => 'switch',
				'name'    => 'Автоматическое удаление вложений',
				'default' => 'off',
				'desc'    => 'Включает автоматическое удаление прикрепленных вложений при удалении записи',
			]
		);
	}
}
