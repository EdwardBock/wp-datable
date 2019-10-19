<?php


namespace Palasthotel\WordPress\Datable;


class PostTypes {

	public function __construct(Plugin $plugin) {
		add_action( 'init', array($this, 'collection'), 0 );
	}

	function getDatableSlug(){
		return 'datable';
	}

	// Register Custom Post Type
	function collection() {

		$labels = array(
			'name'                  => _x( 'Datables', 'Post Type General Name', Plugin::DOMAIN ),
			'singular_name'         => _x( 'Datable', 'Post Type Singular Name', Plugin::DOMAIN ),
			'menu_name'             => __( 'Datables', Plugin::DOMAIN ),
			'name_admin_bar'        => __( 'Datable', Plugin::DOMAIN ),
			'archives'              => __( 'Item Archives', Plugin::DOMAIN ),
			'attributes'            => __( 'Item Attributes', Plugin::DOMAIN ),
			'parent_item_colon'     => __( 'Parent Item:', Plugin::DOMAIN ),
			'all_items'             => __( 'All Items', Plugin::DOMAIN ),
			'add_new_item'          => __( 'Add New Datable', Plugin::DOMAIN ),
			'add_new'               => __( 'Add New', Plugin::DOMAIN ),
			'new_item'              => __( 'New Item', Plugin::DOMAIN ),
			'edit_item'             => __( 'Edit Datable', Plugin::DOMAIN ),
			'update_item'           => __( 'Update Item', Plugin::DOMAIN ),
			'view_item'             => __( 'View Item', Plugin::DOMAIN ),
			'view_items'            => __( 'View Items', Plugin::DOMAIN ),
			'search_items'          => __( 'Search Item', Plugin::DOMAIN ),
			'not_found'             => __( 'Not found', Plugin::DOMAIN ),
			'not_found_in_trash'    => __( 'Not found in Trash', Plugin::DOMAIN ),
			'featured_image'        => __( 'Featured Image', Plugin::DOMAIN ),
			'set_featured_image'    => __( 'Set featured image', Plugin::DOMAIN ),
			'remove_featured_image' => __( 'Remove featured image', Plugin::DOMAIN ),
			'use_featured_image'    => __( 'Use as featured image', Plugin::DOMAIN ),
			'insert_into_item'      => __( 'Insert into item', Plugin::DOMAIN ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', Plugin::DOMAIN ),
			'items_list'            => __( 'Items list', Plugin::DOMAIN ),
			'items_list_navigation' => __( 'Items list navigation', Plugin::DOMAIN ),
			'filter_items_list'     => __( 'Filter items list', Plugin::DOMAIN ),
		);
		$args = array(
			'label'                 => __( 'Datable', Plugin::DOMAIN ),
			'description'           => __( 'Collections of datables', Plugin::DOMAIN ),
			'labels'                => $labels,
			'supports'              => array( 'title' , 'custom-fields'),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-calendar-alt',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type( $this->getDatableSlug(), apply_filters(Plugin::FILTER_POST_TYPE, $args, $args) );

	}

}