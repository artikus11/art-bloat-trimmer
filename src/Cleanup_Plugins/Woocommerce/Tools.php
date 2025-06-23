<?php
/**
 * Class Tools
 *
 * @since   2.0.0
 * @package art-bloat-trimmer/src/Cleanup_Plugins/Woocommerce
 */

namespace Art\BloatTrimmer\Cleanup_Plugins\Woocommerce;

use Art\BloatTrimmer\Interfaces\Init_Hooks_Interface;

class Tools implements Init_Hooks_Interface {

	public function init_hooks(): void {

		add_filter( 'woocommerce_debug_tools', [ $this, 'add_tools' ], PHP_INT_MAX );
	}


	public function add_tools( array $tools ): array {

		$tools['clear_scheduler_actions'] = [
			'name'     => 'Очистить все выполненые крон задачи (статусы: canceled, complete, failed)',
			'desc'     => 'Удаляет все выполненные, неудавшиеся и отменныне крон задачи',
			'button'   => 'Очистить',
			'callback' => [ Utilities::class, 'clear_scheduler_actions' ],
		];

		$tools['clear_scheduler_actions_logs'] = [
			'name'     => 'Очистить все логи крон задач',
			'desc'     => 'Очищает таблицу журнала запланированных задач',
			'button'   => 'Очистить',
			'callback' => [ Utilities::class, 'clear_scheduler_actions_logs' ],
		];

		$tools['clear_order_notes'] = [
			'name'     => 'Очистить уведомления в заказе',
			'desc'     => 'Очищает комментарии заказов в статусе Выполнено и Отменено',
			'button'   => 'Очистить',
			'callback' => [ Utilities::class, 'clear_order_notes' ],
		];

		return $tools;
	}
}
