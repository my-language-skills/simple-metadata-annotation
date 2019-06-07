<?php

//creating metaboxes for educational metadata

use \vocabularies\smdan_Metadata_annotation as annotation_meta;


defined ("ABSPATH") or die ("No script assholes!");

/**
 * Function for producing metaboxes in all active locations
 */
function smdan_create_metaboxes() {

	if (1 != get_current_blog_id() || !is_multisite()){

		//getting locations to place metaboxes
		$active_locations = get_option('smdan_locations') ?: [];

		//for every active location initializaing annotation vocabulary to place metaboxes
		foreach ($active_locations as $location => $val) {
			new annotation_meta($location);
		}

	}

}


add_action( 'custom_metadata_manager_init_metadata', 'smdan_create_metaboxes');
