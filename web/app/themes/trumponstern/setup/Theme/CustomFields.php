<?php
/**
 * This class defines custom fields for our post types
 */

namespace Theme;

class CustomFields {

	/**
	 * Optional custom field prefix
	 * @var string
	 */
	protected $prefix;
	/**
	 * Array of box names - correspond to method names
	 * @var array
	 */
	protected $boxes = array(
		'tags',
		'episode',
		'episode_bookmarks',
		'episode_external_links'
	);

	/**
	 * Constructor - should fire off all metabox creation when called.
	 */
	public function __construct(){
		$this->add_show_on_filters();
		$this->load_boxes();
	}

	///////////////
	// Metaboxes //
	///////////////

	/**
	 * Loop through our boxes property and invoke the corresponding
	 * function that will define its metabox(es)
	 */
	protected function load_boxes(){
		if ($this->boxes && !empty($this->boxes)){
			foreach ($this->boxes as $box) {
				add_action('cmb2_admin_init', array($this, $box));
			}
		}
	}

	public function tags(){

		$cmb2 = new_cmb2_box(array(
			'id' => 'tags',
			'title' => 'Extended Info',
			'object_types' => array('term'),
			'taxonomies' => array('post_tag')
		));

		$cmb2->add_field(array(
			'id' => 'thumbnail',
			'name' => 'Thumbnail',
			'type' => 'file',
			'options' => array(
				'url' => false
			)
		));

	}

	public function episode(){

		$cmb2 = new_cmb2_box(array(
			'id' => 'episode',
			'title' => 'Extended Information',
			'object_types' => array('episode')
		));

		$cmb2->add_field(array(
			'id' => 'mp3',
			'name' => 'MP3 File',
			'type' => 'file',
			'options' => array(
				'url' => false
			)
		));

	}

	public function episode_bookmarks(){

		$cmb2 = new_cmb2_box(array(
			'id' => 'episode_bookmarks',
			'title' => 'Bookmarks',
			'object_types' => array('episode')
		));

		$group_id = $cmb2->add_field(array(
			'id' => 'bookmarks',
			'name' => 'Bookmarks',
			'type' => 'group',
			'options' => array(
				'sortable' => true
			)
		));

		$cmb2->add_group_field($group_id, array(
			'id' => 'time',
			'name' => 'Time',
			'type' => 'text',
			'desc' => 'Formatted like "03:22"'
		));

		$cmb2->add_group_field($group_id, array(
			'id' => 'description',
			'name' => 'Description',
			'type' => 'textarea_small'
		));

	}

	public function episode_external_links(){

		$cmb2  = new_cmb2_box(array(
			'id' => 'episode_external_links',
			'title' => 'External Links',
			'object_types' => array('episode')
		));

		$group_id = $cmb2->add_field(array(
			'id' => 'external_links',
			'name' => 'Links',
			'type' => 'group',
			'options' => array(
				'sortable' => true
			)
		));

		$cmb2->add_group_field($group_id, array(
			'id' => 'label',
			'name' => 'Label',
			'type' => 'textarea_small'
		));

		$cmb2->add_group_field($group_id, array(
			'id' => 'url',
			'name' => 'URL',
			'type' => 'text_url'
		));

	}

	public function front(){

		$cmb2 = new_cmb2_box( array(
			'id'           => 'front_page_details',
			'title'        => 'Front Page Details',
			'object_types' => array( 'page', ),
			'show_on' => array( 'key' => 'front-page' )
		) );

		$cmb2->add_field( array(
			'id'       => 'hero_image',
			'name'     => 'Hero Image',
			'type'     => 'file',
			'desc'     => 'An image that will appear prominently on the front page.',
			'options'  => array(
				'url'  => false
			)
		) );

		$this->subtitle_field($cmb2);

	}

	public function article(){

		$cmb2 = new_cmb2_box( array(
			'id'           => 'article_details',
			'title'        => 'Article Details',
			'object_types' => array( 'article', ),
		) );

		$this->subtitle_field($cmb2);

		$this->featured_video_url_field($cmb2);

	}

	/////////////////////
	// Reusable Fields //
	/////////////////////
	//
	// Easily reuse field definitions by passing in the
	// current $cmb2 object being manipulated

	/**
	 * Subtitle field
	 * @param  object $cmb2
	 */
	protected function subtitle_field($cmb2){
		return $cmb2->add_field( array(
			'id'       => 'subtitle',
			'name'     => 'Subtitle',
			'type'     => 'textarea_small',
			'desc'     => '(Optional) Enter a subtitle',
		) );
	}

	/**
	 * Featured Video URL field
	 */
	protected function featured_video_url_field($cmb2){
		return $cmb2->add_field( array(
			'id'       => 'featured_video_url',
			'name'     => 'Featured Video URL',
			'type'     => 'oembed',
			'desc'     => '(YouTube only) Add the URL of the YouTube video you would like featured. This will attempt to retrieve the thumbnail from YouTube and set it as the featured image.',
		) );
	}

	/////////////////////
	// Show On Filters //
	/////////////////////

	/**
	 * Create any custom show_on filters that we may want to utilize
	 */
	protected function add_show_on_filters(){
		add_filter('cmb2_show_on', array($this, 'show_on_front_page'), 10, 2);
	}

	public function show_on_front_page($display, $meta_box){

		// Move along if we haven't defined a "show_on" property
		if ( ! isset( $meta_box['show_on']['key'] ) ){
			return $display;
		}

		// Ignore if not the front-page "show_on"
		if ( 'front-page' !== $meta_box['show_on']['key'] ) {
			return $display;
		}

		$post_id = 0;

		// If we're showing it based on ID, get the current ID
		if ( isset( $_GET['post'] ) ) {
			$post_id = $_GET['post'];
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$post_id = $_POST['post_ID'];
		}

		if ( ! $post_id ) {
			return false;
		}

		// Get ID of page set as front page, 0 if there isn't one
		$front_page = get_option( 'page_on_front' );

		// there is a front page set and we're on it!
		return $post_id == $front_page;

	}

}