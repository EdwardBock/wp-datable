<?php

/**
 * Plugin Name: Datable
 * Plugin URI: https://github.com/palasthotel/wp-datable
 * Description: Make your contents datable
 * Version: 1.0.0
 * Author: Palasthotel <rezeption@palasthotel.de>
 * Author URI: https://palasthotel.de
 * Text Domain: datable
 * Domain Path: /languages
 * Requires at least: 4.0
 * Tested up to: 5.2.4
 * License: http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @copyright Copyright (c) 2017, Palasthotel
 * @package Palasthotel\WordPress\Datable
 */

namespace Palasthotel\WordPress\Datable;

/**
 * @property Database database
 * @property WPQueryExtension $wpQueryExtension
 * @property PostTypes $postTypes
 * @property MetaBoxes $metaBoxes
 * @property PostTableColumns $postTableColumns
 * @property Settings settings
 * @property PostDatable postDatable
 */
class Plugin{

	const DOMAIN = "datable";

	const FILTER_POST_TYPE_SLUG = "datable_post_type_slug";
	const FILTER_POST_TYPE_ARGS = "datable_post_type_args";

	const OPTION_POST_TYPES = "datable_post_types";
	const OPTION_POST_TYPE_FOR_AUTO_GENERATION = "datable_post_type_for_auto_generation";
	const OPTION_POST_AUTO_GENERATE_NUMBER = "datable_auto_generate_number";

	public function __construct() {

		load_plugin_textdomain(
			self::DOMAIN,
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);

		require_once dirname(__FILE__)."/vendor/autoload.php";

		$this->database         = new Database();
		$this->postTypes        = new PostTypes($this);
		$this->wpQueryExtension = new WPQueryExtension($this);
		$this->metaBoxes        = new MetaBoxes($this);
		$this->postDatable      = new PostDatable($this);
		$this->postTableColumns = new PostTableColumns($this);
		$this->settings         = new Settings($this);

		// TODO: date migration

		// TODO:

		register_activation_hook( __FILE__, array( $this, "activation" ) );
		register_deactivation_hook( __FILE__, array( $this, "deactivation" ) );
	}

	public function activation(){
		$this->database->createTables();

		$this->postTypes->init();
		flush_rewrite_rules();
	}

	public function deactivation(){
		// schedules?
	}

}

new Plugin();
