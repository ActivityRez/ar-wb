<?php
/**
 *	ActivityRez Web Booking Engine
 *	Activity PHP File
 *
 *	@author Ryan Freeman <ryan@stoked-industries.com>
 *	@package ActivityRez
 *	@subpackage Web Booking Engine
 */

global $wp_query, $wb, $query, $currency, $currencySymbol, $testMode, $availableLangs, $wbSlim, $langPath, $wbArgs;
function getChildDisplayPrice( $child ){
	if(isset($child['display_price']) && !empty($child['display_price']) ){
		return __("Prices starting at ",'arez').currencyFormat($child['display_price']);
	}
	
	if(isset($child['prices']) && is_array( $child['prices']) && count($child['prices'])){
		$low = null;
		foreach( $child['prices'] as $price ){
			//empty price continue 
			if(!isset($price['amount']) || $price['amount'] < 1 ) continue;
			if(isset($price['guest_type']) && preg_match('/adult/i',$price['guest_type']) > 0 )
				return currencyFormat($price['amount']);
			if(!$low || $price['amount'] < $low)
				$low = $price['amount'];
		}
		return currencyFormat($low);
	}
	
	return __("Click for prices",'arez');
}

function currencyFormat( $amount ){
	global $wb;
	
	//for now just hard code this
	return 'US$'.number_format($amount);
}

function getChildURL( $child ){
	global $wb;
	return $wb['wb_url'] .'/'. $child['slug'];
}
$activityDefault = array(
	'title'=>'',
	'activityID'=>'',
	'duration'=>'',
	'description'=>'',
	'destination'=>'',
	'children'=>'',
	'instructions'=>'',
	'address'=>''
);
if(!isset($wb['activity'])){
	$wb['activity'] = array();
}
$wb['activity'] = array_merge($activityDefault,$wb['activity']); ?>

<!--[if IE]><script type="text/javascript">window['isIE'] = true;</script><![endif]-->
<div id="webbooker-activity" data-bind="if: ActivityView.activity(), visible: ActivityView.show()" style="display:none">
	<div id="activity-header" class="container">
		<h1><span class="activity-title"><?php echo $wb['activity']['title'];?></span> <span class="activity-destination"><?php echo $wb['activity']['destination'];?></span></h1>
		<?php if ($wb['activity']['duration'] && $wb['activity']['duration'] != '0'){ ?>
		<div class="activity-duration"><?php _e('Duration','arez'); ?>: <span><?php echo $wb['activity']['duration'];?></span></div>
		<?php } ?>
	</div>
	
	<?php if($wb['activity']['media']){ ?>
	<div id="webbooker-activity-media">
		<div class="container gallery-wrap">
			<ul clas="clearfix">
				<?php foreach($wb['activity']['media'] as $image){ ?>
				<li>
					<img class="activity-image" src="//media.activityrez.com/media/<?php echo $image['hash']; ?>/thumbnail/width/308" alt="Slideshow Image" />
				</li>
				<?php } ?>
			</ul>
		</div>
		<?php if(count($wb['activity']['media']) > 1){ ?>
		<div class="container list-nav">
			<div class="list-nav-left">&#8592;</div>
			<div class="list-nav-right">&#8594;</div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>
	<div class="container clearfix">
		<?php if($wb['activity']['display_price']){ ?>
		<div id="starting-price" class="burst-12">
			<?php _e('Prices Start at','arez'); ?>
			<div>$<span><?php echo $wb['activity']['display_price']; ?></span></div>
		</div>
		<?php } ?>
		<?php
			if( isset($wb['activity']['children']) && is_array($wb['activity']['children']) && count($wb['activity']['children'])){
				foreach($wb['activity']['children'] as $child){
					$display_price = getChildDisplayPrice($child);
					$url = getChildURL($child);
					$poo = $child['media'][0]['hash'];
					
					foreach($child['media'] as $image){
						if($image['featured']){
							$poo = $image['hash'];
						}
					}
					?>
					
					<div class="child-activity clearfix">
						<?php if($poo){ ?>
						<div class="activity-thumbnail">
							<img src="//media.activityrez.com/media/<?php echo $poo; ?>/thumbnail/height/200" />
						</div>
						<?php } else { ?>
						<div class="thumbnail-holder"></div>
						<?php } ?>
						<div class="activity-info">
							<div class="activity-title">
								<span class="name"><?php echo $child['title'];?></span>
								<span class="destination"><?php echo $child['destination'];?></span>
								<?php if($child['destination'] && $child['duration'] && $child['duration'] != 0) echo '|'; ?>
								<span class="duration"><?php if($child['duration'] && $child['duration'] != 0) echo $child['duration'];?></span>
							</div>
							<div class="activity-description"><?php if ( isset($child['shortDesc']) ) { echo $child['shortDesc']; } ?></div>
						</div>
						<div class="activity-price">
							<div class="starting"><?php _e('Prices start at','arez'); ?></div>
							<div class="amount"><?php echo $display_price;?></div>
							<div class="vary">(<?php _e('Prices may vary','arez'); ?>)</div>
							<a href="<?php echo $url; ?>" title="<?php _e('View Activity Details','arez'); ?>"><?php _e('Details','arez'); ?></a>
						</div>
					</div><!-- /.child-activity -->
					<?php
				}
			} else {
		?>
			<div class="steps">
				<div class="step"><span>1.</span> Select a date</div>
				<div class="step"><span>2.</span> Select a time</div>
				<div class="step"><span>3.</span> Select your guests</div>
				<div class="step"><span>4.</span> Click Check Out</div>
			</div>
			<div id="activity-guest-add" class="clearfix" data-bind="with: WebBooker.MiniCart">
				<div id="activity-datepicker"></div>
				<div id="activity-time-guest">
					<select class="time-select" data-bind="options: times, optionsCaption: '<?php _e('Select time','arez'); ?>', optionsText: 'startTime', value: time, enable: date"></select>
					<div id="activity-cutoff" class="section" data-bind="visible: !canBook()">
						<p><?php _e('This activity is currently not available for booking at the selected date and time.', 'arez'); ?></p>
						<!-- ko if: activity --><p data-bind="visible: activity().reseller_csPhone.length > 0"><?php _e('Please choose another date and time or contact an agent at: ', 'arez'); ?><span data-bind="text: activity().reseller_csPhone"></span></p><!-- /ko -->
					</div>
					<div id="activity-noprices" class="section" data-bind="visible: !guests().length">
						<p><?php _e('No pricing information was found for this activity.','arez');?></p>
					</div><!-- /activity-noprices -->
					<div id="activity-prices" data-bind="foreach: guests">
						<div class="guest-add clearfix">
							<div class="guest-name">
								<div class="name" data-bind="text: name"></div>
								<div class="price" data-bind="money: price"></div>
							</div>
							<select data-bind="value: qty">
								<option>0</option>
								<option>1</option>
								<option>2</option>
								<option>3</option>
								<option>4</option>
								<option>5</option>
								<option>6</option>
								<option>7</option>
								<option>8</option>
								<option>9</option>
							</select>
						</div>
					</div>
				</div>
				<div id="activity-checkout">
					<div id="activity-cart">
						<div class="subtotal"><?php _e('Subtotal','arez'); ?> 
							<!-- ko if: cartItem() -->
							<span id="activity-ticketsleft" class="section" data-bind="visible: ticketsLeft, css: { 'notickets': ticketsLeft() === 'No more tickets left!', 'hastickets': /only/gi.test( ticketsLeft() ) }">
								<span data-bind="text: ticketsLeft"></span>
							</span><!-- /activity-ticketsleft -->
							<span class="amount" data-bind="money: cartItem().subtotal"></span>
							<!-- /ko -->
							<!-- ko if: !cartItem() -->
							<span class="amount" data-bind="money: 0"></span>
							<!-- /ko -->
						</div>
						<button data-bind="enable: canCheckout, click: checkout"><?php _e('Check Out','arez'); ?></button>
						<a data-bind="attr: { href: WebBooker.searchUrl }, click: WebBooker.ActivityView.analyticsContinueShopping"><?php _e('Back to Search','arez'); ?></a>
					</div>
					<?php if($wb['activity']['instructions']) { ?>
					<div class="special-instructions"><?php echo $wb['activity']['instructions'];?></div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
		
		<div id="webbooker-activity-main">
			<div id="webbooker-activity-description">
				<div class="description-head"><?php _e('Description','arez'); ?></div>
				<div class="description-body"><?php echo $wb['activity']['description']; ?></div>
			</div>
			<div id="webbooker-activity-sidebar" class="clearfix">
				<div id="webbooker-activity-map" data-bind="visible: WebBooker.ActivityView.activity().address_lat">
					<div><?php $wb['activity']['address'] ?></div>
					<div id="map_canvas" style="height: 260px"></div>
				</div><!-- /webbooker-activity-map -->
				<?php if(is_active_sidebar( 'activity-widget-area' )){ ?> 
				<div id="activity-widget-area">
					<?php dynamic_sidebar( 'activity-widget-area' ); ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div><!-- /webbooker-activity -->

<div id="qrcode-wrapper" style="display:none;">
	<div id="qrcode"></div>
	<p><?php _e("Use your smartphone's QR reader to visit this activity page.",'arez');?></p>
</div>