<?php

/**
 *	ActivityRez Web Booking Engine
 *	Search PHP File
 *
 *	@author Ryan Freeman <ryan@stoked-industries.com>
 *	@package ActivityRez
 *	@subpackage Web Booking Engine
 */
global $wb;
?>

<div id="webbooker-search" data-bind="if: WebBooker.Catalog.show">
	<div class="container" data-bind="with: WebBooker.Catalog">
		<div id="search-header" class="clearfix">
			<h2><?php _e('Search Results','arez'); ?></h2>
			<select data-bind="options: search_filter_data.sorts, value: search_params.sort, optionsText: function(item){ return item.label; }"></select>
		</div>
		<!-- <span data-bind="text: totalResultsText"></span> -->
				       
	
		<div id="webbooker-search-results">
			<div class="loading" data-bind="visible: WebBooker.Catalog.isSearching && !WebBooker.Catalog.hasSearched()" style="display: none;">
				<?php _e('Loading activities','arez');?>&hellip;
			</div>
			<div id="no-results" data-bind="visible: WebBooker.Catalog.searchResults().length == 0 && !isSearching() && WebBooker.Catalog.pageIndex() >= 1"><?php _e('Your activity search returned no results.','arez'); ?></div>
			<div class="results" data-bind="foreach: searchResults, visible: WebBooker.Catalog.searchResults().length > 0">
				<div class="activity clearfix" data-bind="click: link">
					<div class="activity-thumbnail" data-bind="visible: thumbnail_url">
						<img data-bind="attr: { 'src': thumbnail_url }" alt="<?php _e('Activity Thumbnail','arez'); ?>" />
					</div>
					<div class="thumbnail-holder" data-bind="visible: !thumbnail_url()"></div>
					<div class="activity-info">
						<div class="activity-title">
							<span class="name" data-bind="html: title"></span>
							<!-- ko if: destination --><span class="destination" data-bind="text: __(destination)"></span> <!-- /ko -->
							<!-- ko if: destination && duration -->|<!-- /ko -->
							<!-- ko if: duration --><span class="duration" data-bind="text: __(duration)"></span><!-- /ko -->
						</div>
						<div class="activity-description" data-bind="text: shortDesc"></div>
						<!-- ko if: WebBooker.Agent.user_id() > 0 -->
						<div class="agents-info">
							<div class="for-agents"><?php _e('For Agents','arez'); ?></div>
							<div class="show-hide">
								<div>
									<?php _e('Root Activity ID','arez'); ?>: #<span data-bind="text: activityID"></span>
								</div>
								<div class="commissions"  data-bind="if: WebBooker.hasReseller && r2 > 0">
									 | <?php _e('Commission Rate','arez'); ?>: <span data-bind="text: r2 + '%'"></span>
								</div>
							</div>
						</div>
						<!-- /ko -->
					</div>
					<div class="activity-price">
						<!-- ko if: display_price -->
						<div class="starting"><?php _e('Prices start at','arez'); ?></div>
						<div class="amount" data-bind="money: display_price"></div>
						<div class="vary">(<?php _e('Prices may vary','arez'); ?>)</div>
						<a data-bind="attr: { 'href': url }" title="<?php _e('View Activity Details','arez'); ?>"><?php _e('Details','arez'); ?></a>
						<!-- /ko -->
						<!-- ko ifnot: display_price -->
						<a data-bind="attr: { 'href': url }" title="<?php _e('View Activity Details','arez'); ?>"><?php _e('Click to see Prices','arez'); ?></a>
						<!-- /ko -->
					</div>
				</div><!-- /activity -->
			</div><!-- /results -->
		</div><!-- /webbooker-search-results -->
		
		<div id="webbooker-search-footer" class="container" data-bind="visible: parseInt(WebBooker.Catalog.totalResults(),10) > WebBooker.Catalog.searchResults().length">
			<div class="loading" data-bind="visible: isSearching()">
				<?php _e('Loading',"arez"); ?>&hellip;
			</div>
			<div data-bind="visible: !isSearching(), click: function(){ WebBooker.Catalog.pageIndex(WebBooker.Catalog.pageIndex() + 1); }">
				<button><span><?php _e('Load More','arez'); ?> (<span data-bind="text:parseInt(WebBooker.Catalog.totalResults(),10) - WebBooker.Catalog.searchResults().length"></span>)</span></button>
			</div>
		</div><!-- /webbooker-search-footer -->
	</div>
</div><!-- /webbooker-search -->
