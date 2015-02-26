<?php

class Tests {
	public $num_tests = 1000;

	public $tests = array();

	public $data = array();

	public $results = array();

	public function __construct( $num_tests = 1000, $tests = array() ) {
		$this->num_tests = $num_tests;
		$this->tests = $tests;
	}

	public function run_tests() {
		foreach ( $this->tests as $test ) {
			$this->run_test( $this->num_tests, $test );
		}
	}

	public function run_test( $n, $test ) {
		for ( $i = 0; $i < $n; $i++ ) {
			$start = $this->start_timer();
			// Run actual test here
			$stop = $this->stop_timer();
			$this->add_result( 'test name', $this->get_elapsed_time( $start, $stop ) );
		}
	}

	public function calculate_results() {
		$results = array();

		foreach ( $this->tests as $test ) {
			$results[ $test ] = array(
				'mean'   => $this->format( $this->get_mean( $this->data[ $test ] ) ),
				'median' => $this->format( $this->get_median( $this->data[ $test ] ) ),
				'min'    => $this->format( $this->get_min( $this->data[ $test ] ) ),
				'max'    => $this->format( $this->get_max( $this->data[ $test ] ) ),
				'sd'     => $this->format( $this->get_standard_deviation( $this->data[ $test ] ) ),
			);
		}

		return $results;
	}

	public function format( $num ) {
		return sprintf( '%.10F', $num );
	}

	public function get_results() {
		if ( empty( $this->results ) ) {
			$this->results = $this->calculate_results();
		}

		return $this->results;
	}

	public function start_timer() {
		return microtime( true );
	}

	public function stop_timer() {
		return microtime( true );
	}

	public function get_elapsed_time( $start, $stop ) {
		return $stop - $start;
	}

	public function add_result( $bucket, $result ) {
		$this->data[ $bucket ][] = $result;
	}

	public function get_mean( $array ) {
		return array_sum( $array ) / count( $array );
	}

	public function get_median( $array ) {
		rsort( $array );
		$middle = (int) round( count( $array ) / 2 );

		return $array[ $middle - 1 ];
	}

	public function get_standard_deviation( $array ){
		return sqrt( array_sum( array_map( array( $this, 'get_sd_square' ), $array, array_fill( 0, count( $array ), ( array_sum( $array ) / count( $array ) ) ) ) ) / ( count( $array ) - 1 ) );
	}

	function get_sd_square( $x, $mean ) {
		return pow( $x - $mean, 2 );
	}

	public function get_max( $array ) {
		return max( $array );
	}

	public function get_min( $array ) {
		return min( $array );
	}

}

$test = new Tests();
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
