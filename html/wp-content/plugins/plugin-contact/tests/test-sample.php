<?php
/**
 * Class SampleTest
 *
 * @package Plugin_Contact
 */

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	public function test_sample() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );
		$this->assertTrue( shortcode_exists('ntp_plugin_form') );
	}
}