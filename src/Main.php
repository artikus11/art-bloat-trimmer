<?php

namespace Art\BloatTrimmer;

use Art\BloatTrimmer\Admin\Options;
use Art\BloatTrimmer\CLI\Command_Manager;
use Art\BloatTrimmer\Helpers\Condition;
use Art\BloatTrimmer\Helpers\Utils;

class Main {

	protected Utils $utils;


	protected Options $options;


	protected Loader_Manager $module_loader;


	protected Command_Manager $cli_manager;


	protected Condition $condition;


	public function __construct() {

		$this->utils         = new Utils();
		$this->condition     = new Condition();
		$this->options       = new Options( $this->utils, $this->condition );
		$this->module_loader = new Loader_Manager( $this->options, $this->utils, $this->condition );
		$this->cli_manager   = new Command_Manager();
	}


	public function init(): void {

		add_action( 'plugins_loaded', [ $this, 'initialize' ], - PHP_INT_MAX );

		add_filter( 'plugin_action_links_' . $this->utils->get_plugin_basename(), [ $this, 'add_plugin_action_links' ], 10, 1 );
	}


	public function initialize(): void {

		$this->module_loader->initialize();
		$this->cli_manager->initialize();

		$this->initialize_updater();
	}


	public function add_plugin_action_links( array $links ): array {

		$plugin_links = [
			'settings' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( admin_url( 'options-general.php?page=art-bloat-trimmer' ) ),
				'Настройки'
			),
		];

		return array_merge( $plugin_links, $links );
	}


	protected function initialize_updater(): void {

		$updater = new Updater( $this->utils->get_plugin_file() );
		$updater->set_repository( $this->utils->get_plugin_slug() );
		$updater->set_username( 'artikus11' );
		$updater->init();
	}
}
