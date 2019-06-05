<?php
/**
 * Area Test
 *
 * Plugin Name: AgentFire Test
 * Description: Area Test plugin
 * Plugin URI: https://agentfire.com
 * Version: 1.0.0
 * Author: Author Name
 * License: Proprietary
 * Network: false
 *
 * @link https://agentfire.com
 * @since 1.0.0
 * @package AgentFire\Plugin
 * @wordpress-plugin
 */

namespace AgentFire\Plugin;

define( 'AGENTFIRE_TEST_PATH', plugin_dir_path( __FILE__ ) );
define( 'AGENTFIRE_TEST_FILE', __FILE__ );

require AGENTFIRE_TEST_PATH . 'vendor/autoload.php';

add_action( 'plugins_loaded', function() {
	new Test();
} );
