<?php
/**
 * Plugin Name: Art Bloat Trimmer
 * Plugin URI: wpruse.ru
 * Text Domain: art-bloat-trimmer
 * Domain Path: /languages
 * Description: Cleans WP code from unnecessary garbage and more!
 * Version: 2.2.0
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

use Art\BloatTrimmer\Main;
use Art\BloatTrimmer\Uninstall;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ABT_PLUGIN_DIR    = __DIR__;
const ABT_PLUGIN_AFILE  = __FILE__;
const ABT_PLUGIN_VER    = '2.2.0';
const ABT_PLUGIN_NAME   = 'Bloat Trimmer';
const ABT_PLUGIN_SLUG   = 'art-bloat-trimmer';
const ABT_PLUGIN_PREFIX = 'abt';

define( 'ABT_PLUGIN_URI', untrailingslashit( plugin_dir_url( ABT_PLUGIN_AFILE ) ) );
define( 'ABT_PLUGIN_FILE', plugin_basename( __FILE__ ) );

require ABT_PLUGIN_DIR . '/vendor/autoload.php';

register_uninstall_hook( __FILE__, [ Uninstall::class, 'init' ] );

( new Main() )->init();
