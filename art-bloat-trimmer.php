<?php
/**
 * Plugin Name: Art Bloat Trimmer
 * Plugin URI: wpruse.ru
 * Text Domain: art-bloat-trimmer
 * Domain Path: /languages
 * Description: Cleans WP code from unnecessary garbage and more!
 * Version: 2.1.1
 * Author: Artem Abramovich
 * Author URI: https://wpruse.ru/
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * WC requires at least: 5.2.0
 * WC tested up to: 6.1
 *
 * RequiresWP: 5.5
 * RequiresPHP: 7.4
 *
 * Copyright Artem Abramovich
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ABT_PLUGIN_DIR   = __DIR__;
const ABT_PLUGIN_AFILE = __FILE__;
const ABT_PLUGIN_VER   = '2.1.1';
const ABT_PLUGIN_NAME  = 'Bloat Trimmer';
const ABT_PLUGIN_SLUG  = 'art-bloat-trimmer';
const ABT_PLUGIN_PREFIX  = 'abt';

define( 'ABT_PLUGIN_URI', untrailingslashit( plugin_dir_url( ABT_PLUGIN_AFILE ) ) );
define( 'ABT_PLUGIN_FILE', plugin_basename( __FILE__ ) );

require ABT_PLUGIN_DIR . '/vendor/autoload.php';

( new Art\BloatTrimmer\Main() )->init();
