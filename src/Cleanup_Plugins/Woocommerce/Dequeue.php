<?php

namespace Art\BloatTrimmer\Cleanup_Plugins\Woocommerce;

if ( ! class_exists( 'Woocommerce' ) ) {
	return;
}


class Dequeue {

	public static function init_hooks(): void {

		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'dequeue' ], 9999 );
	}


	public static function dequeue(): void {

		if ( is_admin() ) {
			return;
		}

		$enqueues = self::enqueues();

		$enqueues = self::has_product( $enqueues );

		$enqueues = self::has_archives_products( $enqueues );

		$enqueues = self::has_cart_checkout( $enqueues );

		$enqueues = self::has_account( $enqueues );

		foreach ( $enqueues as $key => $enqueue ) {

			if ( 'scripts' === $key ) {
				foreach ( $enqueue as $item ) {
					wp_dequeue_script( $item );
				}
			}

			if ( 'styles' === $key ) {
				foreach ( $enqueue as $item ) {
					wp_dequeue_style( $item );
				}
			}
		}
	}


	/**
	 * @param  array $enqueues
	 *
	 * @return array
	 */
	protected static function has_product( array $enqueues ): array {

		if ( is_product() ) {
			unset(
				$enqueues['scripts'][ array_search( 'jquery-blockui', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'wc-single-product', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'flexslider', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'photoswipe', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'zoom', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'prettyPhoto', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'prettyPhoto-init', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'photoswipe-ui-default', $enqueues['scripts'], true ) ],
				$enqueues['styles'][ array_search( 'photoswipe', $enqueues['styles'], true ) ],
				$enqueues['styles'][ array_search( 'photoswipe-default-skin', $enqueues['styles'], true ) ],
				$enqueues['styles'][ array_search( 'woocommerce_prettyPhoto_css', $enqueues['styles'], true ) ]
			);
		}

		return $enqueues;
	}


	/**
	 * @param  array $enqueues
	 *
	 * @return array
	 */
	protected static function has_archives_products( array $enqueues ): array {

		global $post;

		$has_wc_blocks = self::is_wc_blocks( $post );

		$has_wc_shortcode_products = self::is_wc_shortcode_products( $post );

		if ( is_woocommerce() || is_product_category() || is_product_tag() || $has_wc_blocks || $has_wc_shortcode_products ) {
			unset(
				$enqueues['scripts'][ array_search( 'woocommerce', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'wc-add-to-cart', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'wc-cart-fragments', $enqueues['scripts'], true ) ],
				$enqueues['styles'][ array_search( 'woocommerce-layout', $enqueues['styles'], true ) ],
				$enqueues['styles'][ array_search( 'woocommerce-smallscreen', $enqueues['styles'], true ) ],
				$enqueues['styles'][ array_search( 'woocommerce-general', $enqueues['styles'], true ) ]
			);
		}

		return $enqueues;
	}


	/**
	 * @param  array $enqueues
	 *
	 * @return array
	 */
	private static function has_cart_checkout( array $enqueues ): array {

		if ( is_cart() || is_checkout() ) {
			unset(
				$enqueues['scripts'][ array_search( 'wc-add-to-cart', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'wc-checkout', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'wc-cart', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'wc-cart-fragments', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'wc-country-select', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'select2', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'selectWoo', $enqueues['scripts'], true ) ],
				$enqueues['styles'][ array_search( 'select2', $enqueues['styles'], true ) ]
			);
		}

		return $enqueues;
	}


	/**
	 * @param  array $enqueues
	 *
	 * @return array
	 */
	private static function has_account( array $enqueues ): array {

		if ( is_account_page() ) {
			unset(
				$enqueues['scripts'][ array_search( 'wc-country-select', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'select2', $enqueues['scripts'], true ) ],
				$enqueues['scripts'][ array_search( 'selectWoo', $enqueues['scripts'], true ) ],
				$enqueues['styles'][ array_search( 'select2', $enqueues['styles'], true ) ]
			);
		}

		return $enqueues;
	}


	protected static function enqueues(): array {

		return [
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
	}


	/**
	 * @param  \WP_Post $post
	 *
	 * @return bool
	 */
	protected static function is_wc_blocks( $post ): bool {

		if ( empty( $post ) ) {
			return false;
		}

		$parse_content  = parse_blocks( $post->post_content );
		$blocks_name    = array_filter( wp_list_pluck( $parse_content, 'blockName' ) );
		$wc_blocks_name = [];

		foreach ( $blocks_name as $block_name ) {
			if ( str_contains( $block_name, 'woocommerce' ) ) {
				$wc_blocks_name[] = $block_name;
			}
		}

		return ! empty( $wc_blocks_name );
	}


	/**
	 * @param  \WP_Post $post
	 *
	 * @return bool
	 */
	protected static function is_wc_shortcode_products( $post ): bool {

		if ( empty( $post ) ) {
			return false;
		}

		return has_shortcode( $post->post_content, 'products' );
	}
}
