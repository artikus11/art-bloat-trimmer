<?php
/**
 * Class Integrations
 *
 * @since   2.0.0
 * @package art-bloat-trimmer/src/Cleanup_Plugins/Woocommerce
 */

namespace Art\BloatTrimmer\Cleanup_Plugins\Woocommerce;

use Art\BloatTrimmer\Helpers\Utils;

class Integrations {

	protected Utils $utils;


	public function init_hooks(): void {

		$this->initialize_compatibility();
	}


	protected function initialize_compatibility(): void {

		add_action( 'before_woocommerce_init', function () {

			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
					'custom_order_tables',
					Utils::get_plugin_basename(),
					true
				);
			}
		} );
	}
}
