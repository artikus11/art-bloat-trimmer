<?php
/**
 * Plugin main file.
 */

use tad\FunctionMocker\FunctionMocker;

define( 'PLUGIN_MAIN_FILE', realpath( __DIR__ . '/../art-bloat-trimmer.php' ) );

define( 'PLUGIN_PATH', realpath( dirname( PLUGIN_MAIN_FILE ) ) );

require_once PLUGIN_PATH . '/vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	/**
	 * WordPress ABSPATH.
	 */
	define( 'ABSPATH', PLUGIN_PATH . '/../../../' );
}

FunctionMocker::init(
	[
		'blacklist'             => [
			realpath( PLUGIN_PATH ),
		],
		'whitelist'             => [
			realpath( PLUGIN_PATH . '/art-bloat-trimmer.php' ),
			realpath( PLUGIN_PATH . '/src' ),
		],
		'redefinable-internals' => [
			'class_exists',
			'define',
			'defined',
			'constant',
			'filter_input',
			'function_exists',
			'ini_get',
			'mb_strtolower',
			'phpversion',
			'realpath',
			'time',
			'error_log',
			'rename',
		],
	]
);

WP_Mock::bootstrap();
