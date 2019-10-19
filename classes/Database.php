<?php


namespace Palasthotel\WordPress\Datable;


use Palasthotel\WordPress\Datable\Model\Count;
use Palasthotel\WordPress\Datable\Model\Datable;

class Database {

	/**
	 * @return \wpdb
	 */
	public function wpdb() {
		global $wpdb;

		return $wpdb;
	}

	/**
	 * @return string
	 */
	public function table() {
		return $this->wpdb()->prefix . "datable_dates";
	}

	/**
	 * @param Datable $datable
	 *
	 * @return false|int date id
	 */
	public function addDate( $datable ) {
		return $this->wpdb()->insert(
			$this->table(),
			array(
				"collection_id" => $datable->collection_id,
				"content_id"    => $datable->content_id,
				"start_date"    => $datable->start_date,
				"end_date"      => $datable->end_date,
				"start_time"    => $datable->start_time,
				"end_time"      => $datable->end_time,
			)
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
			return $this->wpdb()->update(
				$this->table(),
				array(
					"collection_id" => $datable->collection_id,
					"content_id"    => $datable->content_id,
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
	 * @param $collection_id
	 *
	 * @return Datable[]|bool
	 */
	public function getDatesByCollection( $collection_id ) {
		$results = $this->wpdb()->get_results(
			$this->wpdb()->prepare(
				"SELECT id, collection_id, content_id, start_date, end_date, start_time, end_time 
					FROM {$this->table()} 
					WHERE collection_id = %d 
					ORDER BY start_date ASC, ISNULL(start_time), start_time ASC",
				$collection_id
			)
		);

		return $this->mapRows( $results );
	}

	/**
	 * @param $collection_id
	 *
	 * @return bool|\Palasthotel\WordPress\Datable\Model\Datable
	 */
	public function getFirstDateOfCollection( $collection_id ) {
		$results = $this->wpdb()->get_row(
			$this->wpdb()->prepare(
				"SELECT id, collection_id, content_id, start_date, end_date, start_time, end_time 
					FROM {$this->table()} 
					WHERE collection_id = %d 
					ORDER BY start_date ASC, ISNULL(start_time), start_time ASC LIMIT 1",
				$collection_id
			)
		);

		return $this->parseRow( $results );
	}

	/**
	 * @param $collection_id
	 *
	 * @return bool|\Palasthotel\WordPress\Datable\Model\Datable
	 */
	public function getLastDateOfCollection( $collection_id ) {
		$results = $this->wpdb()->get_row(
			$this->wpdb()->prepare(
				"SELECT id, collection_id, content_id, start_date, end_date, start_time, end_time 
					FROM {$this->table()} 
					WHERE collection_id = %d 
					ORDER BY start_date DESC, start_time DESC LIMIT 1",
				$collection_id
			)
		);

		return $this->parseRow( $results );
	}

	/**
	 * @param $collection_id
	 *
	 * @return bool|\Palasthotel\WordPress\Datable\Model\Count
	 */
	public function countCollection( $collection_id ) {
		$result = $this->wpdb()->get_results(
			$this->wpdb()->prepare(
				"SELECT count(id) as n, isNULL({$this->table()}.content_id) as nullpost
					FROM {$this->table()} 
					WHERE collection_id = %d GROUP BY nullpost",
				$collection_id
			)
		);

		return ( is_countable( $result ) ) ? Count::parse( $result ) : false;
	}

	/**
	 * @param int $content_id
	 *
	 * @return Datable|false
	 */
	public function getContentDate( $content_id ) {
		$results = $this->wpdb()->get_row( $this->wpdb()->prepare(
			"SELECT id, collection_id, content_id, start_date, end_date, start_time, end_time
				FROM {$this->table()} WHERE content_id = %d LIMIT 1",
			$content_id
		) );

		return $this->parseRow( $results );
	}

	private function parseRow( $row ) {
		if ( is_object( $row ) ) {
			return Datable::parse( $row );
		}

		return false;
	}

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

		\dbDelta( "CREATE TABLE IF NOT EXISTS {$this->table()}
		(
		 id bigint(20) unsigned auto_increment,
		 
		 collection_id bigint(20) unsigned DEFAULT NULL COMMENT 'post id of collection',
		 content_id bigint(20) unsigned DEFAULT NULL COMMENT 'post id of content',
		 
		 start_date DATE NOT NULL,
		 end_date DATE DEFAULT NULL,
		 start_time TIME DEFAULT NULL,
		 end_time TIME NULL,
		 
		 primary key (id),
		 
		 key (collection_id),
		 key (content_id),
		 	 
		 key (start_date),
		 key (end_date),
		 key (start_time),
		 key (end_time),
		 key the_date (start_date, end_date, start_time, end_time)
		 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;" );


	}

}