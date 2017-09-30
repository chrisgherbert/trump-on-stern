<?php
/**
 * The base Post class for our site
 */

namespace Content;

use bermanco\ExtendedTimberClasses;

class Post extends ExtendedTimberClasses\Post {

	public $PostClass = '\Content\Post';

	public function get_open_graph_data(){
		$open_graph = new OpenGraph\Episode($this);
		$open_graph->set_logo_url(get_template_directory_uri() . '/assets/img/trump-on-stern-og-image.png');
		return $open_graph->get_data();
	}

}