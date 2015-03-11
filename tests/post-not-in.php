<?php
ini_set( 'max_execution_time', 0 );

// Include our base test class.
include __DIR__ . '/tests.php';

/*
 * Class to test post__not_in query performance.
 */
class Post_Not_In_Tests extends Tests {

	/*
	 * Run each individual test.
	 *
	 * Run the correct query based on the test we are doing,
	 * which for now is just a normal query and a post__not_in
	 * query.
	 *
	 * @param int $n Number of tests to run.
	 * @param string $test Test to run.
	 * @param string $group Name of group test is part of.
	 * @return void
	 */
	public function run_test( $n, $test, $group ) {
		$query_args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 10,
		);

		if ( 'normal_home' === $test ) {
			$query_args['posts_per_page'] = 15;
		} else if ( 'post__not_in_home' === $test ) {
			$recent_posts = get_posts( array( 'posts_per_page' => 5, 'post_status' => 'publish', 'fields'=> 'ids' ) );
			$query_args['post__not_in'] = $recent_posts;
		} else if ( 'normal_related' === $test ) {
			$query_args['posts_per_page'] = 5;
		} else if ( 'post__not_in_related' === $test ) {
			$recent_post = get_posts( array( 'posts_per_page' => 1, 'post_status' => 'publish', 'fields'=> 'ids' ) );
			$query_args['post__not_in'] = $recent_post;
			$query_args['posts_per_page'] = 4;
		}

		for ( $i = 0; $i < $n; $i++ ) {
			$start = $this->start_timer();
			$query = new WP_Query( $query_args );

			if ( 'loop' === $group ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					the_title();
					the_author();
					the_content();
				}
			}

			$stop = $this->stop_timer();
			$this->add_result( $group, $test, $this->get_elapsed_time( $start, $stop ) );
		}
	}

}

$test = new Post_Not_In_Tests( 1000, array( 'normal_home', 'post__not_in_home', 'normal_related', 'post__not_in_related' ), array( 'query', 'loop' ) );
$test->run_tests();
$test->output_results();
