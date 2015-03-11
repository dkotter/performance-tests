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
		$total = 10;
		$recent_posts = get_posts( array( 'posts_per_page' => 5, 'post_status' => 'publish', 'fields'=> 'ids' ) );

		if ( 'normal_home' === $test ) {
			$query_args['posts_per_page'] = 15;
		} else if ( 'post__not_in_home' === $test ) {
			$query_args['post__not_in'] = $recent_posts;
		} else if ( 'normal_related' === $test ) {
			$recent_posts = get_posts( array( 'posts_per_page' => 1, 'post_status' => 'publish', 'fields'=> 'ids' ) );
			$query_args['posts_per_page'] = 5;
			$total = 4;
		} else if ( 'post__not_in_related' === $test ) {
			$recent_posts = get_posts( array( 'posts_per_page' => 1, 'post_status' => 'publish', 'fields'=> 'ids' ) );
			$query_args['post__not_in'] = $recent_posts;
			$query_args['posts_per_page'] = 4;
		}

		for ( $i = 0; $i < $num; $i++ ) {
			if ( 'loop' === $group ) {
				$time = $this->run_loop_tests( $test, $query_args, $total, $recent_posts );
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
	 * Run a standard loop, to test how long that takes.
	 *
	 * @param string $test Test name.
	 * @param array $query_args Query arguments.
	 * @param int $total Total posts to grab.
	 * @param array $recent Recent post array.
	 * @return array
	 */
	public function run_loop_tests( $test, $query_args, $total, $recent ) {
		$start = 0;
		$stop  = 0;

		if ( in_array( $test, array( 'post__not_in_home', 'post__not_in_related' ) ) ) {
			$start = $this->start_timer();
			$query = new WP_Query( $query_args );

			while ( $query->have_posts() ) {
				$query->the_post();
				$title = get_the_title();
				$author = get_the_author();
				$content = get_the_content();
			}

			$stop = $this->stop_timer();
		} else if ( in_array( $test, array( 'normal_home', 'normal_related' ) ) ) {
			$start = $this->start_timer();
			$query = new WP_Query( $query_args );

			for ( $n = 0; $n < $total; $n++ ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					if ( in_array( get_the_ID(), $recent ) ) {
						continue;
					}

					$title = get_the_title();
					$author = get_the_author();
					$content = get_the_content();
				}
			}

			$stop = $this->stop_timer();
		}

		return array( $start, $stop );
	}

}

$tests = array(
	'normal_home',
	'post__not_in_home',
	'normal_related',
	'post__not_in_related',
);
$groups = array( 'query', 'loop' );
$test = new Post_Not_In_Tests( 1000, $tests, $groups );
$test->run_tests();
$test->output_results();
