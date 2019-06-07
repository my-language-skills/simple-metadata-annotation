<?php

//network settings functionality

use \vocabulariess\smdan_Metadata_annotation as annotation_meta;

defined ("ABSPATH") or die ("No script assholes!");

/**
 * Function for adding network settings page
 */
function smdan_add_network_settings() {

    //adding settings metaboxes and settigns sections
    add_meta_box('smdan-metadata-network-location', 'Annotation Metadata', 'smdan_network_render_metabox_schema_locations', 'smd_net_set_page', 'normal', 'core');
    add_meta_box('smdan-network-metadata-properties', 'Annotation Properties Management', 'smdan_network_render_metabox_properties', 'smd_net_set_page', 'normal', 'core');

    add_settings_section( 'smdan_network_meta_locations', '', '', 'smdan_network_meta_locations' );

    add_settings_section( 'smdan_network_meta_properties', '', '', 'smdan_network_meta_properties' );

    //registering settings
    register_setting('smdan_network_meta_locations', 'smdan_net_locations');
	register_setting ('smdan_network_meta_properties', 'smdan_net_shares');
	register_setting ('smdan_network_meta_properties', 'smdan_net_freezes');


	// getting options values from DB
	$post_types = smd_get_all_post_types();
	$locations = get_option('smdan_net_locations');
	$shares = get_option('smdan_net_shares');
	$freezes = get_option('smdan_net_freezes');


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
		add_settings_field ('smdan_net_'.$key, ucfirst($data[0]), function () use ($key, $data, $shares, $freezes){
			$checked_share = isset($shares[$key]) ? true : false;
			$checked_freeze = isset($freezes[$key]) ? true : false;
			?>
				<label for="smdan_net_shares[<?=$key?>]"><i>Share</i> <input type="checkbox" name="smdan_net_shares[<?=$key?>]" id="smdan_net_shares[<?=$key?>]" value="1" <?php checked(1, $checked_share);?>></label>
				<label for="smdan_net_freezes[<?=$key?>]"><i>Freeze</i> <input type="checkbox" name="smdan_net_freezes[<?=$key?>]" id="smdan_net_freezes[<?=$key?>]" value="1" <?php checked(1, $checked_freeze);?>></label>
				<br><span class="description"><?=$data[1]?></span>
			<?php
		}, 'smdan_network_meta_properties', 'smdan_network_meta_properties');
	}

}

/**
 * Function for rendering settings page
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
 * Function for rendering metabox of locations
 */
function smdan_network_render_metabox_schema_locations(){
	?>
	<div id="smdan_network_meta_locations" class="smdan_network_meta_locations">
		<span class="description">Description for annotation network locations metabox</span>
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
 * Function for rendering metabox for properties management
 */
function smdan_network_render_metabox_properties(){
	?>
	<div id="smdan_network_meta_properties" class="smdan_network_meta_properties">
		<span class="description">Description for annotation network properties metabox</span>
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
 * Handler for locations settings update
 */
function smdan_update_network_locations() {

	check_admin_referer('smdan_network_meta_locations-options');

	//Wordpress Database variable for database operations
    global $wpdb;

	$locations = isset($_POST['smdan_net_locations']) ? $_POST['smdan_net_locations'] : array();

	//collecting locations of general meta accumulative option from POST request
	$locations_general = get_blog_option(1, 'smd_net_locations') ?: array();

	$locations_general = array_merge($locations_general, $locations);

	if (isset($locations_general['metadata'])){
		unset($locations_general['metadata']);
	}
	if (isset($locations_general['site-meta'])){
		unset($locations_general['site-meta']);
	}

	update_blog_option(1, 'smdan_net_locations', $locations);
	update_blog_option(1, 'smd_net_locations', $locations_general);

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
 * Handler for properties settings update
 */
function smdan_update_network_options() {

	check_admin_referer('smdan_network_meta_properties-options');

	//Wordpress Database variable for database operations
    global $wpdb;

    //collecting network options values from request
    $freezes = isset($_POST['smdan_net_freezes']) ? $_POST['smdan_net_freezes'] : array();
    $shares = isset($_POST['smdan_net_shares']) ? $_POST['smdan_net_shares'] : array();
    //if property is frozen, it's automatically shared
    $shares = array_merge($shares, $freezes);

    //updating network options in DB
	update_blog_option(1, 'smdan_net_freezes', $freezes);
	update_blog_option(1, 'smdan_net_shares', $shares);

	//Grabbing all the site IDs
    $siteids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

    //Going through the sites
    foreach ($siteids as $site_id) {

    	if (1 == $site_id){
    		continue;
    	}

    	switch_to_blog($site_id);

    	//> we merge values received from network settings with local values of every blog
    	$freezes_local = get_option('smdan_freezes') ?: array();
    	$frezees_local = array_merge($freezes_local, $freezes);

    	$shares_local = get_option('smdan_shares') ?: array();
    	$shares_local = array_merge($shares_local, $shares);
    	//<

    	//updating local options
    	update_option('smdan_freezes', $frezees_local);
    	update_option('smdan_shares', $shares_local);

    	smdan_update_overwrites();
    }

    restore_current_blog();

	// At the end we redirect back to our options page.
    wp_redirect(add_query_arg(array('page' => 'smd_net_set_page',
    'settings-updated' => 'true'), network_admin_url('settings.php')));

    exit;
}


add_action( 'network_admin_menu', 'smdan_add_network_settings', 1001); //third parameter means priority, bigger => later executed hooked function
add_action( 'network_admin_edit_smdan_update_network_locations', 'smdan_update_network_locations');
add_action( 'network_admin_edit_smdan_update_network_options', 'smdan_update_network_options');
