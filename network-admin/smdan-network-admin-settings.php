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
  add_meta_box('smdan-net-box-location', __('Annotation Metadata', 'simple-metadata-annotation'), 'smdan_network_render_metabox_schema_locations', 'smd_net_set_page', 'normal', 'core');
  add_meta_box('smdan-net-box-properties', __('Annotation Properties Management', 'simple-metadata-annotation'), 'smdan_network_render_metabox_properties', 'smd_net_set_page', 'normal', 'core');
  smdan_add_net_metabox_for_options();


  add_settings_section( 'smdan_network_meta_locations', '', '', 'smdan_network_meta_locations' );

  add_settings_section( 'smdan_network_meta_properties', '', '', 'smdan_network_meta_properties' );


  //Network options
  add_site_option('smdan_net_locations', '');
  add_site_option('smdan_net_', '');

	// getting options values from DB
	$post_types = smd_get_all_post_types();
	$locations = (array) get_site_option('smdan_net_locations');
	$props_values = (array) get_site_option('smdan_net_');

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
		add_settings_field ('smdan_net_'.$key, ucfirst($data[0]), function () use ($key, $data, $props_values){
      $props_values[$key] = !empty($props_values[$key]) ? $props_values[$key] : '0';

      ?>
      <?php if ($props_values[$key]=='1') {
if (isset($_GET['hello747'])) {
        function runMyFunction477() {
          if (isset($_GET['field_name'])) {
            $key = $_GET['field_name'];

              global $wpdb;
                 //If we have more than one or 0 ids in the array then return and stop operation
                 //If we have no chapters or posts to distribute data also stop operation
                 $prefixx = $wpdb->prefix;
                 $post_meta_texte = "_postmeta";
$prefixx_blog =$prefixx.'blogs';

                 //getting metadata of site-meta/books info post
                   $select_all_id_blogs = $wpdb->get_results("
                       SELECT blog_id FROM $prefixx_blog",ARRAY_N);
                  foreach ($select_all_id_blogs as $key1 => $valuee) {
                    $postMetaTable = $prefixx . $valuee[0] . $post_meta_texte;
                    $metadata_meta_key_site = 'smdan_'.strtolower($key).'_annotation_';
                $recuperation_de_la_table = $wpdb->get_results("
                    DELETE FROM $postMetaTable  WHERE meta_key like '%{$metadata_meta_key_site}%' ");
                  }
          }
}


  runMyFunction477();
  }
  if ($props_values[$key]=='1') {
  echo "<a onClick=\"javascript: return confirm('Are you sure to delete all meta-data of this field in the site?');\" style='color:red; text-decoration: none; font-size: 14px;'href = 'admin.php?page=smd_net_set_page&hello747=true&field_name=$key'>X</a>";}

  ?>
        &nbsp;&nbsp;
      <?php } ?>
        <label for="smdan_net_disable[<?=$key?>]"><?php esc_html_e('Disable', 'simple-metadata-annotation'); ?> <input type="radio"  name="smdan_net_[<?=$key?>]" value="1" id="smdan_net_disable[<?=$key?>]" <?php if ($props_values[$key]=='1') { echo "checked='checked'"; }
        ?>  ></label>
        <label for="smdan_net_local_value[<?=$key?>]"><?php esc_html_e('Local value', 'simple-metadata-annotation'); ?> <input type="radio"  name="smdan_net_[<?=$key?>]" value="0" id="smdan_net_local_value[<?=$key?>]" <?php if ($props_values[$key]=='0' ) { echo "checked='checked'"; }
        ?>  ></label>
        <label  for="smdan_net_share[<?=$key?>]"><?php esc_html_e('Share', 'simple-metadata-annotation'); ?> <input type="radio"  name="smdan_net_[<?=$key?>]" value="2" id="smdan_net_share[<?=$key?>]" <?php if ($props_values[$key]=='2') { echo "checked='checked'"; }
        ?>  ></label>
        <label for="smdan_net_freeze[<?=$key?>]"><?php esc_html_e('Freeze', 'simple-metadata-annotation'); ?> <input type="radio"  name="smdan_net_[<?=$key?>]" value="3" id="smdan_net_freeze[<?=$key?>]"  <?php if ($props_values[$key]=='3') { echo "checked='checked'"; }
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
 * Adds the metabox 'Options' in the network page
 *
 * @since   1.3
 */
function smdan_add_net_metabox_for_options(){
  //Options metabox
  add_settings_field ('smdan_net_options_hide_annotation', __('Hide annotation', 'simple-metadata-annotation'), 'smdan_render_net_options_hide_annotation', 'smd_net_section_options', 'smd_net_section_options');
  add_site_option('smdan_net_hide_metadata_annotation', '');
}


/**
 * Display the option 'Hide dates' in the metabox 'Options'
 *
 * @since   1.3
 */
function smdan_render_net_options_hide_annotation(){
  ?>
  <label for="smdan_net_hide_annotation">
    <input type="checkbox" id="smdan_net_hide_metadata_annotation" name="smdan_net_hide_metadata_annotation" value="true"
      <?php checked('true', get_site_option('smdan_net_hide_metadata_annotation'))?>
    >
  </label><br>
  <span class="description">
      <?php
      esc_html_e('If selected the metadata tags for Simple Metadata Annotation will be hide');
      ?>
  </span>
  <?php
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

    $props_values = isset($_POST['smdan_net_']) ? $_POST['smdan_net_'] : array();
    //if property is frozen, it's automatically shared


    //updating network options in DB
	update_site_option('smdan_net_', $props_values);

	//Grabbing all the site IDs
    $siteids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

    //Going through the sites
    foreach ($siteids as $site_id) {

    	if (1 == $site_id){
    		continue;
    	}

    	switch_to_blog($site_id);

    	//> we merge values received from network settings with local values of every blog

    	$props_values_local = get_option('smdan_') ?: array();
    	$props_values_local = array_merge($props_values_local, $props_values);
    	//<

    	//updating local options
    	update_option('smdan_', $props_values_local);

    	smdan_update_overwrites();
    }

    restore_current_blog();

	// At the end we redirect back to our options page.
    wp_redirect(add_query_arg(array('page' => 'smd_net_set_page',
    'settings-updated' => 'true'), network_admin_url('settings.php')));

    exit;
}

/**
* Update hide option in the metabox 'Options' when save changes is clicked
*
* @since 1.3
*
*/
function smdan_update_net_hide_annotation(){
  //checking admin reffer to prevent direct access to this function
  check_admin_referer('smd_net_section_options-options');

  //getting the value of checkbox
  $is_hide_annotation = isset($_POST['smdan_net_hide_metadata_annotation']) ? $_POST['smdan_net_hide_metadata_annotation'] : '';
  //updating network options
  update_site_option('smdan_net_hide_metadata_annotation', $is_hide_annotation);

  // smd-general-functions.php
  smd_net_overwrite_in_all_sites('smdan_hide_metadata_annotation', $is_hide_annotation );

}
// When simple-metadata option metabox save changes
// It needs simple-metadata plugin installed
add_action('network_admin_edit_smd_update_network_options', 'smdan_update_net_hide_annotation', 10);

//third parameter means priority, bigger => later executed hooked function
add_action( 'network_admin_menu', 'smdan_add_network_settings', 1000);
add_action( 'network_admin_edit_smdan_update_network_locations', 'smdan_update_network_locations');
add_action( 'network_admin_edit_smdan_update_network_options', 'smdan_update_network_options');
