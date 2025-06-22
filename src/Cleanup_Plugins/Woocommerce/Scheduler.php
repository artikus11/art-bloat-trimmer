<?php

namespace Art\BloatTrimmer\Cleanup_Plugins\Woocommerce;

use Automattic\WooCommerce\Internal\Utilities\Users;

class Scheduler {

	public function init_hooks(): void {

		add_filter( 'action_scheduler_retention_period', [ $this, 'retention_period' ], 100 );
		add_filter( 'action_scheduler_default_cleaner_statuses', [ $this, 'default_cleaner_statuses' ], 100 );
		add_filter( 'action_scheduler_cleanup_batch_size', [ $this, 'cleanup_batch_size' ], 100 );
	}


	public function retention_period(): int {

		return DAY_IN_SECONDS;
	}


	public function default_cleaner_statuses( $statuses ) {

		$statuses[] = 'failed';

		return $statuses;
	}


	public function cleanup_batch_size(): int {

		return 100;
	}
}
