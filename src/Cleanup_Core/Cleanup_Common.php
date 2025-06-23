<?php

namespace Art\BloatTrimmer\Cleanup_Core;

use Art\BloatTrimmer\Admin\Options;
use Art\BloatTrimmer\Interfaces\Init_Hooks_Interface;

class Cleanup_Common implements Init_Hooks_Interface {

	public function init_hooks(): void {

		if ( 'on' === Options::get( 'cleanup_count_comments', 'admin' ) ) {
			$this->disable_count_comments();
		}

		if ( 'on' === Options::get( 'delete_intermediate_image_sizes', 'admin' ) ) {
			$this->delete_intermediate_image();
		}
	}


	public function delete_intermediate_image(): void {

		add_filter( 'intermediate_image_sizes', [ $this, 'delete_intermediate_image_sizes' ] );
	}


	/**
	 * Отключает создание миниатюр файлов для указанных размеров.
	 *
	 * @param  array $sizes
	 *
	 * @return array
	 */
	public function delete_intermediate_image_sizes( array $sizes ): array {

		return array_diff( $sizes, [
			'medium_large',
			'1536x1536',
			'2048x2048',
		] );
	}


	public function disable_count_comments(): void {

		// Отменим запрос по подсчету комментариев
		add_filter( 'wp_count_comments', [ $this, 'count_comments_empty' ] );
	}


	public function count_comments_empty(): object {

		return (object) [
			'approved'            => 0,
			'awaiting_moderation' => 0,
			'moderated'           => 0,
			'spam'                => 0,
			'trash'               => 0,
			'post-trashed'        => 0,
			'total_comments'      => 0,
			'all'                 => 0,
		];
	}
}
