<?php


namespace Palasthotel\WordPress\Datable\Model;


/**
 * @property null|int id
 * @property string start_date
 * @property null|string end_date
 * @property null|string start_time
 * @property null|string end_time
 * @property null|int collection_id
 * @property null|int content_id
 */
class Datable {

	private static $timezone;

	public static function timezone() {
		if ( self::$timezone == NULL ) {
			$tz = get_option( "timezone_string" );
			if ( ! empty( $tz ) && $tz !== false ) {
				self::$timezone = new \DateTimeZone( $tz );
			} else {
				self::$timezone = new \DateTimeZone( date_default_timezone_get() );
			}
		}

		return self::$timezone;
	}

	/**
	 * @param {object} $row
	 *
	 * @return Datable
	 */
	public static function parse( $row ) {
		$d                = new Datable( $row->start_date );
		$d->id            = $row->id;
		$d->end_date      = $row->end_date;
		$d->start_time    = $row->start_time;
		$d->end_time      = $row->end_time;
		$d->collection_id = $row->collection_id;
		$d->content_id    = $row->content_id;

		return $d;
	}

	/**
	 * @var int
	 */
	private $collection_id;

	/**
	 * Datable constructor.
	 *
	 * @param null|string $start_date
	 */
	public function __construct( $start_date = NULL ) {

		if ( $start_date == NULL ) {
			try {
				$now = new \DateTime();
				$now->setTimezone( self::timezone() );
				$this->start_date = $now->format( "Y-m-d" );
			} catch ( \Exception $e ) {
				\error_log( $e->getMessage() );
				$this->start_date = date( "Y-m-d" );
			}
		} else {
			$this->start_date = $start_date;
		}

		$this->id            = NULL;
		$this->collection_id = NULL;
		$this->content_id    = NULL;
		$this->end_date      = NULL;
		$this->start_time    = NULL;
		$this->end_time      = NULL;

	}

	public function hasCollection() {
		return $this->collection_id !== NULL;
	}

	public function hasContent() {
		return $this->content_id !== NULL;
	}

	/**
	 * @return bool
	 */
	public function hasStartTime() {
		return $this->start_time !== NULL;
	}

	/**
	 * @return bool
	 */
	public function hasEndTime() {
		return $this->end_time !== NULL;
	}

	/**
	 * @return bool
	 */
	public function hasTimeRange() {
		return $this->start_time !== NULL && $this->end_time !== NULL && $this->start_time != $this->end_time;
	}

	/**
	 * @return bool
	 */
	public function isMultipleDays() {
		return $this->end_date !== NULL && $this->end_date != $this->start_date;
	}

	/**
	 * @return bool
	 */
	public function isSingleDay() {
		return $this->end_date === NULL || $this->start_date == $this->end_date;
	}
}