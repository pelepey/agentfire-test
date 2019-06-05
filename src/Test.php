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

		Map::getInstance()->addHooks();

        add_action( 'init', [PinPostType::getInstance(), 'register'], 0 );
        add_action( 'init', [PinTagTax::getInstance(), 'register'], 0 );
	}
}
