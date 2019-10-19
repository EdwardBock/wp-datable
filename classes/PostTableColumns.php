<?php


namespace Palasthotel\WordPress\Datable;


use Palasthotel\WordPress\Datable\Model\Datable;

class PostTableColumns {
	public function __construct(Plugin $plugin) {
		$this->plugin = $plugin;
		add_filter( 'manage_posts_columns' , array($this, 'add_column') );
		add_action( 'manage_posts_custom_column' , array($this,'custom_columns'), 10, 2 );
	}
	public function add_column($columns){

		if(get_post_type() !== $this->plugin->post_types->getDatableSlug()) return $columns;
		$date_label = $columns["date"];
		unset($columns["date"]);
		$columns["info"] = "Info";
		$columns["first_date"] = "First Date";
		$columns["last_date"] = "Last Date";
		$columns["date"] = $date_label;
		return $columns;
	}
	public function custom_columns($column, $collection_id){
		if($column == 'first_date'){
			$first = $this->plugin->database->getFirstDateOfCollection($collection_id);
			if($first) $this->renderDateDetails($first);
		} else	if($column == 'last_date'){
			$last = $this->plugin->database->getLastDateOfCollection($collection_id);
			if($last) $this->renderDateDetails($last);
		} else if($column == "info"){
			$count = $this->plugin->database->countCollection($collection_id);
			if($count){
				echo "Dates: ".$count->overall();
				echo "<span class='description'>";
				echo "<br>With content: ".$count->withPost();
				echo "<br> Without content: ".$count->withoutPost();
				echo "</span>";
			}
		}
	}

	/**
	 * @param Datable $date
	 */
	private function renderDateDetails($date){

		if($date->isSingleDay()){
			echo $date->start_date;
			if($date->hasTimeRange()){
				echo "<br>";
				echo $date->start_time." - ".$date->end_time;
			} else if($date->hasStartTime()){
				echo " - ".$date->start_time;
			}
		} else {
			echo $date->start_date;
			if($date->hasStartTime()){
				echo " - ".$date->start_time;
			}
			echo "<br>";
			echo $date->end_date;
			if($date->hasEndTime()){
				echo " - ".$date->end_time;
			}
		}
	}
}