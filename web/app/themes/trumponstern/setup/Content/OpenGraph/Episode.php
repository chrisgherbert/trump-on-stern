<?php

namespace Content\OpenGraph;
use bermanco\ExtendedTimberClasses\OpenGraph\Base;

class Episode extends Base {

	public function get_title(){

		$date = $this->post->date();

		$title = "Donald Trump on the Howard Stern Show ($date)";

		return array(
			'key' => 'og:title',
			'value' => $title
		);

	}

}