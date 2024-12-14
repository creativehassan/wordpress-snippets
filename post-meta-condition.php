<?php
use Elementor\Controls_Manager;
use ElementorPro\Modules\DisplayConditions\Classes\Comparator_Provider;
use ElementorPro\Modules\DisplayConditions\Classes\Comparators_Checker;
use ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Post_Meta_Condition extends Condition_Base {
	const CONDITION_KEY = 'post_meta';

	public function get_name() {
		return 'post_meta';
	}

	public function get_label() {
		return esc_html__( 'Post Meta', 'elementor-pro' );
	}

	public function get_group() {
		return 'post';
	}

	/**
	 * Check the condition.
	 *
	 * @param array $args The condition arguments.
	 * @return bool
	 */
	public function check( $args ) : bool {
		$current_post_id = get_the_ID();

		if ( ! $current_post_id ) {
			return false; // Not on a single post.
		}

		// Get the meta value from the current post.
		$meta_key = $args['meta_key'];
		$expected_value = $args['expected_value'];
		$post_meta_value = get_post_meta( $current_post_id, $meta_key, true );

		// Compare the meta value with the expected value.
		return $post_meta_value === $expected_value;
	}

	public function get_options() {
		$this->add_control( 'meta_key', [
			'type' => Controls_Manager::TEXT,
			'label' => esc_html__( 'Meta Key', 'elementor-pro' ),
			'description' => esc_html__( 'Enter the meta key to check.', 'elementor-pro' ),
			'required' => true,
		] );

		$this->add_control( 'expected_value', [
			'type' => Controls_Manager::TEXT,
			'label' => esc_html__( 'Expected Value', 'elementor-pro' ),
			'description' => esc_html__( 'Enter the value to match.', 'elementor-pro' ),
			'required' => true,
		] );
	}
}

// Include that in functions.php
add_action( 'elementor/display_conditions/register', function( $conditions_manager ) {
	include get_stylesheet_directory() . '/conditions/post-meta-condition.php';
	$conditions_manager->register_condition_instance( new Post_Meta_Condition() );
} );
