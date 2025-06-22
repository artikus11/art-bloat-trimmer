<?php
/**
 * Class Utils
 */

namespace Art\BloatTrimmer;

/**
 * Class Utils
 */
class Utils {

	/**
	 * Get plugin path.
	 *
	 * @return string
	 */
	public static function get_plugin_path(): string {

		return constant( 'ABT_PLUGIN_DIR' );
	}


	/**
	 * Get plugin version.
	 *
	 * @return string
	 */
	public static function get_plugin_version(): string {

		return constant( 'ABT_PLUGIN_VER' );
	}


	/**
	 * Get plugin URL.
	 *
	 * @return string
	 */
	public static function get_plugin_url(): string {

		return constant( 'ABT_PLUGIN_URI' );
	}


	/**
	 * Get plugin slug.
	 *
	 * @return string
	 */
	public static function get_plugin_slug(): string {

		return constant( 'ABT_PLUGIN_SLUG' );
	}


	/**
	 * Get plugin file.
	 *
	 * @return string
	 */
	public static function get_plugin_file(): string {

		return constant( 'ABT_PLUGIN_AFILE' );
	}


	/**
	 * Get plugin base name.
	 *
	 * @return string
	 */
	public static function get_plugin_basename(): string {

		return plugin_basename( ABT_PLUGIN_FILE );
	}


	/**
	 * Get plugin title.
	 *
	 * @return string
	 */
	public static function get_plugin_title(): string {

		return constant( 'ABT_PLUGIN_NAME' );
	}


	/**
	 * Get plugin prefix.
	 *
	 * @return string
	 */
	public static function get_plugin_prefix(): string {

		return constant( 'ABT_PLUGIN_PREFIX' );
	}


	public static function plural_form( $number, $titles ) {

		if ( is_string( $titles ) ) {
			$titles = preg_split( '/, */', $titles );
		}

		if ( empty( $titles[2] ) ) {
			$titles[2] = $titles[1];
		}

		$cases = [ 2, 0, 1, 1, 1, 2 ];

		$intnum = abs( (int) wp_strip_all_tags( $number ) );

		$title_index = ( $intnum % 100 > 4 && $intnum % 100 < 20 )
			? 2
			: $cases[ min( $intnum % 10, 5 ) ];

		return $titles[ $title_index ];
	}


	public static function is_woocommerce_active(): bool {

		if ( class_exists( 'WC' ) ) {
			return true;
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return true;
		}

		return false;
	}


	public static function is_rank_math_active(): bool {

		if ( class_exists( 'RankMath' ) ) {
			return true;
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active( 'seo-by-rank-math/rank-math.php' ) ) {
			return true;
		}

		return false;
	}
}
