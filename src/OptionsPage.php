<?php
/**
 * Created by PhpStorm.
 * User: oleksandr
 * Date: 02.06.19
 * Time: 14:44
 */

namespace AgentFire\Plugin;


use AgentFire\Plugin\Test\Traits\Singleton;

class OptionsPage {
	use Singleton;

    /**
     * @return void
     */
	public function acfInitPage() {
		if ( function_exists('\acf_add_options_page') ) {
			\acf_add_options_page(array(
				'page_title' 	=> 'AgentFire Test',
				'menu_title' 	=> 'AgentFire Test',
				'menu_slug' 	=> 'agentfire-test',
			));
		}
	}

    /**
     * @return void
     */
	public function registerOptions() {
		if( !function_exists('\acf_add_local_field_group') ) {
		    return;
        }

        $templates = \wp_get_theme()->get_page_templates( null, 'page' );

        $templates = array_merge([
            '' => 'None'
        ], $templates);


        \acf_add_local_field_group(array(
            'key' => 'group_5cf3bb239c509',
            'title' => 'AgentFire Test Options',
            'fields' => array(
                array(
                    'key' => 'field_5cf3bbb6d87c6',
                    'label' => 'Mapbox Token',
                    'name' => 'at_mapbox_token',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'agentfire-test',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));
	}
}