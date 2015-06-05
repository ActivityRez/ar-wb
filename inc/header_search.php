<div id="header-mini-search">
	<form class="clearfix container" data-bind="with: WebBooker.Catalog">
		<select id="mini-destination" data-bind="options: search_filter_data.destinations, value: search_params.destination, optionsCaption: __('<?php echo __('Choose a Destination','arez') . '...'; ?>'), optionsText: '__name'"></select>
		<select id="mini-category" data-bind="options: search_filter_data.categories, value: search_params.category, optionsCaption: __('<?php echo __('Choose a Type','arez') . '...'; ?>'), optionsText: '__name'"></select>
		<span class="cal-holder">
			<input id="mini-date-start" placeholder="Start Date" type="text" readonly="true" data-bind="value: search_params.date_start" />
		</span>
		<span class="cal-holder">
			<input id="mini-date-end" placeholder="End Date" type="text" readonly="true" data-bind="value: search_params.date_end" />
		</span>
		<button type="submit" data-bind="click: loadWithFilters"><?php _e('Go'); ?>!</button>
		<div class="clear-form" data-bind="click: clearFilters">&#10006;</div>
	</form>
</div>