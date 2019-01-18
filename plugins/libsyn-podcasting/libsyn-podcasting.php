<?php
/*
Plugin Name: Libsyn Publisher Hub
Plugin URI: https://wordpress.org/plugins/libsyn-podcasting/
Description: Post or edit Libsyn Podcast episodes directly through Wordpress.
Version: 1.0.1.3
Author: Libsyn
Author URI: https://www.libsyn.com
License: GPLv3
*/

define("LIBSYN_NS", "libsynmodule_");
define("LIBSYN_DIR", basename(dirname(__FILE__)));
define("LIBSYN_ADMIN_DIR", basename(dirname(__FILE__))."/admin/");

/* Handle Short Codes */
if ( ! function_exists( 'libsyn_unqprfx_embed_shortcode' ) ) :

	function libsyn_enqueue_script() {
		wp_enqueue_script( 'jquery' );
	}
	add_action( 'wp_enqueue_scripts', 'libsyn_enqueue_script' );
	
	/* Add iframe shortcode */
	function libsyn_unqprfx_embed_shortcode( $atts, $content = null ) {
		$defaults = array(
			'src' => '',
			'width' => '100%',
			'height' => '480',
			'scrolling' => 'no',
			'class' => 'podcast-class',
			'frameborder' => '0',
			'placement' => 'bottom'
		);

		foreach ( $defaults as $default => $value ) { // add defaults
			if ( ! @array_key_exists( $default, $atts ) ) { // hide warning with "@" when no params at all
				$atts[$default] = $value;
			}
		}

		$src_cut = substr( $atts["src"], 0, 35 ); // special case for google maps
		if( strpos( $src_cut, 'maps.google' ) ){
			$atts["src"] .= '&output=embed';
		}

		// get_params_from_url
		if( isset( $atts["get_params_from_url"] ) && ( $atts["get_params_from_url"] == '1' || $atts["get_params_from_url"] == 1 || $atts["get_params_from_url"] == 'true' ) ) {
			if( $_GET != NULL ){
				if( strpos( $atts["src"], '?' ) ){ // if we already have '?' and GET params
					$encode_string = '&';
				}else{
					$encode_string = '?';
				}
				foreach( $_GET as $key => $value ){
					$encode_string .= $key.'='.$value.'&';
				}
			}
			$atts["src"] .= $encode_string;
		}

		$html = '';
		if( isset( $atts["same_height_as"] ) ){
			$same_height_as = $atts["same_height_as"];
		}else{
			$same_height_as = '';
		}
		
		if( $same_height_as != '' ){
			$atts["same_height_as"] = '';
			if( $same_height_as != 'content' ){ // we are setting the height of the iframe like as target element
				if( $same_height_as == 'document' || $same_height_as == 'window' ){ // remove quotes for window or document selectors
					$target_selector = $same_height_as;
				}else{
					$target_selector = '"' . $same_height_as . '"';
				}
				$html .= '
					<script>
					jQuery(function($){
						var target_height = $(' . $target_selector . ').height();
						$("iframe.' . $atts["class"] . '").height(target_height);
						//alert(target_height);
					});
					</script>
				';
			} else { // set the actual height of the iframe (show all content of the iframe without scroll)
				$html .= '
					<script>
					jQuery(function($){
						$("iframe.' . $atts["class"] . '").bind("load", function() {
							var embed_height = $(this).contents().find("body").height();
							$(this).height(embed_height);
						});
					});
					</script>
				';
			}
		}
		$html .= '<iframe style="display:block;" ';
		foreach( $atts as $attr => $value ) {
			if( $attr != 'same_height_as' ){ // remove some attributes
				if( $value != '' ) { // adding all attributes
					$html .= ' ' . $attr . '="' . $value . '"';
				} else { // adding empty attributes
					$html .= ' ' . $attr;
				}
			}
		}
		$html .= '></iframe>';
		//handle player placement
		if($atts['placement'] == "top"){
			$html = $html."<br />";
		} else {
			$html = "<br /><br />".$html;
		}
		return $html;
	}
	add_shortcode( 'iframe', 'libsyn_unqprfx_embed_shortcode' );
	add_shortcode( 'podcast', 'libsyn_unqprfx_embed_shortcode' );

endif; // end of if(function_exists('libsyn_unqprfx_embed_shortcode'))
	
/* Extend WP_Error for message displays */
if ( !class_exists( 'LIBSYN_Notification' ) ) :
class LIBSYN_Notification extends WP_Error {
	
/* Usage:

global $libsyn_notifications;

//The notification message.
$message = __( 'Congratulations! You have successfully updated your profile.', 'your-textdomain' );

//Statuses: error, warning, success, info

//Optionally specify a status and an/or an icon.
$data = array(
  'status' => 'success',
  'icon'   => 'thumbs-up'
);


$libsyn_notifications->add( 'profile-updated', $message, $data );

//or pass just status
$libsyn_notifications->add( 'profile-not-optimal', $message, 'warning' );

*/

  private $html = '';
  private $status = 'error';
  private $icon = '';
  public $container_class = 'libsyn-podcasting-message';
  /**
   * Initialize the notification.
   *
   *
   * @param string|int $code Error code
   * @param string $message Error message
   * @param mixed $data Optional. Error data.
   * @return WP_Error
   */
  public function __construct( $code = '', $message = '', $data = '' ) {
    if ( empty($code) )
      return;
    $this->add( $code, $message, $data );
  }
  /**
   * Add a notification or append additional message to an existing notification.
   *
   * @param string|int $code Notification code.
   * @param string $message Notification message.
   * @param mixed $data Optional. Notification data.
   */
  public function add($code, $message, $data = '') {
    $this->errors[$code][] = $message;
    if(!empty($data))
      $this->error_data[$code] = $data;
    if(!empty($data)) {
		if(is_array($data) && !empty($data['status'])) {
			//optional pass array of data to handle later i.e. icon (below)
			$this->status = $data['status'];
		} else {
			//default to just setting status
			$this->status = $data;
		}
	}
	if(!empty($data['icon'])) $this->icon = $data['icon']; //icon not supported currently
  }
  /**
   * Build the html string with all the notifications.
   *
   * @return string The html for the notifications.
   */
  public function build( $container_class = '' ) {
    $html                 = '';
    $status               = $this->status;
    $icon                 = $this->icon;
    $container_class      = ( $container_class ) ? $container_class : $this->container_class;
    foreach ( $this->errors as $code => $message ) {
      $html .= "<div class=\"notice notice-$status is-dismissible $container_class $container_class-$code\">\n";
	  $html .= "<p><strong><span style=\"display: block; margin: 0.5em 0.5em 0 0; clear: both;\">";
      $html .= $this->get_error_message( $code ) . "\n";
	  $html .= "</span></strong></p>";
      // $html .= "<button type=\"button\" class=\"notice-dismiss\"><span class=\"screen-reader-text\">Dismiss this notice.</span></button></div>";
      $html .= "</div>";
    }
    return $html;
  }
  /**
   * Echo html string with all the notifications.
   *
   * @param string $container_class The class for the notification container.
   * @return void        If at least one notification is present, echoes the notifications HTML.
   */
  public function display( $container_class = '' ) {
    if ( !empty( $this->errors ) )
      echo $this->build( $container_class );
  }
}
/**
 * Create an instance of LIBSYN_Notification for site-wide usage.
 *
 * @since 1.0
 */
$libsyn_notifications = new LIBSYN_Notification();
/**
 * Create an action to display all notifications.
 *
 * It is now possibile to display all the registered notifications just
 * adding do_action('libsyn_notifications') to a page or template file.
 *
 * Using the 'libsyn_container_class' filter, it is also possible to change
 * the default notifications container class.
 *
 * @param mixed $note A LIBSYN_Notification object. Defaults to the global object.
 * @return void        If at least one notification is present, echoes the notifications HTML.
 */
function libsyn_notifications( $note = '' ) {
  global $libsyn_notifications;
  if ( !$libsyn_notifications ) { $libsyn_notifications = $note; }
  if ( !is_libsyn_notification( $libsyn_notifications ) ) { return false; }
  /**
   * Add a filter to change the default notifications container class.
   */
  $container_class = apply_filters( 'libsyn_container_class', $libsyn_notifications->container_class );
  /**
   * Build and display the notifications.
   */
  $libsyn_notifications->display( $container_class );
}
add_action( 'admin_notices', 'libsyn_notifications', 10, 1 );
/**
 * Check whether variable is a LIBSYN_Notification Object.
 *
 * This is just an alias of the is_wp_error class.
 * Checking a class returns true even with class extensions.
 *
 * @uses is_wp_error()
 *
 * @param mixed $note Check if unknown variable is a LIBSYN_Notification or WP_Error object.
 * @return bool True, if LIBSYN_Notification or WP_Error. False, if not LIBSYN_Notification or WP_Error.
 */
function is_libsyn_notification( $note ) {
  return is_wp_error( $note );
}
endif;


/* Add Oembed */
function libsyn_add_oembed_handlers() {
	wp_oembed_add_provider( 'http://html5-player.libsyn.com/*', 'http://oembed.libsyn.com/', false );
}
libsyn_add_oembed_handlers();

/* admin menu */
function libsyn_plugin_admin_menu() {
	add_menu_page('Libsyn Publisher Hub', 'Libsyn Publisher Hub', 'administrator', LIBSYN_ADMIN_DIR . 'settings.php', '', plugins_url('lib/images/icon.png', __FILE__));
	add_submenu_page(LIBSYN_ADMIN_DIR . 'settings.php', 'Settings', 'Settings', 'administrator', LIBSYN_ADMIN_DIR . 'settings.php');
	add_submenu_page(LIBSYN_ADMIN_DIR . 'settings.php', 'Content', 'Content', 'administrator', LIBSYN_ADMIN_DIR . 'content.php');
	add_submenu_page(LIBSYN_ADMIN_DIR . 'settings.php', 'Import Feed', 'Import Feed', 'administrator', LIBSYN_ADMIN_DIR . 'imports.php');
	add_submenu_page(LIBSYN_ADMIN_DIR . 'settings.php', 'Debug Log', 'Debug', 'administrator', LIBSYN_ADMIN_DIR . 'debug_log.php');
	//add_submenu_page( LIBSYN_ADMIN_DIR . 'settings.php', 'Plugin Support', 'Plugin Support', 'administrator', LIBSYN_ADMIN_DIR . 'support.php');
	add_submenu_page( LIBSYN_ADMIN_DIR . 'views/box_about.php', 'About Plugin', 'About', 'administrator', LIBSYN_ADMIN_DIR . '/views/box_about.php');
}
add_action('admin_menu', 'libsyn_plugin_admin_menu');

function libsyn_unqprfx_plugin_meta( $links, $file ) { // add 'Plugin page' and 'Donate' links to plugin meta row
	if ( strpos( $file, 'libsyn.php' ) !== false ) {
		$links = array_merge( $links, array( '<a href="http://libysn.com/libsyn-wordpress-plugin/" title="Libsyn Wordpress Plugin">' . __('Libsyn') . '</a>' ) );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'libsyn_unqprfx_plugin_meta', 10, 2 );

/* Add Libsyn Post Meta */
function add_libsyn_post_meta($post) {
	// if( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
		// if( function_exists( 'register_block_type' ) ) {
			//TODO: Add the action for the Gutenberg Meta
		// }				
	// } else {
		add_meta_box(
			'libsyn-meta-box',
			__( 'Post Episode'),
			'\Libsyn\Post::addLibsynPostMeta',
			'post',
			'normal',
			'default'
		);
	// }
}

/* Include all Libsyn Classes */
/**
 * This will include the base Libsyn Podcast Plugin classes
 * Note this is currently not being used since it caused problems with
 * some clients' PHP versions
 * 
 * @param string $scope 
 * @return array
 */
function build_libsyn_includes($scope) {
	$classesDir = array();
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator(
				plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/'
			),
		RecursiveIteratorIterator::SELF_FIRST
	);
	   foreach($iterator as $file) { 
		if($file->isDir()) {
			$path = $file->getRealpath() ;
			$path2 = PHP_EOL;
			$path3 = $path.$path2;
			$result = end(explode('/', $path3));
			if(str_replace(array("\r\n", "\r", "\n"), "", $result)!=='includes') $classesDir[] = $path;
		}
	}
	$includesArray = array();$libsyn_includes = array();
	foreach($classesDir as $row) foreach (glob($row.'/*.php') as $filename) $includesArray[$filename] = 'include';
	foreach($includesArray as $key => $val) $libsyn_includes[] = $key;
	usort($libsyn_includes, "libsyn_sort_array");
	return array_reverse($libsyn_includes);
}

/**
 * This is the base Libsyn Podcast Plugin classes for include
 * @param string $scope 
 * @return array
 */
function build_libsyn_includes_original($scope) {
	return array (
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/'.'Libsyn.php',
		// plugin_dir_path( __FILE__ ) .  $scope . '/lib/'.'functions.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Api.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Post.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Site.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Playlist.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Utilities.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Cron.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Cron/ImporterEmailer.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Destination.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Importer.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Integration.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Page.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Playlist.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Sanitize.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Table.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'PlaylistWidget.php',
	);
}

/**
 * This is the required files for the logger class 
 * (Requires PHP version 5.4+ since it uses Traits)
 * @param string $scope 
 * @return array
 */
function build_libsyn_logger_includes($scope) {
	return array (
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Psr/Log/LoggerInterface.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Psr/Log/AbstractLogger.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Psr/Log/InvalidArgumentException.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Psr/Log/LoggerAwareInterface.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Psr/Log/LoggerAwareTrait.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Psr/Log/LoggerTrait.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Psr/Log/LogLevel.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Psr/Log/NullLogger.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/lib/Libsyn/' . 'Service/Logger.php',
	);		
}

/**
 * List of plugin files
 * (Requires PHP version 5.4+ since it uses Traits)
 * @param string $scope 
 * @return array
 */
function build_libsyn_include_scripts($scope) {
	return array (
		plugin_dir_path( __FILE__ ) .  $scope . '/config.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/debug_log.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/playlist.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/content.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/settings.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/support.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/views/' . 'box_about.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/views/' . 'box_clear-settings.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/views/' . 'box_playersettings.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/views/' . 'box_support.php',
		plugin_dir_path( __FILE__ ) .  $scope . '/views/support/' . 'initial-setup.php',
	);		
}

/**
 * Simple sort function
 * @param array $a 
 * @param array $b 
 * @return array
 */
function libsyn_sort_array ($a,$b) { return strlen($b)- strlen($a); }

function libsyn_include_file($include) {
	if(file_exists($include)) {
		$is_readable = is_readable($include);
		if($is_readable) {
			try {
				require_once($include); 
			} catch(Exception $e) {
				throw new Exception('Libsyn Podcast library load error');
			}
		} else { //one or more files unreadable
			$data = array(
			  'status' => 'error'
			);
			
			//attempt to make writable.
			@chmod($include, 0777);
			
			//check again
			if(!is_readable($include)) {
				$libsyn_notifications->add('file-unreadable', 'File not readable for the Libsyn Publisher Hub. <em>'.$include.'</em><span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">Please contact your server Administrator or get <a href="https://codex.wordpress.org/Changing_File_Permissions" target=\"_blank\">Help Changing File Permissions</a>', $data);
				if(empty($readableErrors)) {
					$readableErrors = new WP_Error('libsyn-podcasting', $include.' file is not readable and required for the Libsyn Publisher Hub.');
				} else {
					$readableErrors->add('libsyn-podcasting', $include.' file is not readable and required for the Libsyn Publisher Hub.');			
				}
			}
		}
	} else {
		$libsyn_notifications->add('file-missing', 'File is missing and requied for the Libsyn Publisher Hub. <em>'.$include.'</em><span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">Please contact your server Administrator or try <a href="https://codex.wordpress.org/Managing_Plugins" target=\"_blank\">Manually Installing Plugins.</a>', $data);
		if(empty($readableErrors)) {
			$readableErrors = new WP_Error('libsyn-podcasting', $include.' file is missing and required for the Libsyn Publisher Hub.');
		} else {
			$readableErrors->add('libsyn-podcasting', $include.' file is missing and required for the Libsyn Publisher Hub.');
		}			
	}
}

//include plugin.php to run is_plugin_active() check
if(file_exists(ABSPATH . 'wp-admin/includes/plugin.php')) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

//if plugin is active declare plugin
if( is_plugin_active(LIBSYN_DIR.'/'.LIBSYN_DIR.'.php') ) {
	//$libsyn_admin_includes = build_libsyn_includes('admin'); //NOTE: may be able to use this in the future but it is not working on php 5.3
	//global $libsyn_admin_includes;
	global $libsyn_notifications;
	foreach(build_libsyn_includes_original('admin') as $include) {
		libsyn_include_file($include);
	}
	
	//make sure include scripts are readable
	foreach(build_libsyn_include_scripts('admin') as $include) {
		if(file_exists($include)) {
			$is_readable = is_readable($include);
			if($is_readable) {
				//Do nothing.. looks good
			} else { //one or more files unreadable
				$data = array(
				  'status' => 'error'
				);
				
				//attempt to make writable.
				@chmod($include, 0777);
				
				//check again
				if(!is_readable($include)) {
					$libsyn_notifications->add('file-unreadable', 'File not readable for the Libsyn Publisher Hub. <em>'.$include.'</em><span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">Please contact your server Administrator or get <a href="https://codex.wordpress.org/Changing_File_Permissions" target=\"_blank\">Help Changing File Permissions</a>', $data);
					if(empty($readableErrors)) {
						$readableErrors = new WP_Error('libsyn-podcasting', $include.' file is not readable and required for the Libsyn Publisher Hub.');
					} else {
						$readableErrors->add('libsyn-podcasting', $include.' file is not readable and required for the Libsyn Publisher Hub.');
					}
				}
			}
		} else {
			$libsyn_notifications->add('file-missing', 'File is missing and requied for the Libsyn Publisher Hub. <em>'.$include.'</em><span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">Please contact your server Administrator or try <a href="https://codex.wordpress.org/Managing_Plugins" target=\"_blank\">Manually Installing Plugins.</a>', $data);
			if(empty($readableErrors)) {
				$readableErrors = new WP_Error('libsyn-podcasting', $include.' file is missing and required for the Libsyn Publisher Hub.');
			} else {
				$readableErrors->add('libsyn-podcasting', $include.' file is missing and required for the Libsyn Publisher Hub.');
			}
		}		
	}
	
	/* Declare Plugin */
	$plugin = new \Libsyn\Service();
	//check for Logger
	$checkRecommendedPhpVersion = Libsyn\Service\Integration::getInstance()->checkRecommendedPhpVersion();
	if($checkRecommendedPhpVersion){
		foreach(build_libsyn_logger_includes('admin') as $include) {
			if(file_exists($include)) {
				require_once($include); 
			}
		}
		//redeclare plugin with logger
		$plugin = new \Libsyn\Service();
	} else {
		if($plugin->hasLogger) $plugin->logger->error("Plugin:\t Php version is lower than recommended version");
	}
	$api = $plugin->getApi();
	$hasApi = (!empty($api) && $api instanceof \Libsyn\Api);
	if($hasApi !== false) {
		$user_id = $api->getUserId();
		if(!empty($user_id)) {
			add_action( 'add_meta_boxes_post', 'add_libsyn_post_meta');
			
			add_action('save_post', '\Libsyn\Post::handlePost', 10, 2);
			add_filter( 'show_post_locked_dialog', '__return_false' );
			\Libsyn\Post::actionsAndFilters();
		} else {
			if($plugin->hasLogger) $plugin->logger->error("Plugin:\t Libsyn User is not set");
		}
	}

	//playlist
	// add_action( 'widgets_init', function(){
		 // register_widget( 'Libsyn\PlaylistWidget' );
	// });
	
	//playlist ajax
	// add_filter('query_vars','Libsyn\\Playlist::plugin_add_trigger_load_libsyn_playlist');
	// add_action('template_redirect', 'Libsyn\\Playlist::loadLibsynPlaylist');
	// add_filter('query_vars','Libsyn\\Playlist::plugin_add_trigger_load_playlist');
	// add_action('template_redirect', 'Libsyn\\Playlist::loadPlaylist');
	
	//post form ajax
	add_filter('query_vars','Libsyn\\Post::plugin_add_trigger_load_form_data');
	add_action('wp_ajax_load_libsyn_media', 'Libsyn\\Post::loadFormData');
	add_action('wp_ajax_nopriv_load_libsyn_media', 'Libsyn\\Post::loadFormData');
	
	//post form ajax
	add_filter('query_vars','Libsyn\\Post::plugin_add_trigger_remove_ftp_unreleased');
	add_action('wp_ajax_remove_ftp_unreleased', 'Libsyn\\Post::removeFTPUnreleased');
	add_action('wp_ajax_nopriv_remove_ftp_unreleased', 'Libsyn\\Post::removeFTPUnreleased');
	
	//post form player settings dialog ajax
	add_filter('query_vars','Libsyn\\Post::plugin_add_trigger_load_player_settings');
	add_action('wp_ajax_load_player_settings', 'Libsyn\\Post::loadPlayerSettings');
	add_action('wp_ajax_nopriv_load_player_settings', 'Libsyn\\Post::loadPlayerSettings');
	
	//ajax check
	add_filter('query_vars', 'Libsyn\\Utilities::plugin_add_trigger_libsyn_check_ajax');
	add_action( 'wp_ajax_libsyn_check_url', 'Libsyn\\Utilities::checkAjax' );
	add_action( 'wp_ajax_nopriv_libsyn_check_url', 'Libsyn\\Utilities::checkAjax' );
	
	//phpinfo debug_log ajax
	add_filter('query_vars', 'Libsyn\\Utilities::plugin_add_trigger_libsyn_phpinfo');
	add_action( 'wp_ajax_libsyn_phpinfo', 'Libsyn\\Utilities::getPhpinfo' );
	add_action( 'wp_ajax_nopriv_libsyn_phpinfo', 'Libsyn\\Utilities::getPhpinfo' );
	
	//oauth settings save
	add_filter('query_vars', 'Libsyn\\Utilities::plugin_add_trigger_libsyn_oauth_settings');
	add_action( 'wp_ajax_libsyn_oauth_settings', 'Libsyn\\Utilities::saveOauthSettings' );
	add_action( 'wp_ajax_nopriv_libsyn_oauth_settings', 'Libsyn\\Utilities::saveOauthSettings' );
	
	//clear settings
	add_filter('query_vars', 'Libsyn\\Utilities::plugin_add_trigger_libsyn_update_oauth_settings');
	add_action( 'wp_ajax_libsyn_update_oauth_settings', 'Libsyn\\Utilities::updateOauthSettings' );
	add_action( 'wp_ajax_nopriv_libsyn_update_oauth_settings', 'Libsyn\\Utilities::updateOauthSettings' );
	
	/* Add Uninstall Hook */
	register_uninstall_hook( __FILE__, 'Libsyn\\Utilities::uninstallSettings');
	register_deactivation_hook( __FILE__, 'Libsyn\\Utilities::deactivateSettings');
	
	//shortcode embedding
	add_action('save_post', '\Libsyn\Playlist::playlistInit', 10, 2);
	add_shortcode( 'libsyn-playlist', '\Libsyn\Playlist::embedShortcode' );
	
	//setup cron schedules
	$cron = new \Libsyn\Service\Cron();
	add_action('libsyn_cron_importeremailer', 'Libsyn\Service\Cron\ImporterEmailer::ImporterEmailer');//ImporterEmailer Job
	
	//setup meta values
	add_action('wp_head', 'Libsyn\Service\Page::addMeta');
	// <link rel="alternate" type="application/rss+xml" title="Default Podcast Feed" href="https://managedwptest.pairsite.com/feed/podcast/" />
	
	/* Add Meta Links */
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'libsyn_add_plugin_action_links' );
	function libsyn_add_plugin_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=libsyn-podcasting/admin/settings.php">Settings</a>',
				// 'libsyn_debug_log' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=libsyn-podcasting/admin/debug_log.php">Debug Log</a>',
			),
			$links
		);
	}		
	
}