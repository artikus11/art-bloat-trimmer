<?php

namespace Art\BloatTrimmer\CLI\Commands;

use Art\BloatTrimmer\Cleanup_Plugins\Woocommerce\Utilities;
use Art\BloatTrimmer\CLI\Command_Interface;
use Art\BloatTrimmer\Utils;
use Exception;
use WP_CLI;
use function WP_CLI\Utils\make_progress_bar;

class Flush_Command implements Command_Interface {

	protected const TYPES = [ 'actions', 'logs', 'notes', 'all' ];


	protected const METHOD_MAP = [
		'actions' => [
			'select' => 'select_scheduler_actions',
			'clear'  => 'clear_scheduler_actions',
			'count'  => 'get_count_select_scheduler_actions',
			'label'  => 'задач',
		],
		'logs'    => [
			'select'     => 'select_scheduler_actions_logs',
			'clear'      => 'clear_scheduler_actions_logs',
			'count'      => 'get_count_select_scheduler_actions_logs',
			'label'      => 'логов',
			'batch_size' => 5000,
		],
		'notes'   => [
			'select' => 'select_order_notes',
			'clear'  => 'clear_order_notes',
			'count'  => 'get_count_select_order_notes',
			'label'  => 'заметок',
		],
	];


	protected const MESSAGE_TEMPLATES = [
		'select' => 'Получение списка %s',
		'clear'  => 'Удаление %s',
	];


	protected string $type;


	protected bool $is_dry_run;


	protected int $batch_size;


	public static function get_command_info(): array {

		return [
			'command'       => 'flush --type=<type> [--select] [--batch=<size>]',
			'params'        => '',
			'description'   => 'Unified cleanup tool',
			'command_name'  => 'flush',
			'command_param' => '--type=<type> [--select] [--batch=<size>]',
		];
	}


	public static function is_available(): bool {

		return Utils::is_woocommerce_active();
	}


	public static function get_dependencies(): array {

		return [
			'plugins' => [
				[
					'name' => 'WooCommerce',
					'path' => 'woocommerce/woocommerce.php',
				],
			],
		];
	}


	/**
	 * Unified flush command
	 *
	 * ## OPTIONS
	 *
	 * --type=<type>
	 * : What to flush. Possible values: actions, logs, notes, all
	 * ---
	 * default: all
	 * options:
	 *   - actions
	 *   - logs
	 *   - notes
	 *   - all
	 *
	 * [--select]
	 * : Dry-run mode (show what will be deleted)
	 * ---
	 * default: false
	 *
	 * [--batch=<size>]
	 * : Batch size for processing (0 for no batching)
	 * ---
	 * default: 0
	 *
	 * ## EXAMPLES
	 *
	 *     # Flush everything
	 *     $ wp abt flush
	 *     Success: Flush completed.
	 *
	 *     # Preview scheduled actions to be deleted
	 *     $ wp abt flush --type=actions --select
	 *
	 * @when after_wp_load
	 * @throws \WP_CLI\ExitException
	 */
	public function __invoke( array $args, array $assoc_args ): void {

		$this->type = $assoc_args['type'] ?? 'all';
		$this->is_dry_run = isset( $assoc_args['select'] ) && $assoc_args['select'];
		$this->batch_size = isset( $assoc_args['batch'] ) ? (int) $assoc_args['batch'] : 0;

		if ( ! in_array( $this->type, self::TYPES, true ) ) {
			WP_CLI::error( "Неизвестный тип: $this->type" );
		}

		WP_CLI::line(
			sprintf( "Starting %s %s operation...",
				$this->is_dry_run ? 'count' : 'flush',
				$this->type )
		);

		$result = $this->type === 'all'
			? $this->process_all_types(   )
			: $this->process_single_type( );

		WP_CLI::success( $result );
	}


	private function process_all_types( ): string {

		foreach ( self::METHOD_MAP as $type => $methods ) {
			try {
				$result = $this->is_dry_run
					? $this->process_select( $methods['select'], $methods['label'] )
					: $this->process_clear( $type, $methods['clear'], $methods['label'],$methods['batch_size'] ?? null );
				WP_CLI::line( $result );
			} catch ( Exception $e ) {
				WP_CLI::warning( "Ошибка при обработке типа '$type': {$e->getMessage()}" );
			}
		}

		return $this->is_dry_run ? 'Данные успешно получены' : 'Очистка успешно выполнена';
	}


	private function process_single_type( ): string {

		$methods           = self::METHOD_MAP[ $this->type ];
		$batch_size_config = $methods['batch_size'] ?? null;

		return $this->is_dry_run
			? $this->process_select( $methods['select'], $methods['label'] )
			: $this->process_clear( $this->type, $methods['clear'], $methods['label'], $batch_size_config );
	}


	private function process_select( string $method, string $label ): string {

		WP_CLI::line( sprintf( self::MESSAGE_TEMPLATES['select'], $label ) );

		return Utilities::$method();
	}


	private function process_clear( string $type, string $method, string $label,  ?int $default_batch_size = null ): string {

		$message = sprintf( self::MESSAGE_TEMPLATES['clear'], $label );

		WP_CLI::line( $message );

		$actual_batch_size = $this->batch_size === 0 ? 0 : ( $this->batch_size ? : $default_batch_size );

		$total = Utilities::{self::METHOD_MAP[ $type ]['count']}();

		if ( $total === 0 ) {
			return 'Ничего не найдено.';
		}

		if ( $actual_batch_size === 0 || $total <= $actual_batch_size ) {
			WP_CLI::line( "Обработка всех $total элементов..." );

			return Utilities::$method( 0 );
		}

		return $this->process_batched_clear( $method, $message, $total, $actual_batch_size );
	}


	private function process_batched_clear( string $method, string $message, int $total, int $batch_size ): string {

		WP_CLI::line( "Обработка $total элементов пакетами по $batch_size..." );

		$progress       = make_progress_bar( $message, $total );
		$processed      = 0;
		$result_message = '';

		while ( $processed < $total ) {
			$batch_result = Utilities::$method( $batch_size );
			$processed    += $batch_size;
			$progress->tick( $batch_size );

			if ( $processed > $total ) {
				$progress->tick( $total - ( $processed - $batch_size ) );
				$processed = $total;
			}

			$result_message = $batch_result;
		}

		$progress->finish();

		wp_cache_flush();

		return $result_message;
	}
}
