<?php

/*
 * Base class used to run our tests.
 *
 * This will be extended in child classes.
 *
 * @return void
 */
class Tests {

	/*
	 * Store the number of tests we want to run.
	 *
	 * @var int
	 */
	public $num_tests = 1000;

	/*
	 * Store names of the tests we want to run.
	 *
	 * @var array
	 */
	public $tests = array();

	/*
	 * Store data of the tests we run.
	 *
	 * @var array
	 */
	public $data = array();

	/*
	 * Store overall results of the tests we run.
	 *
	 * @var array
	 */
	public $results = array();

	/*
	 * Initialize the class.
	 *
	 * @param int $num_tests The number of tests to run.
	 * @param array $tests The test names to run.
	 * @return void
	 */
	public function __construct( $num_tests = 1000, $tests = array() ) {
		$this->num_tests = $num_tests;
		$this->tests = $tests;
	}

	/*
	 * Run all our tests.
	 *
	 * @return void
	 */
	public function run_tests() {
		foreach ( $this->tests as $test ) {
			$this->run_test( $this->num_tests, $test );
		}
	}

	/*
	 * Run each individual test.
	 *
	 * Will need to be overwritten in child
	 * classes, adding in the actual code
	 * we want to test.
	 *
	 * @param int $n Number of tests to run.
	 * @param string $test Test to run.
	 * @return void
	 */
	public function run_test( $n, $test ) {
		for ( $i = 0; $i < $n; $i++ ) {
			$start = $this->start_timer();
			// Run actual test here
			$stop = $this->stop_timer();
			$this->add_result( $test, $this->get_elapsed_time( $start, $stop ) );
		}
	}

	/*
	 * Calculate our results.
	 *
	 * This calculates the mean, median, min,
	 * max, and standard deviation.
	 *
	 * @return array
	 */
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

	/*
	 * Format our results.
	 *
	 * @param float $num Number to format.
	 * @return string
	 */
	public function format( $num ) {
		return sprintf( '%.10F', $num );
	}

	/*
	 * Get our results.
	 *
	 * @return array
	 */
	public function get_results() {
		if ( empty( $this->results ) ) {
			$this->results = $this->calculate_results();
		}

		return $this->results;
	}

	/*
	 * Output our results.
	 *
	 * @return array
	 */
	public function output_results() {
		if ( empty( $this->results ) ) {
			$this->results = $this->calculate_results();
		}

		foreach ( $this->results as $function => $data ) {
			echo "$function: \n\n";
			echo "  --------\n";
			echo "     Range: {$data['min']} - {$data['max']} seconds\n";
			echo "    Median: {$data['median']} seconds\n";
			echo "      Mean: {$data['mean']} seconds\n";
			echo "        SD: {$data['sd']} seconds\n\n";
		}
	}

	/*
	 * Start our timer.
	 *
	 * @return float
	 */
	public function start_timer() {
		return microtime( true );
	}

	/*
	 * Stop our timer.
	 *
	 * @return float
	 */
	public function stop_timer() {
		return microtime( true );
	}

	/*
	 * Get the overall elapsed time.
	 *
	 * @param float $start Start time.
	 * @param float $stop Stop time.
	 * @return float
	 */
	public function get_elapsed_time( $start, $stop ) {
		return $stop - $start;
	}

	/*
	 * Add our result to our data array.
	 *
	 * @param string $bucket Test bucket.
	 * @param array $result Test result.
	 * @return void
	 */
	public function add_result( $bucket, $result ) {
		$this->data[ $bucket ][] = $result;
	}

	/*
	 * Get the mean from our data.
	 *
	 * @param array $array Data from test.
	 * @return float
	 */
	public function get_mean( $array ) {
		return array_sum( $array ) / count( $array );
	}

	/*
	 * Get the median from our data.
	 *
	 * @param array $array Data from test.
	 * @return float
	 */
	public function get_median( $array ) {
		rsort( $array );
		$middle = (int) round( count( $array ) / 2 );

		return $array[ $middle - 1 ];
	}

	/*
	 * Get the standard deviation from our data.
	 *
	 * @param array $array Data from test.
	 * @return float
	 */
	public function get_standard_deviation( $array ){
		return sqrt( array_sum( array_map( array( $this, 'get_sd_square' ), $array, array_fill( 0, count( $array ), ( array_sum( $array ) / count( $array ) ) ) ) ) / ( count( $array ) - 1 ) );
	}

	/*
	 * Get the standard deviation squared.
	 *
	 * @param float $x Result item.
	 * @param float $mean Result mean.
	 * @return float
	 */
	function get_sd_square( $x, $mean ) {
		return pow( $x - $mean, 2 );
	}

	/*
	 * Get the max from our data.
	 *
	 * @param array $array Data from test.
	 * @return float
	 */
	public function get_max( $array ) {
		return max( $array );
	}

	/*
	 * Get the min from our data.
	 *
	 * @param array $array Data from test.
	 * @return float
	 */
	public function get_min( $array ) {
		return min( $array );
	}

}
