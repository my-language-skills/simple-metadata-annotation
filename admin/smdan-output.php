<?php
/**
 * Function for printing metatags
 *
 * Description. (use period)
 *
 * @link URL
 *
 * @package simple-metadata-annotation
 * @subpackage admin/output
 * @since x.x.x (when the file was introduced)
 */

//functionality for printing metatags

use \vocabularies\smdan_Metadata_annotation as annotation_meta;

/**
* Function for printing metatags in site front-end
*
* @since
*
*/

function smdan_print_tags ($type) {

	$locations = get_option('smdan_locations');

	//Checking if we are executing Book Info or Site-Meta data for the front page - Site Level - Book Level
	if(!is_plugin_active('pressbooks/pressbooks.php')){
		$front_schema = 'site-meta';
	}else{
		$front_schema = 'metadata';
	}

	//recieving post type of current post
	$post_schema = get_post_type();
	// Retrieve current $post_id
	$post_id = get_the_ID();

	//defining if page is post or front-page
	if ( is_front_page() ) {
		if (isset($locations[$front_schema]) && $locations[$front_schema] && smd_is_post_CreativeWork($post_id)) {
			$annotation_meta = new annotation_meta($front_schema);
			echo $annotation_meta->smdan_get_metatags($type);
		}
	} elseif (!is_home()){
		if (isset($locations[$post_schema]) && $locations[$post_schema] && smd_is_post_CreativeWork($post_id)) {
			$annotation_meta = new annotation_meta($post_schema);
			echo $annotation_meta->smdan_get_metatags($type);
		}
	}

}
