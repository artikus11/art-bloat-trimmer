<?php
/**
 * Class Dequeue
 *
 * @since   2.1.1
 * @package art-bloat-trimmer/src/Cleanup_Plugins/Woocommerce
 */

namespace Art\BloatTrimmer\Cleanup_Plugins\Woocommerce;

use Art\BloatTrimmer\Interfaces\Init_Hooks_Interface;
use WP_Post;

class Dequeue implements Init_Hooks_Interface {

	private ?WP_Post $current_post;


	private array $enqueues = [
		'styles'  => [
			'woocommerce-inline',
			'photoswipe',
			'photoswipe-default-skin',
			'select2',
			'woocommerce_prettyPhoto_css',
			'woocommerce-layout',
			'woocommerce-smallscreen',
			'woocommerce-general',
			'wc-blocks-vendors-style',
			'wc-blocks-style',
		],
		'scripts' => [
			'flexslider',
			'js-cookie',
			'jquery-blockui',
			'jquery-cookie',
			'jquery-payment',
			'photoswipe',
			'photoswipe-ui-default',
			'prettyPhoto',
			'prettyPhoto-init',
			'select2',
			'selectWoo',
			'wc-address-i18n',
			'wc-add-payment-method',
			'wc-cart',
			'wc-cart-fragments',
			'wc-checkout',
			'wc-country-select',
			'wc-credit-card-form',
			'wc-add-to-cart',
			'wc-add-to-cart-variation',
			'wc-geolocation',
			'wc-lost-password',
			'wc-password-strength-meter',
			'wc-single-product',
			'woocommerce',
			'zoom',
			'wc-blocks-middleware',
			'wc-blocks',
			'wc-blocks-registry',
			'wc-vendors',
			'wc-shared-context',
			'wc-shared-hocs',
			'wc-price-format',
			'wc-active-filters-block-frontend',
			'wc-stock-filter-block-frontend',
			'wc-attribute-filter-block-frontend',
			'wc-price-filter-block-frontend',
			'wc-reviews-block-frontend',
			'wc-all-products-block-frontend',
		],
	];


	public function __construct() {

		global $post;
		$this->current_post = $post;
	}


	public function init_hooks(): void {

		add_action( 'wp_enqueue_scripts', [ $this, 'cleanup_assets' ], PHP_INT_MAX );
	}


	public function cleanup_assets(): void {

		if ( is_admin() ) {
			return;
		}

		$this->prepare_assets_to_remove();
		$this->remove_assets();
	}


	private function prepare_assets_to_remove(): void {

		if ( is_product() ) {
			$this->remove_product_assets();
		}

		if ( $this->should_remove_archive_assets() ) {
			$this->remove_archive_assets();
		}

		if ( is_cart() || is_checkout() ) {
			$this->remove_cart_checkout_assets();
		}

		if ( is_account_page() ) {
			$this->remove_account_assets();
		}

		$this->enqueues = array_map( 'array_filter', $this->enqueues );
	}


	private function remove_assets(): void {

		foreach ( $this->enqueues['scripts'] as $script ) {
			wp_dequeue_script( $script );
		}

		foreach ( $this->enqueues['styles'] as $style ) {
			wp_dequeue_style( $style );
			wp_deregister_style( $style );
		}
	}


	private function remove_product_assets(): void {

		$this->remove_assets_from_list(
			'scripts',
			[
				'jquery-blockui',
				'wc-single-product',
				'flexslider',
				'photoswipe',
				'photoswipe-ui-default',
				'zoom',
				'prettyPhoto',
				'prettyPhoto-init',
			]
		);

		$this->remove_assets_from_list(
			'styles',
			[
				'photoswipe',
				'photoswipe-default-skin',
				'woocommerce_prettyPhoto_css',
			]
		);
	}


	private function remove_archive_assets(): void {

		$this->remove_assets_from_list(
			'scripts',
			[
				'woocommerce',
				'wc-add-to-cart',
				'wc-cart-fragments',
			]
		);

		$this->remove_assets_from_list(
			'styles',
			[
				'woocommerce-layout',
				'woocommerce-smallscreen',
				'woocommerce-general',
			]
		);
	}


	private function remove_cart_checkout_assets(): void {

		$this->remove_assets_from_list(
			'scripts',
			[
				'wc-checkout',
				'wc-cart',
				'wc-cart-fragments',
				'wc-country-select',
				'select2',
				'selectWoo',
			]
		);

		$this->remove_assets_from_list(
			'styles',
			[
				'select2',
			]
		);
	}


	private function remove_account_assets(): void {

		$this->remove_assets_from_list(
			'scripts',
			[
				'wc-country-select',
				'select2',
				'selectWoo',
			]
		);

		$this->remove_assets_from_list(
			'styles',
			[
				'select2',
			]
		);
	}


	private function remove_assets_from_list( $type, $assets ): void {

		$this->enqueues[ $type ] = array_diff(
			$this->enqueues[ $type ],
			$assets
		);
	}


	private function should_remove_archive_assets(): bool {

		return is_woocommerce()
				|| is_product_category()
				|| is_product_tag()
				|| $this->has_wc_blocks()
				|| $this->has_wc_shortcode_products();
	}


	private function has_wc_blocks(): bool {

		if ( empty( $this->current_post ) ) {
			return false;
		}

		$blocks      = parse_blocks( $this->current_post->post_content );
		$block_names = array_filter( wp_list_pluck( $blocks, 'blockName' ) );

		foreach ( $block_names as $name ) {
			if ( str_contains( $name, 'woocommerce' ) ) {
				return true;
			}
		}

		return false;
	}


	private function has_wc_shortcode_products(): bool {

		return $this->current_post && has_shortcode( $this->current_post->post_content, 'products' );
	}
}
