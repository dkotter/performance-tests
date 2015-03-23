<?php
ini_set( 'max_execution_time', 0 );

// Include our base test class.
include __DIR__ . '/tests.php';

/*
 * Class to test fields => ids query and loop performance.
 */
class Fields_IDs_Loop_Tests extends Tests {

	/*
	 * Run each individual test.
	 *
	 * Run the correct query based on the test we are doing,
	 * which for now is just a normal query and a fields => ids
	 * query.
	 *
	 * @param int $num Number of tests to run.
	 * @param string $test Test to run.
	 * @param string $group Name of group test is part of.
	 * @return void
	 */
	public function run_test( $num, $test, $group ) {
		$query_args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 10,
		);

		if ( in_array( $test, array( 'fields_ids', 'setup_postdata' ) ) ) {
			$query_args['fields'] = 'ids';
		}

		for ( $i = 0; $i < $num; $i++ ) {
			if ( 'loop' === $group ) {
				$time = $this->run_loop_tests( $test, $query_args );
			} else {
				$start = $this->start_timer();
				$query = new WP_Query( $query_args );
				$stop = $this->stop_timer();
				$time = array( $start, $stop );
			}

			$this->add_result( $group, $test, $this->get_elapsed_time( $time[0], $time[1] ) );
		}
	}

	/*
	 * Run a loop, to test how long that takes.
	 *
	 * Running three tests here: a standard loop with a
	 * standard query. With a fields => ids query, running
	 * a custom loop through that array of ID's. And a fields => ids
	 * query, passing those ids into setup_postdata.
	 *
	 * @param string $test Test name.
	 * @param array $query_args Query arguments.
	 * @return array
	 */
	public function run_loop_tests( $test, $query_args ) {
		$start = 0;
		$stop  = 0;

		if ( 'normal' === $test ) {
			$start = $this->start_timer();
			$query = new WP_Query( $query_args );

			while ( $query->have_posts() ) {
				$query->the_post();
				$title = get_the_title();
				$link  = get_the_permalink();
			}

			$stop = $this->stop_timer();
		} else if ( 'fields_ids' === $test ) {
			$start = $this->start_timer();
			$query = new WP_Query( $query_args );

			if ( $query->have_posts() ) {
				$post_ids = $query->posts;

				foreach ( $post_ids as $post_id ) {
					$title = get_the_title( $post_id );
					$link  = get_the_permalink( $post_id );
				}
			}

			$stop = $this->stop_timer();
		} else if ( 'setup_postdata' === $test ) {
			$start = $this->start_timer();
			$query = new WP_Query( $query_args );

			if ( $query->have_posts() ) {
				$post_ids = $query->posts;

				foreach ( $post_ids as $post_id ) {
					global $post;
					$post = get_post( (int) $post_id );
					setup_postdata( $post );
					$title = get_the_title();
					$link  = get_the_permalink();
				}
			}

			$stop = $this->stop_timer();
		}

		return array( $start, $stop );
	}

}

$tests = array(
	'normal',
	'fields_ids',
	'setup_postdata',
);
$groups = array( 'query', 'loop' );
$test = new Fields_IDs_Loop_Tests( 1000, $tests, $groups );
$test->run_tests();
$test->output_results();
