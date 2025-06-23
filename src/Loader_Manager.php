<?php

namespace Art\BloatTrimmer;

use Art\BloatTrimmer\Admin\Options;
use Art\BloatTrimmer\Admin\Sections\Admin;
use Art\BloatTrimmer\Admin\Sections\General;
use Art\BloatTrimmer\Admin\Sections\Head;
use Art\BloatTrimmer\Cleanup_Core\Autoremove_Attachments;
use Art\BloatTrimmer\Cleanup_Core\Cleanup_Bar;
use Art\BloatTrimmer\Cleanup_Core\Cleanup_Common;
use Art\BloatTrimmer\Cleanup_Core\Cleanup_Dashboard;
use Art\BloatTrimmer\Cleanup_Core\Cleanup_Head;
use Art\BloatTrimmer\Cleanup_Core\Cleanup_Widgets;
use Art\BloatTrimmer\Cleanup_Core\Disable_Aggressive_Updates;
use Art\BloatTrimmer\Cleanup_Core\Disable_Comments;
use Art\BloatTrimmer\Cleanup_Core\Disable_Embeds;
use Art\BloatTrimmer\Cleanup_Core\Disable_Emoji;
use Art\BloatTrimmer\Cleanup_Core\Disable_Feed;
use Art\BloatTrimmer\Cleanup_Core\Disable_Xml_Rpc;
use Art\BloatTrimmer\Admin\Sections\Plugins;
use Automattic\WooCommerce\Internal\Utilities\Users;

class Loader_Manager {

	protected Options $options;


	protected Utils $utils;


	public function __construct( Options $options, Utils $utils ) {

		$this->options = $options;
		$this->utils   = $utils;
	}


	public function initialize(): void {

		$this->initialize_settings_modules();
		$this->initialize_conditional_modules();
	}


	protected function initialize_settings_modules(): void {

		$core_modules = [
			General::class,
			Admin::class,
			Head::class,
			Plugins\Woocommerce::class,
			Plugins\RankMath::class,
			Plugins\Yoast::class,
		];

		foreach ( $core_modules as $module_class ) {
			( new $module_class( $this->options, $this->utils ) )->init_hooks();
		}
	}


	protected function initialize_conditional_modules(): void {

		$modules = [
			'disable_aggressive_updates' => [
				'class'     => Disable_Aggressive_Updates::class,
				'condition' => function () {

					return is_admin() && 'on' === $this->options->get( 'disable_aggressive_updates', 'general' );
				},
			],
			'disable_emoji'              => [
				'class'     => Disable_Emoji::class,
				'condition' => function () {

					return 'on' === $this->options->get( 'disable_emoji', 'general' );
				},
			],
			'disable_feed'               => [
				'class'     => Disable_Feed::class,
				'condition' => function () {

					return 'on' === $this->options->get( 'disable_feed', 'general' );
				},
			],
			'disable_embeds'             => [
				'class'     => Disable_Embeds::class,
				'condition' => function () {

					return 'on' === $this->options->get( 'disable_embeds', 'general' );
				},
			],
			'disable_xml_rpc'            => [
				'class'     => Disable_Xml_Rpc::class,
				'condition' => function () {

					return 'on' === $this->options->get( 'disable_xml', 'general' );
				},
			],
			'disable_comments'           => [
				'class'     => Disable_Comments::class,
				'condition' => function () {

					return 'on' === $this->options->get( 'disable_comments', 'general' );
				},
			],
			'autoremove_attachments'     => [
				'class'     => Autoremove_Attachments::class,
				'condition' => function () {

					return is_admin() && 'on' === $this->options->get( 'autoremove_attachments', 'general' );
				},
			],
			'cleanup_head'               => [
				'class'     => Cleanup_Head::class,
				'condition' => function () {

					return ! is_admin();
				},
			],
			'cleanup_dashboard'          => [
				'class'     => Cleanup_Dashboard::class,
				'condition' => function () {

					return is_admin() && 'on' === $this->options->get( 'cleanup_dashboard', 'admin' );
				},
			],
			'cleanup_admin_bar'          => [
				'class'     => Cleanup_Bar::class,
				'condition' => function () {

					return 'on' === $this->options->get( 'cleanup_admin_bar', 'admin' );
				},
			],
			'cleanup_widgets'            => [
				'class'     => Cleanup_Widgets::class,
				'condition' => function () {

					return is_admin();
				},
			],
			'cleanup_common'             => [
				'class'     => Cleanup_Common::class,
				'condition' => function () {

					return is_admin();
				},
			],
			'integrations_woocommerce'   => [
				'class'     => Cleanup_Plugins\Woocommerce\Integrations::class,
				'condition' => function () {

					return $this->utils->is_woocommerce_active();
				},
			],
			'scheduler_woocommerce'      => [
				'class'     => Cleanup_Plugins\Woocommerce\Scheduler::class,
				'condition' => function () {

					return $this->utils->is_woocommerce_active() && Users::is_site_administrator();
				},
			],
			'tools_woocommerce'          => [
				'class'     => Cleanup_Plugins\Woocommerce\Tools::class,
				'condition' => function () {

					return $this->utils->is_woocommerce_active() && Users::is_site_administrator();
				},
			],
			'disabled_woocommerce'       => [
				'class'     => Cleanup_Plugins\Woocommerce\Disabled::class,
				'condition' => function () {

					return is_admin() && $this->utils->is_woocommerce_active();
				},
			],
			'dequeue_woocommerce'        => [
				'class'     => Cleanup_Plugins\Woocommerce\Dequeue::class,
				'condition' => function () {

					return $this->utils->is_woocommerce_active() && 'on' === $this->options->get( 'woocommerce_dequeue', 'plugins' );
				},
			],
			'disabled_rank_math'         => [
				'class'     => Cleanup_Plugins\RankMath\Disabled::class,
				'condition' => function () {

					return $this->utils->is_rank_math_active();
				},
			],
			'disabled_yoast'             => [
				'class'     => Cleanup_Plugins\Yoast\Disabled::class,
				'condition' => function () {

					return $this->utils->is_yoast_active();
				},
			],

		];

		foreach ( $modules as $module ) {
			if ( $module['condition']() ) {
				( new $module['class']() )->init_hooks();
			}
		}
	}
}
