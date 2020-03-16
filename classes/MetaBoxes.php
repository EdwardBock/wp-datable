<?php

namespace Palasthotel\WordPress\Datable;

use Palasthotel\WordPress\Datable\Model\Datable;

/**
 * @property Plugin plugin
 */
class MetaBoxes {

	const DATES_BOX_ID = "datable_dates";

	/**
	 * MetaBoxes constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		$this->addSavePostAction();
	}

	/**
	 * add save_post action
	 */
	public function addSavePostAction(){
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	/**
	 * remove save_post action
	 */
	public function removeSavePostAction(){
		remove_action( 'save_post', array( $this, 'save_post' ) );
	}

	/**
	 * add dates meta box
	 */
	function add_meta_boxes() {
		add_meta_box(
			self::DATES_BOX_ID,
			__( 'Dates', Plugin::DOMAIN ),
			array( $this, 'render_dates' ),
			$this->plugin->settings->getPostTypes(),
			"normal",
			"high"
		);
	}

	/**
	 * render dates meta box
	 */
	function render_dates() {
		$db = $this->plugin->database;
		?>
		<ul>
			<?php
			$dates = $db->getDates( get_the_ID() );
			foreach ( $dates as $date ) {
				echo "<li>";
				$this->renderRow( $date );
				echo "</li>";
			}
			?>
		</ul>
		<input type="hidden" value="save" name="datable[action]"/>
		<div>
			<?php $this->renderRow(); ?>
		</div>
		<?php
	}

	/**
	 * @param null|Datable $datable
	 */
	public function renderRow( $datable = NULL ) {
		$start_date = ( $datable ) ? $datable->start_date : "";
		$id         = ( $datable && $datable->id ) ? $datable->id : "";
		$datable_id = ($datable && $datable->hasDatablePostId())? $datable->datable_id: "";
		$end_date   = ( $datable && $datable->end_date ) ? $datable->end_date : "";
		$start_time = ( $datable && $datable->start_time ) ? $datable->start_time : "";
		$end_time   = ( $datable && $datable->end_time ) ? $datable->end_time : "";
		?>
		<input type="hidden" name="datable[id][]" value="<?= $id; ?>"/>

		<label>Start <input type="date" name="datable[start_date][]"
		                    value="<?= $start_date; ?>"/></label>

		<label>End <input type="date" name="datable[end_date][]"
		                  value="<?= $end_date; ?>"/></label>

		<label>Start time <input type="time" name="datable[start_time][]"
		                         value="<?= $start_time; ?>"/></label>

		<label>End time <input type="time" name="datable[end_time][]"
		                       value="<?= $end_time; ?>"/></label>

		<?php
		if(!empty($datable_id))
			printf("<p><a href='%s'>Datable Post</a></p>", get_the_permalink($datable_id))
		?>
		<hr/>
		<?php
	}

	/**
	 * save_post hook
	 * @param $post_id
	 */
	function save_post( $post_id ) {
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( isset( $_POST["datable"] ) && is_array( $_POST["datable"] ) ) {
			$datable = $_POST["datable"];

			$start_dates = array_map( function ( $d ) {
				return sanitize_text_field( $d );
			}, $datable["start_date"] );
			$end_dates   = array_map( function ( $d ) {
				return sanitize_text_field( $d );
			}, $datable["end_date"] );
			$start_times = array_map( function ( $d ) {
				return sanitize_text_field( $d );
			}, $datable["start_time"] );
			$end_times   = array_map( function ( $d ) {
				return sanitize_text_field( $d );
			}, $datable["end_time"] );
			$ids         = array_map( function ( $d ) {
				return intval( $d );
			}, $datable["id"] );

			foreach ( $start_dates as $index => $start_date ) {
				if ( empty( $start_date ) ) {
					continue;
				}
				$end_date   = $end_dates[ $index ];
				$start_time = $start_times[ $index ];
				$end_time   = $end_times[ $index ];
				$id         = $ids[ $index ];
				$this->saveDate( $id, $post_id, $start_date, $end_date, $start_time, $end_time );
			}

		}

	}

	/**
	 * @param int|null $id
	 * @param int $content_id
	 * @param string $start_date
	 * @param string $end_date
	 * @param string $start_time
	 * @param string $end_time
	 *
	 * @return false|int
	 */
	private function saveDate( $id, $content_id, $start_date, $end_date, $start_time, $end_time ) {
		$datable             = new Datable( $start_date );
		$datable->content_id = $content_id;
		$datable->end_date   = ( empty( $end_date ) ) ? NULL : $end_date;
		$datable->start_time = ( empty( $start_time ) ) ? NULL : $start_time;
		$datable->end_time   = ( empty( $end_time ) ) ? NULL : $end_time;
		$datable->id         = ( empty( $id ) ) ? NULL : $id;

		return $this->plugin->database->insertOrUpdate( $datable );
	}


}