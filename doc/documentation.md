Copy and paste the simple metadata lifecycle plugins, you have to change the function names and then to modify as desired the new metaboxes.
we create a big meta data block, then inside we create fields, here it is in the file (admin/smd....-class.php). To see how we can create our own field, we have to go to the plugin simple-metadata/symbionts/custom-metadata/custom_metadata.php and then we can create using this file for the documentation.

Don't forget to add in the simple-metadata plugin, in each smd-...-related-content folder the lines to write the metatags, for example : 	
if (is_plugin_active('simple-metadata-annotation/simple-metadata-annotation.php') && (isset(get_option('smdan_locations')['site-meta']) || isset(get_option('smdan_locations')['metadata'])){
		smdan_print_tags($type);
	}
Like  you can see smdan_locations, is a function where create in the new plugin
do not forget to modify also the simple-metadata.php file this line :
if (is_plugin_active('simple-metadata-education/simple-metadata-education.php') || is_plugin_active('simple-metadata-lifecycle/simple-metadata-lifecycle.php') || is_plugin_active('simple-metadata-annotation/simple-metadata-annotation.php'))

by adding our new plugins
