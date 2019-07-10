<?php

/**
 * Plugin Name: Simple Metadata Annotation
 * Plugin URI: https://github.com/my-language-skills/simple-metadata-annotation
 * Description: Simple Metadata add-on for annotation of web-site content.
 * Version: 1.1
 * Author: My Language Skills team
 * Author URI: https://github.com/my-language-skills
 * License: GPL 3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain: simple-metadata-annotation
 * Domain Path: /languages
*/



defined ("ABSPATH") or die ("No script assholes!");

require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

//we only enable plugin functionality if main plugin - Simple Metadata - is installed
if(is_plugin_active('simple-metadata/simple-metadata.php')){
	include_once plugin_dir_path( __FILE__ ) . "admin/vocabularies/smdan-annotation-class.php";
	include_once plugin_dir_path( __FILE__ ) . "admin/smdan-admin-settings.php";
	include_once plugin_dir_path( __FILE__ ) . "admin/smdan-output.php";
	include_once plugin_dir_path( __FILE__ ) . "admin/smdan-init-metaboxes.php";
	//loavding network settings only for multisite installation
	if (is_multisite()){
		include_once plugin_dir_path( __FILE__ ) . "network-admin/smdan-network-admin-settings.php";
	}

} else { //if Simple Metadata is not installed we show notice
	if (is_multisite()){ //notice for multisite installation
		add_action( 'network_admin_notices', function () {
			?>
    		<div class="notice notice-info is-dismissible">
        		<p>
							<?php printf(esc_html__('%s functionality is deprecated due to the following reason: %s
												plugin is not installed or not activated. Please, install %s
												in order to fix the problem.', 'simple-metadata'),
											'<strong>"Simple Metadata Annotation"</strong>',
											'<strong>"Simple Metadata"</strong>',
											'<strong>"Simple Metadata"</strong>');
							?>
						</p>
    		</div>
    	<?php
		});
	} else { //notice for single-site installation
		add_action( 'admin_notices', function () {
			?>
    		<div class="notice notice-info is-dismissible">
        		<p>
							<?php printf(esc_html__('%s functionality is deprecated due to the following reason: %s
												plugin is not installed or not activated. Please, install %s
												in order to fix the problem.', 'simple-metadata'),
											'<strong>"Simple Metadata Annotation"</strong>',
											'<strong>"Simple Metadata"</strong>',
											'<strong>"Simple Metadata"</strong>');
							?>
						</p>
    		</div>
    	<?php
		});
	}
}
