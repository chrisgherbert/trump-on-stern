<?php

namespace Content;

class Episode extends \bermanco\ExtendedTimberClasses\Post {

	public $post_type = 'episode';

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