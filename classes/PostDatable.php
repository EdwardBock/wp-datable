<?php


namespace Palasthotel\WordPress\Datable;


/**
 * @property Plugin plugin
 */
class PostDatable {

	/**
	 * PostDatable constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct($plugin) {
		$this->plugin = $plugin;

		// important that this happens after MetaBox->post_save
		$this->addSavePostAction();
	}

	/**
	 * add save_post action
	 */
	private function addSavePostAction(){
		add_action('save_post', array($this, 'save_post'), 20);
	}

	/**
	 * remove save_post action
	 */
	private function removeSavePostAction(){
		remove_action('save_post', array($this, 'save_post'), 20);
	}

	/**
	 * save_post hook
	 * @param $post_id
	 */
	public function save_post($post_id){
		$dates = $this->plugin->database->getDates($post_id);
		$contentTitle = get_the_title($post_id);
		foreach ($dates as $date){
			if(!$date->hasDatablePostId()){
				$this->plugin->metaBoxes->removeSavePostAction();
				$this->removeSavePostAction();
				$datable_id = wp_insert_post(array(
					"post_type" => $this->plugin->postTypes->getDatableSlug(),
					"post_title" => $contentTitle." ".$date->id,
					"post_status" => "publish",
				));
				wp_reset_postdata();
				$this->addSavePostAction();
				$this->plugin->metaBoxes->addSavePostAction();
				if($datable_id) $this->plugin->database->updateDatablePostId($date->id, $datable_id);
			}
		}
	}


}