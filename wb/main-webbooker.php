<?php
/**
 * Bootstrapper for custom Web Bookers.
 *
 * @package ActivityRez
 * @subpackage Web Booking Engine
 * @author Ryan Freeman <ryan@stoked-industries.com>
 */
global $wb,$wbArgs;
?>
<div id="webbooker">
	<div id="webbooker-main">
		<?php
			$extend = true;
			if( isset( $_REQUEST['activityID'] ) ) {
				arez_include('activity.php');
				$extend = false;
			} else if ( isset( $_REQUEST['displayItinerary'] ) ) {
				arez_include('itinerary.php');
			} else if ( isset($wbArgs['destination'])){
				arez_include('destinations-list.php');
				$extend = false;				
			} else if ( isset($wbArgs['tag'])){
				arez_include('taxonomy-list.php');
				$extend = false;				
			} else if ( isset($wbArgs['mood'])){
				arez_include('taxonomy-list.php');
				$extend = false;				
			} else if ( isset($wbArgs['category'])){
				arez_include('taxonomy-list.php');
				$extend = false;				
			}
			
			if( $extend == true ) {
				if ( bot_detected() ) {
					arez_include( 'search-static.php' );
				} else {
					arez_include( 'home.php' );
					arez_include( 'password-reset.php' );
					arez_include( 'search.php' );
					arez_include( 'dashboard.php' );
					arez_include( 'checkout.php' );
					arez_include( 'confirmation.php' );
					arez_include( 'itinerary.php' );
					arez_include( 'contact.php' );
					arez_include( 'aboutus.php' );
					arez_include( 'activity.php' );
					arez_include( '404.php' );
				}
			}
		?>
		<!-- <div id="init-loader" data-bind="visible: WebBooker.showInitLoader">
			<img src="<?php echo PLUGIN_DIR_URL . 'images/ajax-loader.gif'; ?>" alt="<?php _e('Image Loader','arez'); ?>"><br><br>
			<?php _e('Loading...','arez'); ?>
		</div> -->
	</div><!-- /webbooker-main -->
</div><!-- /webbooker -->