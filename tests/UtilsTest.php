<?php

namespace Art\BloatTrimmer\Tests;

use Art\BloatTrimmer\Utils;
use WP_Mock;
use tad\FunctionMocker\FunctionMocker;

class UtilsTest extends ArtBloatTrimmerTestCase {

	public function test_get_plugin_path(): void {

		$this->assertEquals( '/path/to/plugin', Utils::get_plugin_path() );
	}


	public function test_get_plugin_version(): void {

		$this->assertEquals( '1.0.0', Utils::get_plugin_version() );
	}


	public function test_get_plugin_url(): void {

		$this->assertEquals( 'https://example.com/plugin', Utils::get_plugin_url() );
	}


	public function test_get_plugin_slug(): void {

		$this->assertEquals( 'bloat-trimmer', Utils::get_plugin_slug() );
	}


	public function test_get_plugin_file(): void {

		$this->assertEquals( '/path/to/plugin/plugin.php', Utils::get_plugin_file() );
	}


	public function test_get_plugin_basename(): void {

		define( 'ABT_PLUGIN_FILE', 'art-bloat-trimmer/art-bloat-trimmer.php' );
		// Мокаем функцию plugin_basename
		WP_Mock::userFunction( 'plugin_basename', [
			'return' => 'art-bloat-trimmer/art-bloat-trimmer.php',
		] );

		$this->assertEquals(
			'art-bloat-trimmer/art-bloat-trimmer.php',
			Utils::get_plugin_basename(),
			'Ожидается, что базовое имя плагина будет "art-bloat-trimmer/art-bloat-trimmer.php"'
		);
	}


	public function test_get_plugin_title(): void {

		$this->assertEquals( 'Bloat Trimmer', Utils::get_plugin_title() );
	}


	public function test_get_plugin_prefix(): void {

		$this->assertEquals( 'abt_', Utils::get_plugin_prefix() );
	}


	public function test_plural_form_with_string_input() {

		// Тест с входной строкой
		$this->assertEquals(
			'яблоко',
			Utils::plural_form( 1, 'яблоко,яблока,яблок' ),
			'Для числа 1 должна быть выбрана форма "яблоко"'
		);

		$this->assertEquals(
			'яблока',
			Utils::plural_form( 2, 'яблоко,яблока,яблок' ),
			'Для числа 2 должна быть выбрана форма "яблока"'
		);

		$this->assertEquals(
			'яблок',
			Utils::plural_form( 5, 'яблоко,яблока,яблок' ),
			'Для числа 5 должна быть выбрана форма "яблок"'
		);

		$this->assertEquals(
			'яблок',
			Utils::plural_form( 11, 'яблоко,яблока,яблок' ),
			'Для числа 11 должна быть выбрана форма "яблок"'
		);

		$this->assertEquals(
			'яблоко',
			Utils::plural_form( 21, 'яблоко,яблока,яблок' ),
			'Для числа 21 должна быть выбрана форма "яблоко"'
		);
	}


	public function test_plural_form_with_array_input() {

		// Тест с входным массивом
		$this->assertEquals(
			'яблоко',
			Utils::plural_form( 1, [ 'яблоко', 'яблока', 'яблок' ] ),
			'Для числа 1 (массив) должна быть выбрана форма "яблоко"'
		);

		$this->assertEquals(
			'яблока',
			Utils::plural_form( 2, [ 'яблоко', 'яблока', 'яблок' ] ),
			'Для числа 2 (массив) должна быть выбрана форма "яблока"'
		);

		$this->assertEquals(
			'яблок',
			Utils::plural_form( 5, [ 'яблоко', 'яблока', 'яблок' ] ),
			'Для числа 5 (массив) должна быть выбрана форма "яблок"'
		);

		$this->assertEquals(
			'яблок',
			Utils::plural_form( 11, [ 'яблоко', 'яблока', 'яблок' ] ),
			'Для числа 11 (массив) должна быть выбрана форма "яблок"'
		);

		$this->assertEquals(
			'яблоко',
			Utils::plural_form( 21, [ 'яблоко', 'яблока', 'яблок' ] ),
			'Для числа 21 (массив) должна быть выбрана форма "яблоко"'
		);
	}


	public function test_plural_form_without_third_form() {

		// Тест без третьей формы
		$this->assertEquals(
			'яблоко',
			Utils::plural_form( 1, 'яблоко,яблока' ),
			'Для числа 1 (без третьей формы) должна быть выбрана форма "яблоко"'
		);

		$this->assertEquals(
			'яблока',
			Utils::plural_form( 2, 'яблоко,яблока' ),
			'Для числа 2 (без третьей формы) должна быть выбрана форма "яблока"'
		);

		$this->assertEquals(
			'яблока',
			Utils::plural_form( 5, 'яблоко,яблока' ),
			'Для числа 5 (без третьей формы) должна быть выбрана форма "яблока"'
		);

		$this->assertEquals(
			'яблока',
			Utils::plural_form( 11, 'яблоко,яблока' ),
			'Для числа 11 (без третьей формы) должна быть выбрана форма "яблока"'
		);

		$this->assertEquals(
			'яблоко',
			Utils::plural_form( 21, 'яблоко,яблока' ),
			'Для числа 21 (без третьей формы) должна быть выбрана форма "яблоко"'
		);
	}


	public function test_plural_form_with_edge_cases() {

		// Граничные случаи
		$this->assertEquals(
			'яблок',
			Utils::plural_form( 0, 'яблоко,яблока,яблок' ),
			'Для числа 0 должна быть выбрана форма "яблок"'
		);

		$this->assertEquals(
			'яблоко',
			Utils::plural_form( - 1, 'яблоко,яблока,яблок' ),
			'Для числа -1 должна быть выбрана форма "яблоко"'
		);

		$this->assertEquals(
			'яблок',
			Utils::plural_form( 105, 'яблоко,яблока,яблок' ),
			'Для числа 105 должна быть выбрана форма "яблока"'
		);
	}


	public function test_is_woocommerce_active_when_class_exists() {

		FunctionMocker::replace( 'class_exists', function ( $class_name ) {

			return 'WC' === $class_name;
		} );

		$this->assertTrue( Utils::is_woocommerce_active() );
	}


	public function test_is_woocommerce_active_when_plugin_is_active() {

		FunctionMocker::replace( 'class_exists', function () {

			return false;
		} );

		WP_Mock::userFunction( 'is_plugin_active', [
			'args'   => [ 'woocommerce/woocommerce.php' ],
			'return' => true,
		] );

		$this->assertTrue( Utils::is_woocommerce_active() );
	}


	public function test_is_woocommerce_active_when_neither_class_nor_plugin_is_active() {

		FunctionMocker::replace( 'class_exists', function () {

			return false;
		} );

		WP_Mock::userFunction( 'is_plugin_active', [
			'args'   => [ 'woocommerce/woocommerce.php' ],
			'return' => false,
		] );

		$this->assertFalse( Utils::is_woocommerce_active() );
	}


	public function test_is_rank_math_active_when_class_exists() {

		FunctionMocker::replace( 'class_exists', function ( $class_name ) {

			return 'RankMath' === $class_name;
		} );

		$this->assertTrue( Utils::is_rank_math_active() );
	}


	public function test_is_rank_math_active_when_plugin_is_active() {

		FunctionMocker::replace( 'class_exists', function () {

			return false;
		} );

		WP_Mock::userFunction( 'is_plugin_active', [
			'args'   => [ 'seo-by-rank-math/rank-math.php' ],
			'return' => true,
		] );

		$this->assertTrue( Utils::is_rank_math_active() );
	}


	public function test_is_rank_math_active_when_neither_class_nor_plugin_is_active() {

		FunctionMocker::replace( 'class_exists', function () {

			return false;
		} );

		WP_Mock::userFunction( 'is_plugin_active', [
			'args'   => [ 'seo-by-rank-math/rank-math.php' ],
			'return' => false,
		] );

		$this->assertFalse( Utils::is_rank_math_active() );
	}
}
