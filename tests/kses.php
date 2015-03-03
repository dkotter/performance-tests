<?php
ini_set( 'max_execution_time', 0 );

// Include our base test class.
include __DIR__ . '/tests.php';

/*
 * Class to test kses performance.
 */
class Kses_Tests extends Tests {

	/*
	 * Run each individual test.
	 *
	 * Run the correct query based on the test we are doing,
	 * which for now is just a normal query and a post__not_in
	 * query.
	 *
	 * @param int $n Number of tests to run.
	 * @param string $test Test to run.
	 * @return void
	 */
	public function run_test( $n, $test ) {
		$query_args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
		);
		$query = new WP_Query( $query_args );
		$post = $query->posts[0];
		$post_content = $post->post_content;

		for ( $i = 0; $i < $n; $i++ ) {
			$start = $this->start_timer();
			if ( 'wp_kses' === $test ) {
				$p_tags = array(
					'a'      => array(
						'href',
						'class',
						'id',
						'target',
						'title',
						'alt',
					),
					'span'   => array(
						'class',
						'id',
					),
					'em'     => array(),
					'strong' => array(),
					'i'      => array(),
					'b'      => array(),
				);
				$test( $post_content, $p_tags );
			} else {
				$test( $post_content );
			}
			$stop = $this->stop_timer();
			$this->add_result( $test, $this->get_elapsed_time( $start, $stop ) );
		}
	}

}

$test = new Kses_Tests( 1000, array( 'wp_kses_post', 'wp_kses', 'esc_html', 'esc_attr', 'wp_strip_all_tags', 'strip_tags' ) );
$test->run_tests();
$test->output_results();
