<?php
namespace Libsyn;

class Post extends \Libsyn{
	
	protected $libsyn_wp_post_id;

	/**
	 * Adds the actions for the Post Page
	 * @return mixed
	 */
	public static function actionsAndFilters() {
		add_filter('attachment_fields_to_save', array('\Libsyn\Post', 'updateAttachmentMeta'), 4);
		add_action('wp_ajax_save-attachment-compat', array('\Libsyn\Post', 'mediaExtraFields'), 0, 1);
		
		//ftp/unreleased tab
		add_action('media_upload_libsyn_ftp_unreleased', array('\Libsyn\Post', 'libsynFtpUnreleasedContent') ); // (adds external media content)
		add_action( 'admin_enqueue_scripts', array( '\Libsyn\Post', 'mediaAsset' ) ); // (adds primary media selection asset)
		add_action( 'admin_enqueue_scripts', array( '\Libsyn\Post', 'imageAsset' ) ); // (adds primary media selection asset)
		add_action( 'admin_enqueue_scripts', array( '\Libsyn\Post', 'ftpUnreleasedAsset' ) ); // (adds primary ftp/unreleased selection asset)
		add_action( 'admin_enqueue_scripts', array('\Libsyn\Post', 'enqueueValidation') );  // (adds meta form validation scripts)
		add_action('wp_ajax__libsyn_ajax_fetch_custom_list', array('\Libsyn\Post', '_libsyn_ajax_fetch_custom_list_callback')); // (handles ajax destinations calls)
		add_action('init', array('\Libsyn\Post', 'loadBlockMeta'), 0, 1);
	}
	
    /**
     * Handles registering meta data values.
     * 
     * 
     * @return <type>
     */
	public static function loadBlockMeta () {

		register_meta( 'post', 'libsyn-post-error', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-error_post-type', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );
		

		register_meta( 'post', 'libsyn-post-error_post-permissions', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-error_api', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'playlist-podcast-url', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );
		
		register_meta( 'post', 'libsyn-episode-thumbnail', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-category-selection', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-player_use_thumbnail', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-player_use_theme', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-player_height', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-player_width', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-player_placement', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-player_use_download_link', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-player_use_download_link_text', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-player_custom_color', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-itunes-explicit', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-update-id3', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-release-date', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-simple-download', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-release-date', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-update-release-date', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-is_draft', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', '_isLibsynPost', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-new-media-media', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-subtitle', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-category-selection', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-new-media-image', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-keywords', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-itunes', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-itunes-episode-number', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-itunes-season-number', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-itunes-episode-type', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-itunes-episode-summary', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-itunes-episode-title', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-post-episode-itunes-episode-author', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_meta( 'post', 'libsyn-destination-releases', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'array',
		) );

		register_meta( 'post', 'libsyn-post-episode-advanced-destination-form-data', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'array',
		) );

		register_meta( 'post', 'libsyn-post-episode-advanced-destination-form-data-enabled', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );
	}
	
	public static function libsynFtpUnreleasedContent() {
		$libsyn_error = false;
		$plugin = new Service();
		$current_user_id = $plugin->getCurrentUserId();
		$api = $plugin->retrieveApiById($current_user_id);
		if ($api instanceof \Libsyn\Api) {
			$isRefreshExpired = $api->isRefreshExpired();
			if($isRefreshExpired) { //expired attempt to refresh
				$refreshApi = $api->refreshToken();
			} else {
				$refreshApi = true;
			}
			if($refreshApi) { //successfully refreshed
				$ftp_unreleased = $plugin->getFtpUnreleased($api)->{'ftp-unreleased'};
			} else { $libsyn_error = true; }
		} 

		if($libsyn_error) echo "<p>Oops, you do not have your Libsyn Account setup properly to use this feature, please go to Settings and try again.</p>";
	}
	
	public static function mediaExtraFields($attachment){
		  global $post;
		  update_post_meta($post->ID, 'meta_link', $attachment['attachments'][$post->ID]['meta_link']);
	}
	
	public static function imageAsset() {
		$type = 'image';
		self::mediaSelectAssets($type);
	}
	
	public static function mediaAsset() {
		$type = 'media';
		self::mediaSelectAssets($type);
	}
	
	public static function ftpUnreleasedAsset() {
		$type = 'libsyn';
		self::mediaSelectAssets($type);
	}
	
	public static function enqueueValidation(){
		wp_enqueue_script('jquery_validate', plugins_url(LIBSYN_DIR.'/lib/js/jquery.validate.min.js'), array('jquery'));
		wp_enqueue_script('libsyn_meta_validation', plugins_url(LIBSYN_DIR.'/lib/js/meta_form.js'));
	}

	public static function updateAttachmentMeta($attachment){
		global $post;
		if(!empty($post->ID) && !empty($attachment['attachments']) && !empty($attachment['attachments'][$post->ID])) {
			update_post_meta($post->ID, 'meta_link', $attachment['attachments'][$post->ID]['meta_link']);
		}
	}

	public static function getCurrentPageUrl() {
		global $wp;
		return add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
	}
	
	/**
	 * Sets up Media select button
	 * 
	 * @param <string> $type 
	 * 
	 * @return <mixed>
	 */
	public static function mediaSelectAssets( $type ) {
		wp_enqueue_media();
		wp_register_script( 'libsyn-nmp-media-'.strtolower($type), plugins_url( LIBSYN_DIR.'/lib/js/media.' . strtolower($type) . '.min.js'), array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'libsyn-nmp-media-'.strtolower($type), 'libsyn_nmp_media',
			array(
				'title'     => __( 'Upload or Choose Your Custom ' . ucfirst($type) . ' File', 'libsyn-nmp-'.strtolower($type) ), 
				'button'    => __( 'Insert into Input Field', 'libsyn-nmp-'.strtolower($type) ),
			)
		);
		wp_enqueue_script( 'libsyn-nmp-media-'.strtolower($type) );

	}

	/**
	 * Adds Meta box html
	 * 
	 * @param <WP_Post> $object 
	 * @param <mixed> $box 
	 * 
	 * @return <mixed>
	 */
	public static function addLibsynPostMeta( $object, $box ) {
		$sanitize = new \Libsyn\Service\Sanitize();
		wp_nonce_field( basename( __FILE__ ), 'libsyn_post_episode_nonce' );
		$plugin = new Service();
		$current_user_id = $plugin->getCurrentUserId();
		$api = $plugin->retrieveApiById($current_user_id, true);
		
		/* Handle saved api */
		$render = false; //default rendering to false
		$refreshTokenProblem = false;
		if ($api instanceof \Libsyn\Api) {
			$isRefreshExpired = $api->isRefreshExpired();
			if($isRefreshExpired) { //refresh has expired
				if($plugin->hasLogger) $plugin->logger->info("Post:\tAPI Refresh Expired");
				if(current_user_can( 'upload_files' )===false || current_user_can( 'edit_posts' )===false) $render = false; //check logged in user privileges.
				$refreshApi = $api->refreshToken(); //attempt to refresh before not rendering
				if($refreshApi) { //successfully refreshed
					$api = $plugin->retrieveApiById($current_user_id);
					$render = true;
				}
			}
			$showId = $api->getShowId();
			if(empty($showId)) { //show_id has not been set in settings
				if(empty($message) && !is_array($messages)) { //make sure message has not been set yet
					$messages = array('post' => array('error', 'notice'));
				}
				$messages['post']['error'][] = "Show Id has not been set yet in the settings.  Please go to the Libsyn Podcasting <strong><a href=\"" . $plugin->admin_url('admin.php') . "?page=" . LIBSYN_DIR . "%2Fadmin%2Fsettings.php\">Settings Page</a></strong> to correct this.";
				$render = false;
				$showIdProblem = true;
			}
		}
		if(!empty($api) && $api instanceof \Libsyn\Api && !is_null($api->getShowId())) { $render = (!isset($showIdProblem)) ? true : false; } else { $render = false; $refreshTokenProblem = true; }
		$isPowerpress = \Libsyn\Service\Integration::getInstance()->checkPlugin('powerpress');
		?>
		
		<?php wp_enqueue_script( 'jquery-ui-dialog', array('jquery-ui')); ?>
		<?php wp_enqueue_script( 'jquery-ui-datepicker', array('jquery-ui')); ?>
		<?php wp_enqueue_style( 'wp-jquery-ui-dialog'); ?>
		<?php wp_enqueue_style( 'wp-jquery-ui-datepicker'); ?>
		<?php wp_enqueue_script( 'libsyn-player-settings-post-page', plugins_url(LIBSYN_DIR . '/lib/js/libsyn_player_settings_post_page.js')); ?>
		<?php wp_enqueue_style( 'libsyn-jquery-ui', plugins_url(LIBSYN_DIR . '/lib/css/jquery-ui-theme/jquery-ui-1.10.0.custom.css')); ?>
		
		<?php /* Loading Spinner */ ?>
		<div class="loading-libsyn-form" style="background: url(<?php echo plugins_url(LIBSYN_DIR.'/lib/images/spinner.gif'); ?>);background-repeat: no-repeat;background-position: left center;display: none;"><br><br><br><br>Loading...</div>
		<div class="configuration-problem" style="display: none;">
			<p>Please configure your <a href="<?php echo $plugin->admin_url('admin.php'); ?>?page=<?php echo LIBSYN_DIR; ?>/admin/settings.php">Libsyn Podcast Plugin</a> with your Libsyn Hosting account to use this feature.</p>
		</div>
		<div class="api-problem-box" style="display: none;">
			<p> We encountered a problem with the Libsyn API.  Please Check your <a href="<?php echo $plugin->admin_url('admin.php'); ?>?page=<?php echo LIBSYN_DIR; ?>/admin/settings.php">settings</a> and try again.</p>
		</div>
		<?php /* Render Main Box */ ?>
		<?php IF($render): ?>
		<?php wp_enqueue_script( 'jquery-filestyle', plugins_url(LIBSYN_DIR . '/lib/js/jquery-filestyle.min.js')); ?>
		<?php wp_enqueue_style( 'jquery-filestyle', plugins_url(LIBSYN_DIR . '/lib/css/jquery-filestyle.css')); ?>
		<?php wp_enqueue_style( 'jquery-simplecombobox', plugins_url(LIBSYN_DIR . '/lib/css/jquery.libsyn-scombobox.min.css')); ?>
		<?php wp_enqueue_script( 'jquery-simplecombobox', plugins_url(LIBSYN_DIR . '/lib/js/jquery.libsyn-scombobox.min.js')); ?>
		<?php wp_enqueue_style( 'libsyn-meta-form', plugins_url(LIBSYN_DIR . '/lib/css/libsyn_meta_form.css')); ?>
		<?php wp_enqueue_style( 'libsyn-meta-form-boxes', plugins_url(LIBSYN_DIR . '/lib/css/libsyn_meta_boxes.css' )); ?>
		<?php wp_enqueue_script( 'colorPicker', plugins_url(LIBSYN_DIR . '/lib/js/jquery.colorpicker.js' )); ?>
		<?php wp_enqueue_style( 'colorPickerStyle', plugins_url(LIBSYN_DIR . '/lib/css/jquery.colorpicker.css' )); ?>
		<?php wp_enqueue_script( 'libsyn-advanced-destination', plugins_url(LIBSYN_DIR . '/lib/js/libsyn_advanced_destination_post_page.js')); ?>		
		
		<?php //handle admin notices
			if(!isset($messages) || !is_array($messages)) { //make sure messages has not been set yet
				$messages = array('post' => array('error', 'notice'));
			}
			//remove post error if any
			$libsyn_error_post = get_post_meta($object->ID, 'libsyn-post-error', true);
			if($libsyn_error_post == 'true') {
				$messages['post']['error'][] = "There was an error posting content, please check settings and try again.";
			}
			delete_post_meta($object->ID, 'libsyn-post-error', 'true', true);
			
			//remove post error if any
			$libsyn_error_post_type = get_post_meta($object->ID, 'libsyn-post-error_post-type', true);
			if($libsyn_error_post_type == 'true') {
				$messages['post']['error'][] = "There was an error when creating the Libsyn Post, looks like you may have a custom post type setup in Wordpress.";
			}
			delete_post_meta($object->ID, 'libsyn-post-error_post-type', 'true', true);
			
			//remove post error if any
			$libsyn_error_post_permissions = get_post_meta($object->ID, 'libsyn-post-error_post-permissions', true);
			if($libsyn_error_post_permissions == 'true') {
				$messages['post']['error'][] = "There was an error when creating the Libsyn Post, looks like your user does not have permissions to post in Wordpress.";
			}
			delete_post_meta($object->ID, 'libsyn-post-error_post-permissions', 'true', true);
			
			$libsyn_error_api = get_post_meta($object->ID, 'libsyn-post-error_api', true);
			if($libsyn_error_api == 'true') {
				$messages['post']['error'][] = "There was an error with your Libsyn configuration, please check the settings page and try again.";
			}
			delete_post_meta($object->ID, 'libsyn-post-error_api', 'true', true);

			//render error messages
			if(!empty($messages['post'])){
				if(!empty($messages['post']['error']) && is_array($messages['post']['error'])) { //display error messages
					foreach ($messages['post']['error'] as $post_message) {
						if($plugin->hasLogger) $plugin->logger->error("Post:\t".$post_message);
						?><div class="error is-dismissible"><p><?php _e($post_message) ?></p></div><?php
					}
				}
				
				if(!empty($messages['post']['notice']) && is_array($messages['post']['notice'])) { //display admin messages
					foreach ($messages['post']['notice'] as $post_message) {
						if($plugin->hasLogger) $plugin->logger->info("Post:\t".$post_message);
						?><div class="updated is-dismissible"><p><?php _e($post_message) ?></p></div><?php
					}
				}				
			}

			/* Set Notifications */
			global $libsyn_notifications;
			do_action('libsyn_notifications');
		?>
		
		<?php /* Playlist Page Dialog */?>
		<div id="libsyn-playlist-page-dialog" class="hidden" title="Create Podcast Playlist">
				<span style="font-weight:bold;">Playlist Type:</span><br>
				<input type="radio" name="playlist-media-type" value="audio" id="playlist-media-type-audio" checked="checked"></input>Audio
				<input type="radio" name="playlist-media-type" value="video" id="playlist-media-type-video"></input>Video
				<div style="padding:5px;display:none;" id="playlist-dimensions-div">
					<label for="playlist-video-width">Width </label>
					<input name="playlist-video-width" id="playlist-video-width" type="text" value="640"></input>
					<br>
					<label for="playlist-video-height">Height</label>
					<input name="playlist-video-height" id="playlist-video-height" type="text" value="360"></input>
				</div>
				<br><span style="font-weight:bold;">Playlist Source:</span><br>
				<input type="radio" name="playlist-feed-type" value="<?php if(isset($api)&&($api!==false)) _e("libsyn-podcast-".$api->getShowId());else _e("my-podcast"); ?>" id="my-podcast" checked="checked"></input>My Libsyn Podcast
				<br>
				<input type="radio" name="playlist-feed-type" value="other-podcast" id="other-podcast"></input>Other Podcast
				<label for="<?php _e( 'podcast-url', 'playlist-dialog' ); ?>"><?php _e( 'Podcast Url:' ); ?></label>
				<input class="widefat" id="<?php _e( "podcast-url", 'playlist-dialog' ); ?>" name="<?php _e( "podcast-url", 'playlist-dialog' ) ?>" type="text" value="<?php _e(esc_attr( get_post_meta( $object->ID, 'playlist-podcast-url', true ) )); ?>" type="url" style="display:none;" class="other-url" placeholder="http://www.your-wordpress-site.com/rss"></input>
			</p>
		</div>
		<div id="libsyn-player-settings-page-dialog" class="hidden" title="Player Settings"></div>
		<script type="text/javascript">
			(function ($){
				$(document).ready(function() {
					//set form up
					var data = '<?php echo $object->ID; ?>';
					$('.loading-libsyn-form').fadeIn('normal');
					$('.libsyn-post-form').hide();
					window.libsyn_admin_site_url = '<?php echo $plugin->admin_url(); ?>';
					window.libsyn_data_id = data;

					var setOverlays = function() {						
						//make sure overlays are not over dialogs
						$('.ui-widget-overlay').each(function() {
							$(this).css('z-index', 999);
							$(this).attr('style', 'z-index:999 !important;');
							if(($(this).css("z-index") != typeof 'undefined') && $(this).css("z-index") >= 1000) {
								//worse case scenario hide the overlays
								$(this).fadeOut('fast');
							}
						});
						$('.ui-dialog-title').each(function() {
							$(this).css('z-index', 1002);
						});
						$('.ui-dialog').each(function() {
							$(this).css('z-index', 1002);
						});
						$('.ui-colorpicker').each(function() {
							$(this).css('z-index', 1003);
						});
					}

					//run ajax
					$.ajax({
						url: window.libsyn_admin_site_url + '<?php  echo '?action=load_libsyn_media&load_libsyn_media=1'; ?>',
						type: 'POST',
						data: data,
						cache: false,
						dataType: 'json',
						processData: false, // Don't process the files
						contentType: false, // Set content type to false as jQuery will tell the server its a query string request
						success: function(data, textStatus, jqXHR) {
							 if(!data) {
								//Handle errors here
								$('.loading-libsyn-form').hide();
								$('.api-problem-box').fadeIn('normal');
							 } else if(typeof data.error == 'undefined') { //Successful response
								
								//remove ftp/unreleased
								$.ajax({
									url : window.libsyn_admin_site_url + '<?php echo '?action=remove_ftp_unreleased&remove_ftp_unreleased=1'; ?>',
									type: 'POST',
									data: data,
									cache: false,
									dataType: 'json',
									processData: false, // Don't process the files
									contentType: false, // Set content type to false as jQuery will tell the server its a query string request
									success : function(data) {          
										//do nothing
									},
									error : function(request,error)
									{
										//error
										//alert("Request: "+JSON.stringify(request));
									}
								});
								
								//show div & hide spinner
								$('.loading-libsyn-form').hide();
								$('.libsyn-post-form').fadeIn('normal');								
								$("#libsyn-categories").empty();
								
								//handle categories section
								if(typeof data != 'undefined' && data != false && data.length > 0) {
									for(i = 0; i < data.length; i++) {
										if(i==0) { var firstValue = data[i]; }
										$("#libsyn-categories").append("<option value=\"" + data[i] + "\">" + data[i] + "</option>");
									}
								}

								var savedCategory = "<?php echo esc_attr( get_post_meta( $object->ID, 'libsyn-post-episode-category-selection', true ) ); ?>";
								if(savedCategory.length > 0) var firstValue = savedCategory;
								$("#libsyn-categories").scombobox({
									highlight: true,
									highlightInvalid: false
								});
								$("#libsyn-post-episode-category-selection").attr({'class': 'scombobox-value'}).appendTo($("#libsyn-categories"));
								$("#libsyn-categories > input.scombobox-display").val(firstValue);
								$('#libsyn-categories > .scombobox-value[name=libsyn-post-episode-category]').val(firstValue);
								$("#libsyn-categories").scombobox('change', function() {
									$("#libsyn-post-episode-category-selection").val($("#libsyn-categories .scombobox-display").val());
								});
																
								$('#libsyn-categories').children('.scombobox-display').focus(function(){
									$(this).css({'border': '1px solid #60a135'});
									$('.scombobox-dropdown-background').css({'border-color': '#60a135 #60a135 #60a135 -moz-use-text-color', 'border': '1px solid #60a135'});
								}).on("blur", function() {
									$(this).css({'border': '1px solid #CCC'});
									$('.scombobox-dropdown-background').css({'border': '1px solid #CCC', 'border-color': '#ccc #ccc #ccc -moz-use-text-color'});
									var currVal = $("#libsyn-categories > .scombobox-display").val();
									var sel = $('#libsyn-categories select');
									var opt = $('<option>').attr('value', currVal).html(currVal);
									sel.append(opt);
								});

							 } else {
								//Handle errors here
								$('.loading-libsyn-form').hide();
								$('.libsyn-post-form').fadeIn('normal');
								$('.options-error').fadeIn('normal');
								//$('.api-problem-box').fadeIn('normal');
							 }
						},
						error: function(jqXHR, textStatus, errorThrown) {
							// Handle errors here
							$('.loading-libsyn-form').hide();
							$('.configuration-problem').fadeIn('normal');
						}
					});
										
					//Load Player Settings
					$("#libsyn-player-settings-page-dialog").load(window.libsyn_admin_site_url + "<?php echo '?action=load_player_settings&load_player_settings=1'; ?>", function() {
						//add stuff to ajax box
						$("#player_use_theme_standard_image").append('<img src="<?php echo plugins_url( LIBSYN_DIR . '/lib/images/player-preview-standard.jpg'); ?>" style="max-width:95%;" />');
						$("#player_use_theme_mini_image").append('<img src="<?php echo plugins_url( LIBSYN_DIR . '/lib/images/player-preview-standard-mini.jpg'); ?>" style="max-width:95%;" />');
						$("#player_use_theme_custom_image").append('<img src="<?php echo plugins_url( LIBSYN_DIR . '/lib/images/custom-player-preview.jpg'); ?>" style="max-width:95%;" />');
						$(".post-position-shape-top").append('<img src="<?php echo plugins_url( LIBSYN_DIR . '/lib/images/player_position.png'); ?>" style="vertical-align:top;" />');
						$(".post-position-shape-bottom").append('<img src="<?php echo plugins_url( LIBSYN_DIR . '/lib/images/player_position.png'); ?>" style="vertical-align:top;" />');
						
						//validate button
						$('<a>').text('Validate').attr({
							class: 'button'
						}).click( function() {
							var current_feed_redirect_input = validator_url + encodeURIComponent($("#feed_redirect_input").attr('value'));
							window.open(current_feed_redirect_input);
						}).insertAfter("#feed_redirect_input");
						
						//set default value for player use thumbnail
						<?php
							$postPlayerUseThumbnail = get_post_meta( $object->ID, 'libsyn-post-episode-player_use_thumbnail', true );  
							$playerUseThumbnail = (!is_null($postPlayerUseThumbnail)&&!empty($postPlayerUseThumbnail))?$postPlayerUseThumbnail:get_user_option('libsyn-podcasting-player_use_thumbnail');
						?>
						var playerUseThumbnail = '<?php _e($playerUseThumbnail); ?>';
						if(playerUseThumbnail == 'use_thumbnail') {
							$('#player_use_thumbnail').prop('checked', true);
						}
						
						//set default value of player theme
						<?php
							$postPlayerTheme = get_post_meta( $object->ID, 'libsyn-post-episode-player_use_theme', true );
							$playerTheme = (!is_null($postPlayerTheme)&&!empty($postPlayerTheme))?$postPlayerTheme:get_user_option('libsyn-podcasting-player_use_theme');
						?>
						var playerTheme = '<?php echo $playerTheme; ?>';
						if(playerTheme == 'standard') {
							$('#player_use_theme_standard').prop('checked', true);	
							//check if player_use_thumbnail is checked
							if($('#player_use_thumbnail').is(':checked')) {
								if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
							} else {
								$('#player_height').attr({"min": "45"});
								if(parseInt($('#player_height').val()) < 45) $('#player_height').val(45);
							}						
						} else if(playerTheme == 'mini') {
							$('#player_use_theme_mini').prop('checked', true);	
							//check if player_use_thumbnail is checked
							if($('#player_use_thumbnail').is(':checked')) {
								if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
							} else {
								$('#player_height').attr({"min": "26"});
								if(parseInt($('#player_height').val()) < 26) $('#player_height').val(26);
							}
						} else if(playerTheme == 'custom') {
							$('#player_use_theme_custom').prop('checked', true);
							$('#player_width_tr').fadeOut('fast', function() {
								$('#player_custom_color_picker').fadeIn('normal');
							});
							//check if player_use_thumbnail is checked
							if($('#player_use_thumbnail').is(':checked')) {
								if(parseInt($('#player_height').val()) < 90) $('#player_height').val(90);
								if(parseInt($('#player_width').val()) < 450) $('#player_height').val(450);
							} else {
								$('#player_height').attr({"min": "90"});
								if(parseInt($('#player_height').val()) < 90) $('#player_height').val(90);
							}
						} else { //default: getPlayerTheme is not set
							//set default value of player theme to standard if not saved
							$('#player_use_theme_standard').prop('checked', true);
							
							//check if player_use_thumbnail is checked
							if($('#player_use_thumbnail').is(':checked')) {
								if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
							} else {
								$('#player_height').attr({"min": "45"});
								if(parseInt($('#player_height').val()) < 45) $('#player_height').val(45);
							}
						}
						
						//player theme checkbox settings
						$('#player_use_theme_standard').change(function() {
							if($('#player_use_theme_standard').is(':checked')) {
								//check if player_use_thumbnail is checked
								if($('#player_use_thumbnail').is(':checked')) {
									if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
								} else {
									$('#player_height').attr({"min": "45"});
									if(parseInt($('#player_height').val()) < 45) $('#player_height').val(45);
								}
								$('#player_custom_color_picker').fadeOut('fast', function() {
									//$('#player_width_tr').fadeIn('normal');
								});
							} else if($('#player_use_theme_mini').is(':checked')) {
								//check if player_use_thumbnail is checked
								if($('#player_use_thumbnail').is(':checked')) {
									if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
								} else {
									$('#player_height').attr({"min": "26"});
									if(parseInt($('#player_height').val()) < 26) $('#player_height').val(26);
								}
								$('#player_custom_color_picker').fadeOut('fast', function() {
									//$('#player_width_tr').fadeIn('normal');
								});
							} else if($('#player_use_theme_custom').is(':checked')) {
								$('#player_height').attr({"min": "90"});
								$('#player_width').attr({"min": "450"});
								if(parseInt($('#player_height').val()) > 90) $('#player_height').val(90);
								$('#player_width_tr').fadeOut('fast', function() {
									$('#player_custom_color_picker').fadeIn('normal');
								});
							}
						});
						$('#player_use_theme_mini').change(function() {
							if($('#player_use_theme_standard').is(':checked')) {
								//check if player_use_thumbnail is checked
								if($('#player_use_thumbnail').is(':checked')) {
									if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
								} else {
									$('#player_height').attr({"min": "45"});
									if(parseInt($('#player_height').val()) < 45) $('#player_height').val(45);
								}
								$('#player_custom_color_picker').fadeOut('fast', function() {
									//$('#player_width_tr').fadeIn('normal');
								});
							} else if($('#player_use_theme_mini').is(':checked')) {
								//check if player_use_thumbnail is checked
								if($('#player_use_thumbnail').is(':checked')) {
									if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
								} else {
									$('#player_height').attr({"min": "26"});
									if(parseInt($('#player_height').val()) < 26) $('#player_height').val(26);
								}
								$('#player_custom_color_picker').fadeOut('fast', function() {
									//$('#player_width_tr').fadeIn('normal');
								});
							} else if($('#player_use_theme_custom').is(':checked')) {
								$('#player_height').attr({"min": "90"});
								$('#player_width').attr({"min": "450"});
								if(parseInt($('#player_height').val()) > 90) $('#player_height').val(90);
								$('#player_width_tr').fadeOut('fast', function() {
									$('#player_custom_color_picker').fadeIn('normal');
								});
							}
						});
						$('#player_use_theme_custom').change(function() {
							if($('#player_use_theme_standard').is(':checked')) {
								//check if player_use_thumbnail is checked
								if($('#player_use_thumbnail').is(':checked')) {
									if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
								} else {
									$('#player_height').attr({"min": "45"});
									if(parseInt($('#player_height').val()) < 45) $('#player_height').val(45);
								}
								$('#player_custom_color_picker').fadeOut('fast', function() {
									//$('#player_width_tr').fadeIn('normal');
								});
							} else if($('#player_use_theme_mini').is(':checked')) {
								//check if player_use_thumbnail is checked
								if($('#player_use_thumbnail').is(':checked')) {
									if(parseInt($('#player_height').val()) < 200) $('#player_height').val(200);
								} else {
									$('#player_height').attr({"min": "26"});
									if(parseInt($('#player_height').val()) < 26) $('#player_height').val(26);
								}
								$('#player_custom_color_picker').fadeOut('fast', function() {
									//$('#player_width_tr').fadeIn('normal');
								});
							} else if($('#player_use_theme_custom').is(':checked')) {
								$('#player_height').attr({"min": "90"});
								$('#player_width').attr({"min": "450"});
								if(parseInt($('#player_height').val()) > 90) $('#player_height').val(90);
								$('#player_width_tr').fadeOut('fast', function() {
									$('#player_custom_color_picker').fadeIn('normal');
								});
							}
						});
						
						//player values height & width
						<?php
							$postPlayerHeight = get_post_meta( $object->ID, 'libsyn-post-episode-player_height', true );  
							$playerHeight = (!is_null($postPlayerHeight)&&!empty($postPlayerHeight))?$postPlayerHeight:get_user_option('libsyn-podcasting-player_height'); 
						?>
						<?php
							$postPlayerWidth = get_post_meta( $object->ID, 'libsyn-post-episode-player_width', true );  
							$playerWidth = (!is_null($postPlayerWidth)&&!empty($postPlayerWidth))?$postPlayerWidth:get_user_option('libsyn-podcasting-player_width'); 
						?>
						var playerHeight = parseInt('<?php echo $playerHeight; ?>');
						var playerWidth = parseInt('<?php echo $playerWidth; ?>');
						
						//height
						if(isNaN(playerHeight)) {
							$('#player_height').val(360);
						} else {
							if($('#player_use_theme_standard').is(':checked')) {
								if(playerHeight >= 45) $('#player_height').val(playerHeight);
									else $('#player_height').val(45);
							} else if($('#player_use_theme_mini').is(':checked')) {
								if(playerHeight >= 26) $('#player_height').val(playerHeight);
									else $('#player_height').val(26);
							} else if($('#player_use_theme_custom').is(':checked')) {
								if(playerHeight >= 90) $('#player_height').val(playerHeight);
									else $('#player_height').val(90);
							} else {
								$('#player_height').val(360);
							}
						}
						
						//width
						if(isNaN(playerWidth)) {
							$('#player_width').val(450);
						} else {
							if($('#player_use_theme_standard').is(':checked')) {
								if(playerWidth >= 200) $('#player_width').val(playerWidth);
									else $('#player_width').val(200);
							} else if($('#player_use_theme_mini').is(':checked')) {
								if(playerWidth >= 100) $('#player_width').val(playerWidth);
									else $('#player_width').val(100);
							} else if($('#player_use_theme_custom').is(':checked')) {
								if(playerWidth >= 450) $('#player_width').val(playerWidth);
									else $('#player_width').val(450);
							} else {
								$('#player_width').val(450);
							}
						}
						
						//player use thumbnail checkbox settings
						$('#player_use_thumbnail').change(function() {
							if($(this).is(':checked')) {
								//TODO: Add playlist support here
								if($('#player_use_theme_custom').is(':checked')) {
									if($('#player_width').val() == '' || parseInt($('#player_width').val()) <= 450) { //below min width
										$('#player_width').val("450");
										$('#player_width').attr({"min": "450"});
									}
								} else {
									if($('#player_height').val() == '' || parseInt($('#player_height').val()) <= 200) { //below min height
										$('#player_height').val("200");
										$('#player_height').attr({"min": "200"});
									}
								}
							} else {
								if($('#player_use_theme_standard').is(':checked')) {
									$('#player_height').attr({"min": "45"});
								} else if($('#player_use_theme_mini').is(':checked')){
									$('#player_height').attr({"min": "26"});
								} else if($('#player_use_theme_custom').is(':checked')){
									$('#player_height').attr({"min": "90"});
									$('#player_width').attr({"min": "450"});
								}
								
							}
						});
						
						//player placement checkbox settings
						<?php
							$postPlayerPlacement = get_post_meta( $object->ID, 'libsyn-post-episode-player_placement', true );  
							$playerPlacement = (!is_null($postPlayerPlacement)&&!empty($postPlayerPlacement))?$postPlayerPlacement:get_user_option('libsyn-podcasting-player_placement');
						?>
						var playerPlacement = '<?php echo $playerPlacement; ?>';
						if(playerPlacement == 'top') {
							$('#player_placement_top').prop('checked', true);
						} else if(playerPlacement == 'bottom') {
							$('#player_placement_bottom').prop('checked', true);
						} else { //player placement is not set
							$('#player_placement_top').prop('checked', true);
						}
						
						<?php 
						$postUseDownloadLink = get_post_meta( $object->ID, 'libsyn-post-episode-player_use_download_link', true );  
						$playerUseDownloadLink = (!is_null($postUseDownloadLink)&&!empty($postUseDownloadLink))?$postUseDownloadLink:get_user_option('libsyn-podcasting-player_use_download_link');
						?>
						var playerUseDownloadLink = '<?php echo $playerUseDownloadLink; ?>';
						<?php 
						$postUseDownloadLinkText = get_post_meta( $object->ID, 'libsyn-post-episode-player_use_download_link_text', true );
						$playerUseDownloadLinkText = (!is_null($postUseDownloadLinkText)&&!empty($postUseDownloadLinkText))?$postUseDownloadLinkText:get_user_option('libsyn-podcasting-player_use_download_link_text');
						?>
						var playerUseDownloadLinkText = '<?php _e($playerUseDownloadLinkText); ?>';
						if(playerUseDownloadLink == 'use_download_link') {
							$('#player_use_download_link').prop('checked', true);
							if(playerUseDownloadLinkText == '') {
								$('#player_use_download_link_text').val('');
							} else if(playerUseDownloadLinkText.length >= 1) {
								$('#player_use_download_link_text').val(playerUseDownloadLinkText);
							}
							$('#player_use_download_link_text_div').fadeIn('normal');
						}
						
						//player theme checkbox settings
						$('#player_use_download_link').change(function() {
							if($(this).is(':checked')) {
								$('#player_use_download_link_text_div').fadeIn('normal');
							} else {
								$('#player_use_download_link_text_div').hide('fast');
								$('#player_use_download_link_text').val('Download Episode!');
							}
						});
						
						<?php 
						$postCustomColor = get_post_meta( $object->ID, 'libsyn-post-episode-player_custom_color', true );
						$playerCustomColor = (!is_null($postCustomColor)&&!empty($postCustomColor))?$postCustomColor:get_user_option('libsyn-podcasting-player_custom_color', $current_user_id);
						?>
						<?php if(!isset($playerCustomColor) && empty($playerCustomColor)) { ?>
						var playerCustomColor = '87a93a';
						<?php } else { ?>
						var playerCustomColor = '<?php  echo ($playerCustomColor); ?>';
						$('#player_custom_color').attr('value', playerCustomColor);
						$('#player_custom_color').css('background-color', "#" + playerCustomColor);
						<?php } ?>
						
						//color picker settings
						$('#player_custom_color_picker_button, #player_custom_color').click(function() {
							if(typeof libsyn_player_color_picker == 'undefined') {
								// css class can be found in the color picker's library: libsyn-colorpicker.css
								var libsyn_player_color_picker = $('#player_custom_color').colorpicker({
									title: "Choose a color "
									//, parts: ["header", "map", "bar", "hex", "preview", "footer"]
									, parts: 'full'
									, modal: true
									, showOn: 'focus'
									, color: ('#' + playerCustomColor)
									, altField: $('#player_custom_color')
									, okOnEnter: true
									// , showCloseButton: true
									, open: function(ui, event) {
										setOverlays();
										$(".ui-dialog-titlebar-close", ui.dialog | ui).attr("class", "ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close");
									}
									, select: function(event, color) {
										$('#player_custom_color').attr('value', color.formatted);
									}
								});
								libsyn_player_color_picker.colorpicker('open');
								$('.ui-widget-overlay').bind('click',function() {
									libsyn_player_color_picker.colorpicker('close');
								});
							} else {
								libsyn_player_color_picker.colorpicker('close');
							}
						});
					});
		
					$( "#libsyn-upload-media-dialog" ).dialog({
						autoOpen: false,
						draggable: true,
						height: 'auto',
						width: 500,
						modal: true,
						resizable: false,
						open: function(){
							setOverlays();
							$('.ui-widget-overlay').bind('click',function(){
								$('#libsyn-upload-media-dialog').dialog('close');
							})
						},
						buttons: [
							{
								id: "dialog-button-cancel",
								text: "Cancel",
								click: function(){
									$('#libsyn-upload-media-dialog').dialog('close');
								}
							},
							{
								id: "dialog-button-upload",
								text: "Upload",
								class: "button-primary",
								click: function(){
									$('#dialog-button-upload').attr("disabled", true);
									var dlg = $(this);
									var url = "<?php echo $plugin->getApiBaseUri().'/media'; ?>";
									$("#libsyn-media-progressbox-area").show();
									$('#libsyn-upload-media-error-text').html('');
									var mediaUploadForm = new FormData();
									mediaUploadForm.append('show_id', '<?php if(isset($api) && $api instanceof \Libsyn\Api) echo $api->getShowId(); ?>');
									mediaUploadForm.append('form_access_token', '<?php if(isset($api)  && $api instanceof \Libsyn\Api) echo $api->getAccessToken(); ?>');
									mediaUploadForm.append('upload', $('#libsyn-media-file-upload')[0].files[0]);
									$.ajax({
										url: url,
										type: 'POST',
										data: mediaUploadForm,
										processData: false,
										contentType: false,
										success: function (response, textStatus, xhr) {
												$("#libsyn-new-media-media").val("libsyn-upload-" + response._embedded.media.content_id).attr("readonly", true);
												$("#libsyn-media-progressbox-area").slideUp();
												$("#libsyn-preview-media-button").hide();
												dlg.dialog('close');
												//dlg.empty();
												
												
												//add preview
												var file_class = response._embedded.media.file_class;
												var mime_type = response._embedded.media.mime_type;
												mime_type = mime_type.replace("x-","");
												var media_url = response._embedded.media.secure_url;
												var preview_url = media_url.replace("libsyn.com/","libsyn.com/preview/");

												if(file_class == 'audio' || file_class == 'video'){
													if(mime_type != 'undefined' && preview_url != 'undefined'){
														var previewHTML = '<'+file_class+' width="400" controls>';
														previewHTML += '<source src="'+preview_url+'" type="'+mime_type+'">'
														previewHTML += 'Your browser does not support HTML5 audio/video</'+file_class+'>';
														$("#libsyn-upload-media-preview").empty().html(previewHTML);
													}
												}												
										},
										 error: function (xhr, status, error) {
											if((typeof xhr.responseJSON.status !== 'undefined') && xhr.responseJSON.status == '403') {
												$('.upload-error-dialog').empty().append("Error Uploading:  " + error);
												$("#libsyn-media-progressbox-area").hide();
												$("#libsyn-upload-media-preview").empty();
											} else if((typeof xhr.responseJSON.validation_messages !== 'undefined') && xhr.responseJSON.validation_messages.upload.length >= 0) {
												var stringError = xhr.responseJSON.validation_messages.upload;
												$('.upload-error-dialog').empty().append(
													"Error Uploading:  " + xhr.responseJSON.validation_messages.upload
												);
												$('#libsyn-upload-media-error-text').html(error);
												$('#libsyn-upload-media-error').show();
												$("#libsyn-media-progressbox-area").hide();
												$("#libsyn-upload-media-preview").empty();
											} else {
												$('.upload-error-dialog').empty().append("Error Uploading:  " + error);
												$("#libsyn-media-progressbox-area").hide();
												$("#libsyn-upload-media-preview").empty();
											}
											//$('.upload-error').fadeIn('normal');
											
											$('#upload-dialog-spinner').hide();
											$('#dialog-button-upload').attr("disabled", false);
											$('.upload-error-dialog').fadeIn('normal');
										},
										xhr: function() {
											var xhr = new window.XMLHttpRequest();
											xhr.upload.addEventListener("progress", function(evt) {
												if (evt.lengthComputable) {
													var completed = evt.loaded / evt.total;
													var percentComplete = Math.floor(completed * 100);
													$("#libsyn-media-statustxt").html(percentComplete + '%');
													$("#libsyn-media-progressbar").width(percentComplete + '%');
												}
											}, false);
											return xhr;
										}
									});
								}
							}
						]
					});
					$( "#libsyn-upload-asset-dialog" ).dialog({
						autoOpen: false,
						draggable: true,
						height: 'auto',
						width: 'auto',
						modal: true,
						resizable: false,
						open: function(){
							setOverlays();
							$('.ui-widget-overlay').bind('click',function(){
								$('#libsyn-upload-asset-dialog').dialog('close');
							})
						},
						buttons: [
							{
								id: "dialog-button-cancel",
								text: "Cancel",
								click: function(){
									$('#libsyn-upload-asset-dialog').dialog('close');
								}
							}
						]
					});

					$('#libsyn-upload-media').click(function(event) {
						event.preventDefault();
						$("#libsyn-new-media-media").attr("type", "text");
						$("#libsyn-upload-media-dialog").dialog( "open" );
					});
					
					$('#libsyn-clear-media-button').click(function(event) {
						event.preventDefault();
						$("#libsyn-new-media-media").val('').attr('readonly', false);
						$("#libsyn-upload-media-preview").empty();
						$('#libsyn-upload-media-error').hide();
						$('#dialog-button-upload').attr("disabled", false);
						$('.upload-error-dialog').empty();
					});
					
					$('#libsyn-clear-image-button').click(function(event) {
						event.preventDefault();
						$("#libsyn-new-media-image").val('').attr('readonly', false);
					});
					
					if("<?php _e(esc_attr( get_post_meta( $object->ID, 'libsyn-post-episode-itunes-explicit', true ) )); ?>" != "")
						$("#libsyn-post-episode-itunes-explicit").val("<?php _e(esc_attr( get_post_meta( $object->ID, 'libsyn-post-episode-itunes-explicit', true ) )); ?>");
					if("<?php _e(esc_attr( get_post_meta( $object->ID, 'libsyn-post-episode', true ) )); ?>" == "isLibsynPost")
						$("#libsyn-post-episode").prop("checked", true);
					if("<?php _e(esc_attr( get_post_meta( $object->ID, 'libsyn-post-episode-update-id3', true ) )); ?>" == "isLibsynUpdateId3")
						$("#libsyn-post-episode-update-id3").prop("checked", true);
					if("<?php _e(esc_attr( get_post_meta( $object->ID, 'libsyn-post-update-release-date', true ) )); ?>" == "isLibsynUpdateReleaseDate")
						$("#libsyn-post-update-release-date").prop("checked", true);
					if("<?php _e(esc_attr( get_post_meta( $object->ID, 'libsyn-post-episode-simple-download', true ) )); ?>" == "release_date")
						$("#libsyn-post-episode-simple-download-release_date").prop("checked", true);
					if("<?php _e(esc_attr( get_post_meta( $object->ID, 'libsyn-post-episode-simple-download', true ) )); ?>" == "available" || "<?php _e(esc_attr( get_post_meta( $object->ID, 'libsyn-post-episode-simple-download', true ) )); ?>" == "")
						$("#libsyn-post-episode-simple-download-available").prop("checked", true);

					<?php
						//release date
						$libsyn_release_date = $sanitize->mysqlDate(get_post_meta( $object->ID, 'libsyn-release-date', true ));
						$isLibsynUpdateReleaseDateChecked = ( get_post_meta($object->ID, 'libsyn-post-update-release-date', true) )?' checked="checked"':'';
						$isLibsynUpdateId3Checked = ( get_post_meta($object->ID, 'libsyn-post-episode-update-id3', true) )?' checked="checked"':'';
						$hasLibsynReleaseDate = $sanitize->validateMysqlDate($libsyn_release_date);
						if ( !$hasLibsynReleaseDate && !empty($_GET['libsyn_edit_post_id']) ) {
							//check if the post is being edited (from posts page)
							$hasLibsynReleaseDate = true;
						}
						if($hasLibsynReleaseDate) { ?>
							$('#libsyn-post-status').fadeIn('normal');
							$('#libsyn-post-update-release-date').fadeIn('normal');
					<?php } ?>
					
					<?php if(isset($_GET['libsyn_edit_post_id']) && !empty($_GET['libsyn_edit_post_id'])) { ?>
						/* Handle Edit Item */
						<?php 
						//check post duplicate
						
						$duplicateEditPost = $plugin->checkEditPostDuplicate($sanitize->itemId($_GET['libsyn_edit_post_id']));
						if($duplicateEditPost){ ?>
						var post_redirect_url = '<?php echo $plugin->admin_url('post.php').'?post='.$duplicateEditPost->post_id.'&action=edit'; ?>';
						if (typeof window.top.location.href == 'string') window.top.location.href = post_redirect_url;
							else if(typeof document.location.href == 'string') document.location.href = post_redirect_url;
								else if(typeof window.location.href == 'string') window.location.href = post_redirect_url;
									else alert('Unknown javascript error 1025.  Please report this error to support@libsyn.com and help us improve this plugin!');
						<?php } ?>
						var libsyn_edit_post_id = parseInt(<?php echo $sanitize->itemId($_GET['libsyn_edit_post_id']); ?>);
						<?php 
							$temp_show_id = (isset($api) && $api instanceof \Libsyn\Api) ? $api->getShowId() : null;
							$item = $plugin->getEpisode(array('show_id'=>$temp_show_id,'item_id' => $sanitize->itemId($_GET['libsyn_edit_post_id'])));
							update_post_meta($object->ID, 'libsyn-edit-item-id', $sanitize->itemId($_GET['libsyn_edit_post_id']));
							if ( !empty($item->_embedded->post->release_date) ) {
								$libsyn_release_date = $sanitize->mysqlDate($item->_embedded->post->release_date);
								update_post_meta($object->ID, 'libsyn-release-date', $sanitize->mysqlDate($item->_embedded->post->release_date));
							}
						?>
						var libsyn_item = [<?php echo json_encode($item->_embedded->post);?>];
						if(!$.isEmptyObject(libsyn_item)) {
							//set vals
							$('#libsyn-post-episode').prop('checked', true);
							$('#libsyn-new-media-media').attr('readonly', true);
							$('#libsyn-new-media-media').val('http://libsyn-upload-' + libsyn_item[0].primary_content.content_id);
							$('#libsyn-post-episode-subtitle').val(libsyn_item[0].item_subtitle);
							$('#libsyn-categories .scombobox-display input').val(libsyn_item[0].category);
							$('#libsyn-new-media-image').val(libsyn_item[0].thumbnail.url);
							$('#libsyn-new-media-image').attr('readonly', true);
							$('#libsyn-post-episode-keywords').val(libsyn_item[0].extra_rss_tags);
							$('#libsyn-post-episode-itunes-explicit').val(libsyn_item[0].itunes_explicit);
							$('#libsyn-post-episode-itunes-episode-number').val(libsyn_item[0].itunes_episode_number);
							$('#libsyn-post-episode-itunes-season-number').val(libsyn_item[0].itunes_season_number);
							$('#libsyn-post-episode-itunes-episode-type').val(libsyn_item[0].itunes_episode_type);
							$('#libsyn-post-episode-itunes-episode-summary').val(libsyn_item[0].itunes_episode_summary);
							$('#libsyn-post-episode-itunes-episode-title').val(libsyn_item[0].itunes_episode_title);
							$('#libsyn-post-episode-itunes-episode-author').val(libsyn_item[0].itunes_episode_author);
							$('#libsyn-post-episode-update-id3').val(libsyn_item[0].update_id3);
							<?php if( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) { ?>
								$(window).bind("load", function() {
								   //TODO: Make this work better..  possible create libsyn store?
									wp.data.dispatch( 'core/editor' ).editPost( { title: libsyn_item[0].item_title } );
									// wp.data.dispatch( 'core/editor' ).editPost( { content: libsyn_item[0].body } );
									// var libsynEditedContent = wp.data.select('core/editor').getEditedPostContent();
									// console.log(wp.data.select('core/editor').getEditedPostContent());
									// var libsynContentBlock = wp.blocks.parse( libsynEditedContent );
									
									// wp.data.dispatch( 'core/editor' ).insertBlock( libsynContentBlock, 1 ); //put at top
									// wp.data.dispatch( 'core/editor' ).autosave();
									// wp.data.dispatch( 'core/editor' ).resetBlocks({blocks: wp.blocks.parse( libsyn_item[0].body )});
									var el = wp.element.createElement;
									var name = 'core/paragraph'
									var libsynContentBlock = wp.blocks.createBlock('core/paragraph', {
										content: libsyn_item[0].body,
									});
									wp.data.dispatch('core/editor').insertBlocks(libsynContentBlock);
									
							   });
							<?php } else { ?>
							$('#titlewrap input[id=title]').val(libsyn_item[0].item_title);
							$('#wp-content-editor-container').find('textarea').val(libsyn_item[0].body);
							<?php } ?>
						}
						
					<?php } elseif (isset($_GET['isLibsynPost']) && !empty($_GET['isLibsynPost']) && ($_GET['isLibsynPost']=="true"))  { ?>
						$('#libsyn-post-episode').prop('checked', true);
					<?php } ?>
					
					<?php //Check for published destinations
						// $published_destinations = get_post_meta($object->ID, 'libsyn-destination-releases', true);
						if(!empty($libsyn_release_date)){ ?>
							$('#libsyn-advanced-destination-form-container').find('table').css({'margin-left':'-116px', 'overflow-x': 'scroll'});
					<?php } ?>
				});
			}) (jQuery);		
		</script>
		<?php ENDIF; //ENDIF $render?>
		
		<script type="text/javascript">
			(function ($){
				$(document).ready(function() {
					//check for API errors
					<?php if($refreshTokenProblem) {?>
						$('.libsyn-post-form').hide();
						$('.loading-libsyn-form').hide();
						$('.api-problem-box').fadeIn('normal');
					<?php } elseif(!$render) { ?>
						$('.libsyn-post-form').hide();
						$('.loading-libsyn-form').hide();
						$('.configuration-problem').fadeIn('normal');
					<?php } ?>
				});
			}) (jQuery);	
		</script>
		
		<?php if($isPowerpress) { ?>
		<div class="configuration-problem-powerpress" style="border: 1px solid red;">
			<p style="color:red;font-weight:bold;padding-left:10px;">You Currently have 'Powerpress Plugin' installed.
			<br>Please visit the <a href="<?php echo $plugin->admin_url('admin.php'); ?>?page=<?php echo LIBSYN_DIR; ?>/admin/settings.php">settings</a> and make any configuration changes before posting.  (note: The Libsyn plugin will conflict with this plugin)</p>
		</div>
		<?php } ?>
		<div class="libsyn-post-form">
			<table class="form-table">
				<tr valign="top">
					<p><strong><?php echo __( 'The post title and post body above will be used for your podcast episode.', 'libsyn-nmp' ); ?></strong></p>
				</tr>
				<tr valign="top" id="libsyn-post-status" style="display:none;">
					  <th><label for="libsyn-post-episode-status"><?php _e( "Post Status", 'libsyn-post' ); ?></label></th>
					  <td>
					  	<?php //Setup Re-Release header text
							$isDraft = (get_post_meta($object->ID, 'libsyn-is_draft', true) === "true") ? true : false;
							$currentTime = (function_exists('current_time')) ? strtotime(current_time('mysql')) : time();
							if ( !empty($libsyn_release_date) && ($currentTime <= strtotime($libsyn_release_date)) ) {
								$release_text = "Scheduled to release";
							} elseif ( $isDraft ) {
								$release_text = "Draft saved";
							} elseif ( !empty($_GET['libsyn_edit_post_id']) ) {
								$release_text = "Released on Libsyn";
							} else {
								$release_text = "Released";
							}
					  	?>
						<?php IF(!empty($libsyn_release_date)): ?>
						<?php
						if(function_exists('get_date_from_gmt')) {
							?><p id="libsyn-post-episode-status"><strong><?php _e($release_text); ?> on <?php echo date("F j, Y, g:i a", strtotime(get_date_from_gmt($libsyn_release_date))); ?></strong></p><?php 
						} else {
							?><p id="libsyn-post-episode-status"><strong><?php _e($release_text); ?> on <?php echo date("F j, Y, g:i a", strtotime($libsyn_release_date))." GMT"; ?></strong></p><?php 
						} ?>
							
						<?php ELSEIF(!empty($release_text)): ?>
							<p id="libsyn-post-episode-status"><strong><?php _e($release_text); ?></strong></p>
						<?php ENDIF; ?>
					  </td>
				</tr>
				<tr valign="top" id="libsyn-post-update-release-date" style="display:none;">
					  <th><label for="libsyn-post-update-release-date"><?php _e( "Update Release Date", 'libsyn-post' ); ?></label></th>
					  <td>
						<input type="checkbox" name="libsyn-post-update-release-date" id="libsyn-post-update-release-date" value="isLibsynUpdateReleaseDate" <?php if(isset($isLibsynUpdateReleaseDateChecked)) echo $isLibsynUpdateReleaseDateChecked; ?>></input>
					  </td>
				</tr>
				<tr valign="top">
					  <?php $isLibsynPostChecked = ( get_post_meta($object->ID, '_isLibsynPost', true) )?' checked="checked"':''; ?>
					  <th><label for="libsyn-post-episode"><?php _e( "Post Libsyn Episode<span style='color:red;'>*</span>", 'libsyn-post' ); ?></label></th>
					  <td>
						<input type="checkbox" name="libsyn-post-episode" id="libsyn-post-episode" value="isLibsynPost" <?php echo $isLibsynPostChecked ?>></input>
					  </td>
				</tr>
				<tr valign="top">
					  <th><?php _e("Episode Media<span style='color:red;'>*</span>", 'libsyn-post-episode-media'); ?></th>
					  <td>
						<div id="libsyn-primary-media-settings">
							<div id="libsyn-new-media-settings">
								<div class="upload-error" style="display:none;color:red;font-weight:bold;">There was an error uploading media, please check settings and try again.</div>
								<p><?php echo __( 'Select Primary Media for Episode by clicking the button below.', 'libsyn-nmp' ); ?></p>
								<p>
									<button class="button button-primary" id="libsyn-upload-media" title="<?php echo esc_attr__( 'Click here to upload media for episode', 'libsyn-nmp' ); ?>"><?php echo __( 'Upload Media', 'libsyn-nmp' ); ?></button>
									<a href="#" class="libsyn-open-media button button-primary" title="<?php echo esc_attr__( 'Click Here to Open the Media Manager', 'libsyn-nmp' ); ?>"><?php echo __( 'Select Wordpress Media', 'libsyn-nmp' ); ?></a>
									<a href="#" class="libsyn-open-ftp_unreleased button button-primary" title="<?php echo esc_attr__( 'Click Here to Open the Media Manager', 'libsyn-nmp' ); ?>"><?php echo __( 'Select ftp/unreleased', 'libsyn-nmp' ); ?></a>
								</p>
								<p>
								<?php $libsyn_media_media = get_post_meta( $object->ID, 'libsyn-new-media-media', true ); ?>
								<label for="libsyn-new-media-media"><?php echo __( 'Media Url', 'libsyn-nmp' ); ?></label> <input type="url" id="libsyn-new-media-media" name="libsyn-new-media-media" value="<?php echo esc_attr( $libsyn_media_media ); ?>" pattern="https?://.+" <?php if(isset($libsyn_media_media)&&!empty($libsyn_media_media)) echo 'readonly'; ?>></input>
								<button class="button" id="libsyn-clear-media-button" title="<?php echo esc_attr__( 'Clear primary media', 'libsyn-nmp' ); ?>"><?php echo __( 'Clear', 'libsyn-nmp' ); ?></button>
								</p>
							</div>
							<div id="libsyn-upload-media-dialog" class="hidden" title="Upload Media">
								<h3>Select Media to upload:</h3>
								<input id="libsyn-media-file-upload" type="file" name="upload" class="jfilestyle" data-buttonText="Choose Media" data-size="300px"></input>
								<div id="libsyn-media-progressbox-area" style="display:none;">
									<img src="<?php echo plugins_url(LIBSYN_DIR.'/lib/images/3-dots.gif'); ?>">
									<div id="libsyn-media-progressbox" style="width:80;">
										<div id="libsyn-media-progressbar"></div>
										<div id="libsyn-media-statustxt">0%</div>
									</div>
								</div>
								<div class="upload-error-dialog" style="display:none;color:red;font-weight:bold;"></div>
							</div>
						</div>
					  </td>
				</tr>
				<tr id="libsyn-upload-media-preview-area">
					<th scope="row"></th>
					<td id="libsyn-upload-media-preview">	
					</td>
				</tr>
				<tr id="libsyn-upload-media-error" style="display:none;">
					<th scope="row"></th>
					<td>
						<div class="libsyn-media-error">
							 <p id="libsyn-upload-media-error-text"></p>
						</div>
					</td>
				</tr>
				<tr valign="top">
					  <th><?php _e("Episode Subtitle", 'libsyn-post-episode-subtitle'); ?></th>
					  <td>
						<div id="titlediv">
							<div id="titlewrap">
								<input id="libsyn-post-episode-subtitle" type="text" autocomplete="off" value="<?php echo get_post_meta( $object->ID, 'libsyn-post-episode-subtitle', true ); ?>" name="libsyn-post-episode-subtitle" style="width:100%;" maxlength="255"></input>
							</div>
						</div>
					  </td>
				</tr>
				<tr valign="top">
					  <th><?php _e("Episode Category<span style='color:red;'>*</span>", 'libsyn-post-episode-category-selection'); ?></th>
					  <td>
						<div id="titlediv">
							<div id="titlewrap">
								<div class="options-error" style="display:none;color:red;font-weight:bold;">Could not populate categories, manually enter category.</div>
								<select id="libsyn-categories" name="libsyn-post-episode-category">
									<option value="general">general</option>
								</select>
								<input type="hidden" value="<?php echo get_post_meta( $object->ID, 'libsyn-post-episode-category-selection', true ); ?>" name="libsyn-post-episode-category-selection" id="libsyn-post-episode-category-selection"></input>
							</div>
						</div>
					  </td>
				</tr>		
				<tr valign="top">
					  <th><?php _e("Episode Thumbnail", 'libsyn-post-episode-media'); ?></th>
					  <td>
						<div id="libsyn-primary-media-settings">
							<div id="libsyn-new-media-settings">
								<p><?php echo __( 'Select image for episode thumbnail by clicking the button below.', 'libsyn-nmp' ); ?></p>
								<p>
								<?php $libsyn_episode_thumbnail = esc_attr( get_post_meta( $object->ID, 'libsyn-new-media-image', true ) ); ?>
								<a href="#" class="libsyn-open-image button button-primary" title="<?php echo esc_attr__( 'Click Here to Open the Image Manager', 'libsyn-nmp' ); ?>"><?php echo __( 'Select Episode Thumbnail', 'libsyn-nmp' ); ?></a></p>
								<p><label for="libsyn-new-media-image"><?php echo __( 'Media Url', 'libsyn-nmp' ); ?></label><input type="url" id="libsyn-new-media-image" name="libsyn-new-media-image" value="<?php echo (!empty($libsyn_episode_thumbnail))?$libsyn_episode_thumbnail:''; ?>" pattern="https?://.+" <?php if(isset($libsyn_episode_thumbnail)&&!empty($libsyn_episode_thumbnail)) echo 'readonly';?>></input>
								<button class="button" id="libsyn-clear-image-button" title="<?php echo esc_attr__( 'Clear image url', 'libsyn-nmp' ); ?>"><?php echo __( 'Clear', 'libsyn-nmp' ); ?></button>
								</p>
							</div>
							<div id="libsyn-upload-asset-dialog" class="hidden" title="Upload Image">
								<p>Select Image to upload:</p>
								<br>
							</div>
						</div>
					  </td>	
				</tr>
				<tr valign="top">
					  <th><?php _e("Tags/Keywords", 'libsyn-post-episode-keywords'); ?></th>
					  <td>
						<div id="titlediv">
							<div id="titlewrap">
								<input id="libsyn-post-episode-keywords" type="text" autocomplete="off" value="<?php echo get_post_meta( $object->ID, 'libsyn-post-episode-keywords', true ); ?>" name="libsyn-post-episode-keywords" style="width:100%;" maxlength="255" placeholder="keyword1, keyword2, keyword3"></input>
							</div>
						</div>
					  </td>
				</tr>
				<tr valign="top">
					  <th><label for="libsyn-post-update-id3"><?php _e( "Update Id3 Tags", 'libsyn-post' ); ?></label></th>
					  <td>
						<input type="checkbox" name="libsyn-post-episode-update-id3" id="libsyn-post-episode-update-id3" value="isLibsynUpdateId3" <?php if(isset($isLibsynUpdateId3Checked)) echo $isLibsynUpdateId3Checked; ?>></input>&nbsp;&nbsp;Allow Libsyn to update id3 tags with post data. <em>(mp3 files only)</em>
					  </td>
				</tr>
				<tr valign="top">
					<?php /* iTunes Settings */
						$libsyn_post_itunes = ( get_post_meta($object->ID, 'libsyn-post-itunes', true) ) ? ' checked="checked"' : '';
						$libsyn_itunes_episode_number = get_post_meta($object->ID, 'libsyn-post-episode-itunes-episode-number', true);
						$libsyn_itunes_season_number = get_post_meta($object->ID, 'libsyn-post-episode-itunes-season-number', true);
						$libsyn_itunes_episode_type = get_post_meta($object->ID, 'libsyn-post-episode-itunes-episode-type', true);
						$libsyn_itunes_episode_summary = get_post_meta($object->ID, 'libsyn-post-episode-itunes-episode-summary', true);
						$libsyn_itunes_episode_title = get_post_meta($object->ID, 'libsyn-post-episode-itunes-episode-title', true);
						$libsyn_itunes_episode_author = get_post_meta($object->ID, 'libsyn-post-episode-itunes-episode-author', true);
					?>
					  <th><label for="libsyn-post-itunes"><?php _e( "iTunes Optimization", 'libsyn-post' ); ?></label></th>
					  <td>
						<div id="titlediv">
							<button class="button" id="libsyn-itunes-optimization-form-button" title="<?php echo esc_attr__( 'iTunes Optimization', 'libsyn-nmp' ); ?>" data-libsyn-wp-post-id="<?php echo $object->ID; ?>" value="false"><?php echo __( 'iTunes Optimization Tags (Optional)', 'libsyn-nmp' ); ?></button>
							<div id="titlewrap">
								<br />
								<div id="libsyn-itunes-optimization-container" style="display:none;">
									<fieldset>
										<legend class="screen-reader-text"><?php _e( "iTunes Optimization", 'libsyn-post' ); ?></legend>
										<p style="padding-bottom:4px;">
											<label for="libsyn-post-episode-itunes-explicit" style="width:20%;max-width:120px;font-weight:600;">
												<?php _e("iTunes Explicit", 'libsyn-post-episode-rating'); ?>
											</label>
											<select id="libsyn-post-episode-itunes-explicit" name="libsyn-post-episode-itunes-explicit">
												<option value="no">Not Set</option>
												<option value="clean">Clean</option>
												<option value="yes">Explicit</option>
											</select>
										</p>
										<p style="padding-bottom:4px;">
											<label for="libsyn-post-episode-itunes-episode-number" style="width:20%;max-width:120px;font-weight:600;"><?php echo __( 'Episode Number', 'libsyn-nmp' ); ?></label>
											<input type="number" id="libsyn-post-episode-itunes-episode-number" name="libsyn-post-episode-itunes-episode-number" value="<?php echo (!empty($libsyn_itunes_episode_number))?$libsyn_itunes_episode_number:''; ?>" min="1" max="99999"></input>
										</p>
										<p style="padding-bottom:4px;">
											<label for="libsyn-post-episode-itunes-season-number" style="width:20%;max-width:120px;font-weight:600;"><?php echo __( 'Season Number', 'libsyn-nmp' ); ?></label>
											<input type="number" id="libsyn-post-episode-itunes-season-number" name="libsyn-post-episode-itunes-season-number" value="<?php echo (!empty($libsyn_itunes_season_number))?$libsyn_itunes_season_number:''; ?>" min="1" max="99999"></input>
										</p>
										<p style="padding-bottom:4px;">
											<label for="libsyn-post-episode-itunes-episode-type" style="width:20%;max-width:120px;font-weight:600;"><?php echo __( 'Episode Type', 'libsyn-nmp' ); ?></label>
											<select id="libsyn-post-episode-itunes-episode-type" style="max-width:330px;" name="libsyn-post-episode-itunes-episode-type" value="<?php echo (!empty($libsyn_itunes_episode_type))?$libsyn_itunes_episode_type:''; ?>">
												<option name="none" value="" <?php echo (empty($libsyn_itunes_episode_type) || $libsyn_itunes_episode_type=="null") ? 'selected ' : ''; ?>>--Select an option--></option>
												<option name="none" value="full" <?php echo (!empty($libsyn_itunes_episode_type) && $libsyn_itunes_episode_type=="full") ? 'selected ' : ''; ?>>Full</option>
												<option name="none" value="trailer" <?php echo (!empty($libsyn_itunes_episode_type) && $libsyn_itunes_episode_type=="trailer") ? 'selected ' : ''; ?>>Trailer</option>
												<option name="none" value="bonus" <?php echo (!empty($libsyn_itunes_episode_type) && $libsyn_itunes_episode_type=="bonus") ? 'selected ' : ''; ?>>Bonus</option>
											</select>
										</p>
										<p style="padding-bottom:4px;">
											<label for="libsyn-post-episode-itunes-episode-summary" style="width:20%;max-width:120px;font-weight:600;"><?php echo __( 'Episode Summary', 'libsyn-nmp' ); ?></label>
											<textarea wrap="hard" maxlength="4000" rows="8" cols="50" id="libsyn-post-episode-itunes-episode-summary" name="libsyn-post-episode-itunes-episode-summary"><?php echo (!empty($libsyn_itunes_episode_summary))?$libsyn_itunes_episode_summary:''; ?></textarea>
										</p>
										<p style="padding-bottom:4px;">
											<label for="libsyn-post-episode-itunes-episode-title" style="width:20%;max-width:120px;font-weight:600;"><?php echo __( 'Episode Title', 'libsyn-nmp' ); ?></label>
											<input type="text" id="libsyn-post-episode-itunes-episode-title" style="max-width:330px;" name="libsyn-post-episode-itunes-episode-title" value="<?php echo (!empty($libsyn_itunes_episode_title))?$libsyn_itunes_episode_title:''; ?>"></input>
										</p>
										<p style="padding-bottom:4px;">
											<label for="libsyn-post-episode-itunes-episode-author" style="width:20%;max-width:120px;font-weight:600;"><?php echo __( 'Episode Author', 'libsyn-nmp' ); ?></label>
											<input type="text" id="libsyn-post-episode-itunes-episode-author" style="max-width:330px;" name="libsyn-post-episode-itunes-episode-author" value="<?php echo (!empty($libsyn_itunes_episode_author))?$libsyn_itunes_episode_author:''; ?>"></input>
										</p>
									</fieldset>
								</div>
							</div>
						</div>
					  </td>
				</tr>
				<tr valign="top">
					  <th><?php _e("Destinations", 'libsyn-post-episode-advanced-publishing'); ?></th>
					  <td>
						<div id="titlediv">
							<button class="button" id="libsyn-advanced-destination-form-button" title="<?php echo esc_attr__( 'Advanced Destination Publishing', 'libsyn-nmp' ); ?>" data-libsyn-wp-post-id="<?php echo $object->ID; ?>" value="false"><?php echo __( 'Advanced Destination Publishing (Optional)', 'libsyn-nmp' ); ?></button>
							<div id="titlewrap">
								<br />
								<div id="libsyn-advanced-destination-form-container" style="display:none;">
								<!--<div id="libsyn-advanced-destination-form-container" >-->
								<?php 
									$destination = new Service\Destination();
									if(!$api) {
										$libsyn_error = true;
										$destinations = false;
										update_post_meta($object->ID, 'libsyn-post-error_api', 'true');
										if($plugin->hasLogger) $plugin->logger->error( "Post:\tApi is false (likely refresh token has expired)" );
									} else {
										$destinations = $plugin->getDestinations($api);
										if($destinations) {
											$destination_args = array(
												'singular'=> 'libsyn_destination' //Singular label
												,'plural' => 'libsyn_destinations' //plural label, also this well be one of the table css class
												,'ajax'   => true //We won't support Ajax for this table
												,'screen' => get_current_screen()
											);
											//remove Wordpress Destination
											foreach($destinations->destinations as $key => $working_destination)
												if($working_destination->destination_type==='WordPress') unset($destinations->destinations->{$key});
												
											$published_destinations = get_post_meta($object->ID, 'libsyn-destination-releases', true);
											//Prepare Table of elements
											$libsyn_destination_wp_list_table = new \Libsyn\Service\Table($destination_args, $destinations->destinations);
											if(!empty($published_destinations)) {
												$libsyn_destination_wp_list_table->item_headers = array(
													'cb' => '<input type=\"checkbox\"></input>'
													,'id' => 'destination_id'
													,'destination_name' => 'Destination Name'
													,'published_status' => 'Published Status'
													// ,'destination_type' => 'Destination Type'
													,'release_date' => 'Release Date'
													,'expiration_date' => 'Expiration Date'
													// ,'creation_date' => 'Creation Date'
												);											
											} else {
												$libsyn_destination_wp_list_table->item_headers = array(
													'cb' => '<input type=\"checkbox\"></input>'
													,'id' => 'destination_id'
													,'destination_name' => 'Destination Name'
													// ,'destination_type' => 'Destination Type'
													,'release_date' => 'Release Date'
													,'expiration_date' => 'Expiration Date'
													// ,'creation_date' => 'Creation Date'
												);
											}
											$libsyn_destination_wp_list_table->prepare_items();
											$destination->formatDestinationsTableData($destinations, $object->ID);
										}
									}
									?>
									<br />
									<div id="libsyn-post-episode-simple-download-div">
										<strong>Download Availability:</strong><br />
										<input type="radio" name="libsyn-post-episode-simple-download" id="libsyn-post-episode-simple-download-available" value="available"></input>&nbsp;Media Files are always available<br />
										<input type="radio" name="libsyn-post-episode-simple-download" id="libsyn-post-episode-simple-download-release_date"  value="release_date"></input>&nbsp;Media Files are available based on release schedule<br />
									</div>
									<?php 
										echo "<pre>";
										$libsyn_advanced_destination_form_data = $destination->formatDestinationFormData($destinations, $object->ID);
										echo "</pre>";
									?>
									
									<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
									<form id="destinations-table" method="get">			
										<!-- Now we can render the completed list table -->
										<?php if(!empty($libsyn_destination_wp_list_table)) $libsyn_destination_wp_list_table->display(); ?>
										<!-- Destination page-specific Form Data -->
										<?php $libsyn_advanced_destination_form_data = get_post_meta( $object->ID, 'libsyn-post-episode-advanced-destination-form-data', true ); ?>
										<?php if(empty($libsyn_advanced_destination_form_data)) $libsyn_advanced_destination_form_data = $destination->formatDestinationFormData($destinations, $object->ID); ?>
										<?php $libsyn_advanced_destination_form_data = get_post_meta( $object->ID, 'libsyn-post-episode-advanced-destination-form-data', true ); ?>
										<?php $libsyn_advanced_destination_form_data_enabled = get_post_meta( $object->ID, 'libsyn-post-episode-advanced-destination-form-data-enabled', true ); ?>
										<input id="libsyn-post-episode-advanced-destination-form-data-input" name="libsyn-post-episode-advanced-destination-form-data-input" type="hidden"></input>
										<input id="libsyn-post-episode-advanced-destination-form-data-input-enabled" name="libsyn-post-episode-advanced-destination-form-data-input-enabled" type="hidden" value="<?php if(isset($libsyn_advanced_destination_form_data_enabled) && !empty($libsyn_advanced_destination_form_data_enabled) && ($libsyn_advanced_destination_form_data_enabled==='true')) echo $libsyn_advanced_destination_form_data_enabled; ?>"></input>
										<script id="libsyn-post-episode-advanced-destination-form-data" type="application/json"><?php if(!empty($libsyn_advanced_destination_form_data)) echo $libsyn_advanced_destination_form_data; ?></script>
									</form>
								</div>
							</div>
						</div>
					  </td>
				</tr>
				<tr valign="top">
					  <th><?php _e("", 'libsyn-post-episode-footer'); ?></th>
					  <td>
						<div id="titlediv">
							<div id="titlewrap">
								<br />
								<p class="smalltext" style="font-style:italic;"><span style='color:red;'>*</span>&nbsp;Indicates required fields.</p>	
							</div>
						</div>
					  </td>
				</tr>			
			</table>
		</div>
		<?php 
		
	}	

	/**
	 * Callback function for 'wp_ajax_fetch_custom_list' action hook. 
	 * 
	 * Loads the Custom List Table Class and calls ajax_response method
	 */
	function _libsyn_ajax_fetch_custom_list_callback() {
		$destination = new Service\Destination();
		// $wp_list_table = new TT_Example_List_Table();
		// $wp_list_table->ajax_response();
		
		//Have to get the post id from url query
		$url = wp_get_referer();
		$ajax_post_page_query = parse_url( $url, PHP_URL_QUERY );
		// parse the query args
		$post_page_args  = array();
		parse_str( $ajax_post_page_query, $post_page_args );
		// make sure we are editing a post and that post ID is an INT
		if ( isset( $post_page_args[ 'post' ] ) && is_numeric( $post_page_args[ 'post' ] ) && isset( $post_page_args[ 'action' ] ) && $post_page_args[ 'action' ] === 'edit' )
			if ( $id = intval( $post_page_args[ 'post' ] ) ) $post_id = $id;
		if(isset($post_id) && is_int($post_id)) $published_destinations = get_post_meta($post_id, 'libsyn-destination-releases', true);
			else $published_destinations = '';
		
		$plugin = new Service();
		$current_user_id = $plugin->getCurrentUserId();
		$api = $plugin->retrieveApiById($current_user_id);
		if($api instanceof \Libsyn\Api) {
			$destinations = $plugin->getDestinations($api);
		} else {
			$destinations = false;
		}
		
		if($destinations) {
			$destinations = $destination->formatDestinationsTableData($destinations, $post_id);
			$destination_args = array(
				'singular'=> 'libsyn_destination' //Singular label
				,'plural' => 'libsyn_destinations' //plural label, also this well be one of the table css class
				,'ajax'   => true //We won't support Ajax for this table

			);
			//Prepare Table of elements
			$wp_list_table = new \Libsyn\Service\Table($destination_args, $destinations->destinations);
			if(!empty($published_destinations)) {
				$wp_list_table->item_headers = array(
					'cb' => '<input type=\"checkbox\"></input>'
					,'id' => 'destination_id'
					,'published_status' => 'Published Status'
					,'destination_name' => 'Destination Name'
					// ,'destination_type' => 'Destination Type'
					,'release_date' => 'Release Date'
					,'expiration_date' => 'Expiration Date'
					// ,'creation_date' => 'Creation Date'
				);											
			} else {
				$wp_list_table->item_headers = array(
					'cb' => '<input type=\"checkbox\"></input>'
					,'id' => 'destination_id'
					,'destination_name' => 'Destination Name'
					// ,'destination_type' => 'Destination Type'
					,'release_date' => 'Release Date'
					,'expiration_date' => 'Expiration Date'
					// ,'creation_date' => 'Creation Date'
				);
			}
			// $wp_list_table->prepare_items();
			$wp_list_table->ajax_response();
			// $wp_list_table->items = $plugin->getDestinations($api);					
		}
	}
	
	/**
	 * Callback function for 'wp_ajax__ajax_fetch_player_settings' action hook.
	 * 
	 * Loads the Custom List Table Class and calls ajax_response method
	 */
	public static function loadPlayerSettings() {
		$libsyn_error = true;
		$checkUrl  = self::getCurrentPageUrl();
		parse_str($checkUrl, $urlParams);
		if(intval($urlParams['load_player_settings']) === 1) {
			echo '
				<h3 id="player_settings_title">
					<label>Player Settings</label>
				</h3>
				<div class="inside">
					<p id="player-description-text"><em>Below are the default player settings.  You may also modify the size on each individual post on the post page.</em></p>
					<div class="box_clear"></div>
					<table class="form-table">
						<tr valign="top">
							<th>Player Theme</th>
							<td>
								<div>
									<div>
										<input id="player_use_theme_standard" type="radio" value="standard" name="player_use_theme"></input><span style="margin-left:16px;"><strong>Standard</strong>&nbsp;&nbsp;<em style="font-weight:300;">(minimum height 45px)</em></span>
									</div>
									<div style="margin-left:36px;" id="player_use_theme_standard_image">
									</div>
									<br />
									<div>
										<input id="player_use_theme_mini" type="radio" value="mini" name="player_use_theme"></input><span style="margin-left:16px;"><strong>Mini</strong>&nbsp;&nbsp;<em style="font-weight:300;">(minimum height 26px)</em></span>
									</div>
									<div style="margin-left:36px;" id="player_use_theme_mini_image">
									</div>
									<br />
									<div>
										<input id="player_use_theme_custom" type="radio" value="custom" name="player_use_theme"></input><span style="margin-left:16px;"><strong>Custom</strong>&nbsp;&nbsp;<em style="font-weight:300;">(minimum height 90px or 300px(video), width 450px)</em></span>
									</div>
									<div style="margin-left:36px;" id="player_use_theme_custom_image">
									</div>
								</div>
							</td>
						</tr>
						<tr id="player_custom_color_picker" style="display:none;">
							<th>Custom Color</th>
							<td>
								<div>
									<div style="margin-left:36px;">
										<input id="player_custom_color" class="color-picker" name="player_custom_color" value=""></input><button type="button" class="button" data-editor="content" font="400 18px/1 dashicons" id="player_custom_color_picker_button"><span class="dashicons dashicons-art" style="padding-top: 4px;"></span> Pick Color</button>
									</div>				
								</div>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr valign="top">
							<th colspan="2"><input style="margin-left: 2px;" id="player_use_thumbnail" type="checkbox" value="use_thumbnail" name="player_use_thumbnail"></input>&nbsp;Display episode/show artwork on the player?&nbsp;&nbsp;<em style="font-weight:300;">(minimum height 200px)</em></th>
							<td>
							</td>
						</tr>
						<tr id="player_width_tr" valign="top">
							<th>Player Width:</th>
							<td>
								<input id="player_width" type="number" value="" name="player_width" maxlength="4" autocomplete="on" min="200" step="1"></input>
							</td>
						</tr>
						<tr valign="top">
							<th>Player Height:</th>
							<td>
								<input id="player_height" type="number" value="" name="player_height" autocomplete="on" min="45" step="1"></input>
							</td>
						</tr>
						<tr valign="top">
							<th>Player Placement</th>
							<td>
								<div>
									<div>
										<input id="player_placement_top" type="radio" value="top" name="player_placement"></input><span style="margin-left:16px;"><strong>Top</strong>&nbsp;&nbsp;<em style="font-weight:300;">(Before Post)</em></span>
									</div>
									<div style="margin-left:36px;" class="post-position-image-box">
										<div class="post-position-shape-top"></div>
									</div>
									<br />
									<div>
										<input id="player_placement_bottom" type="radio" value="bottom" name="player_placement"></input><span style="margin-left:16px;"><strong>Bottom</strong>&nbsp;&nbsp;<em style="font-weight:300;">(After Post)</em></span>
									</div>
									<div style="margin-left:36px;" class="post-position-image-box">
										<div class="post-position-shape-bottom"></div>
									</div>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<th colspan="2"><input style="margin-left: 2px;" id="player_use_download_link" type="checkbox" value="use_download_link" name="player_use_download_link"></input>&nbsp;Display download link below the player?</th>
							<td>
							</td>
						</tr>
						<tr valign="top" style="display:none;" id="player_use_download_link_text_div">
							<th></th>
							<td>
								Download Link Text:&nbsp;&nbsp;<input id="player_use_download_link_text" type="text" value="" name="player_use_download_link_text" maxlength="256" min="200"></input>
							</td>
						</tr>
						<tr valign="bottom">
							<th></th>
							<td>
								<br />
									<input type="submit" value="Save Player Settings" class="button button-primary" id="player-settings-submit" name="submit"></input>
							</td>
						</tr>
						<tr valign="bottom">
							<th style="font-size:.8em;font-weight:200;">**<em>height and width in Pixels (px)</em></th>
							<td></td>
						</tr>
					</table>
					<br />
				</div>';
		} else {
			echo "Could not load player settings.";
		}
		exit;
	}

	
	/**
	 * simple function checks the camel case of a form name prefix "libsyn-post-episode"
	 * 
	 * @pram  <int> $id  WP post id ($object->ID)
	 * @param <string> $prefix 
	 * @param <string> $camelCaseName 
	 * 
	 * @return <mixed>
	 */
	public static function checkFormItem( $id, $prefix, $camelCaseName ) {
		$cc_text = preg_replace(array('/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'), ' $0', $camelCaseName);
		$cc_text = ucwords($cc_text);
		$check = esc_attr( get_post_meta( $id, 'libsyn-post-episode-'.$prefix.'-'.$camelCaseName, true ) );
		if(!empty($check)&&$check==$cc_text) return true; else return false;
	}
	
	/**
	 * Handles the post data fields from addLibsynPostMeta
	 * 
	 * @param <int> $post_id 
	 * @param <WP_Post> $post 
	 * 
	 * @return <bool>
	 */
	public static function handlePost( $post_id, $post ) {
		if (isset($post->post_status)&&'auto-draft'==$post->post_status) return;
		
		/* Verify the nonce before proceeding. */
		if (!isset($_POST['libsyn_post_episode_nonce'])||!wp_verify_nonce($_POST['libsyn_post_episode_nonce'], basename( __FILE__ ))) return $post_id;
		
		/* Check if the current post type is 'post' or 'revision' (currently do not support custom post types) */
		if($post->post_type !== 'post' && $post->post_type!=='revision') {
			update_post_meta($post->ID, 'libsyn-post-error_post-type', 'true');
			//return $post_id;
			//error_log('Post-type:  '.$post->post_type);
		}
		
		/* Get the post type object. */
		$post_type = get_post_type_object($post->post_type);
		
		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can($post_type->cap->edit_post, $post_id)) {
			update_post_meta($post->ID, 'libsyn-post-error_post-permissions', 'true');
			return $post_id;
		}

		/* Get the posted data and sanitize it for use as an HTML class. */		
		$new_meta_values = array();
		$new_meta_values['libsyn-post-episode'] = (isset($_POST['libsyn-post-episode'])) ? $_POST['libsyn-post-episode'] : '';
		$new_meta_values['libsyn-post-update-release-date'] = isset($_POST['libsyn-post-update-release-date']) ? $_POST['libsyn-post-update-release-date'] : '';
		$new_meta_values['libsyn-new-media-media'] = (isset($_POST['libsyn-new-media-media'])) ? $_POST['libsyn-new-media-media'] : '';
		$new_meta_values['libsyn-post-episode-subtitle'] = (isset($_POST['libsyn-post-episode-subtitle'])) ? $_POST['libsyn-post-episode-subtitle'] : '';
		$new_meta_values['libsyn-post-episode-category-selection'] = (isset($_POST['libsyn-post-episode-category-selection'])) ? $_POST['libsyn-post-episode-category-selection'] : '';
		$new_meta_values['libsyn-new-media-image'] = (isset($_POST['libsyn-new-media-image'])) ? $_POST['libsyn-new-media-image'] : '';
		$new_meta_values['libsyn-post-episode-keywords'] = (isset($_POST['libsyn-post-episode-keywords'])) ? $_POST['libsyn-post-episode-keywords'] : '';
		$new_meta_values['libsyn-post-episode-itunes-explicit'] = (isset($_POST['libsyn-post-episode-itunes-explicit'])) ? $_POST['libsyn-post-episode-itunes-explicit'] : '';
		$new_meta_values['libsyn-post-episode-itunes-episode-number'] = (isset($_POST['libsyn-post-episode-itunes-episode-number'])) ? intval($_POST['libsyn-post-episode-itunes-episode-number']) : '';
		$new_meta_values['libsyn-post-episode-itunes-season-number'] = (isset($_POST['libsyn-post-episode-itunes-season-number'])) ? intval($_POST['libsyn-post-episode-itunes-season-number']) : '';
		$new_meta_values['libsyn-post-episode-itunes-episode-type'] = (isset($_POST['libsyn-post-episode-itunes-episode-type'])) ? $_POST['libsyn-post-episode-itunes-episode-type'] : '';
		$new_meta_values['libsyn-post-episode-itunes-episode-summary'] = (isset($_POST['libsyn-post-episode-itunes-episode-summary'])) ? $_POST['libsyn-post-episode-itunes-episode-summary'] : '';
		$new_meta_values['libsyn-post-episode-itunes-episode-title'] = (isset($_POST['libsyn-post-episode-itunes-episode-title'])) ? $_POST['libsyn-post-episode-itunes-episode-title'] : '';
		$new_meta_values['libsyn-post-episode-itunes-episode-author'] = (isset($_POST['libsyn-post-episode-itunes-episode-author'])) ? $_POST['libsyn-post-episode-itunes-episode-author'] : '';
		$new_meta_values['libsyn-post-episode-update-id3'] = (isset($_POST['libsyn-post-episode-update-id3'])) ? $_POST['libsyn-post-episode-update-id3'] : '';
		
		//player settings
		if (isset($_POST['player_use_thumbnail'])) {
			if(!empty($_POST['player_use_thumbnail'])&&$_POST['player_use_thumbnail']==='use_thumbnail') {
				$new_meta_values['libsyn-post-episode-player_use_thumbnail'] = $_POST['player_use_thumbnail'];
			} elseif(empty($_POST['player_use_thumbnail'])) {
				$new_meta_values['libsyn-post-episode-player_use_thumbnail'] = 'none';
			}
		} else {
			$new_meta_values['libsyn-post-episode-player_use_thumbnail'] = get_user_option('libsyn-podcasting-player_use_thumbnail');
			if(empty($new_meta_values['libsyn-post-episode-player_use_thumbnail'])) $new_meta_values['libsyn-post-episode-player_use_thumbnail'] = 'none';
		}
		$new_meta_values['libsyn-post-episode-player_use_theme'] = (isset($_POST['player_use_theme'])) ? $_POST['player_use_theme'] : get_user_option('libsyn-podcasting-player_use_theme');
		$new_meta_values['libsyn-post-episode-player_width'] = (isset($_POST['player_width'])) ? $_POST['player_width'] : get_user_option('libsyn-podcasting-player_width');
		$new_meta_values['libsyn-post-episode-player_height'] = (isset($_POST['player_height'])) ? $_POST['player_height'] : get_user_option('libsyn-podcasting-player_height');
		$new_meta_values['libsyn-post-episode-player_placement'] = (isset($_POST['player_placement'])) ? $_POST['player_placement'] : get_user_option('libsyn-podcasting-player_placement');
		$new_meta_values['libsyn-post-episode-player_use_download_link'] = (isset($_POST['player_use_download_link'])) ? $_POST['player_use_download_link'] : get_user_option('libsyn-podcasting-player_use_download_link');
		$new_meta_values['libsyn-post-episode-player_use_download_link_text'] = (isset($_POST['player_use_download_link_text'])) ? $_POST['player_use_download_link_text'] : get_user_option('libsyn-podcasting-player_use_download_link_text');
		$new_meta_values['libsyn-post-episode-player_custom_color'] = (isset($_POST['player_custom_color'])) ? $_POST['player_custom_color'] : get_user_option('libsyn-podcasting-player_custom_color');
		$new_meta_values['libsyn-post-episode-advanced-destination-form-data'] = (isset($_POST['libsyn-post-episode-advanced-destination-form-data-input'])) ? $_POST['libsyn-post-episode-advanced-destination-form-data-input'] : get_user_option('libsyn-post-episode-advanced-destination-form-data');
		$new_meta_values['libsyn-post-episode-advanced-destination-form-data-input-enabled'] = (isset($_POST['libsyn-post-episode-advanced-destination-form-data-input-enabled'])) ? $_POST['libsyn-post-episode-advanced-destination-form-data-input-enabled'] : get_user_option('libsyn-post-episode-advanced-destination-form-data-input-enabled');
		$new_meta_values['libsyn-post-episode-simple-download'] = isset($_POST['libsyn-post-episode-simple-download']) ? $_POST['libsyn-post-episode-simple-download'] : '';
		
		//Handle new Meta Values
		self::handleMetaValueArray( $post_id, $new_meta_values );
		
		/* Call Post to Libsyn based on post_status */
		try{
			switch($post->post_status) {
				case __('future', 'libsyn-nmp'):
					self::postEpisode($post, true);
					break;
					
				case __('draft', 'libsyn-nmp'):
					self::postEpisode($post, false, true);
					break;
					
				case __('pending', 'libsyn-nmp'):
					//echo("Pending, not sure where to do here");exit;
					break;
					
				case __('private', 'libsyn-nmp'):
					//echo("We do not handle private");exit;
					break;
					
				case __('publish', 'libsyn-nmp'):
					self::postEpisode($post);
					break;
					
				default:
					return;
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	
	/**
	 * Handle meta values based on the way they are setup in array.
	 * see (array) $new_meta_values
	 * 
	 * @param <array> $new_meta_values
	 * 
	 * @return <mixed>
	 */
	public static function handleMetaValueArray( $post_id, $new_meta_values ) {
		/* If a new meta value was added and there was no previous value, add it. */
		foreach ($new_meta_values as $key => $val) {
			$meta_value = get_post_meta($post_id, $key, true);
			$sanitize = new \Libsyn\Service\Sanitize();
			if(!isset($url)) $url = '';
			
			//sanitize value
			if($key==='libsyn-new-media-image') {
				$clean_val = $sanitize->url_raw($val);
			} elseif($key==='libsyn-new-media-media'&&(strpos($val, 'libsyn-ftp-')===false||strpos($url, 'libsyn-upload-')===false)) {
				$clean_val = $sanitize->url_raw($val);
			} elseif($key==='libsyn-post-episode-advanced-destination-form-data') {
				$clean_val = $sanitize->json($val);
			} elseif($key==='libsyn-post-episode-itunes-episode-number' || $key==='libsyn-post-episode-itunes-season-number') {
				$clean_val = $sanitize->numeric($val);
			} else {
				$clean_val = $sanitize->text($val);
			}
			
			//setup post meta
			if (!empty($clean_val)&&empty($meta_value)) // no meta_value so create
				add_post_meta($post_id, $key, $clean_val, true);
			elseif (!empty($clean_val)&&$clean_val!==$meta_value) //doesn't match old value so update
				update_post_meta($post_id, $key, $clean_val);
			elseif (empty($clean_val)&&!empty($meta_value)) //old value doesn't exist, delete it
				delete_post_meta($post_id, $key, $meta_value);
		}
	}
	
	
	
	/**
	 * Attaches form field values
	 * 
	 * @param <array> $form_fields 
	 * @param <WP_Post> $post 
	 * 
	 * @return <mixed>
	 */
	public static function attachFieldsToEdit( $form_fields, $post ) {
		$field_value = get_post_meta($post->ID, 'location', true);
		$form_fields['location'] = array(
			'value' => $field_value ? $field_value : '',
			'label' => __( 'Location' , 'libsyn-nmp'),
			'helps' => __( 'Set a location for this attachment', 'libsyn-nmp' ),
		);
		return $form_fields;
	}

	/**
	 * Handles the Meta post box classes
	 * 
	 * @param <mixed> $classes 
	 * 
	 * @return <mixed>
	 */
	public static function metaPostClasses( $classes ) {
		/* Get the current post ID. */
		$post_id = get_the_ID();

		/* If we have a post ID, proceed. */
		if ( !empty( $post_id ) ) {
			$post_class = get_post_meta( $post_id, 'libsyn_post_episode', true );
			if ( !empty( $post_class ) ) $classes[] = sanitize_html_class( $post_class );
		}
		return $classes;		
	
	}
	
	/**
	 * Main Post script which handles Libsyn API posting. Used for post scheduled/immediate post.
	 * 
	 * @param <WP_Post> $post 
	 * @param <int> $post_id 
	 * @param <bool> $schedule 
	 * @param <bool> $draft 
	 * 
	 * @return <Libsyn_Item|mixed>
	 */
	public static function postEpisode( $post, $isSchedule=false, $isDraft=false ) {
		$sanitize = new \Libsyn\Service\Sanitize();
		
		/* Back out quickly if the post to libsyn is not checked */
		if(get_post_meta($post->ID, 'libsyn-post-episode', true)!=='isLibsynPost') return;
		$plugin = new Service();
		$current_user_id = $plugin->getCurrentUserId();
		$api = $plugin->retrieveApiById($current_user_id);		

		//Create item API array
		$item = array();
		$item['show_id'] = ($api instanceof \Libsyn\Api) ? $api->getShowId() : null;
		$item['item_title'] = $post->post_title;
		$item['item_subtitle'] = get_post_meta($post->ID, 'libsyn-post-episode-subtitle', true);
		$item['thumbnail_url'] = get_post_meta($post->ID, 'libsyn-new-media-image', true);
		
		if( function_exists('strip_shortcodes') ) {
			$content = wpautop(wp_kses_post(strip_shortcodes(self::stripShortcode('smart_track_player', self::stripShortcode('podcast', self::stripShortcode('podcast', $post->post_content))))));
		} else {
			$content = wpautop(wp_kses_post(self::stripShortcode('smart_track_player', (self::stripShortcode('podcast', self::stripShortcode('podcast', $post->post_content))))));
		}
		$item['item_category'] = get_post_meta($post->ID, 'libsyn-post-episode-category-selection', true);
		$item['itunes_explicit'] = get_post_meta($post->ID, 'libsyn-post-episode-itunes-explicit', true);
		if($item['itunes_explicit']==='explicit') $item['itunes_explicit'] = 'yes';
		$item['itunes_episode_number'] = get_post_meta($post->ID, 'libsyn-post-episode-itunes-episode-number', true);
		$item['itunes_episode_number'] = (!empty($item['itunes_episode_number'])) ? intval($item['itunes_episode_number']) : null; //set as int
		$item['itunes_season_number'] = get_post_meta($post->ID, 'libsyn-post-episode-itunes-season-number', true);
		$item['itunes_season_number'] = (!empty($item['itunes_season_number'])) ? intval($item['itunes_season_number']) : null; //set as int
		$item['itunes_episode_type'] = get_post_meta($post->ID, 'libsyn-post-episode-itunes-episode-type', true);
		$item['itunes_episode_summary'] = get_post_meta($post->ID, 'libsyn-post-episode-itunes-episode-summary', true);
		$item['itunes_episode_title'] = get_post_meta($post->ID, 'libsyn-post-episode-itunes-episode-title', true);
		$item['itunes_episode_author'] = get_post_meta($post->ID, 'libsyn-post-episode-itunes-episode-author', true);
		$item['update_id3'] = get_post_meta($post->ID, 'libsyn-post-episode-update-id3', true);
		$item['item_keywords'] = get_post_meta($post->ID, 'libsyn-post-episode-keywords', true);
		
		//player settings //post params are height(int),theme(standard,mini),width(int)
		$item['height'] = get_post_meta($post->ID, 'libsyn-post-episode-player_height', true);
		$item['width'] = get_post_meta($post->ID, 'libsyn-post-episode-player_width', true);
		$item['theme'] = get_post_meta($post->ID, 'libsyn-post-episode-player_use_theme', true);
		$item['custom_color'] = get_post_meta($post->ID, 'libsyn-post-episode-player_custom_color', true);
		
		//handle primary content
		$url = get_post_meta($post->ID, 'libsyn-new-media-media', true);
		if(strpos($url, 'libsyn-ftp-')!==false) $content_id = str_replace('http:', '', str_replace('https:', '', str_replace('/', '', str_replace('libsyn-ftp-', '', $url))));
		if(strpos($url, 'libsyn-upload-')!==false) $content_id = str_replace('http:', '', str_replace('https:', '', str_replace('/', '', str_replace('libsyn-upload-', '', $url))));
		if(isset($content_id)&&is_numeric($content_id)) { //then is ftp/unreleased
			$item['primary_content_id'] = intval($content_id);
		} elseif(!empty($url)) { //is regular
			$item['primary_content_url'] = $sanitize->url_raw($url);
		} else {
			//throw new Exception('Primary media error, please check your Libsyn settings.');
		}
		
		//handle simple download
		$simple_download =  get_post_meta($post->ID, 'libsyn-post-episode-simple-download', true);
		if(empty($simple_download) || $simple_download === "available") {
			$item['always_available'] = 'true';
		} elseif($simple_download === "release_date") {
			$item['always_available'] = 'false';
		}
		
		//handle is draft
		if($isDraft) {
			$item['is_draft'] = 'true';
		} else {
			$item['is_draft'] = 'false';
		}
		update_post_meta($post->ID, 'libsyn-is_draft', $item['is_draft']);
	
		//is this post an update or new?
		$wp_libsyn_item_id = get_post_meta( $post->ID, 'libsyn-item-id', true );
		$isUpdatePost = (empty($wp_libsyn_item_id)) ? false : true;
		$update_release =  get_post_meta($post->ID, 'libsyn-post-update-release-date', true);
		$isReRelease = ($update_release === 'isLibsynUpdateReleaseDate');
		
		
		if($isUpdatePost) { //update post
			$item['item_id'] = $wp_libsyn_item_id;
			if($isSchedule) {
				$releaseDate = $post->post_date_gmt;
			} else {
				if ( $isReRelease ) {
					$releaseDate = 'now';
				} else {
					$releaseDate =  $sanitize->mysqlDate(get_post_meta($post->ID, 'libsyn-release-date', true));
				}
			}
		} else { //new release
			if($isSchedule) {
				$releaseDate = $post->post_date_gmt;
				$item['always_available'] = 'true';
			} else {
				$releaseDate = $sanitize->mysqlDate(get_post_meta($post->ID, 'libsyn-release-date', true));
				if( $isReRelease) {
					$releaseDate = 'now';
				} elseif ( !empty($releaseDate) ) {
					$releaseDate = $releaseDate;
				} else {
					$releaseDate = 'now';
				}
				
			}
		}
		
		//handle update id3
		$update_id3 =  get_post_meta($post->ID, 'libsyn-post-episode-update-id3', true);
		if(!empty($update_id3) && $update_id3 == "isLibsynUpdateId3") {
			$item['update_id3'] = 'true';
		} else {
			$item['update_id3'] = 'false';
		}
		
		//handle edit item
		$wp_libsyn_edit_item_id = get_post_meta( $post->ID, 'libsyn-edit-item-id', true );
		if(!empty($wp_libsyn_edit_item_id)) $item['item_id'] = intval($wp_libsyn_edit_item_id);
		
		//set custom_permalink_url
		$item['custom_permalink_url'] = get_permalink( $post->ID );

		//get destinations & handle destinations
		if($api instanceof \Libsyn\Api) {
			$destinations = $plugin->getDestinations($api);
		} else {
			$destinations = false;
		}
		
		$item['releases'] = array();
		if(!empty($destinations->destinations)) {
			foreach($destinations->destinations as $destination) {
				if($destination->destination_type!=='WordPress') {
					$item['releases'][] = array(
						'destination_id'	=>	$destination->destination_id,
						'release_date'		=>	$releaseDate,
						//'expiration_date'			=> $expiresDate, //TODO: Perhaps add expires for posts eventually (optional feature)
					);
				}
			}
		}
		
		//handle advanced destinations
		$advanced_destinations = get_post_meta($post->ID, 'libsyn-post-episode-advanced-destination-form-data', true );
		$advanced_destinations_enabled = get_post_meta($post->ID, 'libsyn-post-episode-advanced-destination-form-data-input-enabled', true );
		if(!empty($advanced_destinations_enabled) && ($advanced_destinations_enabled == 'true') && !empty($advanced_destinations) && ($advanced_destinations !== '[]') && (!$isUpdatePost || ($isUpdatePost && $isReRelease))) {
			$advanced_destinations = json_decode($advanced_destinations);
			if(is_object($advanced_destinations)||is_array($advanced_destinations)) {
				unset($item['releases']); //we have data unset current set releases
				$item['releases'] = array();
				$savedDestinations = ($isUpdatePost && $isReRelease) ? get_post_meta($post->ID, 'libsyn-destination-releases', true) : false;
				
				//First loop: set the release elements to catch data for.
				foreach($advanced_destinations as $property => $value){
					if(strpos($property, 'libsyn-advanced-destination-checkbox-')!==false && $value === 'checked') {//use only checked elements
						$destination_id = intval(str_replace('libsyn-advanced-destination-checkbox-', '', $property));
						$working_release = array();
						$working_release['destination_id'] = $destination_id;
						//Second loop: fill in the release elements which are checked.
						foreach($advanced_destinations as $prop => $val){
							//handle form-table elements
							switch($prop) {
								case 'set_release_scheduler_advanced_release_lc__'.$destination_id.'-1':
									//release_date publish with the previous release date
									$working_release['release_date'] = null; //set default
									if($val === 'checked' && !empty($savedDestinations)) {
										foreach($savedDestinations as $working_savedDestination) {
											if(!empty($working_savedDestination->destination_id) && $working_savedDestination->destination_id == $destination_id) {//found saved destination
												if(!empty($working_savedDestination->release_date)) {
													$working_release['release_date'] = $working_savedDestination->release_date;
												}
											}
										}
									}
								case 'set_release_scheduler_advanced_release_lc__'.$destination_id.'-0': 
									//release_date publish immediately checkbox
									$working_release['release_date'] = $releaseDate;
									break;
								case 'set_release_scheduler_advanced_release_lc__'.$destination_id.'-2':
									//release_date set new release date
									if($val==='checked'){
										if(is_array($advanced_destinations)){
											if(isset($advanced_destinations['release_scheduler_advanced_release_lc__'.$destination_id.'_date']) && isset($advanced_destinations['release_scheduler_advanced_release_lc__'.$destination_id.'_time_select_select-element'])){
												$time_of_day = date('H:i:s', strtotime($advanced_destinations['release_scheduler_advanced_release_lc__'.$destination_id.'_time_select_select-element']));
												$working_release['release_date'] = date('Y-m-d H:i:s', strtotime($advanced_destinations['release_scheduler_advanced_release_lc__'.$destination_id.'_date'].' '.$time_of_day));
												$working_release['release_date'] = (!empty($working_release['release_date']) && function_exists('get_gmt_from_date')) ? get_gmt_from_date($working_release['release_date']) : $working_release['release_date'];
											}						
										} elseif(is_object($advanced_destinations)) {
											if(isset($advanced_destinations->{'release_scheduler_advanced_release_lc__'.$destination_id.'_date'}) && isset($advanced_destinations->{'release_scheduler_advanced_release_lc__'.$destination_id.'_time_select_select-element'})){
												$time_of_day = date('H:i:s', strtotime($advanced_destinations->{'release_scheduler_advanced_release_lc__'.$destination_id.'_time_select_select-element'}));
												$working_release['release_date'] = date('Y-m-d H:i:s', strtotime($advanced_destinations->{'release_scheduler_advanced_release_lc__'.$destination_id.'_date'}.' '.$time_of_day));
												$working_release['release_date'] = (!empty($working_release['release_date']) && function_exists('get_gmt_from_date')) ? get_gmt_from_date($working_release['release_date']) : $working_release['release_date'];
											}
										}
									}
									break;
								case 'set_expiration_scheduler_advanced_release_lc__'.$destination_id.'-0':
									//release_date publish immediately checkbox
									//do nothing will never expire						
									break;
								case 'set_expiration_scheduler_advanced_release_lc__'.$destination_id.'-1':
									if($val === 'checked' && !empty($savedDestinations)) {
										foreach($savedDestinations as $working_savedDestination) {
											if(!empty($working_savedDestination->destination_id) && $working_savedDestination->destination_id == $destination_id) {//found saved destination
												if(!empty($working_savedDestination->expiration_date)) {
													$working_release['expiration_date'] = $working_savedDestination->expiration_date;
												}
											}
										}
									}
									break;
								case 'set_expiration_scheduler_advanced_release_lc__'.$destination_id.'-2':
									if($val==='checked'){
										if(is_array($advanced_destinations)){
											if(isset($advanced_destinations['expiration_scheduler_advanced_release_lc__'.$destination_id.'_date']) && isset($advanced_destinations['expiration_scheduler_advanced_release_lc__'.$destination_id.'_time_select_select-element'])){
												$time_of_day = date('H:i:s', strtotime($advanced_destinations['expiration_scheduler_advanced_release_lc__'.$destination_id.'_time_select_select-element']));
												$working_release['expiration_date'] = date('Y-m-d H:i:s', strtotime($advanced_destinations['expiration_scheduler_advanced_release_lc__'.$destination_id.'_date'].' '.$time_of_day));
												$working_release['expiration_date'] = (!empty($working_release['expiration_date']) && function_exists('get_gmt_from_date')) ? get_gmt_from_date($working_release['expiration_date']) : $working_release['expiration_date'];
											}											
										} elseif(is_object($advanced_destinations)) {
											if(isset($advanced_destinations->{'expiration_scheduler_advanced_release_lc__'.$destination_id.'_date'}) && isset($advanced_destinations->{'expiration_scheduler_advanced_release_lc__'.$destination_id.'_time_select_select-element'})){
												$time_of_day = date('H:i:s', strtotime($advanced_destinations->{'expiration_scheduler_advanced_release_lc__'.$destination_id.'_time_select_select-element'}));
												$working_release['expiration_date'] = date('Y-m-d H:i:s', strtotime($advanced_destinations->{'expiration_scheduler_advanced_release_lc__'.$destination_id.'_date'}.' '.$time_of_day));
												$working_release['expiration_date'] = (!empty($working_release['expiration_date']) && function_exists('get_gmt_from_date')) ? get_gmt_from_date($working_release['expiration_date']) : $working_release['expiration_date'];
											}
										}
									}									
									break;
								default:
									//nothing
							}
						}
						if(isset($working_release) && !empty($working_release) && isset($working_release['release_date']) && !empty($working_release['release_date'])){
							$item['releases'][] = $working_release;
						}
					}
				}
			}
			
		}
		
		//check to make sure release_date is set or releases
		if(empty($item['releases']) && empty($item['release_date'])) {
			$item['release_date'] = $releaseDate;
		}

		//run post
		if($plugin->hasLogger) $plugin->logger->info( "Post:\tSubmitting Post to API" );
		if($api instanceof \Libsyn\Api) {
			//bug fix for required fields
			if(empty($item['item_title'])) {
				$item['item_title'] = ' ';
			}
			$libsyn_post = $plugin->postPost($api, array_filter($item));
		} else {
			$libsyn_post = false;
		}
		if($libsyn_post!==false) {
			self::updatePost($post, $libsyn_post, $isUpdatePost);
		} else  { add_post_meta($post->ID, 'libsyn-post-error', 'true', true); }
	}
	
	/**
	 * Temp change global state of WP to fool shortcode
	 * 
	 * @param <string> $code name of the shortcode
	 * @param <string> $content 
	 * 
	 * @return <string> content with shortcode striped
	 */
	public static function stripShortcode( $code, $content ) {
		global $shortcode_tags;

		$stack = $shortcode_tags;
		if($code=="all") $shortcode_tags = array();
			else $shortcode_tags = array($code => 1);

		$content = strip_shortcodes($content);

		$shortcode_tags = $stack;
		return $content;
	}
	
	/**
	 * Just updates the WP_Post after a successful Libsyn Episode Post
	 * 
	 * @param <WP_Post> $post 
	 * @param <object> $libsyn_post
	 * 
	 * @return <bool>
	 */
	public static function updatePost( $post, $libsyn_post, $isUpdatePost ) {
		global $wpdb;
		
		//grab player settings
		$playerTheme	= get_post_meta($post->ID, 'libsyn-post-episode-player_use_theme', true);
		$playerHeight	= get_post_meta($post->ID, 'libsyn-post-episode-player_height', true);
		// $playerWidth	= get_post_meta($post->ID, 'libsyn-post-episode-player_width', true);
		$playerWidth	= $playerWidth = '100%'; //always 100%
		// if(!empty($playerTheme) && $playerTheme == "custom") { //custom player override 100% width
			// $playerWidth = '100%';
		// }
		
		$playerPlacement = get_post_meta($post->ID, 'libsyn-post-episode-player_placement', true);
		$playerPlacement = ($playerPlacement==='top')?$playerPlacement:'bottom'; //defaults to bottom

		//check for download link
		$playerUseDownloadLink = get_post_meta($post->ID, 'libsyn-post-episode-player_use_download_link', true);
		$playerUseDownloadLink = ($playerUseDownloadLink==='use_download_link')?true:false;
		$playerUseDownloadLinkText = get_post_meta($post->ID, 'libsyn-post-episode-player_use_download_link_text', true);
		if(($playerUseDownloadLink)  && !empty($playerUseDownloadLinkText)) {
			$download_link = '<br /><br /><a class="libsyn-download-link" href ="'.$libsyn_post->primary_content_url.'" target="_blank">'.$playerUseDownloadLinkText.'</a><br />';
		} else {
			$download_link = '';
		}
		$integration = new Service\Integration();
		$prefixBlock = '';
		$suffixBlock = '';
		if($integration instanceof \Libsyn\Service\Integration) {
			$checkDivi = $integration->checkPlugin('divi-builder');
			if($checkDivi) {
				//divi builder found add shortcode block container
				$prefixBlock = '[et_pb_section bb_built="1" fullwidth="on" specialty="off" prev_background_color="#000000"][et_pb_fullwidth_code admin_label="Libsyn Podcast Post"]';
				$suffixBlock = '[/et_pb_fullwidth_code][/et_pb_section]';
			}
		}

		//update post db
		if(!$isUpdatePost) {
			if($playerPlacement==='top') {
				$wpdb->update(
					$wpdb->prefix . 'posts',
					array(
						'post_content' => $prefixBlock . '[podcast src="'.$libsyn_post->url.'" height="'.$playerHeight.'" width="'.$playerWidth.'" placement="'.$playerPlacement.'" theme="'.$playerTheme.'"]'.$download_link . $suffixBlock . wp_kses_post(self::stripShortcode('podcast', $post->post_content)),
						'post_modified' => date("Y-m-d H:i:s"),
						'post_modified_gmt' => gmdate("Y-m-d H:i:s"),
					),
					array('ID' => $post->ID)
				);
			} else {
				$wpdb->update(
					$wpdb->prefix . 'posts',
					array(
						'post_content' => wp_kses_post(self::stripShortcode('podcast', $post->post_content)) . $prefixBlock . '[podcast src="'.$libsyn_post->url.'" height="'.$playerHeight.'" width="'.$playerWidth.'" placement="'.$playerPlacement.'" theme="'.$playerTheme.'"]' . $download_link . $suffixBlock,
						'post_modified' => date("Y-m-d H:i:s"),
						'post_modified_gmt' => gmdate("Y-m-d H:i:s"),
					),
					array('ID' => $post->ID)
				);				
			}
		} else {
			//shortcode stuff
			$shortcode_pattern = get_shortcode_regex();
			preg_match('/'.$shortcode_pattern.'/s', $post->post_content, $matches);
			if(is_array($matches)) {
				if (isset($matches[2]) && $matches[2] == 'podcast') { // matches (has player shortcode)
					$post_content_text = $post->post_content;
					$post_content_text = preg_replace('#<a class="libsyn-download-link"(.*?)</a>#', '', str_replace('<br /><br /><a class="libsyn-download-link"', '<a class="libsyn-download-link"', $post_content_text));
					$podcast_shortcode_text = '[podcast src="'.$libsyn_post->url.'" height="'.$playerHeight.'" width="'.$playerWidth.'" placement="'.$playerPlacement.'" theme="'.$playerTheme.'"]';
					$new_post_content = preg_replace('#(?<=[podcast).+(?=])#s', '', $post_content_text);
					$new_post_content = str_replace($matches[0], $prefixBlock . $podcast_shortcode_text . $download_link . $suffixBlock, $new_post_content);

					$wpdb->update(
						$wpdb->prefix . 'posts',
						array(
							'post_content' => $new_post_content,
							'post_modified' => date("Y-m-d H:i:s"),
							'post_modified_gmt' => gmdate("Y-m-d H:i:s"),
						),
						array('ID' => $post->ID)
					);
				} elseif(!isset($matches[2])) { //somehow doesn't have the player shortcode and is update
					$new_post_content = preg_replace('/' . preg_quote('<!-- START '.$post->ID.' Download Link -->').'.*?' .preg_quote('<!-- END '.$post->ID.' Download Link -->') . '/', '', $post->post_content);

					if($playerPlacement==='top') {
						$wpdb->update(
							$wpdb->prefix . 'posts',
							array(
								'post_content' => $prefixBlock . '[podcast src="'.$libsyn_post->url.'" height="'.$playerHeight.'" width="'.$playerWidth.'" placement="'.$playerPlacement.'" theme="'.$playerTheme.'"]'.$download_link.wp_kses_post(self::stripShortcode('podcast', $new_post_content)) . $suffixBlock,
								'post_modified' => date("Y-m-d H:i:s"),
								'post_modified_gmt' => gmdate("Y-m-d H:i:s"),
							),
							array('ID' => $post->ID)
						);
					} else {
						$wpdb->update(
							$wpdb->prefix . 'posts',
							array(
								'post_content' => wp_kses_post(self::stripShortcode('podcast', $new_post_content)) . $prefixBlock . '[podcast src="'.$libsyn_post->url.'" height="'.$playerHeight.'" width="'.$playerWidth.'" placement="'.$playerPlacement.'" theme="'.$playerTheme.'"]' . $download_link . $suffixBlock,
								'post_modified' => date("Y-m-d H:i:s"),
								'post_modified_gmt' => gmdate("Y-m-d H:i:s"),
							),
							array('ID' => $post->ID)
						);				
					}					
				}
			}
		}
		/* Update Post Meta */
		update_post_meta($post->ID, 'libsyn-item-id', $libsyn_post->id);
		update_post_meta($post->ID, 'libsyn-release-date', $libsyn_post->release_date);
		update_post_meta($post->ID, 'libsyn-destination-releases', $libsyn_post->releases);
		$plugin = new Service();
		$current_user_id = $plugin->getCurrentUserId();
		$api = $plugin->retrieveApiById($current_user_id);
		if(!empty($libsyn_post->show_id)) {
			if($api instanceof \Libsyn\Api) {
				$show = $plugin->getShow($api, $libsyn_post->show_id)->{'user-shows'};
				if(!empty($show)) {//matched show
					if(!empty($show->{'feed_url'})) {
						update_post_meta($post->ID, 'libsyn-show-feed_url', $show->{'feed_url'});
					}
					if(!empty($show->{'show_title'})) {
						update_post_meta($post->ID, 'libsyn-show-show_title', $show->{'show_title'});
					}
				} else {//log error
					if($plugin->hasLogger) $plugin->logger->error("Post:\tProblem saving libsyn-show-feed_url\t");
				}
			}
		}
		//TODO: Add social tags and additional post meta here
	}
	
	/**
	 * Handles WP callback to send variable to trigger AJAX response.
	 * 
	 * @param <array> $vars 
	 * 
	 * @return <array>
	 */
	public static function plugin_add_trigger_load_form_data($vars) {
		$vars[] = 'load_libsyn_media';
		return $vars;
	}
	
	/**
	 * Handles WP callback to send variable to trigger AJAX response.
	 * 
	 * @param <array> $vars 
	 * 
	 * @return <mixed>
	 */
	public static function plugin_add_trigger_remove_ftp_unreleased( $vars ) {
		$vars[] = 'remove_ftp_unreleased';
		return $vars;
	}
	
	/**
	 * Handles WP callback to send variable to trigger AJAX response.
	 * 
	 * @param <array> $vars 
	 * 
	 * @return <mixed>
	 */
	public static function plugin_add_trigger_load_player_settings( $vars ) {
		$vars[] = 'load_player_settings';
		return $vars;
	}
	
	/**
	 * Handle ajax page for loading post page form data
	 * 
	 * 
	 * @return <mixed>
	 */
	public static function loadFormData() {
		$libsyn_error = true;
		$checkUrl  = self::getCurrentPageUrl();
		parse_str($checkUrl, $urlParams);
		if(intval($urlParams['load_libsyn_media']) == 1&&(current_user_can( 'upload_files' ))&&(current_user_can( 'edit_posts' ))) {
			global $wpdb;
			$libsyn_error = false;
			$plugin = new Service();
			$current_user_id = $plugin->getCurrentUserId();
			$api = $plugin->retrieveApiById($current_user_id);
			$wpdb->delete($wpdb->prefix . 'posts', array('post_mime_type' => 'libsyn/ftp-unreleased'));
			$wpdb->delete($wpdb->prefix . 'posts', array('post_mime_type' => 'audio/ftp-unreleased'));
			$wpdb->delete($wpdb->prefix . 'posts', array('post_mime_type' => 'video/ftp-unreleased'));

			$wpdb->get_results($wpdb->prepare("DELETE FROM ".$wpdb->prefix."postmeta WHERE meta_value LIKE %s", "/libsyn/ftp-unreleased%"));
			if ($api instanceof \Libsyn\Api){
				$isRefreshExpired = $api->isRefreshExpired();
				if($isRefreshExpired) { //refresh expired attempt to refresh
					$refreshApi = $api->refreshToken();
				} else { //nothing to do token looks good
					$refreshApi = true;
				}
				if($refreshApi) { //successfully refreshed
					/* Remove and add FTP/Unreleased Media */
					$ftp_unreleased = $plugin->getFtpUnreleased($api)->{'ftp-unreleased'};
					if(!empty($ftp_unreleased)) {
						foreach($ftp_unreleased as $media) {
							// We need to make sure we are working with only audio/video files...
							if((strpos($media->mime_type, 'audio')!== false)||(strpos($media->mime_type, 'video') !== false)) {
								
								//for new versions of wordpress - handle media info in metadata
								if(strpos($media->mime_type, 'video') !== false) {
									
								} elseif(strpos($media->mime_type, 'audio')!== false) {
									
								} else {
									//neither audio or video
								}
								
								$file_name = explode('.', $media->file_name);
								$mime_type = explode('/', $media->mime_type);
								$data = array(
										'post_author'			=>	$plugin->getCurrentUserId(),
										'post_date'				=>	date("Y-m-d H:i:s"),
										'post_date_gmt'			=>	date("Y-m-d H:i:s"),
										'post_content'			=>	'Libsyn FTP/Unreleased Media: '.$media->file_name,
										'post_title'			=>	$file_name[0],
										'post_excerpt'			=>	'',
										'post_status'			=>	'inherit',
										'comment_status'		=>	'open',
										'ping_status'			=>	'closed',
										'post_password'			=>	'',
										'post_name'				=>	'libsyn-ftp-'.$media->content_id,
										'to_ping'				=>	'',
										'pinged'				=>	'',
										'post_modified'			=>	date("Y-m-d H:i:s"),
										'post_modified_gmt'		=>	date("Y-m-d H:i:s"),
										'post_content_filtered'	=>	'',
										'post_parent'			=>	0,
										'guid'					=>	$media->file_name,
										'menu_order'			=>	0,
										'post_type'				=>	'attachment',
										'post_mime_type'		=>	'libsyn/ftp-unreleased',
										'comment_count'			=>	0,
								);
								//$wpdb->insert($wpdb->prefix . 'posts', $data);
								$post_id = wp_insert_post($data, false);
								
								//add post meta
								add_post_meta($post_id, '_wp_attached_file', '/libsyn/ftp-unreleased/'.$media->file_name);
							}
						}
					}
					/* Get categories and send output on success */
					$categories = $plugin->getCategories($api)->{'categories'};
					if(!is_array($categories)) $categories = array($categories);
					$json = array();
					foreach($categories as $category)
						if(isset($category->item_category_name)) $json[] = $category->item_category_name;
					//if(empty($json)) $libsyn_error = true;	
				} else { $libsyn_error = true; }
			} else { $libsyn_error = true; }
			//set output
			header('Content-Type: application/json');
			if(!$libsyn_error) echo json_encode($json);
				else echo json_encode(array());
			exit;
		}
	}
	
    /**
     * Cleares post meta and posts for ftp/unreleased data.
     * 
     * 
     * @return <type>
     */
	public static function removeFTPUnreleased() {
		global $wpdb;
		$libsyn_error = true;
		$checkUrl  = self::getCurrentPageUrl();
		parse_str($checkUrl, $urlParams);
		if(intval($urlParams['remove_ftp_unreleased']) === 1) {
			$wpdb->delete($wpdb->prefix . 'posts', array('post_mime_type' => 'libsyn/ftp-unreleased'));
			$wpdb->delete($wpdb->prefix . 'posts', array('post_mime_type' => 'audio/ftp-unreleased'));
			$wpdb->delete($wpdb->prefix . 'posts', array('post_mime_type' => 'video/ftp-unreleased'));
			$wpdb->get_results($wpdb->prepare("DELETE FROM ".$wpdb->prefix."postmeta WHERE meta_value LIKE %s", "/libsyn/ftp-unreleased%"));
			$libsyn_error = false;
			
			//set output
			header('Content-Type: application/json');
			if(!$libsyn_error) echo json_encode(true);
				else echo json_encode(false);
			exit;
		}
		return;
	}
	
}
?>
