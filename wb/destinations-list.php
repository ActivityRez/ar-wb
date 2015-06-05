<?php
global $wp_query, $wb, $query, $currency, $currencySymbol, $testMode, $availableLangs, $wbSlim, $langPath, $wbArgs;

$prevUrl = $nextURL = false;
$count = (int)$wb['activities']['total'];
$remaining = 0;
if(!$wpArgs['page']){
	$wpArgs['page'] = 1;
}
if($count > 50){
	$offset = 1;
	if(isset($wpArgs['page']) && $wpArgs['page'] > 0){
		$offset = $wpArgs['page'];
	}
	$remaining = $count - (50 * $offset);
}

if(isset($wbArgs['page']) && $wbArgs['page'] > 1){
	$prevUrl = $wb['wb_url'].'/destination/'.$wbArgs['destination'].'/page/'.$wpArgs['page'];
}

if( $remaining > 0 ){
	$nextURL = $wb['wb_url'].'/destination/'.$wbArgs['destination'].'/page/'.($wpArgs['page'] + 1 );
}

?>

<div id="page-taxonomy" class="container">
	<h2><?php echo $wbArgs['destination'];?></h2>
	<div class="blurb"><?php echo __("Find great activities &amp; adventures for your dream vacation in ",'arez').__($wbArgs['destination'],'arez');?></div>
	<div id="taxonomy-results" class="clearfix categorySection" >
		<?php foreach($wb['activities']['data'] as $activity){
			$url = $wb['wb_url'].'/'.$activity['slug']; ?>
		<div class="activity clearfix" data-bind="click: function(){ window.location = '<?php echo $url;?>';}">
			<?php if($activity['json_input']['media']) { ?>
			<div class="activity-thumbnail">
					<img src="<?php echo featuredImage($activity['json_input']['media']);?>" alt="<?php _e("Activity Thumbnail Image URL",'arez');?>" />
			</div>
			<?php } else { ?>
			<div class="thumbnail-holder"></div>
			<?php } ?>
			<div class="activity-info">
				<div class="activity-title">
					<span class="name"><?php echo $activity['title'];?></span>
					<?php if($activity['json_input']['duration'] && $activity['json_input']['duration'] != '0'){ ?>
					<span class="duration"><?php echo $activity['json_input']['duration'];?></span>
					<?php } ?>
				</div>
				<div class="activity-description"><?php echo $activity['shortDesc'];?></div>
			</div>
			<div class="activity-price">
				<div class="starting"><?php _e('Prices start at','arez'); ?></div>
				<div class="amount">$<?php echo $activity['display_price']; ?></div>
				<div class="vary">(<?php _e('Prices may vary','arez'); ?>)</div>
				<a href="<?php echo $url;?>"><?php _e('Details','arez'); ?></a>
			</div>
		</div><!-- /activity -->
		<?php } ?>
	</div>
	<div class="clearfix navigation">
		<?php if($prevUrl){ ?>
		<a class="nav-previous" href="<?php echo $prevUrl;?>">&larr; Previous</a>
		<?php } ?>
		
		<?php if($nextURL){ ?>
		<a class="nav-next" href="<?php echo $nextURL;?>">Next &rarr;</a>		
		<?php } ?>
	</div>
</div>