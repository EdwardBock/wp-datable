<?php


namespace Palasthotel\WordPress\Datable;


/**
 * Class WPQueryExtension
 *
 * @property Plugin plugin
 * @package Palasthotel\WordPress\Datable
 */
class WPQueryExtension {

	/**
	 * WPQueryExtension constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct(Plugin $plugin) {
		$this->plugin = $plugin;
		add_filter( 'posts_join', array( $this, 'posts_join' ), 10, 2 );
		add_filter( 'posts_join', array( $this, 'posts_join' ), 10, 2 );
		add_filter( 'posts_orderby', array( $this, 'posts_orderby' ), 10, 2 );
	}

	/**
	 * @param \WP_Query $wp_query
	 *
	 * @return bool
	 */
	public function hasDatable($wp_query){
		$datable = $wp_query->get("datable");
		return $datable && is_array($datable);
	}

	/**
	 * @param string $join
	 * @param \WP_Query $wp_query
	 *
	 * @return string
	 */
	public function posts_join($join, $wp_query){

		if(!$this->hasDatable($wp_query)) return $join;

		$db = $this->plugin->database;
		$join .= " LEFT JOIN {$db->tableContents()} ON ({$db->wpdb()->posts}.ID = {$db->tableContents()}.post_id) ";
		$join .= " LEFT JOIN {$db->tableDates()} ON ({$db->tableContents()}.date_id = {$db->tableDates()}.id)";

		return $join;
	}

	/**
	 *
	 * @param  string $where
	 *
	 * @param \WP_Query $wp_query
	 *
	 * @return string
	 */
	function posts_where( $where, $wp_query ) {

		if ( !$this->hasDatable($wp_query) ) return $where;

		$db = $this->plugin->database;
		$where .= " AND ( {$db->tableDates()}.start_date IS NOT NULL AND {$db->tableDates()}.start_date >= now() )";

		return $where;
	}

	/**
	 * @param string $orderby
	 * @param \WP_Query $wp_query
	 *
	 * @return string
	 */
	function posts_orderby($orderby, $wp_query){

		if(!$this->hasDatable($wp_query)) return $orderby;

		$db = $this->plugin->database;
		return " {$db->tableDates()}.start_date ASC,".
		       " ISNULL({$db->tableDates()}.start_time), {$db->tableDates()}.start_time ASC, "
		       .$orderby;
	}
}