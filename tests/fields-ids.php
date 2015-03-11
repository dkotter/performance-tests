<?php
ini_set( 'max_execution_time', 0 );

// Include our base test class.
include __DIR__ . '/tests.php';

/*
 * Class to test post__not_in query performance.
 */
class Fields_IDs_Tests extends Tests {

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
			'posts_per_page' => 10,
		);

		if ( 'fields_ids' === $test ) {
			$query_args['fields'] = 'ids';
		}

		for ( $i = 0; $i < $n; $i++ ) {
			$start = $this->start_timer();
			$query = new WP_Query( $query_args );
			$stop = $this->stop_timer();
			$this->add_result( $test, $this->get_elapsed_time( $start, $stop ) );
		}
	}

}

$test = new Fields_IDs_Tests( 1000, array( 'normal', 'fields_ids' ) );
$test->run_tests();
$test->output_results();
