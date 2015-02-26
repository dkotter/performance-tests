<?php
ini_set( 'max_execution_time', 0 );

include __DIR__ . '/tests.php';

class Post_Not_In_Tests extends Tests {

	public function run_test( $n, $test ) {
		$query_args = array(
			'post_type'      => 'post',
			'post_status'    => array( 'publish', 'private' ),
			'posts_per_page' => 18,
		);

		if ( 'normal' === $test ) {
			$query_args['posts_per_page'] = 23;
		} else if ( 'post__not_in' === $test ) {
			$query_args['post__not_in'] = array( 3971, 3970, 3969, 3968, 2873 );
		}

		for ( $i = 0; $i < $n; $i++ ) {
			$start = $this->start_timer();
			$query = new WP_Query( $query_args );
			$stop = $this->stop_timer();
			$this->add_result( $test, $this->get_elapsed_time( $start, $stop ) );
		}
	}

}

$test = new Post_Not_In_Tests( 1000, array( 'normal', 'post__not_in' ) );
$test->run_tests();
$results = $test->get_results();

foreach ( $results as $function => $data ) {
	echo "$function: \n\n";
	echo "  --------\n";
	echo "     Range: {$data['min']} - {$data['max']}\n";
	echo "    Median: {$data['median']}\n";
	echo "      Mean: {$data['mean']}\n";
	echo "        SD: {$data['sd']}\n\n";
}
