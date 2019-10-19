<?php


namespace Palasthotel\WordPress\Datable;


use Palasthotel\WordPress\Datable\Model\Datable;

/**
 * @property Plugin plugin
 */
class MetaBoxes {

	const COLLECTION_BOX_ID = "datable_collection_dates";
	const DATE_BOX_ID = "datable_date";

	public function __construct(Plugin $plugin) {
		$this->plugin = $plugin;

		// TODO: add single date meta box to every post type that user wants to

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action('save_post', array($this, 'save_post'));
	}

	function add_meta_boxes() {
		add_meta_box(
			self::COLLECTION_BOX_ID,
			__( 'Dates', Plugin::DOMAIN ),
			array( $this, 'render_collection' ),
			$this->plugin->post_types->getDatableSlug(),
			"normal",
			"high"
		);
		add_meta_box(
			self::DATE_BOX_ID,
			__('Date', Plugin::DOMAIN),
			array($this, 'render_date'),
			$this->plugin->settings->getPostTypes(),
			'side',
			'high'
		);
	}



	function render_collection(){
		$db = $this->plugin->database;
		?>
		<ul>
			<?php
			$dates = $db->getDatesByCollection(get_the_ID());
			foreach ($dates as $date){
				echo "<li>";
				$this->renderRow($date);
				echo "</li>";
			}
			?>
		</ul>
		<input type="hidden" value="save" name="datable[action]" />
		<div>
			<?php $this->renderRow(); ?>
		</div>
		<?php
	}

	/**
	 * @param null|Datable $datable
	 */
	public function renderRow($datable = null){
		$start_date = ($datable)? $datable->start_date:"";
		$id = ($datable && $datable->id)? $datable->id: "";
		$end_date = ($datable && $datable->end_date)? $datable->end_date:"";
		$start_time = ($datable && $datable->start_time)? $datable->start_time:"";
		$end_time = ($datable && $datable->end_time)? $datable->end_time:"";
		?>
			<input type="hidden" name="datable[id][]" value="<?= $id; ?>" />

			<label>Start <input type="date" name="datable[start_date][]" value="<?= $start_date; ?>" /></label>

			<label>End <input type="date" name="datable[end_date][]" value="<?= $end_date; ?>" /></label>

			<label>Start time <input type="time" name="datable[start_time][]" value="<?= $start_time; ?>" /></label>

			<label>End time <input type="time" name="datable[end_time][]" value="<?= $end_time; ?>" /></label>
			<hr />
		<?php
	}

	function render_date(){
		$datable = $this->plugin->database->getContentDate(get_the_ID());
		$start_date = ($datable)? $datable->start_date:"";
		$id = ($datable && $datable->id)? $datable->id: "";
		$end_date = ($datable && $datable->end_date)? $datable->end_date:"";
		$start_time = ($datable && $datable->start_time)? $datable->start_time:"";
		$end_time = ($datable && $datable->end_time)? $datable->end_time:"";
		?>
		<input type="hidden" name="datable[id]" value="<?php echo $id; ?>" />
		<label>Start<br><input type="date" name="datable[start_date]" value="<?= $start_date; ?>" /></label><br>

		<label>End<br><input type="date" name="datable[end_date]" value="<?= $end_date; ?>" /></label><br>

		<label>Start time<br><input type="time" name="datable[start_time]" value="<?= $start_time; ?>" /></label><br>

		<label>End time<br><input type="time" name="datable[end_time]" value="<?= $end_time; ?>" /></label><br>
		<?php
	}

	function save_post($post_id){
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if(isset($_POST["datable"]) && is_array($_POST["datable"])){
			$datable = $_POST["datable"];
			$start_dates = array_map(function($d){ return sanitize_text_field($d); }, $datable["start_date"]);
			$end_dates = array_map(function($d){ return sanitize_text_field($d); }, $datable["end_date"]);
			$start_times = array_map(function($d){ return sanitize_text_field($d); }, $datable["start_time"]);
			$end_times = array_map(function($d){ return sanitize_text_field($d); }, $datable["end_time"]);
			$ids = array_map(function($d){ return intval($d); }, $datable["id"]);

			foreach ($start_dates as $index => $start_date){
				if(empty($start_date)) continue;
				$end_date = $end_dates[$index];
				$start_time = $start_times[$index];
				$end_time = $end_times[$index];
				$id = $ids[$index];

				$datable = new Datable($start_date);
				$datable->collection_id = $post_id;
				$datable->end_date = (empty($end_date))? null: $end_date;
				$datable->start_time = (empty($start_time))? null: $start_time;
				$datable->end_time = (empty($end_time))? null: $end_time;
				$datable->id = (empty($id))? null: $id;
				$result = $this->plugin->database->insertOrUpdate($datable);
			}


		}


	}


}