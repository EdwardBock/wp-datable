<?php


namespace Palasthotel\WordPress\Datable;


class DateQuery {

	/**
	 * DateQuery constructor.
	 *
	 * @param array $args
	 */
	public function __construct($args) {
		// if its not an array delete value and make it an array
		if(!is_array($args)) $args = array();

		$_args = array_merge(array(
			""
		),$args);
	}

	private function execute(){
		// TODO: Query over dates
		// setup post context with the_post()
		// setup datable context too
	}

	private function the_post(){
		// return and 
	}
}