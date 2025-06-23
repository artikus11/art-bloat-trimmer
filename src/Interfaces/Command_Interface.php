<?php

namespace Art\BloatTrimmer\Interfaces;

interface Command_Interface {

	/**
	 * @return array{
	 *     command: string,
	 *     description: string,
	 *     params?: string
	 *     command_name: string
	 *     command_param?: string
	 * }
	 */
	public static function get_command_info(): array;


	public static function is_available(): bool;


	public static function get_dependencies(): array;
}
