<?php

namespace Art\BloatTrimmer\Cleanup_Core;

class Autoremove_Attachments {

	public function init_hooks(): void {

		add_action( 'before_delete_post', [ $this, 'remove_attachments' ], PHP_INT_MAX, 1 );
	}


	public function remove_attachments( $post_id ): void {

		$post_id = (int) $post_id;

		$args = [
			'post_parent'            => $post_id,
			'post_type'              => 'attachment',
			'post_mime_type'         => 'image',
			'posts_per_page'         => 100,
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		];

		$attachments = get_children( $args );

		foreach ( $attachments as $attachment ) {
			wp_delete_post( $attachment->ID, true );
		}
	}
}
