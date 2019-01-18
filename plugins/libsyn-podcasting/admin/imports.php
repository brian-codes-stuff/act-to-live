<?php
$plugin = new Libsyn\Service();
$utilities = new Libsyn\Utilities();
$importer = new Libsyn\Service\Importer();
$sanitize = new Libsyn\Service\Sanitize();
$current_user_id = $plugin->getCurrentUserId();
$api = $plugin->retrieveApiById($current_user_id);
$integration = new Libsyn\Service\Integration();
$render = true;
$error = false;
$libsyn_text_dom = $plugin->getTextDom();
add_action( 'wp_feed_options', 'Libsyn\\Utilities::disableFeedCaching' ); //disable feed caching


//testing
// $importerEmailer = (new Libsyn\Service\Cron\ImporterEmailer())->activate();


/* Handle saved api */
if ($api instanceof \Libsyn\Api && !$api->isRefreshExpired()){
	$refreshApi = $api->refreshToken();
	if($refreshApi) { //successfully refreshed
		$api = $plugin->retrieveApiById($current_user_id);
	} else { //in case of a api call error...
		$handleApi = true; 
		$clientId = (!isset($clientId))?$api->getClientId():$clientId; 
		$clientSecret = (!isset($clientSecret))?$api->getClientSecret():$clientSecret; 
		$api = false;
		if(isset($showSelect)) unset($showSelect);
	}
}

//Handle Checking Feed Status
$hasIncompleteImport = false;
$feedImportTriggered = get_user_option('libsyn-podcasting-feed_import_triggered');
$ppFeedTriggered = get_user_option('libsyn-podcasting-pp_feed_triggered');
$feedImportId = get_user_option('libsyn-podcasting-feed_import_id');
$originFeedUrl = get_user_option('libsyn-podcasting-feed_import_origin_feed');
$libsynFeedUrl = get_user_option('libsyn-podcasting-feed_import_libysn_feed');
$feedImportPosts = get_user_option('libsyn-podcasting-feed_import_posts');
$importedPostIds = get_user_option('libsyn-podcasting-imported_post_ids');
$hasSavedData = (!empty($ppFeedTriggered) || !empty($feedImportId) || !empty($feedImportPosts) || !empty($importedPostIds));

//Handle Powerpress Integration
$checkPowerpress = $integration->checkPlugin('powerpress');
if($checkPowerpress) {
	global $wp_rewrite;
	if(in_array('podcast', $wp_rewrite->{'feeds'}) && in_array('feed', $wp_rewrite->{'feeds'})) {
		$ppFeedUrl = get_site_url() . "/feed/podcast";
		// $powerpressFeed = fetch_feed( $ppFeedUrl );
		$powerpressFeed = $utilities->fetchFeed( $ppFeedUrl );
	} else {
		$ppFeedUrl = get_site_url() . "/feed";
		// $powerpressFeed = fetch_feed( $ppFeedUrl );
		$powerpressFeed = $utilities->fetchFeed( $ppFeedUrl );
	}

	if(!is_wp_error($powerpressFeed) && $powerpressFeed instanceof SimplePie) {
		$feed_args = array(
			'singular'=> 'libsyn_feed_item' //Singular label
			,'plural' => 'libsyn_feed_items' //plural label, also this well be one of the table css class
			,'ajax'   => false //We won't support Ajax for this table
			,'screen' => get_current_screen()
		);
		//setup new array with feed data
		$powerpress_feed_items = array();
		$x=0;
		foreach ($powerpressFeed->get_items() as $feed_item) {
			$powerpress_feed_items[$x] = new stdClass();
			$powerpress_feed_items[$x]->id = $x;
			$powerpress_feed_items[$x]->title = $feed_item->get_title();
			$powerpress_feed_items[$x]->content = $feed_item->get_content();
			$powerpress_feed_items[$x]->description = $feed_item->get_description();
			$powerpress_feed_items[$x]->permalink = "<a href=\"".$feed_item->get_permalink()."\" target=\"_blank\">".$feed_item->get_permalink()."</a>";
			$powerpress_feed_items[$x]->release_date = $feed_item->get_date();
			$x++;
		}
		//Prepare Table of elements
		$libsyn_feed_wp_list_table = new \Libsyn\Service\Table($feed_args, $powerpress_feed_items);
		$libsyn_feed_wp_list_table->item_headers = array(
			'id' => 'id'
			,'title' => 'Episode Title'
			,'description' => 'Description'
			,'permalink' => 'Episode Link'
			,'release_date' => 'Release Date'
		);
		$libsyn_feed_wp_list_table->prepare_items();
	} elseif( is_wp_error($powerpressFeed) ) {
		if(!empty($powerpressFeed->{'errors'}) && !empty($powerpressFeed->{'errors'}['simplepie-error'][0])) {
				$msg = "Feed Reader Error:\t" . $powerpressFeed->{'errors'}['simplepie-error'][0];
		} else {
				$msg = "Your Powerpress feed cannot be read by the importer.  The feed may be invalid.";
		}
		if($plugin->hasLogger) $plugin->logger->error("Importer:\t".$msg);
		$error = true;
	} else {
		$checkPowerpress = false;
	}
}


//check for clear or inserting of posts
$clearImports = false;
$addImports = false;
if(!empty($_POST['libsyn-importer-action'])) {
	switch($_POST['libsyn-importer-action']) {
		case "clear_imports":
			//handle clear imports
			$clearImports = true;
			break;
		case "add_player_to_posts":
			//handle add player to posts
			$addImports = true;
			break;
	}
}

//Check in case feed import timed out
if(empty($feedImportId) && ($ppFeedTriggered || $feedImportTriggered)) {
	
	$importStatus = (!empty($api) && $api instanceof \Libsyn\Api) ? $plugin->feedImportStatus($api) : false;
	if(!empty($importStatus->{'feed-import'}) && is_array($importStatus->{'feed-import'})) {
		if(!empty($importStatus->{'feed-import'}[0]->parent_job_id)) {
			$feedImportId = $importStatus->{'feed-import'}[0]->parent_job_id;
		}
	}
}

//Handle Feed
if(!empty($feedImportId)) {
	$msg = "Import Success!  Please reload the page to check the status of your import.";
	$error = false;
	
	//get the job status
	if(!empty($feedImportPosts) && is_array($feedImportPosts)) {//pass $feedImportPosts as items to handle feed import update only media
		$importGuids = array();
		foreach($feedImportPosts as $post) {
			if(!empty($post->guid)) {
				$importGuids[] = $post->guid;
			}
		}
		if(!isset($importStatus)) {
			$importStatus = (!empty($api) && $api instanceof \Libsyn\Api) ? $plugin->feedImportStatus($api, array('job_id' => $feedImportId, 'items' => $importGuids)) : false;
		}
	} else {
		if(!isset($importStatus)) {
			$importStatus = (!empty($api) && $api instanceof \Libsyn\Api) ? $plugin->feedImportStatus($api, array('job_id' => $feedImportId)) : false;
		}
	}

	$feed_args = array(
		'singular'=> 'libsyn_feed_item' //Singular label
		,'plural' => 'libsyn_feed_items' //plural label, also this well be one of the table css class
		,'ajax'   => false //We won't support Ajax for this table
		,'screen' => get_current_screen()
	);
	//setup new array with feed data
	$imported_feed = array();
	$x=0;

	//Feed Import Status
	if(!empty($importStatus->{'feed-import'}) && is_array($importStatus->{'feed-import'})) {
		foreach ($importStatus->{'feed-import'} as $row) {
			if(!empty($feedImportPosts) && !empty($ppFeedTriggered)) {//has powerpress or local feed
				foreach ($feedImportPosts as $feed_item) {
					if(function_exists('url_to_postid') && !empty($feed_item->{'link'})) {
						$feedItemLink = url_to_postid($feed_item->{'link'});
					} else {
						$feedItemLink = false;
					}
					if(function_exists('get_permalink') && !empty($feed_item->{'id'})) {
						$feedItemId = get_permalink($feed_item->{'id'});
					} else {
						$feedItemId = false;
					}
					if(function_exists('url_to_postid') && !empty($row->custom_permalink_url)) {
						$rowCustomPermalinkUrl = url_to_postid($row->custom_permalink_url);
					} else {
						$rowCustomPermalinkUrl = '';
					}
					
					if(!empty($feedItemLink) ) {
						$working_id = url_to_postid($feed_item->{'link'});
					} else if(!empty($feed_item->{'id'}) && !empty($feedItemId) ) {
						$working_id = $feed_item->{'id'};
					} else {
						$working_id = null;
					}

					if( //Check to make sure working_id matches up to what we imported
						!empty($working_id) && 
						(!empty($feed_item->{'guid'}) && !empty($row->guid) && ($feed_item->{'guid'} === $row->guid)) ||
						(!empty($row->custom_permalink_url) && ($rowCustomPermalinkUrl == $working_id)) ||
						(!empty($row->guid) && strpos($row->guid, $working_id) !== false)
					) {

						$contentStatus = $row->primary_content_status;
						switch ($contentStatus) {
							case "":
							case null:
							case false:
								$contentStatus = "unavailable";
								$contentStatusColor = "style=\"color:red;\"";
								$hasIncompleteImport = true;
								break;
							case "awaiting_payload":
								$contentStatus = "pending download";
								$contentStatusColor = "style=\"color:orange;\"";
								$hasIncompleteImport = true;
								break;
							case "failed":
								$contentStatusColor = "style=\"color:red;\"";
								break;
							case "available":
								if($addImports) {
									try {
										if(!empty($working_id)) {
											$importer->addPlayer($working_id, $row->url);
											if(!empty($api) && $api instanceof \Libsyn\Api) {
												$importer->createMetadata($working_id, $row, $api);
											} else {
												$importer->createMetadata($working_id, $row);
											}
										}
									} catch (Exception $e) {
										//TODO: Log error
									}
									$msg = "Successfully added Libsyn Player to Imported Posts!";
								}
								$contentStatusColor = "";
								break;
							default:
								$contentStatusColor = "";
						}
						if($clearImports) {
							if(!empty($working_id)) {
								try {
									$importer->clearPlayer($working_id);
									$importer->clearMetadata($working_id);
								} catch (Exception $e) {
									//TODO: Log error
								}
							}
						}
						
						$duplicate = false;
						if(!empty($imported_feed)) {
							foreach($imported_feed as $feed) { //check to make sure this is not a duplicate
								if(!empty($feed->id) && $feed->id === $row->id) {
									$duplicate = true;
								}
							}
						}
						if(!$duplicate) {
							$contentStatus = ucfirst($contentStatus);
							$imported_feed[$x] = new stdClass();
							$imported_feed[$x]->id = $row->id;
							$imported_feed[$x]->title = $row->item_title;
							$imported_feed[$x]->content = $row->item_body;
							$imported_feed[$x]->subtitle = $row->item_subtitle;
							$imported_feed[$x]->permalink = "<a " . $contentStatusColor ." href=\"".$row->url."\" target=\"_blank\">" . $contentStatus . "</a>";
							$imported_feed[$x]->status = $contentStatus;
							$imported_feed[$x]->release_date = $row->release_date;
							$x++;
						}
						if(isset($contentStatus)) unset($contentStatus);
					}
					if( isset($feedItemLink) ) unset($feedItemLink);
					if( isset($feedItemId) ) unset($feedItemId);
					if( isset($rowCustomPermalinkUrl) ) unset($rowCustomPermalinkUrl);
					if( isset($working_id) ) unset($working_id);
				}
			} elseif(empty($ppFeedTriggered)) {//has external feed import (make sure not pp feed import)
				if(function_exists('url_to_postid') && !empty($row->custom_permalink_url)) {
					$rowCustomPermalinkUrl = url_to_postid($row->custom_permalink_url);
				} else {
					$rowCustomPermalinkUrl = '';
				}
				if(!empty($row->custom_permalink_url) && empty($rowCustomPermalinkUrl)) {//check that this is not actually a wp post already
					$contentStatus = $row->primary_content_status;
					switch ($contentStatus) {
						case "":
						case null:
						case false:
							$contentStatus = "unavailable";
							$contentStatusColor = "style=\"color:red;\"";
							$hasIncompleteImport = true;
							break;
						case "awaiting_payload":
							$contentStatus = "pending download";
							$contentStatusColor = "style=\"color:orange;\"";
							$hasIncompleteImport = true;
							break;
						case "failed":
							$contentStatusColor = "style=\"color:red;\"";
							break;
						case "available":
							if($addImports) {
								if(!empty($row->id)) {//checking item_id isset
									try {
										$working_post_id = $importer->createPost($row);
										if(!empty($working_post_id)) {
											if(!empty($api) && $api instanceof \Libsyn\Api) {
												$importer->createMetadata($working_post_id, $row, $api);
											} else {
												$importer->createMetadata($working_post_id, $row);
											}
											$importer->addPlayer($working_post_id, $row->url);
											$importedPostIds = get_user_option('libsyn-podcasting-imported_post_ids');
											if(empty($importedPostIds)) {
												$importedPostIds = array();
											} else {
												$importedPostIds = (!empty($importedPostIds)) ? $importedPostIds : array();
											}
											$importedPostIds[] = $working_post_id;
											update_user_option($current_user_id, 'libsyn-podcasting-imported_post_ids', $importedPostIds, false);
											

										}
									} catch (Exception $e) {
										//TODO: Log error
									}
								}
								$msg = "Successfully added Libsyn Player to Imported Posts!";
							}
							$contentStatusColor = "";
							break;
						default:
							$contentStatusColor = "";
					}
					
					$duplicate = false;
					if(!empty($imported_feed)) {
						foreach($imported_feed as $feed) { //check to make sure this is not a duplicate
							if(!empty($feed->id) && $feed->id === $row->id) {
								$duplicate = true;
							}
						}
					}
					if(!$duplicate) {
						$contentStatus = ucfirst($contentStatus);
						$imported_feed[$x] = new stdClass();
						$imported_feed[$x]->id = $row->id;
						$imported_feed[$x]->title = $row->item_title;
						$imported_feed[$x]->content = $row->item_body;
						$imported_feed[$x]->subtitle = $row->item_subtitle;
						$imported_feed[$x]->permalink = "<a " . $contentStatusColor ." href=\"".$row->url."\" target=\"_blank\">" . $contentStatus . "</a>";
						$imported_feed[$x]->status = $contentStatus;
						$imported_feed[$x]->release_date = $row->release_date;
						$x++;
					}
					if(isset($contentStatus)) unset($contentStatus);
				}
				if( isset($working_id) ) unset($working_id);
				if( isset($rowCustomPermalinkUrl) ) unset($rowCustomPermalinkUrl);
			}
		}

		//Prepare Table of elements
		$libsyn_feed_status_wp_list_table = new \Libsyn\Service\Table($feed_args, $imported_feed);
		$libsyn_feed_status_wp_list_table->item_headers = array(
			'id' => 'id'
			,'title' => 'Episode Title'
			,'subtitle' => 'Subtitle'
			,'permalink' => 'Episode Link'
			,'release_date' => 'Release Date'
		);
		$libsyn_feed_status_wp_list_table->prepare_items();
	}
}

//Handle clear imports
if($clearImports) {
	try {
		delete_user_option($current_user_id, 'libsyn-podcasting-pp_feed_triggered', false);
		delete_user_option($current_user_id, 'libsyn-podcasting-feed_import_triggered', false);
		delete_user_option($current_user_id, 'libsyn-podcasting-feed_import_id', false);
		delete_user_option($current_user_id, 'libsyn-podcasting-feed_import_origin_feed', false);
		delete_user_option($current_user_id, 'libsyn-podcasting-feed_import_libysn_feed', false);
		delete_user_option($current_user_id, 'libsyn-podcasting-feed_import_posts', false);
		$importedPostIds = get_user_option('libsyn-podcasting-imported_post_ids');
		if(!empty($importedPostIds) && is_string($importedPostIds)) {
			$importedPostIds = json_decode($importedPostIds, true);
			if(is_array($importedPostIds)) {
				foreach($importedPostIds as $postId) {
					if(!empty($postId)) {
						if(function_exists('wp_delete_post')) wp_delete_post($postId, false);//setting 2nd param true forces delete and not to trash
						$importer->clearPlayer($postId);
						$importer->clearMetadata($postId);
					}

				}
			}
		}
		delete_user_option($current_user_id, 'libsyn-podcasting-imported_post_ids', false);
	} catch (Exception $e) {
		//TODO: Log error
	}
	$msg = "Cleared importer settings and posts from Wordpress";
}

if(isset($_POST['msg'])) $msg = $_POST['msg'];
if(isset($_POST['error'])) $error = ($_POST['error']==='true')?true:false;

/* Handle Form Submit */
if (!empty( $_POST )) {
	if($api instanceof \Libsyn\Api) { //Brand new setup or changes?
		if(!empty($_POST['libsyn-powerpress-feed-url']) && ($_POST['libsyn-powerpress-feed-url'] == "true")) {
			if(!empty($powerpressFeed)) {
				if(!is_wp_error($powerpressFeed) && !empty($powerpressFeed->feed_url)) {
					$importFeedUrl = $sanitize->url_raw($powerpressFeed->feed_url);
				} elseif(!empty($ppFeedUrl)) {//There may be a error when loading the feed try to use the feed url built above
					$importFeedUrl = $ppFeedUrl;
				}
			}
			if(empty($importFeedUrl)) {
				if(!is_wp_error($powerpressFeed)) {
					$msg = "Powerpress feed seems to be invalid.  Please check the following URL:  <em>{$powerpressFeed}</em>";
					$error = true;
				} else {
					$msg = "Powerpress feed seems to be invalid.  Please check your Powerpress Feed settings.";
					$error = true;
				}
			} else {
				update_user_option($current_user_id, 'libsyn-podcasting-pp_feed_triggered', 'true', false);
			}
		} elseif(!empty($_POST['libsyn-import-feed-url'])) {
			$importFeedUrl = $sanitize->url_raw($_POST['libsyn-import-feed-url']);
		}
		if(!empty($importFeedUrl)) {
			//run feed importer
			update_user_option($current_user_id, 'libsyn-podcasting-feed_import_triggered', 'true', false);
			$importData = $plugin->feedImport($api, array('feed_url' => $importFeedUrl));
			if(!empty($importData) && $importData->{'status'} == "success") {//save callback data
				if(!empty($importData->origin_feed)) {
					update_user_option($current_user_id, 'libsyn-podcasting-feed_import_origin_feed', $importData->origin_feed, false);
				}
				if(!empty($importData->feed_url)) {
					update_user_option($current_user_id, 'libsyn-podcasting-feed_import_libysn_feed', $importData->feed_url, false);
				}
				if(!empty($importData->job_id)) {
					update_user_option($current_user_id, 'libsyn-podcasting-feed_import_id', $importData->job_id, false);
				}
				if(!empty($importData->entries)) {
					update_user_option($current_user_id, 'libsyn-podcasting-feed_import_posts', $importData->entries, false);
				}
				
				//setup cron emailer
				$importerEmailer = (new Libsyn\Service\Cron\ImporterEmailer())->activate();
				
			} else {
				$msg = "Feed Importer failed to import your feed please check your settings and try again.";
			}
		}

		if(!empty($feedImportId)) {//has existing feed import data
			
		}
	} else { //Failed Api check
		$msg = "Could not run import since your Libsyn Show is not configured.  Please visit the Settings page."; 
	}
	
	//Need to redirect back to refesh page
	$msgParam = (!empty($msg)) ? '&'.urlencode($msg) : '';
	$url = $plugin->admin_url('admin.php').'?page='.LIBSYN_DIR.'/admin/imports.php'.$msgParam;
	
	echo "<script type=\"text/javascript\">
			(function($){
				$(document).ready(function(){
					function sleepDelay(delay) {
						var start = new Date().getTime();
						$('.form-table').css({'opacity': 0.3});
						$('#libsyn-loading-img').css({'display': 'block'});
						while (new Date().getTime() < start + delay);
					}

					sleepDelay(10000);
					if (typeof window.top.location.href == 'string') window.top.location.href = \"".$url."\";
						else if(typeof document.location.href == 'string') document.location.href = \"".$url."\";
							else if(typeof window.location.href == 'string') window.location.href = \"".$url."\";
								else alert('Unknown Libsyn Plugin error 1012.  Please report this error to support@libsyn.com and help us improve this plugin!');
				});
			})(jQuery);
		 </script>";

}


//handle force page reload while media importer is running or not available
if($hasIncompleteImport && !empty($libsyn_feed_status_wp_list_table)) {
$msgParam = (!empty($msg)) ? '&'.urlencode($msg) : '';
$url = $plugin->admin_url('admin.php').'?page='.LIBSYN_DIR.'/admin/imports.php'.$msgParam;
echo "<script type=\"text/javascript\">
		(function($){
			$(document).ready(function(){
				console.log('has incomplete');
				setTimeout(function(){
					if (typeof window.top.location.href == 'string') window.top.location.href = \"".$url."\";
						else if(typeof document.location.href == 'string') document.location.href = \"".$url."\";
							else if(typeof window.location.href == 'string') window.location.href = \"".$url."\";
								else alert('Unknown Libsyn Plugin error 1013.  Please report this error to support@libsyn.com and help us improve this plugin!');
				}, 15000);
			});
		})(jQuery);
	 </script>";		
}

/* Handle API Creation/Update*/
if((!$api)||($api->isRefreshExpired())) { //does not have $api setup yet in WP
	$render = false;
}

/* Set Notifications */
global $libsyn_notifications;
do_action('libsyn_notifications');
?>

<?php wp_enqueue_script( 'jquery-ui-dialog', array('jquery-ui')); ?>
<?php wp_enqueue_style( 'wp-jquery-ui-dialog'); ?>
<?php wp_enqueue_script('jquery_validate', plugins_url(LIBSYN_DIR.'/lib/js/jquery.validate.min.js'), array('jquery')); ?>
<?php wp_enqueue_script('libsyn_meta_validation', plugins_url(LIBSYN_DIR.'/lib/js/meta_form.js')); ?>
<?php wp_enqueue_style( 'animate', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css' ); ?>
<?php wp_enqueue_script( 'jquery-easing', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js' ); ?>
<?php wp_enqueue_style( 'metaBoxes', plugins_url(LIBSYN_DIR.'/lib/css/libsyn_meta_boxes.css' )); ?>
<?php wp_enqueue_style( 'metaForm', plugins_url(LIBSYN_DIR.'/lib/css/libsyn_meta_form.css' )); ?>

	<style media="screen" type="text/css">
	.code { font-family:'Courier New', Courier, monospace; }
	.code-bold {
		font-family:'Courier New', Courier, monospace; 
		font-weight: bold;
	}
	</style>
	
	<!-- Main Body Area -->
	<div class="wrap">
	  <?php if (isset($msg)) echo $plugin->createNotification($msg, $error); ?>
	  <h2><?php _e("Publisher Hub - Import Feed", $libsyn_text_dom); ?><span style="float:right;"><a href="http://www.libsyn.com/"><img src="<?php _e(plugins_url( LIBSYN_DIR . '/lib/images/libsyn_dark-small.png'), $libsyn_text_dom); ?>" title="Libsyn Podcasting" height="28px"></a></span></h2>
	  <form name="<?php echo LIBSYN_NS . "form" ?>" id="<?php echo LIBSYN_NS . "form" ?>" method="post">
		 <div id="poststuff">
		  <div id="post-body">
			<div id="post-body-content">
			<?php if((isset($api) && ($api !== false)) || $render) { ?>
			
			<!-- BOS Existing API -->
			  <div class="stuffbox" style="width:93.5%">
				<h3 class="inside hndle"><label><?php _e("Source Feed Information", $libsyn_text_dom); ?></label></h3>
				<div class="inside" style="margin: 15px;">
				  <table class="form-table">
					<tr valign="top" style="border-bottom:none;">
					  <th></th>
					  <td>
					    <div id="libsyn-loading-img"></div>
						<div style="width:50%;">
							<?php if(!$hasSavedData) { ?>
							<p class="libsyn-import-information"><em>Here you can import a external Podcast Feed into your Libsyn account for use under Wordpress.</em></p>
							<?php } else { ?>
							<p class="libsyn-import-information"><em><!--Feed Import Text --></em></p>
							<?php } ?>
						</div>
					  </td>
					</tr>
					<?php if($checkPowerpress && empty($libsyn_feed_status_wp_list_table)) { ?>
					<tr valign="top">
					  <th><?php _e("Powerpress", $libsyn_text_dom); ?></th>
					  <td>
						<div class="input-field">
							<p style="font-size:1.1em;font-weight:bold;">Local Powerpress Feed Detected!</p>
							<?php if(!empty($ppFeedUrl)) { ?><p><strong>Powerpress Feed Url:</strong>&nbsp;&nbsp;<?php echo '<a href="' . $ppFeedUrl . '" target="_blank" title="Powerpress Feed Url" alt="Powerpress Feed Url">' . $ppFeedUrl . '</a>'; ?></p><?php } ?>
							<br />
							<!-- TODO: Display Feed table here -->
							<?php if(!empty($libsyn_feed_wp_list_table) && empty($libsyn_feed_status_wp_list_table)) {
								$libsyn_feed_wp_list_table->display();
							} ?>
							<p>Would you like to import the feed below to your Libsyn account and update existing posts with the Libsyn Player?  <br /><strong>Note:  </strong>This would not replace any existing expisodes in your libsyn account.</p><br />
							<div style="display:inline;">
								<button type="button" id="libsyn_import_powerpress_feed" class="button button-primary libsyn-dashicions-upload"><?php echo __('Import Local Feed Above', $libsyn_text_dom); ?></button>
								&nbsp;-OR-&nbsp;
								<button type="button" id="libsyn_toggle_show_feed_importer" class="button button-primary libsyn-dashicions-download" onclick="toggleShowFeedImporter()"><?php echo __('Import from a different Feed URL', $libsyn_text_dom); ?></button>
								<input type="hidden" id="libsyn-powerpress-feed-url" name="libsyn-powerpress-feed-url" />
							</div>
						</div>
					  </td>
					</tr>
					<?php } ?>
					<?php if(!empty($feedImportId)) { ?>
					<tr valign="top">
					  <th><?php _e("Feed Import", $libsyn_text_dom); ?></th>
					  <td>
						<div class="input-field">
							<?php if(!empty($libsyn_feed_status_wp_list_table) && $hasIncompleteImport) { ?>
							<p style="font-size:1.1em;font-weight:bold;">Feed Import Status - <span style="color:orange;">Processing</span></p>
							<?php } elseif(!empty($libsyn_feed_status_wp_list_table) && !$hasIncompleteImport) { ?>
							<p style="font-size:1.1em;font-weight:bold;">Feed Import Status - <span style="color:green;">Success</span></p>
							<?php } elseif(empty($libsyn_feed_status_wp_list_table)) { ?>
							<p style="font-size:1.1em;font-weight:bold;">Feed Import Status - <span style="color:red;">Failed</span></p>
							<?php } else { ?>
							<p style="font-size:1.1em;font-weight:bold;">Feed Import Status</p>
							<?php } ?>
							<br />
							<?php IF(!empty($libsyn_feed_status_wp_list_table)): ?>
							<?php if($hasIncompleteImport) { ?>
							<p><strong>Your feed import is currently processing.</strong> This page will refresh and update the Episode Link for each episode as the process runs. You will receive an email, as well as notice on this page once the import is fully complete.</p>
							<?php } ?>
							<?php if(!empty($libsyn_feed_status_wp_list_table) && !$hasIncompleteImport) { ?>
							<p>
							Congratulations! You have successfully imported your RSS feed. Your next step is to create a 301 redirect which will point your old RSS feed to your new RSS feed, and setup a special "new feed URL" tag in your Libsyn feed. Please follow these steps to setup a 301 redirect:
							<br />
							<ul>
								<li>–&nbsp;Download Redirection</li>
								<li>–&nbsp;Go under Tools --> Redirection and hit Add New</li>
								<li>–&nbsp;In the Source URL, enter your old feed URL</li>
								<li>–&nbsp;In the Target URL, enter your Libsyn RSS feed URL</li>
								<li>–&nbsp;Hit Add Redirect</li>
							</ul>
							Please follow these steps to setup a new feed URL tag in your Libsyn feed:
							<br />
							<ul>
								<li>–&nbsp;Log into your <a href="https://login.libsyn.com" target="_blank" title="Libsyn Dashboard" alt="Libsyn Dashboard">Libsyn Dashboard</a></li>
								<li>–&nbsp;Select Destinations</li>
								<li>–&nbsp;Select Libsyn Classic Feed</li>
								<li>–&nbsp;Scroll towards the bottom and select Advanced Options</li>
								<li>–&nbsp;Enter the iTunes redirect tag in the Extra RSS Tags text box:</li>
								<li><strong>&lt;itunes:new-feed-url&gt;<?php if(!empty($libsynFeedUrl)) echo $libsynFeedUrl; else echo 'http://www.myfeedurl.com/rss.xml'; ?>&lt;/itunes:new-feed-url&gt;</strong></li>
								<li><small><?php if(!empty($libsynFeedUrl)) echo 'Note: “'.$libsynFeedUrl.'” is your current imported destination (Libsyn) feed url.'; else echo 'Replace “http://www.myfeedurl.com/rss.xml” with whatever the URL of the feed you will be using (Libsyn) is.'; ?></small></li>
							</ul>
							<br />
							<?php } ?>
							<br />
							<?php if(!empty($ppFeedTriggered)) { ?>
							<p style="font-size:1.1em;font-weight:bold;">If Migrating from Powerpress</p>
							Once your redirects are in place, your next step is to update the player on your WordPress posts to the Libsyn player.
							<br />
							<ul>
								<li>–&nbsp;Come back to this page, scroll to the bottom, and hit "Add Libsyn Player to Wordpress Posts".</li>
								<li>–&nbsp;Go under Plugins --> Installed Plugins</li>
								<li>–&nbsp;Hit Deactivate for PowerPress</li>
							</ul>
							This will update your player on your Wordpress posts to your Libsyn player, and completes your migration process from Powerpress to Libsyn.
							<?php } ?>
							</p>
							<?php ELSEIF(empty($libsyn_feed_status_wp_list_table)): ?>
							<p>We initiated the feed importer, but it appears that the media imported into your Libsyn account may already exist or media failed to download from your feed.  Please make sure the media from your feed import is available and/or make sure the media does not already exist in your Libsyn account.</p><br /><p><strong>Depending on the size of your feed, the importer may take some time to process, try waiting a few minutes then refreshing your browswer.</strong></p><br /><p>If the importer is still not working then you may "<strong>Clear all imports data</strong>" and try again.</p><br />
							<?php ENDIF; ?>
							<!-- TODO: Display Feed table here -->
							<?php if(!empty($libsyn_feed_status_wp_list_table)) {
								$libsyn_feed_status_wp_list_table->display();
							} ?>
							<div style="display:inline;">
								<button type="button" id="libsyn_add_player_to_posts" class="button button-primary libsyn-dashicions-format-video"><?php echo __('Add Libsyn Player to Wordpress Posts', $libsyn_text_dom); ?></button>
								&nbsp;-OR-&nbsp;
								<button type="button" class="button button-primary libsyn-dashicions-trash libsyn_clear_imports"><?php echo __('Clear all Imports Data', $libsyn_text_dom); ?></button>
								<input type="hidden" id="libsyn-importer-action" name="libsyn-importer-action" />
							</div>
						</div>
					  </td>
					</tr>
					<?php } ?>
					<tr valign="top" id="libsyn-feed-import-tr" <?php if($checkPowerpress) echo 'style="display:none;"'; ?>>
					  <th><?php _e("Feed URL", $libsyn_text_dom); ?></th>
					  <td>
						<div class="input-field">
							<input type="url" style="width:64%;" name="libsyn-import-feed-url" id="libsyn-import-feed-url" class="validate" pattern="https?://.+">
							<span class="helper-text" data-error="Invalid Feed" data-success="Feed Valid"></span>
							<button type="button" id="libsyn_import_feed_rss" class="button button-primary libsyn-dashicions-update"><?php echo __('Import Feed', $libsyn_text_dom); ?></button>
							<?php if($hasSavedData) { ?>
							<button type="button" class="button button-primary libsyn-dashicions-trash libsyn_clear_imports"><?php echo __('Clear all Imports Data', $libsyn_text_dom); ?></button>
							<?php } ?>
						</div>
					  </td>
					</tr>
					<?php if(is_int($api->getShowId())) { ?>
					<tr valign="top">
						<th></th>
						<td>
							<div class="inside" style="margin: 15px;">Libsyn is connected to your Wordpress account successfully.</div>
						</td>
					</tr>					
					<?php } ?>
				  </table>
				</div>
			  </div>
			<!-- EOS Existing API -->
			  <!-- Dialogs -->
			  <div id="import-libsyn-player-dialog" class="hidden" title="Post Import">
				<p><span style="color:red;font-weight:600;">Warning!</span> By accepting you will modifying your Wordpress Posts with adding the player to the available feed import posts.  Would you like to proceed?</p>
				<p id="extra-text"></p>
				<br>
			  </div>
			  <div id="clear-settings-dialog" class="hidden" title="Confirm Clear Settings">
				<p><span style="color:red;font-weight:600;">Warning!</span> By accepting you will be removing all your import settings.  Click yes to continue.</p>
				<p id="extra-text"><span style="color:gray;font-weight:600;">NOTE:</span>  You will also need to remove any imported posts from your within the Libsyn Account Dashboard.</p>
				<br>
			  </div>
			  <div id="accept-import-dialog" class="hidden" title="Confirm Import">
				<p><span style="color:red;font-weight:600;">Warning!</span> By accepting you will importing the episodes in your external feed into your Libsyn Account. Would you like to proceed?</p>
				<br>
			  </div>			  
			
			<?php } else { ?>
			<!-- BOS Existing API -->
			  <div class="stuffbox" style="width:93.5%">
				<h3 class="hndle"><span><?php _e("Plugin needs configured", $libsyn_text_dom); ?></span></h3>
				<div class="inside" style="margin: 15px;">
				  <p style="font-size: 1.8em;"><?php _e("The Libsyn Publisher Hub is either not setup or something is wrong with the configuration, please visit the <a href='".admin_url('admin.php?page='.LIBSYN_ADMIN_DIR . 'settings.php')."'>settings page</a>.", $libsyn_text_dom); ?></p>
				</div>
			  </div>
			<!-- EOS Existing API -->				
			<?php } ?>
			<!-- BOS Libsyn WP Post Page -->
			<div class="stuffbox" id="libsyn-wp-post-page" style="display:none;width:93.5%;">
				
			</div>
			<!-- EOS Libsyn WP Post Page -->
			</div>
		  </div>
		</div>
	  </form>
	</div>
<?php //re-enable feed caching
add_action( 'wp_feed_options', 'Libsyn\\Utilities::enableFeedCaching' );
?>
	<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				var setOverlays = function() {
					//make sure overlays are not over dialogs
					$('.ui-widget-overlay').each(function() {
						$(this).css('z-index', 999);
						$(this).attr('style', 'z-index:999;');
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
				
				//check ajax
				var check_ajax_url = "<?php echo $sanitize->text($plugin->admin_url() . '?action=libsyn_check_url&libsyn_check_url=1'); ?>";
				var ajax_error_message = "<?php __('Something went wrong when trying to load your site\'s base url.
						Please make sure your "Site Address (URL)" in Wordpress settings is correct.', LIBSYN_DIR); ?>";		
				$.getJSON( check_ajax_url).done(function(json) {
					if(json){
						//success do nothing
					} else {
						//redirect to error out
						var ajax_error_url = "<?php echo $plugin->admin_url('admin.php').'?page='.LIBSYN_DIR.'/admin/post.php&error=true&msg='; ?>" + ajax_error_message;
						if (typeof window.top.location.href == "string") window.top.location.href = ajax_error_url;
								else if(typeof document.location.href == "string") document.location.href = ajax_error_url;
									else if(typeof window.location.href == "string") window.location.href = ajax_error_url;
										else alert("Unknown javascript error 1028.  Please report this error to support@libsyn.com and help us improve this plugin!");
					}
				}).fail(function(jqxhr, textStatus, error) {
						//redirect to error out
						var ajax_error_url = "<?php echo $plugin->admin_url('admin.php').'?page='.LIBSYN_DIR.'/admin/post.php&error=true&msg='; ?>" + ajax_error_message;
						if (typeof window.top.location.href == "string") window.top.location.href = ajax_error_url;
								else if(typeof document.location.href == "string") document.location.href = ajax_error_url;
									else if(typeof window.location.href == "string") window.location.href = ajax_error_url;
										else alert("Unknown javascript error 1029.  Please report this error to support@libsyn.com and help us improve this plugin!");
				});
				$("#libsyn_toggle_show_feed_importer").click( function() {
					$("#libsyn-feed-import-tr").toggle('fast');
				});
				$("#libsyn_import_feed_rss").click( function() {		
					//check if input fields are valid
					if($('#libsyn-import-feed-url').valid()) {
						if($('#libsyn-import-feed-url').val() !== "" && $('#libsyn-import-feed-url').prop("validity").valid) {
							//handle submission & dialog
							$( "#accept-import-dialog" ).dialog({
								autoOpen: false,
								draggable: false,
								height: 'auto',
								width: 'auto',
								modal: true,
								resizable: false,
								open: function(event,ui){
									setOverlays();
									$('.ui-widget-overlay').bind('click',function(){
										$('#accept-import-dialog').dialog('close');
									});
								},
								buttons: [
									{
										id: "import-posts-dialog-button-confirm",
										text: "Proceed with Import",
										click: function(){
											// $('#<?php echo LIBSYN_NS . 'form'; ?>').append('<input type="hidden" name="clear-settings-data" value="<?php echo time(); ?>" />');
											$("#libsyn-powerpress-feed-url").val("false");
											$('#accept-import-dialog').dialog('close');
											$("#<?php echo LIBSYN_NS . "form" ?>").submit();								
										}
									},
									{
										id: "import-posts-dialog-button-cancel",
										text: "Cancel",
										click: function(){
											$('#accept-import-dialog').dialog('close');
										}
									}
								]
							});
							$("#accept-import-dialog").dialog( "open" );
						} else {
							if(!$('#libsyn-import-feed-url').prop("validity").valid){
								$('#libsyn-import-feed-url').nextAll().remove().after('<label id="libsyn-import-feed-url-error" class="error" for="libsyn-import-feed-url">Feed URL not valid.</label>');
							} else if($('#libsyn-import-feed-url').val() == "") {
								$('#libsyn-import-feed-url').nextAll().remove().after('<label id="libsyn-import-feed-url-error" class="error" for="libsyn-import-feed-url">You must enter a Feed Import URL.</label>');
							}
						}
					}
				});
				$("#libsyn_import_powerpress_feed").click( function() {
					//handle submission & dialog
					$( "#accept-import-dialog" ).dialog({
						autoOpen: false,
						draggable: false,
						height: 'auto',
						width: 'auto',
						modal: true,
						resizable: false,
						open: function(event,ui){
							setOverlays();
							$('.ui-widget-overlay').bind('click',function(){
								$('#accept-import-dialog').dialog('close');
							});
						},
						buttons: [
							{
								id: "import-posts-dialog-button-confirm",
								text: "Proceed with Import",
								click: function(){
									// $('#<?php echo LIBSYN_NS . 'form'; ?>').append('<input type="hidden" name="clear-settings-data" value="<?php echo time(); ?>" />');
									$("#libsyn-powerpress-feed-url").val("true");
									$('#accept-import-dialog').dialog('close');
									$("#<?php echo LIBSYN_NS . "form" ?>").submit();								
								}
							},
							{
								id: "import-posts-dialog-button-cancel",
								text: "Cancel",
								click: function(){
									$('#accept-import-dialog').dialog('close');
								}
							}
						]
					});
					$("#accept-import-dialog").dialog( "open" );
				});
				$("#libsyn_add_player_to_posts").click( function() {
					<?php if( $checkPowerpress ) { ?>
					$('#import-libsyn-player-dialog').children('#extra-text').empty().append("<span style=\"color:green;font-weight:600;\">NOTE:</span>  You may uninstall the Powerpress Plugin after this to avoid duplicate players appearing in your posts.");
					<?php } ?>
					//handle submission & dialog
					$( "#import-libsyn-player-dialog" ).dialog({
						autoOpen: false,
						draggable: false,
						height: 'auto',
						width: 'auto',
						modal: true,
						resizable: false,
						open: function(event,ui){
							setOverlays();
							$('.ui-widget-overlay').bind('click',function(){
								$('#import-libsyn-player-dialog').dialog('close');
							});
						},
						buttons: [
							{
								id: "import-posts-dialog-button-confirm",
								text: "Add Libsyn Player",
								click: function(){
									// $('#<?php echo LIBSYN_NS . 'form'; ?>').append('<input type="hidden" name="clear-settings-data" value="<?php echo time(); ?>" />');
									$("#libsyn-importer-action").val("add_player_to_posts");
									$('#import-libsyn-player-dialog').dialog('close');
									$("#<?php echo LIBSYN_NS . "form" ?>").submit();								
								}
							},
							{
								id: "import-posts-dialog-button-cancel",
								text: "Cancel",
								click: function(){
									$('#import-libsyn-player-dialog').dialog('close');
								}
							}
						]
					});
					$("#import-libsyn-player-dialog").dialog( "open" );
				});
				$(".libsyn_clear_imports").each(function() {
					$(this).click( function() {
						//handle submission & dialog
						$( "#clear-settings-dialog" ).dialog({
							autoOpen: false,
							draggable: false,
							height: 'auto',
							width: 'auto',
							modal: true,
							resizable: false,
							open: function(event,ui){
								setOverlays();
								$('.ui-widget-overlay').bind('click',function(){
									$('#clear-settings-dialog').dialog('close');
								});
							},
							buttons: [
								{
									id: "clear-settings-dialog-button-confirm",
									text: "Clear Imports",
									click: function(){
										// $('#<?php echo LIBSYN_NS . 'form'; ?>').append('<input type="hidden" name="clear-settings-data" value="<?php echo time(); ?>" />');
										$("#libsyn-importer-action").val("clear_imports");
										$('#clear-settings-dialog').dialog('close');
										$("#<?php echo LIBSYN_NS . "form" ?>").submit();								
									}
								},
								{
									id: "clear-settings-dialog-button-cancel",
									text: "Cancel",
									click: function(){
										$('#clear-settings-dialog').dialog('close');
									}
								}
							]
						});	
						$("#clear-settings-dialog").dialog( "open" );
					});
					$('#libsyn-import-feed-url').focus(function() {
						$('#libsyn-import-feed-url').siblings('.helper-text').empty();
					});
				});
			});
		})(jQuery);
	</script>
	<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				// function validateFeedUrl () {
					// $( "#<?php echo LIBSYN_NS . "form" ?>" ).validate({
						// rules: {
							// field: {
								// required: true,
								// url: true
							// }
						// }
					// });
				// }
			});
		})(jQuery);
	</script>