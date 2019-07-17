<?php
namespace vocabularies;

/**
 * The class for the educational custom vocabulary including operations and metaboxes
 *
 */
class smdan_Metadata_annotation{

	/**
	 * The type level that identifies where these metaboxes will be created
	 *
	 * @since    0.x
	 * @access   public
	 */
	public $type_level;

	/**
	 * The variable that holds the values from the database for the vocabulary output
	 *
	 * @since    0.x
	 * @access   public
	 */
	public $metadata;

	/**
	 * The variable that holds the group id of the metabox
	 *
	 * @since    0.x
	 * @access   public
	 */
	public $groupId;

	/**
	 * The variable that holds the properties of this vocabulary
	 *
	 * @since    0.x
	 * @access   public
	 */

	public static $annotation_properties = array(
		'annotation'=>array( 'Text area', 'If you are not finish', 3)
	);

	public function __construct($typeLevelInput) {

		$this->groupId = 'annotation';
		$this->type_level = $typeLevelInput;

		$this->smdan_add_metabox( $this->type_level );
	}

	/**
	 * Function to render fields, which are frozen by admin/network admin
	 *
	 * @since
	 *
	 */
	public function render_frozen_field ($field_slug, $field, $value) {
		global $post;

		//Getting the origin for overwritten data
				$dataFrom = is_plugin_active('pressbooks/pressbooks.php') ? 'Book-Info' : 'Site-Meta';

			//getting value of post meta
				$meta_value = $label = get_post_meta($post->ID, $field_slug, true);

				//taking property name from name of field
				$property = explode('_', $field_slug)[1];

				//looping through all properties to get name of given property
				foreach (self::$annotation_properties as $key => $value) {

					if (strtolower($key) == $property){
						$property = $value[0];
					}
				}

		?>
				<p>
					<?php printf(esc_html__('%s is overwritten by %s. The value is "%s"', 'simple-metadata-annotation'), $property, $dataFrom, $label); ?>
				<input type="hidden" name="<?=$field_slug?>" value="<?=$meta_value?>" />
				<?php
	}

	/**
	 * Function to render fields, which are disabled by admin/network admin
	 *
	 * @since
	 *
	 */
	public function render_disable_field ($field_slug, $field, $value) {
		global $post;

		//Getting the origin for overwritten data
				$dataFrom = is_plugin_active('pressbooks/pressbooks.php') ? 'Book-Info' : 'Site-Meta';

			//getting value of post meta
				$meta_value = $label = get_post_meta($post->ID, $field_slug, true);

				//gettign porperty name from field name
				$property = explode('_', $field_slug)[1];

				//getting label of this property
				foreach (self::$annotation_properties as $key => $value) {
					if (strtolower($key) == $property){
						$property = $value[0];
					}
				}
		?>
				<p> </p>
				<?php
	}
	/**
	 * The function which produces the metaboxes for the vocabulary
	 *
	 * @param string Accepting a string so we can distinguish on witch place each metabox is created
	 *
	 * @since 0.x
	 */
	public function smdan_add_metabox( $meta_position ) {

		//creating metabox
		x_add_metadata_group( $this->groupId,$meta_position, array(
			'label' 		=>	__('Annotation', 'simple-metadata-annotation'),
			'priority' 		=>	'high'
		) );

		//adding fields to metabox
		foreach ( self::$annotation_properties as $property => $details ) {

			$callback = null;
			$freezes = [];
			$disable = [];

			$freezesS = get_option('smdan_');
			foreach ((array) $freezesS as $key => $value) {
				if ($value=='3') {
					$freezes[$key] = '1';
				}
				if ($value=='1') {
					$disable[$key] = '1';
				}
			}

			//if this property is frozen, we render its metafield correspondingly
			if ($meta_position != 'site-meta' && $meta_position!= 'metadata' && isset($freezes[$property]) && $freezes[$property]){

					$callback = 'render_frozen_field';

			}

			if ($meta_position != 'site-meta' && $meta_position!= 'metadata' && isset($disable[$property]) && $disable[$property]){
				$callback = 'render_disable_field';
			}

			$fieldId = strtolower('smdan_' . $property . '_' .$this->groupId. '_' .$meta_position);
			//Checking if we need a dropdown field
			if(!isset($details[2])){
					x_add_metadata_field( $fieldId, $meta_position, array(
						'group'       => $this->groupId,
						'label'       => $details[0],
						'description' => $details[1],
						'display_callback' => array($this, $callback)
					) );

			}else {
				if ( $details[2] == 'number' ) {
						x_add_metadata_field( $fieldId, $meta_position, array(
							'group'            => $this->groupId,
							'field_type'       => 'number',
							'label'            => $details[0],
							'description'      => $details[1],
							'display_callback' => array($this, $callback)
						) );
				}elseif ( $details[2] == 'multiple' ){
						x_add_metadata_field( $fieldId, $meta_position, array(
							'group'            => $this->groupId,
							'multiple'         => true,
							'label'            => $details[0],
							'description'      => $details[1],
							'display_callback' => array($this, $callback)
						) );
				}
				elseif ( $details[2] = 3 ) {
					x_add_metadata_field( $fieldId, $meta_position, array(
						'group'       => $this->groupId,
						'field_type' => 'textarea',
						'label'       => $details[0],
						'description' => $details[1],
						'display_callback' => array($this, $callback)
						) );
				} else {
						x_add_metadata_field( $fieldId, $meta_position, array(
							'group'            => $this->groupId,
							'field_type'       => 'select',
							'values'           => $details[2],
							'label'            => $details[0],
							'description'      => $details[1],
							'display_callback' => array($this, $callback)
						) );
				}
			}
		}
	}

	/**
	 * A function needed for the array of metadata that comes from each post site-meta cpt or chapter
	 * It automatically returns the first item in the array.
	 * @since 0.x
	 *
	 */
	private function smdan_get_first( $my_array ) {
		if ( $my_array == '' ) {
			return '';
		} else {
			return $my_array[0];
		}
	}

	/**
	 * Gets the value for the microtags from $this->metadata.
	 *
	 * @since    0.x
	 *
	 */
	private function smdan_get_value( $propName ) {
		$array = isset( $this->metadata[ $propName ] ) ? $this->metadata[ $propName ] : '';
		if ( !stripos($propName, 'additionalClass')) {
			$value = $this->smdan_get_first( $array );
		} elseif (stripos($propName, 'url') || stripos($propName, 'desc')) {
			$value = $this->smdan_get_first( $array );
		} else {
			//We always use the get_first function except if our level is metadata coming from pressbooks
			$value = $array;
		}

		return $value;
	}


	/**
	 * A function that returns all the metadata from the site_meta cpt
	 * This is like when we use pressbooks to gather all data from Book Info
	 * We are always working on a single post -- automatic
	 * This function will be mostly used when the plugin is on wordpress mode and not on pressbooks mode.
  */
	public static function get_site_meta_metadata(){

		$post_type = is_plugin_active ('pressbooks/pressbooks.php') ? 'metadata' : 'site-meta';

		$args = array(
			'post_type' => $post_type,
			'posts_per_page' => 1,
			'post_status' => 'publish',
			'orderby' => 'modified',
			'no_found_rows' => true,
			'cache_results' => true,
		);

		$q = new \WP_Query();
		$results = $q->query( $args );

		if ( empty( $results ) ) {
			return false;
		}

		return get_post_meta( $results[0]->ID );
	}

	/**
	 * Function that creates the vocabulary metatags
	 *
	 * @since    0.x
	 * @access   public
	 */
	public function smdan_get_metatags($type) {
		//Getting the information from the database
				if($this->type_level == 'metadata' || $this->type_level == 'site-meta'){
						$this->metadata = self::get_site_meta_metadata();
				} else {
						$this->metadata = get_post_meta( get_the_ID() );
				}

		$html = "\n<!--annotation METADATA-->\n";
	$cleanCollect = [];
		//looping through all properties and printing tags only for those, which are defined
		foreach ( self::$annotation_properties as $key => $desc ) {
			//Constructing the key for the data
			//Add strtolower in all vocabs remember
			$dataKey = strtolower('smdan_' . $key . '_' . $this->groupId .'_'. $this->type_level);
			$dataKeyDesc = strtolower('smdan_' . $key . '_desc_' . $this->groupId .'_'. $this->type_level);

			//Getting the data
			$val = $this->smdan_get_value($dataKey);
			$valDesc = $this->smdan_get_value($dataKeyDesc);

			//Checking if the value exists and that the key is in the array for the schema
			if(empty($val) || $val == '--Select--'){
				continue;
			} else {
				$cleanCollect[$key]['val'] = $val;
				if(!empty($valDesc)){
					$cleanCollect[$key]['desc'] = $valDesc;
				}
			}
		}
		$html .=  "      <span itemprop='description'> $val </span>\n"
	."	</span>\n"
	."	</div>\n";
		$html .= "<!--END OF annotation METADATA-->\n";

		return $html;
	}
}
