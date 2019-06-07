<?php
namespace vocabularies;

use \vocabularies\SMDE_Metadata_Educational;
use \vocabularies\SMDE_Metadata_Classification as class_meta;

/**
 * The class for the educational custom vocabulary including operations and metaboxes
 *
 */
class SMDE_Metadata_Lrmi extends SMDE_Metadata_Educational {



	/**
	 * The variable that holds the relations between LRMI properties names and LOM
	 *
	 * @since    0.x
	 * @access   public
	 */
	public static $lrmi_properties = array(

      	'interactivityType'		=> 'interactivityType',
      	'learningResourceType'	=> 'learningResourceType',
		'educationalRole'		=> 'endUserRole',
		'educationalUse'		=> 'educationalUse',
		'typicalAgeRange' 		=> 'typicalAgeRange',
		'timeRequired'			=> 'typicalLearningTime'
	);


	/**
	 * Function that creates the vocabulary metatags
	 *
	 * @since    0.x
	 * @access   public
	 */
	public function smde_get_metatags() {
		//Getting post meta of site-meta of metadata (Book Info) post
        if($this->type_level == 'metadata' || $this->type_level == 'site-meta'){
            $this->metadata = self::get_site_meta_metadata();
        }else{
            $this->metadata = get_post_meta( get_the_ID() );
        }

		//Keys for looping
		$loop_keys = array(
			'typicalAgeRange',
			'learningResourceType',
			'interactivityType',
			'timeRequired',
			'educationalUse'
		);

		//initializing variable for schema type string
		$val = '';

        //Starting point of educational schema part 1
        $html  = "\n<!-- LRMI Microtags -->\n";


		$partTwoMetadata = null;

		//going through all properties of class and ones, which don't require specific markup
		foreach ( self::$lrmi_properties as $key => $desc ) {
			//Constructing the key for the data
			//Add strtolower in all vocabs remember
			$dataKey = strtolower('smde_' . $desc . '_' . $this->groupId .'_'. $this->type_level);
			//Getting the data
			$val = $this->smde_get_value($dataKey);

			//Checking if the value exists and that the key is in the array for the schema
			if(empty($val) || $val == '--Select--'){
				continue;
			}else{
				if(in_array($key,$loop_keys)){ // checking only for proerties which don't require specific markup
					//if the schema is timeRequired, we are using a specific format to display it,
					//like the example here: https://schema.org/timeRequired
					if ( 'timeRequired' == $key ) {
						$val = 'PT'. $val.'H';
					}
					$html .= "<meta itemprop = '" . $key . "' content = '" . $val . "'>\n";
				}else{
					$partTwoMetadata[$key] = $val;
				}
			}
		}
		//Ending schema part 1

		//Starting point of educational schema part 2
		if ( isset( $this->metadata['pb_title'] ) ) {
			$this->metadata['pb_title'] = $this->metadata['pb_title'][0];
			$html .= "<span itemprop = 'educationalAlignment' itemscope itemtype = 'http://schema.org/AlignmentObject'>\n"
			         ."	<meta itemprop = 'alignmentType' content = 'educationalSubject'/>\n"
			         ."	<meta itemprop = 'targetName' content = '" .$this->metadata['pb_title']. "'>\n"
			         ."</span>\n";
		}

		if(isset( $partTwoMetadata['educationalRole'] )){
			$html .= "<span itemprop = 'audience' itemscope itemtype = 'http://schema.org/EducationalAudience'>\n"
			         ."	<meta itemprop = 'educationalRole' content = '$partTwoMetadata[educationalRole]'/>\n"
			         ."</span>\n";
		}

		//initilizing instance of classification vocabulary class and calling its method for prinitng metatags
		$class_meta = new class_meta($this->type_level);
		if (is_multisite() && get_blog_option(1, 'smde_net_for_lang')){
			$html .= $class_meta->smde_get_metatags_lang();
		} else {
			$html .= $class_meta->smde_get_metatags();
		}

        $html .= "<!-- END OF LRMI MICROTAGS-->\n";
		echo $html;
	}
}
