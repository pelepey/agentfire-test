<?php

declare( strict_types=1 );

namespace AgentFire\Plugin;

/**
 * Class Test
 * @package AgentFire\Plugin
 */
class Test {
	public function __construct() {
		$optionsPage = OptionsPage::getInstance();

		add_action('acf/init', [$optionsPage, 'acfInitPage']);
		add_action('acf/init', [$optionsPage, 'registerOptions']);

        add_action( 'init', [PinTagTax::getInstance(), 'register'], 0 );

		Map::getInstance()->addHooks();
		PinPostType::getInstance()->addHooks();
	}
}
