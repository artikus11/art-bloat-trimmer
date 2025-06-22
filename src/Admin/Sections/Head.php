<?php

namespace Art\BloatTrimmer\Admin\Sections;

use Art\BloatTrimmer\Admin\Settings;

class Head extends Settings {

	protected string $section_id = 'head';


	/**
	 * @return void
	 */
	public function section(): void {

		$this->wposa->add_section(
			[
				'id'        => $this->section_id,
				'title'     => '',
				'title_nav' => 'Очистка &lt;head&gt;',
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
				'id'      => 'cleanup_head_generator',
				'type'    => 'switch',
				'name'    => 'Отключение версии ядра',
				'default' => 'off',
				'desc'    => 'Удаляет метатег вида <code>&lt;meta name="generator" content="WordPress 6.4.3" /&gt;</code> из секции <code>&lt;head&gt;</code>',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'cleanup_head_shortlink',
				'type'    => 'switch',
				'name'    => 'Отключение короткой ссылки',
				'default' => 'off',
				'desc'    => 'Удаляет короткую ссылку вида <code>&lt;link rel="shortlink" href="https://site.loc/?p=51159" /&gt;</code> из секции <code>&lt;head&gt;</code>',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'cleanup_head_wp_json',
				'type'    => 'switch',
				'name'    => 'Отключение ссылок на REST API',
				'default' => 'off',
				'desc'    => 'Удаляет ссылки вида <code>&lt;link rel="alternate" type="text/xml+oembed" href="https://site.loc/wp-json/oembed/..." &gt;</code> из секции <code>&lt;head&gt;</code>',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'cleanup_head_rsd_link',
				'type'    => 'switch',
				'name'    => 'Отключение RSD ссылки',
				'default' => 'off',
				'desc'    => 'Удаляет ссылки вида <code>&lt;link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://site.loc/xmlrpc.php?rsd" /&gt;</code> из секции <code>&lt;head&gt;</code>',
			]
		);
	}
}
