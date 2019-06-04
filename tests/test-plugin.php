<?php
/**
 * Class SampleTest
 *
 * @package Agentfire_Test
 */

use AgentFire\Plugin\Test;

/**
 * Sample test case.
 */
class PluginTest extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	public function test_plugin_class_exists() {
		// Replace this with some actual testing code.
		$this->assertTrue( class_exists(Test::class) );
	}
}
