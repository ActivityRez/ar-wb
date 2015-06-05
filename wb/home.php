<?php global $wb,$wbSlim,$testMode,$wp_query; 
	
	$destinations = array();
	foreach($wb['featured_activities'] as $act){
		$destinations[$act['destination']][] = $act;
	}
	//echo "<!--<pre>".print_r($wb,1)."</pre>-->";
?>

<div id="webbooker-home" data-bind="if: Homepage.show">
	<div id="home-banner">
		<ul class="bxslider">
			<?php $banner_images = 3;
				for($i=0;$i<$banner_images;$i++){
				      if(get_theme_mod('banner_image_'.$i)){ ?>
						<li>
							<div class="container">
								<div class="banner-title"><?php echo get_theme_mod('banner_title_'.$i); ?></div>
								<div class="banner-desc"><?php echo get_theme_mod('banner_desc_'.$i); ?></div>
							</div>
							<a class="banner-link" href="<?php echo get_theme_mod('banner_link_'.$i); ?>"></a>
							<!-- <img src="<?php echo get_theme_mod('banner_image_'.$i); ?>" alt="<?php echo get_theme_mod('banner_title_'.$i); ?>" /> -->
							<div class="banner-image" style="background-image: url(<?php echo get_theme_mod('banner_image_'.$i); ?>)"></div>
						</li>
			<?php }}?>
		</ul>
		<div class="container search_wrap">
			<form id="home-main-search" class="clearfix" data-bind="with: WebBooker.Catalog">
				<select id="home-destination" data-bind="options: search_filter_data.destinations, value: search_params.destination, optionsCaption: __('<?php echo __('Choose a Destination','arez') . '...'; ?>'), optionsText: '__name'"></select>
				<select id="home-category" data-bind="options: search_filter_data.categories, value: search_params.category, optionsCaption: __('<?php echo __('Choose a Type','arez') . '...'; ?>'), optionsText: '__name'"></select>
				<span class="cal-holder">
					<input id="home-date-start" placeholder="Start Date" type="text" readonly="true" data-bind="value: search_params.date_start" />
				</span>
				<span class="cal-holder">
					<input id="home-date-end" placeholder="End Date" type="text" readonly="true" data-bind="value: search_params.date_end" />
				</span>
				<button data-bind="click: loadWithFilters">Go!</button>
			</form>
		</div>
	</div>
	<div id="quick-tags">
		<div class="container list-wrap">
			<ul class="clearfix">
				<?php foreach($wb['tags'] as $tag) { 
					$slug = $wb['wb_url'].'/tag/'.$tag['slug'];
				?>
				<li><a href="<?php echo $slug;?>"><?php echo $tag['name'];?></a></li>
				<?php }; ?>
			</ul>
			
		</div>
		<div class="container list-nav">
			<div class="list-nav-left">&#8592;</div>
			<div class="list-nav-right">&#8594;</div>
		</div>
	</div>
	<div id="home-main" class="container clearfix">
		<div id="featured-activities" data-bind="foreach: WebBooker.Homepage.featured_destinations">
			<div class="featured-destination" data-bind="visible: activities().length > 0">
				<h2><a data-bind="text: __(destination)(), attr: { 'href': WebBooker.bootstrap.wb_url + '/destination/' + escape(destination) }"></a></h2>
				<ul class="featured-items clearfix" data-bind="foreach: activities">
					<li>
						<!-- ko if: thumbnail_url-->
						<a class="featLink" data-bind="attr: { 'href': url, 'style': 'background-image: url(' + thumbnail_url() + ')'}"></a>
						<!-- /ko -->
						<!-- ko if: !thumbnail_url()-->
						<a class="featLink" data-bind="attr: { 'href': url }"></a>
						<!-- /ko -->
						<div class="feat-item-title" data-bind="html: title"></div>
						<div class="feat-item-desc" ></div>
					</li>
				</ul>
			</div>
			<!--<?php foreach($destinations as $dest=>$acts){ ?>
			<div class="featured-destination">
				<h2><a href="<?php echo $wb['wb_url']; ?>/destination/<?php echo $dest;?>"><?php echo __($dest,'arez');?></a></h2>
				<ul class="featured-items clearfix">
					<?php
					foreach($acts as $activity){
						$slug = $wb['wb_url'].'/'.$activity['slug'];
					?>	<li>
							<?php if($activity['json_input']['media']) {?>
							<a class="featLink" href="<?php echo $slug;?>" style="background-image: url(//media.activityrez.com/media/<?php echo $activity['json_input']['media'][0]['hash']; ?>/thumbnail/width/336);"></a>
							<?php } else{?>
							<a class="featLink" href="<?php echo $slug;?>"></a>
							<?php } ?>
							<div class="feat-item-title"><?php echo $activity['title'];?></div>
							<div class="feat-item-desc"><?php echo $activity['shortDesc'];?><a href="<?php echo $slug;?>">More&hellip;</a></div>
						</li>
					<?php } ?>
				</ul>
			</div>
		<?php
	    }
	    ?> -->
	    
		</div>
		<div id="sidebar">
			<h3>Types of Activities</h3>
			<ul>
				<?php foreach($wb['moods'] as $tag) { 
					$slug = $wb['wb_url'].'/mood/'.$tag['slug'];
				?>
				<li><a href="<?php echo $slug;?>"><?php echo $tag['name'];?></a></li>
				<?php }; ?>
			</ul>
		</div>
	</div>
	
	<?php if(get_theme_mod('social_proof_quote')){ ?>
	<div id="social-proof">
		<div class="container clearfix">
			<div class="image-wrap">
				<img src="<?php echo get_theme_mod('social_proof_image'); ?>" alt="<?php echo get_theme_mod('social_proof_person'); ?>" />
			</div>
			<div class="quote"><?php echo get_theme_mod('social_proof_quote');?></div>
			<div class="person"><?php echo get_theme_mod('social_proof_person');?></div>
		</div>	
	</div>
	<?php }?>
	
	<div id="user-content" class="container" role="main">
		<?php echo (!empty($wb['hero_summary_text']))?$wb['hero_summary_text']:''; ?>
	</div><!-- #primary -->
	
</div><!-- /webbooker-home -->
