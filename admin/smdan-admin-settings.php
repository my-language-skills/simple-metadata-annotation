<?php

/**
 * Summary (no period for file headers)
 *
 * Description. (use period)
 *
 * @link URL
 *
 * @package simple-metadata-annotation
 * @subpackage admin/settngs
 * @since x.x.x (when the file was introduced)
 */

use \vocabularies\smdan_Metadata_annotation as annotation_meta;


/**
 * Creates settings subpage for Simple Metadata
 *
 * Description
 *
 * @link URL
 *
 * @package simple-metadata-annotation
 * @subpackage simple-metadata-annotation/admin
 * @since x.x.x (when the file was introduced)
 */

defined ("ABSPATH") or die ("No script assholes!");

/**
 * Functions to add plugin settings subpage and registering settings and their sections
 *
 * @since
 *
 */
function smdan_add_annotation_settings() {
	//we don't create settings page in blog 1 (not necessary)
	if ((1 != get_current_blog_id() && is_multisite()) || !is_multisite()){

		//adding subapage to page of main plugin
		add_submenu_page('smd_set_page', __('Annotation', 'simple-metadata-annotation') , __('Annotation', 'simple-metadata-annotation'), 'manage_options', 'smdan_set_page', 'smdan_render_settings');

		//adding metaboxes and sections for settings
		add_meta_box('smdan-metadata-location', __('Annotation', 'simple-metadata-annotation'), 'smdan_render_metabox_schema_locations', 'smd_set_page', 'normal', 'core');

		add_settings_section( 'smdan_meta_locations', '', '', 'smdan_meta_locations' );

		add_meta_box('smdan-metadata-properties', __('Properties Management', 'simple-metadata-annotation'), 'smdan_render_metabox_properties', 'smdan_set_page', 'normal', 'core');

		add_settings_section( 'smdan_meta_properties', '', '', 'smdan_meta_properties' );

		//registering settings for locations and properties management
		register_setting('smdan_meta_locations', 'smdan_locations');
		register_setting ('smdan_meta_properties', 'smdan_shares');

		register_setting ('smdan_meta_properties', 'smdan_');

		register_setting ('smdan_meta_properties', 'smdan_freezes');


		//collecting current values of options
		$post_types = smd_get_all_post_types();
		$locations = get_option('smdan_locations');
		$shares = get_option('smdan_shares');
		$shares1 = get_option('smdan_');
		$freezes = get_option('smdan_freezes');

		//initiazaling variables for network values
		$network_locations = [];
		$network_shares = [];

		$network_shares1 = [];
		$network_freezes = [];
		//in case of multisite installation, we collect options for network
		if (is_multisite()){
			$network_locations = get_site_option('smdan_net_locations');
			$network_shares1 = get_site_option('smdan_net_');
		}

		//creating fields for activating metadata in every public post
		foreach ($post_types as $post_type) {
			if ('metadata' == $post_type){
				$label = 'Book Info';
			} else {
				$label = ucfirst($post_type);
			}
			add_settings_field ('smdan_locations['.$post_type.']', $label, function () use ($post_type, $locations, $network_locations){
				$checked = isset($locations[$post_type]) ? true : false;
				$disabled = isset($network_locations[$post_type]) && $network_locations[$post_type] ? 'disabled' : '';
				?>
					<input type="checkbox" name="smdan_locations[<?=$post_type?>]" id="smdan_locations[<?=$post_type?>]" value="1" <?php checked(1, $checked); echo $disabled;?>>
				<?php
				if ('disabled' == $disabled){
					?>
						<input type="hidden" name="smdan_locations[<?=$post_type?>]" value="1">
					<?php
				}
			}, 'smdan_meta_locations', 'smdan_meta_locations');
		}

		//creating fields for every property in annotation vocabulary
		foreach (annotation_meta::$annotation_properties as $key => $data) {

			add_settings_field ('smdan_'.$key, ucfirst($data[0]), function () use ($key, $data, $shares1, $freezes, $network_shares1, $network_freezes){
				if (!empty($network_shares1)) {
					if ($network_shares1[$key] == '0') {
						$shares1 = get_option('smdan_');
					// $shares1_class[$key] == '0';
					 $valeur_key_anno = '4';

					}else {
						$shares1[$key] = $network_shares1[$key];
						$valeur_key_anno = $shares1[$key];
					}
				}else
					{
					$disabled_ca = '';
					$valeur_key_anno = '4';

				}

				?>
				<?php if ($shares1[$key]=='1') {
if (isset($_GET['hello'])) {
					function runMyFunction() {
						if (isset($_GET['field_name'])) {
							$locations2 = get_option('smdan_locations');
							$key = $_GET['field_name'];
								global $wpdb;
									 //Get the posts table name
									 $postsTable = $wpdb->prefix . "posts";
									 //Get the postmeta table name
									 $postMetaTable = $wpdb->prefix . "postmeta";

									 //defining site-meta post type
									 $meta_type = is_plugin_active('pressbooks/pressbooks.php') ? 'metadata' : 'site-meta';

									 //fetching site-meta/book info post
									 $meta_post = $wpdb->get_results($wpdb->prepare("
											 SELECT ID FROM $postsTable WHERE post_type LIKE %s AND
											 post_status LIKE %s",$meta_type,'publish'),ARRAY_A);

									 //If we have more than one or 0 ids in the array then return and stop operation
									 //If we have no chapters or posts to distribute data also stop operation
									 if(count($meta_post) > 1 || count($meta_post) == 0){
											 return;
									 }

									 //unwrapping ID from subarrays
									 $meta_post_id = $meta_post[0]['ID'];


									 //getting metadata of site-meta/books info post
									 $meta_post_meta = $wpdb->get_results($wpdb->prepare("
											 SELECT `meta_key`, `meta_value` FROM $postMetaTable WHERE `post_id` LIKE %s
											 AND `meta_key` LIKE %s AND `meta_key` LIKE %s
											 AND `meta_value` <>''",$meta_post_id,'%%smdan_%%','%%annotation%%'.$meta_type.'%%')
													 ,ARRAY_A);

									//Array for storing metakey=>metavalue
									 $metaData = [];
									 //unwrapping data from subarrays
									 foreach($meta_post_meta as $meta){
											 $metaData[$meta['meta_key']] = $meta['meta_value'];
									 }
									 //if there are no fields of Life Cycle meta in site-meta/ book info, nothing to share or freeze, exit
									 if(count($metaData) == 0){
											 return;
									 }

									 foreach ($locations2 as $location => $val){
										 if ($location == $meta_type) {
											 continue;
										 }
												 //Getting all posts of $location type
												 $posts_ids = $wpdb->get_results($wpdb->prepare("
												 SELECT `ID` FROM `$postsTable` WHERE `post_type` = %s",$location),ARRAY_A);
												 $posts_ids_meta_type = $wpdb->get_results($wpdb->prepare("
												 SELECT `ID` FROM `$postsTable` WHERE `post_type` = %s",$meta_type),ARRAY_A);
												 //looping through all posts of type $locations
												 foreach ($posts_ids as $post_id) {
													 $post_id = $post_id['ID'];
														 $meta_key = 'smdan_'.strtolower($key).'_annotation_'.$location;
														 $metadata_meta_key = 'smdan_'.strtolower($key).'_annotation_'.$meta_type;
															 delete_post_meta($post_id, $meta_key);
															 delete_post_meta($post_id, $metadata_meta_key);

												 }
												 foreach ($posts_ids_meta_type as $post_id_meta_type) {
													 $post_id_meta_type = $post_id_meta_type['ID'];
														 $meta_key_meta_type = 'smdan_'.strtolower($key).'_annotation_'.$location;
														 $metadata_meta_key_type = 'smdan_'.strtolower($key).'_annotation_'.$meta_type;
															 delete_post_meta($post_id_meta_type, $meta_key_meta_type);
															 delete_post_meta($post_id_meta_type, $metadata_meta_key_type);

												 }
							}
						}
}


	runMyFunction();
	//refresh the page
	?><meta http-equiv="refresh" content="0;URL=admin.php?page=smdan_set_page"><?php
}
 if ($shares1[$key]=='1') {
echo '<a style="color:red; text-decoration: none; font-size: 14px;"href = "admin.php?page=smdan_set_page&hello=true&field_name='.$key.'&sharekey='.$shares1[$key].'">X</a>';}

?>
				&nbsp;&nbsp;
			<?php } ?>
				<label for="smdan_disable[<?=$key?>]">
						<?php esc_html_e('Disable', 'simple-metadata-annotation'); ?>
					  <input type="radio"  name="smdan_[<?=$key?>]" value="1" id="smdan_disable[<?=$key?>]"
							<?php if ($shares1[$key]=='1') { echo "checked='checked'"; } ?>
					 		<?php if ($valeur_key_anno == '1' || $valeur_key_anno == '4') {echo "";}else {echo "disabled";} ?>
						>
				</label>
				<label for="smdan_local_value[<?=$key?>]"><?php esc_html_e('Local value', 'simple-metadata-annotation'); ?>
						<input type="radio"  name="smdan_[<?=$key?>]" value="0" id="smdan_local_value[<?=$key?>]"
								<?php if ($shares1[$key]=='0' || empty($shares1[$key])) { echo "checked='checked'"; } ?>
								<?php if ($valeur_key_anno == '0' || $valeur_key_anno == '4') {echo "";}else {echo "disabled";}  ?>
						>
				</label>
				<label  for="smdan_share[<?=$key?>]"><?php esc_html_e('Share', 'simple-metadata-annotation'); ?> <input type="radio"  name="smdan_[<?=$key?>]" value="2" id="smdan_share[<?=$key?>]" <?php if ($shares1[$key]=='2') { echo "checked='checked'"; }
				?>  <?php  if ($valeur_key_anno == '2' || $valeur_key_anno == '4') {echo "";}else {echo "disabled";}  ?>></label>
				<label for="smdan_freeze[<?=$key?>]"><?php esc_html_e('Freeze', 'simple-metadata-annotation'); ?> <input type="radio"  name="smdan_[<?=$key?>]" value="3" id="smdan_freeze[<?=$key?>]"  <?php if ($shares1[$key]=='3') { echo "checked='checked'"; }
				?> <?php  if ($valeur_key_anno == '3' || $valeur_key_anno == '4') {echo "";}else {echo "disabled";}  ?>></label>
					<br><span class="description"><?=$data[1]?></span>
					<?php

			}, 'smdan_meta_properties', 'smdan_meta_properties');
}
		}
		}

/**
 * Function for rendering settings subpage
 *
 * @since
 *
 */
function smdan_render_settings() {
	if(!current_user_can('manage_options')){
		return;
	}

	wp_enqueue_script('common');
	wp_enqueue_script('wp-lists');
	wp_enqueue_script('postbox');
	?>
        <div class="wrap">
        	<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) { ?>
        	<div class="notice notice-success is-dismissible">
				<p><strong><?php esc_html_e('Settings saved.',  'simple-metadata-annotation'); ?></strong></p>
			</div>
			<?php smdan_update_overwrites(); }?>
            <h2><?php esc_html_e('Simple Metadata Annotation Settings',  'simple-metadata-annotation'); ?></h2>
            <div class="metabox-holder">
					<?php
					do_meta_boxes('smdan_set_page', 'normal', '');
					?>
            </div>
        </div>
        <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready( function($) {
                // close postboxes that should be closed
                $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                // postboxes setup
                postboxes.add_postbox_toggles('smdan_set_page');
            });
            //]]>
        </script>
		<?php
}

/**
 * Function for rendering 'Locations' metabox
 *
 * @since
 *
 */
function smdan_render_metabox_schema_locations(){
	?>
	<div id="smdan_meta_locations" class="smdan_meta_locations">
		<span class="description"><?php esc_html_e('Description for annotation locations metabox', 'simple-metadata-annotation'); ?></span>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'smdan_meta_locations' );
			do_settings_sections( 'smdan_meta_locations' );
			submit_button();
			?>
		</form>
		<p></p>
	</div>
	<?php
}

/**
 * Function for rendering 'annotation properties' metabox
 *
 * @since
 *
 */
function smdan_render_metabox_properties(){
	$locations = get_option('smdan_locations');
	$level = is_plugin_active('pressbooks/pressbooks.php') ? 'metadata' : 'site-meta';
	$label = $level == 'metadata' ? __('Book Info', 'simple-metadata-annotation') : __('Site-Meta', 'simple-metadata-annotation');
	if (isset($locations[$level]) && $locations[$level]){
	?>
	<div id="smdan_meta_properties" class="smdan_meta_properties">
		<span class="description"><?php esc_html_e('Description for annotation properties metabox', 'simple-metadata-annotation'); ?></span>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'smdan_meta_properties' );
			submit_button();
			do_settings_sections( 'smdan_meta_properties' );
			?>
		</form>
		<p></p>
	</div>
	<?php
	} else {
		?>
			<p style="color: red;"> <?php printf(esc_html__('Activate %s location in order to manage properties.', 'simple-metadata-annotation'), $label); ?></p>
		<?php
	}
}

/**
 * Function for updating options and forcing overwritings on settings update
 *
 * @since
 *
 */
 function smdan_update_overwrites(){

 	//collecting options values
 	$locations = get_option('smdan_locations') ?: [];
 	$shares1 = get_option('smdan_') ?: [];
 	$freezes = get_option('smdan_freezes') ?: [];

 	//if nothing is chosen to share or freeze, return
 	if(empty($shares1) && empty($freezes)){
 		return;
 	}

 	//Wordpress Database variable for database operations
 	global $wpdb;
     //Get the posts table name
     $postsTable = $wpdb->prefix . "posts";
     //Get the postmeta table name
     $postMetaTable = $wpdb->prefix . "postmeta";

     //defining site-meta post type
     $meta_type = is_plugin_active('pressbooks/pressbooks.php') ? 'metadata' : 'site-meta';

     //fetching site-meta/book info post
     $meta_post = $wpdb->get_results($wpdb->prepare("
         SELECT ID FROM $postsTable WHERE post_type LIKE %s AND
         post_status LIKE %s",$meta_type,'publish'),ARRAY_A);

     //If we have more than one or 0 ids in the array then return and stop operation
     //If we have no chapters or posts to distribute data also stop operation
     if(count($meta_post) > 1 || count($meta_post) == 0){
         return;
     }

     //unwrapping ID from subarrays
     $meta_post_id = $meta_post[0]['ID'];


     //getting metadata of site-meta/books info post
     $meta_post_meta = $wpdb->get_results($wpdb->prepare("
         SELECT `meta_key`, `meta_value` FROM $postMetaTable WHERE `post_id` LIKE %s
         AND `meta_key` LIKE %s AND `meta_key` LIKE %s
         AND `meta_value` <>''",$meta_post_id,'%%smdan_%%','%%_vocab%%'.$meta_type.'%%')
             ,ARRAY_A);

  	//Array for storing metakey=>metavalue
     $metaData = [];
     //unwrapping data from subarrays
     foreach($meta_post_meta as $meta){
         $metaData[$meta['meta_key']] = $meta['meta_value'];
     }
     //if there are no fields of annotation meta in site-meta/ book info, nothing to share or freeze, exit
     if(count($metaData) == 0){
         return;
     }

     //checking if there is somthing to share for annotation properties
		 if(!empty($shares1)){

	  		//looping through all active locations
	  		foreach ($shares1 as $key => $value) {
	  		if ($value=='2') {
	  		foreach ($locations as $location => $val){
	  			if ($location == $meta_type) {
	  				continue;
	  			}
	          	//Getting all posts of $location type
	          	$posts_ids = $wpdb->get_results($wpdb->prepare("
	          	SELECT `ID` FROM `$postsTable` WHERE `post_type` = %s",$location),ARRAY_A);

	          	//looping through all posts of type $locations
	          	foreach ($posts_ids as $post_id) {
	          		$post_id = $post_id['ID'];

	          		foreach ($shares1 as $key => $value) {
									if ($value=='2') {
	          			$meta_key = 'smdan_'.strtolower($key).'_annotation_'.$location;
	          			$metadata_meta_key = 'smdan_'.strtolower($key).'_annotation_'.$meta_type;
	          			if((!get_post_meta($post_id, $meta_key) || '' == get_post_meta($post_id, $meta_key)) && isset($metaData[$metadata_meta_key])){
	          				update_post_meta($post_id, $meta_key, $metaData[$metadata_meta_key]);
	          			}
											}
	          		}
	          	}
	  					}
	  				}
	  			if ($value=='3') {
	  				foreach ($locations as $location => $val){
	  					if ($location == $meta_type) {
	  						continue;
	  					}
	  		        	//Getting all posts of $location type
	  		        	$posts_ids = $wpdb->get_results($wpdb->prepare("
	  		        	SELECT `ID` FROM `$postsTable` WHERE `post_type` = %s",$location),ARRAY_A);

	  		        	//looping through all posts of type $locations
	  		        	foreach ($posts_ids as $post_id) {
	  		        		$post_id = $post_id['ID'];

	  		        		foreach ($shares1 as $key => $value) {
											if ($value=='3') {
	  		        			$meta_key = 'smdan_'.strtolower($key).'_annotation_'.$location;
	  		        			$metadata_meta_key = 'smdan_'.strtolower($key).'_annotation_'.$meta_type;
	  		        			if(isset($metaData[$metadata_meta_key])){
	  		        				update_post_meta($post_id, $meta_key, $metaData[$metadata_meta_key]);
	  		        			}
												}
	  		        		}
	  		        	}

	  				}
	  			}
	  		}
	  	}
	  }


add_action('admin_menu', 'smdan_add_annotation_settings', 100);
add_action('updated_option', function( $option_name, $old_value, $value ){
	if ('smde_locations' == $option_name){
		$locations_general = get_option('smd_locations') ?: [];
		$value = empty($value) ? [] : $value;
		$locations_general = array_merge($locations_general, $value);

		if (isset($locations_general['metadata'])){
			unset($locations_general['metadata']);
		}
		if (isset($locations_general['site-meta'])){
			unset($locations_general['site-meta']);
		}

		update_option('smd_locations', $locations_general);
	}
}, 10, 3);
