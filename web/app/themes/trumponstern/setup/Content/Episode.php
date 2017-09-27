<?php

namespace Content;

class Episode extends \bermanco\ExtendedTimberClasses\Post {

	public $post_type = 'episode';

	public function get_duration(){

		if (!$attachment_id = $this->meta('mp3_id')){
			return false;
		}

		$path = get_attached_file($attachment_id);

		if (!$path){
			return false;
		}

		try {
			$mp3 = new \Zend_Media_Mpeg_Abs($path);
			return gmdate('i:s', $mp3->getLengthEstimate());
		} catch (\Exception $e) {
			error_log($e->getMessage());
		}

	}

	public function get_bookmarks(){

		$bookmarks = $this->meta('bookmarks');

		if ($bookmarks){

			return array_map(function($item){

				$item['seconds'] = $this->convert_timestamp_to_seconds($item['time']);

				return $item;

			}, $bookmarks);

		}

	}

	protected function convert_timestamp_to_seconds($timestamp){

		$parts = explode(':', $timestamp);

		$minutes = $parts[0];
		$seconds = $parts[1];

		return ($minutes * 60) + $seconds;

	}

	////////////
	// Static //
	////////////

	public static function get_grouped_by_year(){

		$episodes = \Timber::get_posts(array(
			'post_type' => 'episode',
			'orderby' => 'date',
			'order' => 'ASC'
		), get_called_class());

		if (!$episodes){
			return false;
		}

		$years = array();

		foreach ($episodes as $episode){

			$year = $episode->date('Y');

			$years[$year][] = $episode;

		}

		return $years;

	}

}