<?php global $wb; ?>
		<div id="webbooker-sidebar" class="span4">
		<?php
			if ( !dynamic_sidebar('webbooker-sidebar') ){
		?>	
		<div id="search-filters" class="sidebar-container" data-bind="with: WebBooker.Catalog">
			<div class="header gradient-light">
				<h3><?php _e('Search Activities','arez'); ?></h3>
			</div>
			<div class="cb"></div>
			<div class="ribbonFold"></div>
	
			<div class="content">
				<form id="search-activities-form">
					<div id="search-filters-locations" class="box-content collapsible">
						<h4 title="<?php _e('Hide','arez');?>" data-bind="collapseSidebarBox: true"><i class="icon-chevron-up"></i> <?php _e('Location','arez'); ?></h4>
						<div class="collapse-me">
							<select data-bind="options: search_filter_data.destinations, value: search_params.destination, optionsCaption: __('<?php echo __('Choose a Destination','arez') . '...'; ?>'), optionsText: '__name'"></select>
						</div>
					</div><!-- /search-filters-locations -->
	
					<div id="search-filters-categories" class="box-content collapsible" data-bind="visible: search_filter_data.categories().length || search_filter_data.tags().length">
						<h4 title="<?php _e('Show','arez');?>" data-bind="collapseSidebarBox: true"><i class="icon-chevron-down"></i> <?php _e('Categories and Tags','arez'); ?></h4>
						<div class="collapse-me" style="display: none;">
							<select class="select-category" data-bind="visible: search_filter_data.categories().length, options: search_filter_data.categories, value: search_params.category, optionsCaption: '<?php echo __('Choose a category','arez').'...'; ?>', optionsText: '__name', optionsValue: 'name'"></select>
							<select class="select-tag" data-bind="visible: search_filter_data.tags().length, options: search_filter_data.tags, value: search_params.tag, optionsCaption: '<?php echo __('Choose a tag','arez') . '...'; ?>', optionsText: '__name', optionsValue: 'name'"></select>
						</div>
					</div><!-- /search-filters-categories -->
	
					<div id="search-filters-moods" class="box-content collapsible" data-bind="visible: search_filter_data.moods().length">
						<h4 title="<?php _e('Show','arez');?>" data-bind="collapseSidebarBox: true"><i class="icon-chevron-down"></i> <?php _e('Moods','arez'); ?></h4>
						<div class="collapse-me" style="display: none;">
							<ul class="moods clearfix" data-bind="foreach: search_filter_data.moods">
								<li><input type="checkbox" name="search-mood" data-bind="checked: selected" /> <span data-bind="text: __name"></span></li>
							</ul>
						</div>
					</div><!-- /search-filters-moods -->
	
					<div id="search-filters-date" class="box-content collapsible">
						<h4 title="<?php _e('Show','arez');?>" data-bind="collapseSidebarBox: true"><i class="icon-chevron-down"></i> <?php _e('Date','arez'); ?></h4>
						<div class="collapse-me" style="display: none;">
							<div class="pull-left firstDate">
								<label for="datepicker-second"><?php _e('From','arez'); ?></label>
								<input type="text" readonly="true" class="datepicker input-small" name="datepicker-first" id="datepicker-first" data-bind="value: search_params.date_start" />
							</div>
							<div class="pull-left">
								<label for="datepicker-second"><?php _e('To','arez'); ?></label>
								<input type="text" readonly="true" class="datepicker input-small" name="datepicker-second" id="datepicker-second" data-bind="value: search_params.date_end" />
							</div>
							<div style="clear:both"></div>
						</div>
					</div><!-- /search-filters-date -->
	
					<div id="search-filters-keywords" class="box-content collapsible">
						<h4 title="<?php _e('Hide','arez');?>" data-bind="collapseSidebarBox: true"><i class="icon-chevron-up"></i> <?php _e('Keywords','arez'); ?></h4>
						<div class="collapse-me">
							<input type="text" name="keywords" id="search-keywords" data-bind="value: search_params.keywords, valueUpdate: 'afterkeydown'" />
						</div>
					</div><!-- /search-filters-keywords -->
					<div class="actions">
						<button type="reset" data-bind="click: clearFilters" class="buttonGray buttonBig"><i class="icon-refresh icon-white"></i> <?php _e('Reset Filters','arez'); ?></button>
						<button type="submit" data-bind="click: loadWithFilters, enable: !isSearching()" title="<?php _e('Search Activities','arez'); ?>" id="searchActivitiesButton" class="buttonBlue buttonBig"><i class="icon-search icon-white"></i> <?php _e('Search','arez'); ?></button>
					</div>
				</form>
				<div style="clear:both"></div>
				<p style="display:block !important;visibility:visible !important;" class="powered">Powered by <a style="display:inline-block !important;visibility:visible !important;" class="arezLogo" href="https://www.activityrez.com/?utm_source=booking+engine&utm_medium=referral&utm_campaign=powered+by" target="_blank">ActivityRez.com</a></p>
			</div><!-- /content -->
		</div><!-- /search-filters -->

		<div id="cart-sidebar" class="sidebar-container" data-bind="with: WebBooker.Cart">
			<div class="header gradient-light">
				<h3><?php _e('My Itinerary','arez'); ?></h3>
			</div>
			<a href="<?php echo (isset($wb['wb_url'])) ? $wb['wb_url'] : ''; ?>/#/Itinerary" title="<?php _e('Retrieve an itinerary from a previous sale.','arez'); ?>" class="buttonBlue retrieve"><i class="icon-briefcase icon-white"></i> <?php _e('Retrieve An Itinerary','arez'); ?></a>
			<div class="cb"></div>
			<div class="ribbonFold"></div>
			<div class="content" style="display: none" data-bind="visible: WebBooker.wbLoaded">
				<div class="empty" data-bind="visible: WebBooker.Cart.items().length < 1">
					<?php _e('You haven\'t added any activities yet.','arez'); ?>
				</div>

				<div data-bind="visible: items().length > 0, foreach: items" style="display:none">
				<div class="activity">
					<button class="buttonGray" title="Remove Activity" data-bind="click: remove"><i class="icon-remove icon-white"></i> <?php _e('Remove','arez'); ?></button>
					<h4><a data-bind="attr: { 'href': url }, html: title"></a></h4>
					<div class="info">
						<strong><?php _e('Location','arez'); ?>:</strong> <span data-bind="text: __(destination)()"></span><br>
						<strong><?php _e('Date','arez'); ?>:</strong> <span data-bind="text: i18n_date()"></span><br>
						<strong><?php _e('Time','arez'); ?>:</strong> <span data-bind="text: time"></span>
					</div>
					<ul class="guests" data-bind="foreach: guests">
						<!-- ko if: qty() > 0 -->
						<li>
							<strong><span data-bind="text: qty"></span> <span data-bind="text: name"></span>:</strong>
							<span data-bind="money: subtotal"></span>
						</li>
						<!-- /ko -->
					</ul>
				</div><!-- /activity -->
				</div><!-- /foreach items -->

				<div class="cart-total clearfix" data-bind="visible: items().length > 1">
					<span><strong><?php _e('Ticket Total','arez'); ?>:</strong></span>
					<span class="price" data-bind="money: subtotal"></span>
				</div>

				<div class="actions">
					<a href="<?php echo (isset($wb['wb_url'])) ? $wb['wb_url'] : ''; ?>/#/Search" title="Search for more activities" class="buttonBlue" data-bind="visible: WebBooker.CheckoutNav.show"><i class="icon-share-alt icon-white"></i> <?php _e('Return to Activities','arez'); ?></a>
					<button title="<?php _e('Customize itinerary and check out','arez'); ?>" class="buttonBlue" data-bind="enable: items().length > 0, click: viewCart, visible: !WebBooker.CheckoutNav.show(), scrollTopOnClick: true"><i class="icon-shopping-cart icon-white"></i> <?php _e('Customize and Check Out','arez'); ?></button>
				</div>
				<div style="clear:both"></div>
			</div><!-- /content -->
		</div><!-- /cart-sidebar -->

		<?php
		if(!$reseller_passed) {
		?>
		<div id="agents-sidebar" class="sidebar-container" data-bind="with: WebBooker.Agent">
			<div class="header gradient-light">
				<h3><?php _e('Travel Agents','arez'); ?></h3>
			</div>
			<div class="cb"></div>
			<div class="ribbonFold"></div>

			<div class="content">
				<div data-bind="visible: user_id() > 0">
					<p><strong><?php _e('Welcome back,','arez'); ?> <span class="agent-name" data-bind="text: name"></span>!</strong></p>
					<div class="actions">
						<a href="<?php echo $wb['wb_url']; ?>/#/Dashboard" class="buttonBlue" data-bind="scrollTopOnClick: true"><i class="icon-list-alt icon-white"></i> <?php _e('Dashboard','arez'); ?></a>
						<button class="buttonGray" data-bind="click: logout"><i class="icon-ban-circle icon-white"></i> <?php _e('Log Out','arez'); ?></button>
					</div>
					<div style="clear:both"></div>
				</div>
				<div data-bind="visible: !user_id() || user_id() == 0">
					<p><strong><?php _e('Travel Agent Log In','arez'); ?></strong></p>
					<p class="login-error"></p>
					<div class="login-form">
					<form>
						<div class="alert alert-success" data-bind="text: loginSuccess, visible: loginSuccess"></div>
						<input type="text" title="<?php _e('Username','arez'); ?>" placeholder="<?php _e('Username','arez'); ?>" autocorrect="off" autocapitalize="off" data-bind="value: email" />
						<input type="password" autocomplete="off" title="<?php _e('Password','arez'); ?>" placeholder="<?php _e('Password','arez'); ?>" data-bind="value: password" />
						<a class="lostPass" href="#/PasswordResetRequest"><?php _e("Forgot Password?",'arez');?></a>
						<div style="clear:both"></div>
						<button type="submit" class="buttonBlue" data-bind="click: login"><i class="icon-lock icon-white" data-bind="css: {'icon-processing': WebBooker.Agent.isLoggingIn}"></i> <?php _e('Log In','arez'); ?></button>
						<button class="buttonGray" data-bind="click: doShowSignup, scrollTopOnClick: true"><i class="icon-pencil icon-white"></i> <?php _e('Sign Up','arez'); ?></button>
						<div class="alert alert-error" data-bind="text: loginError, visible: loginError"></div>
					</form>
					</div><br>
					<p><strong><?php _e('Sign Up','arez'); ?></strong></p><p><?php _e('Take advantage of our proprietary online booking technology and earn commissions.','arez'); ?> <!-- <a href="/booking/travel-agents/"><?php _e('Learn more.','arez'); ?></a> --></p>
				</div>
			</div><!-- /content -->
		</div><!-- agents-sidebar -->
<?php
		}
	}
?>
</div><!-- /webbooker-sidebar -->
