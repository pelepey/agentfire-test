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

	public function acfInitPage() {
		if ( function_exists('\acf_add_options_page') ) {
			$option_page = \acf_add_options_page(array(
				'page_title' 	=> 'AgentFire Test',
				'menu_title' 	=> 'AgentFire Test',
				'menu_slug' 	=> 'agentfire-test',
			));

			return $option_page;
		}
	}

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
                    'key' => 'field_5cf3bb2ed87c3',
                    'label' => 'Map page slug',
                    'name' => 'at_map_page_slug',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'map',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
                array(
                    'key' => 'field_5cf3bb5ad87c4',
                    'label' => 'Page template',
                    'name' => 'at_map_page_template',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => $templates,
                    'default_value' => array(
                    ),
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 0,
                    'return_format' => 'value',
                    'ajax' => 0,
                    'placeholder' => '',
                ),
                array(
                    'key' => 'field_5cf3bb9dd87c5',
                    'label' => 'Available tags',
                    'name' => 'at_available_tags',
                    'type' => 'textarea',
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
                    'maxlength' => '',
                    'rows' => '',
                    'new_lines' => '',
                ),
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