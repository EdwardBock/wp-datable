<?php


namespace Palasthotel\WordPress\Datable;


use Palasthotel\WordPress\Datable\Model\Count;
use Palasthotel\WordPress\Datable\Model\Datable;

/**
 * @property string table
 * @property \wpdb wpdb
 */
class Database {

	/**
	 * Database constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->table = $this->wpdb->prefix . "datable_dates";
	}

	/**
	 * @param int $datable_id
	 *
	 * @return false|int
	 */
	public function deleteDate($datable_id){
		return $this->wpdb->delete(
			$this->table,
			array(
				"datable_id" => $datable_id
			),
			array("%d")
		);
	}

	/**
	 * @param int $content_id
	 *
	 * @return false|int
	 */
	public function deleteAll($content_id){
		return $this->wpdb->delete(
			$this->table,
			array(
				"content_id" => $content_id,
			),
			array("%d")
		);
	}

	/**
	 * @param Datable $datable
	 *
	 * @return false|int date id
	 */
	public function addDate( $datable ) {
		return $this->wpdb->insert(
			$this->table,
			array(
				"content_id"    => $datable->content_id,
				"datable_id"    => $datable->datable_id,
				"start_date"    => $datable->start_date,
				"end_date"      => $datable->end_date,
				"start_time"    => $datable->start_time,
				"end_time"      => $datable->end_time,
			)
		);
	}

	/**
	 * @param int $id
	 * @param int $datablePostId
	 *
	 * @return false|int
	 */
	public function updateDatablePostId($id, $datablePostId){
		return $this->wpdb->update(
			$this->table,
			array(
				"datable_id" => $datablePostId
			),
			array(
				"id" => $id,
			),
			array("%d"),
			array("%d")
		);
	}

	/**
	 * @param Datable $datable
	 *
	 * @return false|int
	 */
	public function insertOrUpdate( $datable ) {
		if ( $datable->id ) {
			// update if already exists
			return $this->wpdb->update(
				$this->table,
				array(
					"content_id"    => $datable->content_id,
					"datable_id"    => $datable->datable_id,
					"start_date"    => $datable->start_date,
					"end_date"      => $datable->end_date,
					"start_time"    => $datable->start_time,
					"end_time"      => $datable->end_time,
				),
				array(
					"id" => $datable->id,
				)
			);
		}

		return $this->addDate( $datable );
	}

	/**
	 * @param int $content_id
	 *
	 * @return Datable[]|bool
	 */
	public function getDates( $content_id ) {
		$results = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT id, content_id, datable_id, start_date, end_date, start_time, end_time 
					FROM {$this->table} 
					WHERE content_id = %d 
					ORDER BY start_date ASC, ISNULL(start_time), start_time ASC",
				$content_id
			)
		);

		return $this->mapRows( $results );
	}

	/**
	 * @param int $content_id
	 *
	 * @return bool|\Palasthotel\WordPress\Datable\Model\Datable
	 */
	public function getFirstDate( $content_id ) {
		$results = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT id, content_id, datable_id, start_date, end_date, start_time, end_time 
					FROM {$this->table} 
					WHERE content_id = %d 
					ORDER BY start_date ASC, ISNULL(start_time), start_time ASC LIMIT 1",
				$content_id
			)
		);

		return $this->parseRow( $results );
	}

	/**
	 * @param int $content_id
	 *
	 * @return bool|\Palasthotel\WordPress\Datable\Model\Datable
	 */
	public function getLastDate( $content_id ) {
		$results = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT id, content_id, datable_id, start_date, end_date, start_time, end_time 
					FROM {$this->table} 
					WHERE content_id = %d 
					ORDER BY start_date DESC, start_time DESC LIMIT 1",
				$content_id
			)
		);

		return $this->parseRow( $results );
	}

	/**
	 * @param int $content_id
	 *
	 * @return bool|\Palasthotel\WordPress\Datable\Model\Count
	 */
	public function countDates( $content_id ) {
		$result = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT count(id) as n, isNULL({$this->table}.content_id) as nullpost
					FROM {$this->table} 
					WHERE content_id = %d GROUP BY nullpost",
				$content_id
			)
		);

		return ( is_countable( $result ) ) ? Count::parse( $result ) : false;
	}

	/**
	 * @param int $datable_id
	 *
	 * @return Datable|false
	 */
	public function getDate( $datable_id ) {
		$results = $this->wpdb->get_row( $this->wpdb->prepare(
			"SELECT id, content_id, datable_id, start_date, end_date, start_time, end_time
				FROM {$this->table} WHERE datable_id = %d LIMIT 1",
			$datable_id
		) );

		return $this->parseRow( $results );
	}

	/**
	 * @param object $row
	 *
	 * @return bool|\Palasthotel\WordPress\Datable\Model\Datable
	 */
	private function parseRow( $row ) {
		if ( is_object( $row ) ) {
			return Datable::parse( $row );
		}

		return false;
	}

	/**
	 * @param array $rows
	 *
	 * @return array|bool
	 */
	private function mapRows( $rows ) {
		if ( is_countable( $rows ) ) {
			return array_map( function ( $row ) {
				return Datable::parse( $row );
			}, $rows );
		}

		return false;
	}


	/**
	 * create the tables if not exist
	 */
	function createTables() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$postTable = $this->wpdb->posts;

		\dbDelta( "CREATE TABLE IF NOT EXISTS {$this->table}
		(
		 id bigint(20) unsigned auto_increment,
		 
		 content_id bigint(20) unsigned NOT NULL COMMENT 'post id of content',
		 datable_id bigint(20) unsigned DEFAULT NULL COMMENT 'post id of datable',
		 
		 start_date DATE NOT NULL,
		 end_date DATE DEFAULT NULL,
		 start_time TIME DEFAULT NULL,
		 end_time TIME DEFAULT NULL,
		 
		 primary key (id),
		 
		 key (content_id),
		 key (datable_id),
		 	 
		 key (start_date),
		 key (end_date),
		 key (start_time),
		 key (end_time),
		 key the_date( start_date, end_date, start_time, end_time ),
		 
		 CONSTRAINT `datable_content` FOREIGN KEY (`content_id`) 
		     REFERENCES {$postTable} ( ID ) ON DELETE CASCADE
		 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;" );


	}

}