<?php

namespace Art\BloatTrimmer\CLI\Commands;

use Art\BloatTrimmer\CLI\Command_Manager;
use WP_CLI;

class Root_Command {

	protected Command_Manager $command_manager;


	public function __construct() {

		$this->command_manager = new Command_Manager();
	}


	/**
	 * @throws \WP_CLI\ExitException
	 */
	public function __invoke( $args, $assoc_args ): void {

		if ( empty( $args ) ) {
			$this->list_commands();

			return;
		}

		$this->subcommands( $args, $assoc_args );
	}


	public function list_commands(): void {

		WP_CLI::line( 'Welcome to Art Bloat Trimmer CLI!' );

		$this->show_available_commands();
		$this->show_unavailable_commands();
	}


	/**
	 * @param $args
	 * @param $assoc_args
	 *
	 * @return void
	 * @throws \WP_CLI\ExitException
	 */
	protected function subcommands( $args, $assoc_args ): void {

		$subcommand = array_shift( $args );

		$command = $this->command_manager->get_command( $subcommand );

		if ( ! $command ) {
			WP_CLI::error( "Неизвестная подкоманда: <$subcommand>" );
		}

		$instance = new $command['class']();
		$instance->__invoke( $args, $assoc_args );
	}


	/**
	 * Показывает таблицу доступных команд
	 */
	protected function show_available_commands(): void {

		$available = array_filter(
			$this->command_manager->get_all_commands(),
			fn( $cmd ) => $cmd['available']
		);

		if ( ! empty( $available ) ) {
			WP_CLI::line( "\nДоступные команды:" );
			WP_CLI\Utils\format_items(
				'table',
				$available,
				[ 'command', 'description' ]
			);
		}
	}


	/**
	 * Показывает список недоступных команд с причинами
	 */
	protected function show_unavailable_commands(): void {

		$unavailable = array_filter(
			$this->command_manager->get_all_commands(),
			fn( $cmd ) => ! $cmd['available']
		);

		if ( ! empty( $unavailable ) ) {
			WP_CLI::line( "\nНедоступные команды:" );
			foreach ( $unavailable as $cmd ) {
				WP_CLI::line( sprintf(
					"  %-20s %s",
					$cmd['command'],
					$cmd['reason']
				) );
			}
		}
	}
}
