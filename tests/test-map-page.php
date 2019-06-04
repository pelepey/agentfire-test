<?php
/**
 * Class SampleTest
 *
 * @package Agentfire_Test
 */

/**
 * Sample test case.
 */
class MapPageTest extends WP_UnitTestCase {

	/**
     * Tests content injection inside the tag main
	 */
	public function test_content_injection()
    {
        $html = "<main class=\"sample\"> SOme content </main>";
        $inject = "Injection!";
        $expect = "<main class=\"sample\">Injection!</main>";

	    $injected = \AgentFire\Plugin\MapPage::getInstance()->injectContentIntoMainTag($html, $inject);

	    $this->assertEquals($expect, $injected);
	}
}
