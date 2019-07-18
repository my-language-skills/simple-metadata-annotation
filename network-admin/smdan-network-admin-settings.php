<?php

/**
 * Network settings functionality
 *
 * Description. (use period)
 *
 * @link URL
 *
 * @package simple-metadata-annotation
 * @subpackage admin/network
 * @since x.x.x (when the file was introduced)
 */

//network settings functionality

use \vocabularies\smdan_Metadata_annotation as annotation_meta;

defined ("ABSPATH") or die ("No script assholes!");

/**
 * Function for adding network settings page
 *
 * @since
 *
 */
function smdan_add_network_settings() {

  //adding settings metaboxes and settigns sections
  add_meta_box('smdan-metadata-network-location', __('annotation Metadata', 'simple-metadata-annotation'), 'smdan_network_render_metabox_schema_locations', 'smd_net_set_page', 'normal', 'core');
  add_meta_box('smdan-network-metadata-properties', __('annotation Properties Management', 'simple-metadata-annotation'), 'smdan_network_render_metabox_properties', 'smd_net_set_page', 'normal', 'core');

  add_settings_section( 'smdan_network_meta_locations', '', '', 'smdan_network_meta_locations' );

  add_settings_section( 'smdan_network_meta_properties', '', '', 'smdan_network_meta_properties' );


  //Network options
  add_site_option('smdan_net_locations', '');
  add_site_option('smdan_net_', '');

	// getting options values from DB
	$post_types = smd_get_all_post_types();
	$locations = get_site_option('smdan_net_locations');
	$shares1 = get_site_option('smdan_net_');

	//adding settings for locations
	foreach ($post_types as $post_type) {
		if ('metadata' == $post_type){
			$label = 'Book Info';
		} else {
			$label = ucfirst($post_type);
		}
		add_settings_field ('smdan_net_locations['.$post_type.']', $label, function () use ($post_type, $locations){
			$checked = isset($locations[$post_type]) ? true : false;
			?>
				<input type="checkbox" name="smdan_net_locations[<?=$post_type?>]" id="smdan_net_locations[<?=$post_type?>]" value="1" <?php checked(1, $checked);?>>
			<?php
		}, 'smdan_network_meta_locations', 'smdan_network_meta_locations');
	}

	//adding settings for educational properties management
	foreach (annotation_meta::$annotation_properties as $key => $data) {
		add_settings_field ('smdan_net_'.$key, ucfirst($data[0]), function () use ($key, $data, $shares1){
      $shares1[$key] = !empty($shares1[$key]) ? $shares1[$key] : '0';

			?>
      <label for="smdan_net_disable[<?=$key?>]"><?php esc_html_e('Disable', 'simple-metadata-annotation'); ?> <input type="radio"  name="smdan_net_[<?=$key?>]" value="1" id="smdan_net_disable[<?=$key?>]" <?php if ($shares1[$key]=='1') { echo "checked='checked'"; }
      ?>  ></label>
      <label for="smdan_net_local_value[<?=$key?>]"><?php esc_html_e('Local value', 'simple-metadata-annotation'); ?> <input type="radio"  name="smdan_net_[<?=$key?>]" value="0" id="smdan_net_local_value[<?=$key?>]" <?php if ($shares1[$key]=='0' ) { echo "checked='checked'"; }
      ?>  ></label>
      <label  for="smdan_net_share[<?=$key?>]"><?php esc_html_e('Share', 'simple-metadata-annotation'); ?> <input type="radio"  name="smdan_net_[<?=$key?>]" value="2" id="smdan_net_share[<?=$key?>]" <?php if ($shares1[$key]=='2') { echo "checked='checked'"; }
      ?>  ></label>
      <label for="smdan_net_freeze[<?=$key?>]"><?php esc_html_e('Freeze', 'simple-metadata-annotation'); ?> <input type="radio"  name="smdan_net_[<?=$key?>]" value="3" id="smdan_net_freeze[<?=$key?>]"  <?php if ($shares1[$key]=='3') { echo "checked='checked'"; }
      ?> ></label>
				<br><span class="description"><?=$data[1]?></span>
			<?php
		}, 'smdan_network_meta_properties', 'smdan_network_meta_properties');
	}

}

/**
* Function for rendering settings page.
*
* @since
*
*/

function smdan_render_network_settings(){
	wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
	    ?>
	    <div class="wrap">
	    	<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) { ?>
        	<div class="notice notice-success is-dismissible">
				<p><strong>Settings saved.</strong></p>
			</div>
			<?php } ?>
		    <div class="metabox-holder">
			    <?php
			    	do_meta_boxes('smdan_net_set_page', 'normal','');
			    ?>
		    </div>
	    </div>
	    <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready( function($) {
                // close postboxes that should be closed
                $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                // postboxes setup
                postboxes.add_postbox_toggles('<?php echo 'smdan_net_set_page'; ?>');
            });
            //]]>
		</script>
		<?php
}

/**
* Function for rendering metabox of locations.
*
* @since
*
*/

function smdan_network_render_metabox_schema_locations(){
	?>
	<div id="smdan_network_meta_locations" class="smdan_network_meta_locations">
		<span class="description">
      <?php esc_html_e('Description for annotation network locations metabox', 'simple-metadata-annotation'); ?>
    </span>
		<form method="post" action="edit.php?action=smdan_update_network_locations">
			<?php
			settings_fields( 'smdan_network_meta_locations' );
			do_settings_sections( 'smdan_network_meta_locations' );
			submit_button();
			?>
		</form>
		<p></p>
	</div>
	<?php

}

/**
* Function for rendering metabox for properties management.
*
* @since
*
*/

function smdan_network_render_metabox_properties(){
	?>
	<div id="smdan_network_meta_properties" class="smdan_network_meta_properties">
		<span class="description">
      <?php esc_html_e('Description for annotation network properties metabox', 'simple-metadata-annotation'); ?>
    </span>
		<form method="post" action="edit.php?action=smdan_update_network_options">
			<?php
			settings_fields( 'smdan_network_meta_properties' );
			do_settings_sections( 'smdan_network_meta_properties' );
			submit_button();
			?>
		</form>
		<p></p>
	</div>
	<?php
}

/**
* Handler for locations settings update.
*
* @since
*
*/

function smdan_update_network_locations() {

	check_admin_referer('smdan_network_meta_locations-options');

	//Wordpress Database variable for database operations
    global $wpdb;

	$locations = isset($_POST['smdan_net_locations']) ? $_POST['smdan_net_locations'] : array();

	//collecting locations of general meta accumulative option from POST request
	$locations_general = get_site_option('smd_net_locations') ?: array();

	$locations_general = array_merge($locations_general, $locations);

	if (isset($locations_general['metadata'])){
		unset($locations_general['metadata']);
	}
	if (isset($locations_general['site-meta'])){
		unset($locations_general['site-meta']);
	}

	update_site_option('smdan_net_locations', $locations);
	update_site_option('smd_net_locations', $locations_general);

	//Grabbing all the site IDs
    $siteids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

    //Going through the sites
    foreach ($siteids as $site_id) {
    	if (1 == $site_id){
    		continue;
    	}

    	switch_to_blog($site_id);

    	//we merge values received from network settings with local values of every blog
    	$locations_local = get_option('smdan_locations') ?: array();
    	$locations_local_general = get_option('smd_locations') ?: array();

    	$locations_local = array_merge($locations_local, $locations);
    	$locations_local_general = array_merge($locations_local_general, $locations_general);

    	//updating local options
    	update_option('smdan_locations', $locations_local);
    	update_option('smd_locations', $locations_local_general);

    }

    restore_current_blog();

	// At the end we redirect back to our options page.
    wp_redirect(add_query_arg(array('page' => 'smd_net_set_page',
    'settings-updated' => 'true'), network_admin_url('settings.php')));

    exit;
}

/**
* Handler for properties settings update.
*
* @since
*
*/

function smdan_update_network_options() {

	check_admin_referer('smdan_network_meta_properties-options');

	//Wordpress Database variable for database operations
    global $wpdb;

    //collecting network options values from request

    $shares1 = isset($_POST['smdan_net_']) ? $_POST['smdan_net_'] : array();
    //if property is frozen, it's automatically shared


    //updating network options in DB
	update_site_option('smdan_net_', $shares1);

	//Grabbing all the site IDs
    $siteids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

    //Going through the sites
    foreach ($siteids as $site_id) {

    	if (1 == $site_id){
    		continue;
    	}

    	switch_to_blog($site_id);

    	//> we merge values received from network settings with local values of every blog

    	$shares1_local = get_option('smdan_') ?: array();
    	$shares1_local = array_merge($shares1_local, $shares1);
    	//<

    	//updating local options
    	update_option('smdan_', $shares1_local);

    	smdan_update_overwrites();
    }

    restore_current_blog();

	// At the end we redirect back to our options page.
    wp_redirect(add_query_arg(array('page' => 'smd_net_set_page',
    'settings-updated' => 'true'), network_admin_url('settings.php')));

    exit;
}


add_action( 'network_admin_menu', 'smdan_add_network_settings', 1000); //third parameter means priority, bigger => later executed hooked function
add_action( 'network_admin_edit_smdan_update_network_locations', 'smdan_update_network_locations');
add_action( 'network_admin_edit_smdan_update_network_options', 'smdan_update_network_options');
