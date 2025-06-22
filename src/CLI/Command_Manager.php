<?php

namespace Art\BloatTrimmer\CLI;

use Art\BloatTrimmer\CLI\Commands\Root_Command;

use Exception;
use WP_CLI;

class Command_Manager {

	protected array $commands = [];


	public function __construct() {

		$this->scan_commands();
	}


	public function initialize(): void {

		if ( ! defined( 'WP_CLI' ) || ! constant( 'WP_CLI' ) ) {
			return;
		}

		try {
			WP_CLI::add_command( 'abt', Root_Command::class );
		} catch ( Exception $e ) {
			WP_CLI::warning( 'CLI command registration failed: ' . $e->getMessage() );
		}
	}


	protected function scan_commands(): void {

		$commands_dir = __DIR__ . '/Commands';
		$files        = glob( $commands_dir . '/*.php' );

		foreach ( $files as $file ) {

			$class_name = __NAMESPACE__ . '\Commands\\' . basename( $file, '.php' );

			if ( ! class_exists( $class_name ) ) {
				continue;
			}

			if ( ! in_array( Command_Interface::class, class_implements( $class_name ), true ) ) {
				continue;
			}

			$this->commands[] = [
				'class'        => $class_name,
				'info'         => $class_name::get_command_info(),
				'is_available' => $class_name::is_available(),
			];
		}
	}


	public function get_command( string $command_name ): ?array {

		foreach ( $this->commands as $command ) {
			if ( $command['info']['command_name'] === $command_name ) {
				return $command;
			}
		}

		return null;
	}


	public function get_all_commands(): array {

		return array_map( function ( $cmd ) {

			return [
				'command'     => $cmd['info']['command'],
				'description' => $cmd['info']['description'],
				'available'   => $cmd['is_available'],
				'reason'      => $cmd['is_available'] ? '' : $this->get_unavailable_reason( $cmd['class'] ),
			];
		}, $this->commands );
	}


	protected function get_unavailable_reason( string $class_name ): string {

		if ( method_exists( $class_name, 'get_dependencies' ) ) {
			$deps         = $class_name::get_dependencies();
			$deps_plugins = wp_list_pluck( $deps['plugins'], 'name' );

			if ( isset( $deps['plugins'] ) ) {
				return 'Требуются плагины: ' . implode( ', ', $deps_plugins );
			}
		}

		return 'Требуемые зависимости не установлены';
	}
}
