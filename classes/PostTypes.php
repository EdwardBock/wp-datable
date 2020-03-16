<?php

namespace Palasthotel\WordPress\Datable;

/**
 * @property Plugin plugin
 */
class PostTypes {

	/**
	 * PostTypes constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct(Plugin $plugin) {
		$this->plugin = $plugin;
		add_action( 'init', array($this, 'init' ), 0 );
	}

	/**
	 * @return string
	 */
	function getDatableSlug(){
		return apply_filters(Plugin::FILTER_POST_TYPE_SLUG, 'datable');
	}

	/**
	 * post type arguments
	 * @return array
	 */
	private function getPostTypeArgs(){
		$args = array(
			'label'                 => __( 'Datable', Plugin::DOMAIN ),
			'description'           => __( 'Single datable', Plugin::DOMAIN ),
			'supports'              => array( 'title' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => false,
			'show_in_menu'          => false,
			'menu_position'         => 5,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		return apply_filters(Plugin::FILTER_POST_TYPE_ARGS, $args, $args);
	}

	/**
	 * Register Custom Post Type
	 */
	function init() {
		register_post_type( $this->getDatableSlug(), $this->getPostTypeArgs() );
	}

}