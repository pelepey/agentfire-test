<?php


namespace AgentFire\Plugin;


use AgentFire\Plugin\Test\Traits\Singleton;

class PinPostType
{
    use Singleton;

    /**
     * @return void
     */
    public function addHooks()
    {
        add_action( 'init', [$this, 'register'], 0 );
        add_action( 'rest_api_init', [$this, 'registerCoordinateRestMeta'], 0 );
    }

    /**
     * @return void
     */
    public function register() {
        $labels = array(
            'name'                  => _x( 'Pins', 'Post Type General Name', 'at' ),
            'singular_name'         => _x( 'Pin', 'Post Type Singular Name', 'at' ),
            'menu_name'             => __( 'Pins', 'at' ),
            'name_admin_bar'        => __( 'Pin', 'at' ),
            'archives'              => __( 'Item Archives', 'at' ),
            'attributes'            => __( 'Item Attributes', 'at' ),
            'parent_item_colon'     => __( 'Parent Item:', 'at' ),
            'all_items'             => __( 'All Items', 'at' ),
            'add_new_item'          => __( 'Add New Item', 'at' ),
            'add_new'               => __( 'Add New', 'at' ),
            'new_item'              => __( 'New Item', 'at' ),
            'edit_item'             => __( 'Edit Item', 'at' ),
            'update_item'           => __( 'Update Item', 'at' ),
            'view_item'             => __( 'View Item', 'at' ),
            'view_items'            => __( 'View Items', 'at' ),
            'search_items'          => __( 'Search Item', 'at' ),
            'not_found'             => __( 'Not found', 'at' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'at' ),
            'featured_image'        => __( 'Featured Image', 'at' ),
            'set_featured_image'    => __( 'Set featured image', 'at' ),
            'remove_featured_image' => __( 'Remove featured image', 'at' ),
            'use_featured_image'    => __( 'Use as featured image', 'at' ),
            'insert_into_item'      => __( 'Insert into item', 'at' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'at' ),
            'items_list'            => __( 'Items list', 'at' ),
            'items_list_navigation' => __( 'Items list navigation', 'at' ),
            'filter_items_list'     => __( 'Filter items list', 'at' ),
        );

        $capabilities = $this->getCaps();

        $args = array(
            'label'                 => __( 'Pin', 'at' ),
            'description'           => __( 'Pins left on the map', 'at' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'custom-fields' ),
            'taxonomies'            => array( 'pin_tag' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'rewrite'               => false,
            'capabilities'          => $capabilities,
            'show_in_rest'          => true,
            'rest_base'             => 'pins',
        );

        register_post_type( 'pin', $args );
    }

    /**
     * @return void
     */
    public function addPinCapsToRoles()
    {
        $rolesGrantedForAllCaps = ['administrator', 'editor'];
        $simpleUserRoles = ['author', 'contributor', 'subscriber'];

        $allCaps = $this->getCaps();
        $crudCapsForBaseRoles = $allCaps;

        unset($crudCapsForBaseRoles['edit_others_posts']);
        unset($crudCapsForBaseRoles['read_private_posts']);

        /**
         * @param $caps string[]
         * @param $roles string[]
         */
        $grantCapsForRoles = function ($caps, $roles) {
            foreach ($roles as $role) {
                $role = get_role($role);

                foreach ($caps as $cap) {
                    $role->add_cap($cap);
                }
            }
        };

        $grantCapsForRoles($allCaps, $rolesGrantedForAllCaps);
        $grantCapsForRoles($crudCapsForBaseRoles, $simpleUserRoles);
    }

    /**
     * @return array
     */
    public function getCaps()
    {
        return array(
            'edit_post'             => 'edit_pin',
            'read_post'             => 'read_pin',
            'delete_post'           => 'delete_pin',
            'edit_posts'            => 'edit_pins',
            'edit_others_posts'     => 'edit_others_pins',
            'publish_posts'         => 'publish_pins',
            'read_private_posts'    => 'read_private_pins',
        );
    }

    public function registerCoordinateRestMeta()
    {
        register_meta('post', 'lng', [
            'object_subtype' => 'pin',
            'description' => 'Longitude',
            'type' => 'number',
            'single' => true,
            'sanitize_callback' => [$this, 'sanitizeCoordinate'],
            'show_in_rest' => true
        ]);

        register_meta('post', 'lat', [
            'object_subtype' => 'pin',
            'description' => 'Latitude',
            'type' => 'number',
            'single' => true,
            'sanitize_callback' => [$this, 'sanitizeCoordinate'],
            'show_in_rest' => true
        ]);
    }

    public function sanitizeCoordinate($meta_value, $meta_key, $object_type)
    {
        return floatval($meta_value);
    }
}