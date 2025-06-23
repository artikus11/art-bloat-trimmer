<?php

namespace Art\BloatTrimmer\Admin\Sections\Plugins;

use Art\BloatTrimmer\Admin\Sections\Plugins;

class Woocommerce extends Plugins {

	protected function fields(): void {

		if ( ! $this->condition->is_woocommerce_active() ) {
			return;
		}

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'   => 'woocommerce_heading',
				'type' => 'title',
				'name' => 'WooCommerce',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'                => 'woocommerce_disable_feature',
				'type'              => 'switch',
				'name'              => 'Отключить WooCommerce Admin',
				'default'           => 'off',
				'desc'              => 'Будут отлючены все новые функции и разделы: Маркетинг, Аналитика, Бординг и тд относящиеся к WooCommerce Admin',
				'custom_attributes' => [
					'data-disable-feature-all' => 'enable',
				],
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'      => 'woocommerce_dequeue',
				'type'    => 'switch',
				'name'    => 'Переключение стилей и скриптов',
				'default' => 'off',
				'desc'    => 'Подключает стили и скрипты только там где они требуются. <br><strong>Внимание!</strong> Данный функционал может ломать отображение или работу элементов и страниц. Все зависит от используемой темы и активных плагинов. Если такое проиходит отключите эту настройку',
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'                => 'woocommerce_disable_analytics',
				'type'              => 'switch',
				'name'              => 'Отключить Аналитику',
				'default'           => 'off',
				'desc'              => 'Отключение раздела Аналитика',
				'custom_attributes' => [
					'data-disable-feature' => 'enable',
				],
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'                => 'woocommerce_disable_activity_panels',
				'type'              => 'switch',
				'name'              => 'Отключить панель активности',
				'default'           => 'off',
				'desc'              => 'Отключение показ активности сверху справа',
				'custom_attributes' => [
					'data-disable-feature' => 'enable',
				],
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'                => 'woocommerce_disable_marketing',
				'type'              => 'switch',
				'name'              => 'Отключить Маркетинг',
				'default'           => 'off',
				'desc'              => 'Отключение раздела Маркетинг и связанного с ним функционала',
				'custom_attributes' => [
					'data-disable-feature' => 'enable',
				],
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'                => 'woocommerce_disable_launch_your_store',
				'type'              => 'switch',
				'name'              => 'Отключить Видимость сайта',
				'default'           => 'off',
				'desc'              => 'Отключение раздела Видимость сайта (Закрывает для пользователей страницы магазина и показывает на странице Скоро откроемся)',
				'custom_attributes' => [
					'data-disable-feature' => 'enable',
				],
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'                => 'woocommerce_disable_product_editor',
				'type'              => 'switch',
				'name'              => 'Отключить редактор товаров',
				'default'           => 'off',
				'desc'              => 'Отключение функционала связанный с новым редактором товаров. При отключении редактор работать не будет, даже при включении опции "Попробуйте новый редактор товаров"',
				'custom_attributes' => [
					'data-disable-feature' => 'enable',
				],
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'                => 'woocommerce_disable_onboarding',
				'type'              => 'switch',
				'name'              => 'Отключить первичную настройку',
				'default'           => 'off',
				'desc'              => 'Отключение функционала первичной настройки магазина',
				'custom_attributes' => [
					'data-disable-feature' => 'enable',
				],
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'                => 'woocommerce_disable_homescreen',
				'type'              => 'switch',
				'name'              => 'Отключить раздел Обзор',
				'default'           => 'off',
				'desc'              => 'Отключение отдельной страницы Обзор (Homescreen)',
				'custom_attributes' => [
					'data-disable-feature' => 'enable',
				],
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'                => 'woocommerce_disable_payments',
				'type'              => 'switch',
				'name'              => 'Отключить улучшения методов оплаты',
				'default'           => 'off',
				'desc'              => 'Отключение функционала связанного с платежными шлюзами: рекламные рекомендации, настройки на реакт и тд',
				'custom_attributes' => [
					'data-disable-feature' => 'enable',
				],
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'                => 'woocommerce_disable_fse',
				'type'              => 'switch',
				'name'              => 'Отключить FSE',
				'default'           => 'off',
				'desc'              => 'Отключение функционала связанного с FSE (Full Site Edition)',
				'custom_attributes' => [
					'data-disable-feature' => 'enable',
				],
			]
		);

		$this->wposa->add_field(
			$this->section_id,
			[
				'id'                => 'woocommerce_disable_admin_menu',
				'type'              => 'switch',
				'name'              => 'Отключить страницы подменю',
				'default'           => 'off',
				'desc'              => 'Отключает страницы подменю Расширения, Отчеты и др',
				'custom_attributes' => [
					'data-disable-feature' => 'enable',
				],
			]
		);
	}
}
