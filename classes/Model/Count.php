<?php


namespace Palasthotel\WordPress\Datable\Model;


class Count {

	/**
	 * @var int
	 */
	private $withoutPost;
	/**
	 * @var int
	 */
	private $withPost;

	private function __construct($withoutPosts, $withPosts) {
		$this->withoutPost = $withoutPosts;
		$this->withPost = $withPosts;
	}

	public function withPost(){
		return $this->withPost;
	}
	public function withoutPost(){
		return $this->withoutPost;
	}

	public function overall(){
		return $this->withoutPost+$this->withPost;
	}

	public static function parse($rows){
		$without = 0;
		$with = 0;
		foreach ($rows as $row){
			if($row->nullpost == 1){
				$without = intval($row->n);
			} else if($row->nullpost == 0){
				$with = intval($row->n);
			}
		}
		return new Count($without, $with);
	}
}