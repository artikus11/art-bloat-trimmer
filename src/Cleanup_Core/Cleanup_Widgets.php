<?php
/**
 * Class Cleanup_Widgets
 *
 * @since   2.0.0
 * @package art-bloat-trimmer
 */

namespace Art\BloatTrimmer\Cleanup_Core;

use Art\BloatTrimmer\Admin\Options;

class Cleanup_Widgets {

	public function init_hooks(): void {

		add_action( 'widgets_init', [ $this, 'remove_widgets' ], 100 );
	}


	public function remove_widgets() {

		$widgets = Options::get( 'cleanup_widgets', 'admin' );

		if ( $widgets ) {
			foreach ( $widgets as $widget_key => $widget ) {

				if ( 'select_all' === $widget_key ) {
					continue;
				}

				unregister_widget( $widget );
			}
		}
	}
}
