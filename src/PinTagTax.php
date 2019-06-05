<?php


namespace AgentFire\Plugin;


use AgentFire\Plugin\Test\Traits\Singleton;

class PinTagTax
{

    use Singleton;

    /**
     * @return void
     */
    public function register() {

        $labels = array(
            'name'                       => _x( 'Tags', 'Taxonomy General Name', 'at' ),
            'singular_name'              => _x( 'Tag', 'Taxonomy Singular Name', 'at' ),
            'menu_name'                  => __( 'Tag', 'at' ),
            'all_items'                  => __( 'All Items', 'at' ),
            'parent_item'                => __( 'Parent Item', 'at' ),
            'parent_item_colon'          => __( 'Parent Item:', 'at' ),
            'new_item_name'              => __( 'New Item Name', 'at' ),
            'add_new_item'               => __( 'Add New Item', 'at' ),
            'edit_item'                  => __( 'Edit Item', 'at' ),
            'update_item'                => __( 'Update Item', 'at' ),
            'view_item'                  => __( 'View Item', 'at' ),
            'separate_items_with_commas' => __( 'Separate items with commas', 'at' ),
            'add_or_remove_items'        => __( 'Add or remove items', 'at' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'at' ),
            'popular_items'              => __( 'Popular Items', 'at' ),
            'search_items'               => __( 'Search Items', 'at' ),
            'not_found'                  => __( 'Not Found', 'at' ),
            'no_terms'                   => __( 'No items', 'at' ),
            'items_list'                 => __( 'Items list', 'at' ),
            'items_list_navigation'      => __( 'Items list navigation', 'at' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => false,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'show_in_rest'               => true,
            'rest_base'                  => 'pin-tags',
        );
        register_taxonomy( 'pin_tag', array( 'pin' ), $args );
    }
}