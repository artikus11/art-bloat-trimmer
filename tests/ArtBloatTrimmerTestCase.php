<?php

namespace Art\BloatTrimmer\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use tad\FunctionMocker\FunctionMocker;
use WP_Mock;

abstract class ArtBloatTrimmerTestCase extends TestCase {

	public function setUp(): void {

		parent::setUp();
		FunctionMocker::setUp();
		WP_Mock::setUp();

		FunctionMocker::replace(
			'constant',
			function ( $name ) {

				$constants = [
					'ABT_PLUGIN_DIR'    => '/path/to/plugin',
					'ABT_PLUGIN_VER'    => '1.0.0',
					'ABT_PLUGIN_URI'    => 'https://example.com/plugin',
					'ABT_PLUGIN_SLUG'   => 'bloat-trimmer',
					'ABT_PLUGIN_AFILE'  => '/path/to/plugin/plugin.php',
					'ABT_PLUGIN_FILE'   => '/path/to/plugin/plugin.php',
					'ABT_PLUGIN_NAME'   => 'Bloat Trimmer',
					'ABT_PLUGIN_PREFIX' => 'abt_',
					'ABSPATH'           => '/path/to/wordpress/',
				];

				return $constants[ $name ] ?? null;
			}
		);
	}


	public function tearDown(): void {

		parent::tearDown();
		WP_Mock::tearDown();
		FunctionMocker::tearDown();
		Mockery::close();
	}
}
