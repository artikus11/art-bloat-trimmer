<?php

namespace Art\BloatTrimmer\Cleanup_Plugins\Woocommerce;

class Enable_Gutenberg {

	public function init_hooks(): void {

		add_filter( 'use_block_editor_for_post_type', [ $this, 'enable_rest_for_product' ], 10, 2 );
		add_filter( 'woocommerce_taxonomy_args_product_cat', [ $this, 'show_in_rest_for_product' ], 10, 1 );
		add_filter( 'woocommerce_taxonomy_args_product_tag', [ $this, 'show_in_rest_for_product' ], 10, 1 );
		add_filter( 'woocommerce_register_post_type_product', [ $this, 'show_in_rest_for_product' ], 10, 1 );
	}


	public function enable_rest_for_product( $can_edit, $post_type ) {

		if ( 'product' === $post_type ) {
			$can_edit = true;
		}

		return $can_edit;
	}


	public function show_in_rest_for_product( $args ) {

		$args['show_in_rest'] = true;

		return $args;
	}
}
