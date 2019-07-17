<?php
/**
 * Simple Metadata Annotation
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/my-language-skills/simple-metadata
 * @since             0.1
 * @package           simple-metadata-annotation
 *
 * @wordpress-plugin
 * Plugin Name: Simple Metadata Annotation
 * Plugin URI: https://github.com/my-language-skills/simple-metadata-annotation
 * Description: Simple Metadata add-on for annotation of web-site content.
 * Version: 1.0
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
	//loading network settings only for multisite installation
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

/**
 * Internalization
 * It loads the MO file for plugin's translation
 *
 * @since 1.1
 * @author @davideC00
 *
 */
	function smdan_load_plugin_textdomain() {
    load_plugin_textdomain( 'simple-metadata-annotation', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}

/**
 * Called when the activated plugin has been loaded
 */
add_action( 'plugins_loaded', 'smdan_load_plugin_textdomain' );
