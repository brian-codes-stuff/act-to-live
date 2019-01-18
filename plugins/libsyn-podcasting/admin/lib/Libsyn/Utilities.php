<?php
namespace Libsyn;

class Utilities extends \Libsyn {
	
	/**
	 * Handles WP callback to send variable to trigger AJAX response.
	 * 
	 * @param <array> $vars 
	 * 
	 * @return <array>
	 */
	public static function plugin_add_trigger_libsyn_check_ajax($vars) {
		$vars[] = 'libsyn_check_url';
		return $vars;
	}
	
	/**
	 * Handles WP callback to send variable to trigger AJAX response.
	 * 
	 * @param <array> $vars 
	 * 
	 * @return <array>
	 */
	public static function plugin_add_trigger_libsyn_phpinfo($vars) {
		$vars[] = 'libsyn_phpinfo';
		return $vars;
	}
	
	/**
	 * Handles WP callback to save ajax settings
	 * 
	 * @param <array> $vars 
	 * 
	 * @return <array>
	 */
	public static function plugin_add_trigger_libsyn_oauth_settings($vars) {
		$vars[] = 'libsyn_oauth_settings';
		return $vars;
	}
	
	/**
	 * Handles WP callback to clear outh settings
	 * 
	 * @param <array> $vars 
	 * 
	 * @return <array>
	 */
	public static function plugin_add_trigger_libsyn_update_oauth_settings($vars) {
		$vars[] = 'libsyn_update_oauth_settings';
		return $vars;
	}
	
    /**
     * Disables Wordpress Feed Caching
	 * Needed for feed importer loading of local feeds
	 * (default 12 WP cache)
     * 
     * @param <type> $feed  
     * 
     * @return <type>
     */
	public static function disableFeedCaching( $feed ) {
		$feed->enable_cache( false );
	}
	
    /**
     * Re-enables Wordpress Feed Caching
	 * Needed for feed importer loading of local feeds
	 * (default 12 WP cache)
     * 
     * @param <type> $feed  
     * 
     * @return <type>
     */
	public static function enableFeedCaching( $feed ) {
		$feed->enable_cache( true );
	}
	
	/**
	 * Renders a simple ajax page to check against and test the ajax urls
	 * 
	 * 
	 * @return <mixed>
	 */
	public static function checkAjax() {
		$error = true;
		$checkUrl  = self::getCurrentPageUrl();
		parse_str($checkUrl, $urlParams);
		if(intval($urlParams['libsyn_check_url']) === 1) {
			$error = false;
			$json = true; //TODO: may need to do a check here later.
			//set output
			header('Content-Type: application/json');
			if(!$error) echo json_encode($json);
				else echo json_encode(array());
			exit;
		}
	}
	
	/**
	 * Renders a phpinfo dump and returns json
	 * 
	 * 
	 * @return <mixed>
	 */
	public static function getPhpinfo() {
		$error = true;
		$checkUrl  = self::getCurrentPageUrl();
		parse_str($checkUrl, $urlParams);
		if(intval($urlParams['libsyn_phpinfo']) === 1) {
			$data = self::parse_phpinfo();
			
			// header('Content-Type: application/json');
			header('Content-Type: text/html');
			// if(!empty($data)) echo self::arrayToCsv($data);
			if(!empty($data)) {
				echo "<h3>PHP Server Information</h3>\n" . self::prettyPrintArray($data);
			} else echo "";
			exit;
		}
	}
		
	function prettyPrintArray($arr){
		$retStr = '<ul>';
		if (is_array($arr)){
			foreach ($arr as $key=>$val){
				if (is_array($val)){
					$retStr .= '<li>' . $key . ' => ' . self::prettyPrintArray($val) . '</li>';
				}else{
					$retStr .= '<li>' . $key . ' => ' . $val . '</li>';
				}
			}
		}
		$retStr .= '</ul>';
		return $retStr;
	}
	
    /**
     * Parses phpinfo into usable information format
     * 
     * 
     * @return <type>
     */
	function parse_phpinfo() {
		ob_start(); phpinfo(INFO_MODULES); $s = ob_get_contents(); ob_end_clean();
		$s = strip_tags($s, '<h2><th><td>');
		$s = preg_replace('/<th[^>]*>([^<]+)<\/th>/', '<info>\1</info>', $s);
		$s = preg_replace('/<td[^>]*>([^<]+)<\/td>/', '<info>\1</info>', $s);
		$t = preg_split('/(<h2[^>]*>[^<]+<\/h2>)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
		$r = array(); $count = count($t);
		$p1 = '<info>([^<]+)<\/info>';
		$p2 = '/'.$p1.'\s*'.$p1.'\s*'.$p1.'/';
		$p3 = '/'.$p1.'\s*'.$p1.'/';
		for ($i = 1; $i < $count; $i++) {
			if (preg_match('/<h2[^>]*>([^<]+)<\/h2>/', $t[$i], $matchs)) {
				$name = trim($matchs[1]);
				$vals = explode("\n", $t[$i + 1]);
				foreach ($vals AS $val) {
					if (preg_match($p2, $val, $matchs)) { // 3cols
						$r[$name][trim($matchs[1])] = array(trim($matchs[2]), trim($matchs[3]));
					} elseif (preg_match($p3, $val, $matchs)) { // 2cols
						$r[$name][trim($matchs[1])] = trim($matchs[2]);
					}
				}
			}
		}
		return $r;
	}
	
	/**
	 * Saves Settings form oauth settings for dialog
	 * 
	 * 
	 * @return <mixed>
	 */
	public static function saveOauthSettings() {
		$error = true;
		$checkUrl  = self::getCurrentPageUrl();
		$current_user_id = get_current_user_id();
		parse_str($checkUrl, $urlParams);
		if(intval($urlParams['libsyn_oauth_settings']) === 1) {
			$error = false;
			$json = true; //TODO: may need to do a check here later.
			$sanitize = new \Libsyn\Service\Sanitize();		
			
			if(isset($_POST['clientId'])&&isset($_POST['clientSecret'])) { 
				update_user_option($current_user_id, 'libsyn-podcasting-client', array('id' => $sanitize->clientId($_POST['clientId']), 'secret' => $sanitize->clientSecret($_POST['clientSecret'])), false); 
				$clientId = $_POST['clientId']; 
				$clientSecret = $_POST['clientSecret'];
			}
			if(!empty($clientId)) $json = json_encode(array('client_id' => $clientId, 'client_secret' => $clientSecret));
				else $error = true;
			
			//set output
			header('Content-Type: application/json');
			if(!$error) echo json_encode($json);
				else echo json_encode(array());
			exit;
		}
	}
	
	/**
	 * Saves Settings form oauth settings for dialog
	 * 
	 * 
	 * @return <mixed>
	 */
	public static function updateOauthSettings() {
		$error = true;
		$checkUrl  = self::getCurrentPageUrl();
		$current_user_id = get_current_user_id();
		parse_str($checkUrl, $urlParams);
		if(intval($urlParams['libsyn_update_oauth_settings']) === 1) {
			$error = false;
			$json = true;
			$sanitize = new \Libsyn\Service\Sanitize();
			$json = 'true'; //set generic response to true
			
			if(isset($_GET['client_id']) && isset($_GET['client_secret'])) {
				update_user_option($current_user_id, 'libsyn-podcasting-client', array('id' => $sanitize->clientId($_GET['client_id']), 'secret' =>$sanitize->clientSecret($_GET['client_secret'])), false); 
			} else {
				$error=true;
				$json ='false';
			}
			
			//set output
			header('Content-Type: application/json');
			if(!$error) echo json_encode($json);
				else echo json_encode(array());
			exit;
		}
	}
	
	/**
	 * Clears Settings and deletes table for uninstall
	 * 
	 * 
	 * @return <mixed>
	 */
	public static function uninstallSettings() {
		global $wpdb;
		try {
			self::deactivateSettings();
			$option_names = array(
				'libsyn-podcasting-client',
				'libsyn_api_settings',
				'libsyn-podcasting-feed_import_triggered',
				'libsyn-podcasting-pp_feed_triggered',
				'libsyn-podcasting-feed_import_id',
				'libsyn-podcasting-feed_import_posts',
				'libsyn-podcasting-imported_post_ids'
			);
			$service = new \Libsyn\Service();
			$api_table_name = $service->getApiTableName();
			$option_names[] = $api_table_name;
			$current_user_id = get_current_user_id();
			
			foreach($option_names as $option) {
				// Delete option (Normal WP Setup)
				if(!delete_option( $option )) {
					//user may not have delete privs on database
					update_option($option, array()); //fill with empty array
					update_user_option($current_user_id, $option, array(), false); //fill with empty array
				}
				// For site options in (Multisite WP Setup)
				if(!delete_site_option( $option ) && is_multisite()) {
					//user may not have delete privs on database
					update_site_option($option, array()); //fill with empty array
				}
			}
			if($service->hasLogger) $service->logger->info("Utilities:\tremoved settings.");
		} catch(Exception $e) {
			if($service->hasLogger) $service->logger->error("Utilities:\tuninstallSettings()\t".$e->getMessage());
			return false;
		}
		
		//drop libsyn db table
		if(!empty($api_table_name)) {
			try {
				$wpdb->query( "DROP TABLE IF EXISTS ".$api_table_name ); //old without prefix
				$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}".$api_table_name );				
			} catch(Exception $e) {
				if($service->hasLogger) $service->logger->info("Utilities:\tdropping tables\t".$e->getMessage());
				return false;
			}
			if($service->hasLogger) $service->logger->info("Utilities:\tdropped tables.");
		}
		return true;
	}
	
	/**
	 * Clears Settings and deletes table for uninstall
	 * 
	 * 
	 * @return <boolean>
	 */
	public static function deactivateSettings() {
		try {
			//clear settings first
			$service = new \Libsyn\Service();
			$api_table_name = $service->getApiTableName();
			$user_id = get_current_user_id();
			
			//empty settings
			$dataSettings = array(
				'user_id'				=> $user_id,
				'access_token'			=> null,
				'refresh_token'			=> null,
				'refresh_token_expires'	=> null, 
				'access_token_expires'	=> null,
				'show_id'				=> null,
				'plugin_api_id'			=> null,
				'client_id'				=> null,
				'client_secret'			=> null,
				'is_active'				=> 0
			);
			
			if(function_exists('delete_user_option')) {
				if(!delete_user_option($user_id, $api_table_name, false)) {
					if($service->hasLogger) $service->logger->error("Utilities:\tUnable to remove user option: ".$api_table_name);
					update_user_option($user_id, $api_table_name, json_encode($dataSettings));
				}
			} elseif(function_exists('update_user_option')) {
				update_user_option($user_id, $api_table_name, json_encode($dataSettings));
			} else {
				$deactivate = false;
			}
		} catch(Exception $e) {
			if($service->hasLogger) $service->logger->error("Utilities:\tdeactivateSettings()\t".$e->getMessage());
		}		
		return ;
	}
	
    /**
     * Simple function to check if a string is Json
     * 
     * @param <string> $json_string 
     * 
     * @return <boolean>
     */
	public function isJson($json_string) {
		return (!empty($json_string) && (is_string($json_string) && (is_object(json_decode($json_string)) || is_array(json_decode($json_string, true))))) ? true : false;
	}

	/**
	 * Gets the current page url
	 * @return <string>
	 */
	public static function getCurrentPageUrl() {
		global $wp;
		return add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
	}
	
	/**
	 * function will chmod dirs and files recursively
	 * @param type $start_dir 
	 * @param type $debug (set false if you don't want the function to echo)
	 */
	public static function chmod_recursive($start_dir, $debug = false) {
		$dir_perms = 0755;
		$file_perms = 0644;
		$str = "";
		$files = array();
		if (is_dir($start_dir)) {
			$fh = opendir($start_dir);
			while (($file = readdir($fh)) !== false) {
				// skip hidden files and dirs and recursing if necessary
				if (strpos($file, '.')=== 0) continue;
				$filepath = $start_dir . '/' . $file;
				if ( is_dir($filepath) ) {
					@chmod($filepath, $dir_perms);
					self::chmod_recursive($filepath);
				} else {
					@chmod($filepath, $file_perms);
				}
			}
			closedir($fh);
		}
		if ($debug) {
			echo $str;
		}
	}
	
    /**
     * Libsyn Feed Reader based on WP fetch_feed
	 * See also {@link https://core.trac.wordpress.org/browser/tags/5.0/src/wp-includes/feed.php}
	 * 
	 * Build SimplePie object based on RSS or Atom feed from URL.
	 *
	 * @since 2.8.0
     * 
     * @param mixed $url URL of feed to retrieve. If an array of URLs, the feeds are merged
	 * using SimplePie's multifeed feature.
	 * See also {@link ​http://simplepie.org/wiki/faq/typical_multifeed_gotchas}
     * 
     * @return WP_Error|SimplePie WP_Error object on failure or SimplePie object on success
     */
	
	public function fetchFeed( $url ) {
		if( ! class_exists('\SimplePie', false) ) {
			require_once( ABSPATH . WPINC . '/class-simplepie.php' );
		}
		
		require_once( ABSPATH . WPINC . '/class-wp-feed-cache.php' );
		require_once( ABSPATH . WPINC . '/class-wp-feed-cache-transient.php' );
		require_once( ABSPATH . WPINC . '/class-wp-simplepie-file.php' );
		require_once( ABSPATH . WPINC . '/class-wp-simplepie-sanitize-kses.php' );
		
		$feed = new \SimplePie();
		
		$feed->set_sanitize_class( 'WP_SimplePie_Sanitize_KSES' );
		// We must manually overwrite $feed->sanitize because SimplePie's
		// constructor sets it before we have a chance to set the sanitization class
		$feed->sanitize = new \WP_SimplePie_Sanitize_KSES();
		
		
		/* Customize sanitization */
		$feed->sanitize->enable_cache = false;
		$feed->sanitize->timeout = 30;
		$feed->sanitize->useragent = "Libsyn Publisher Hub FeedReader";

		$feed->set_cache_class( 'WP_Feed_Cache' );
		$feed->set_file_class( 'WP_SimplePie_File' );
		
		$feed->set_feed_url( $url );
		/** This filter is documented in wp-includes/class-wp-feed-cache-transient.php */
		//$feed->set_cache_duration( apply_filters( 'wp_feed_cache_transient_lifetime', 12 * HOUR_IN_SECONDS, $url ) );
		$feed->set_cache_duration( apply_filters( 'wp_feed_cache_transient_lifetime', 60, $url ) ); //changing cache time to 60 seconds (instead of 12 hours)
		/**
		 * Fires just before processing the SimplePie feed object.
		 *
		 * @since 3.0.0
		 *
		 * @param object $feed SimplePie feed object (passed by reference).
		 * @param mixed  $url  URL of feed to retrieve. If an array of URLs, the feeds are merged.
		 */
		do_action_ref_array( 'wp_feed_options', array( &$feed, $url ) );
		$feed->init();
		// $feed->set_output_encoding( get_option( 'blog_charset' ) );
		$feed->set_output_encoding( "UTF-8" ); //set statically to UTF-8

		if ( $feed->error() )
			return new \WP_Error( 'simplepie-error', $feed->error() );
		
		return $feed;
	}
}

?>