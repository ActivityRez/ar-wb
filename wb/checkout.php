<?php

/**
 *	ActivityRez Web Booking Engine
 *	Checkout PHP File
 *
 *	@author Ryan Freeman <ryan@stoked-industries.com>
 *	@package ActivityRez
 *	@subpackage Web Booking Engine
 */
global $wb;
?>
<div id="webbooker-checkout" class="container clearfix" data-bind="visible: WebBooker.CheckoutNav.show, with: WebBooker.Checkout" style="display:none">
	<div id="checkout-header" class="clearfix">
		<h2><?php _e('Checkout','arez'); ?>
			<!-- ko if: WebBooker.CheckoutNav.showPayment() && sale.items().length -->
			<span><?php _e('Payment','arez'); ?></span>
			<!-- /ko -->
			<!-- ko if: WebBooker.CheckoutNav.showCustomize() && sale.items().length -->
			<span><?php _e('Ticket Options','arez'); ?></span>
			<!-- /ko -->
		</h2>
		<a id="privacy-policy-link" href="#"><?php _e('Privacy Policy'); ?></a>
	</div>
	<div id="checkout-activities" data-bind="visible: WebBooker.CheckoutNav.showCustomize() && sale.items().length">
		<!-- ko if: WebBooker.isPhone -->
		<div id="lead-guest-info" data-bind="with: WebBooker.Checkout.sale.leadGuest">
			<h3><?php _e('Lead guest contact information'); ?></h3>
			<div>
				<div class="required-wrapper">
					<input placeholder="<?php _e('First Name','arez'); ?>" type="text" data-bind="value: first_name, css: {required: !first_name()}" />
					<div class="required-text" data-bind="if: !email()">required</div>
				</div>
				<div class="required-wrapper">
					<input placeholder="<?php _e('Last Name','arez'); ?>" type="text" data-bind="value: last_name, css: {required: !last_name()}" />
					<div class="required-text" data-bind="if: !email()">required</div>
				</div>
				<div class="required-wrapper">
					<input placeholder="<?php _e('Phone Number','arez'); ?>" type="tel" data-bind="value: phone, css: {required: !phone()}" />
					<div class="required-text" data-bind="if: !email()">required</div>
				</div>
				<div class="required-wrapper">
					<input placeholder="<?php _e('Email Address','arez'); ?>" type="email" data-bind="value: email, css: {required: !email()}" />
					<div class="required-text" data-bind="if: !email()">required</div>
				</div>
				<input id="global-guest-name" type="checkbox" data-bind="checked: WebBooker.Checkout.copyNames" />
				<label class="checkbox-label" for="global-guest-name"><?php _e("Use this name for each guest",'arez');?></label>
			</div>
		</div>
		<!-- /ko -->
		<div data-bind="foreach: WebBooker.Checkout.sale.items">
			<div class="activity">
				<div class="activity-header clearfix">
					<div class="activity-title"><a data-bind="html: title, attr: { 'href': url }"></a> - <span class="date" data-bind="text: i18n_date()"></span> <span>|</span> <span data-bind="text: tickets().length" ></span><span> <?php _e('Guests', 'bg-lang'); ?> | </span><span data-bind="clean_money: subtotal"></span></div>
					<div class="activity-remove" title="<?php _e('Remove Activity','arez'); ?>" data-bind="click: remove"></div>
				</div>
				<!-- ko foreach: tickets -->
				<div class="guest">
					<div class="guest-header clearfix">
						<div class="guest-title" data-bind="text: name"></div>
						<div class="guest-remove" title="<?php _e('Remove Guest','arez'); ?>" data-bind="click: $parent.removeGuest"></div>
					</div>
					<div class="guest-options-container clearfix" data-bind="if: $index() == 0 || (!WebBooker.Checkout.copyNames() || !$parent.copyToAll())">
						<div class="guest-name" data-bind="visible: !WebBooker.Checkout.copyNames()">
							<input type="text" data-bind="value: first_name" placeholder="<?php _e('First Name','arez'); ?>">
							<input type="text" data-bind="value: last_name" placeholder="<?php _e('Last Name','arez'); ?>">
						</div>
						
						<div class="options">
							<!--ko if: $index() == 0 && (options().length > 0 || transportView.transportation().length > 0 || !WebBooker.Checkout.copyNames()) -->
							<div data-bind="text: $parent.copyToAll('true')" style="display: none;"></div>
							<div class="options-header">
								<input type="checkbox" data-bind="checked: $parent.copyToAll, attr: {id:'global-guest-options-' + row_id + $index()}" value="true" />
								<label class="checkbox-label" data-bind="attr: {for:'global-guest-options-' + row_id + $index()}"><?php _e('Apply to all guests on this activity.','arez'); ?></label>
							</div>
							<!-- /ko -->
							<!-- ko if: options().length -->
							<!-- ko if: $index() == 0 || ($index != 0 && !$parent.copyToAll()) -->
							<ul data-bind="foreach: options">
								<li>
									<label><span data-bind="text: name"></span></label>
									<div class="required-wrapper">
										<!-- ko if: type.toLowerCase() == 'dropdown' || type.toLowerCase() == 'combo' || type.toLowerCase() == 'radio' -->
										<select data-bind="options: items, value: selectedItem, optionsText: function(item) {
											if(item.fee && item.fee != '' && item.fee != '0') {
												return item.name + ' +' + parseFloat(item.fee).toFixed(2);
											} else {
												return item.name;
											}
										}, optionsCaption: '<?php _e('Choose...','arez'); ?>', css: { required: required }"></select>
										<div class="required-text" data-bind="if: required && !selectedItem()">required</div>
										<!-- /ko -->
										<!-- ko if: type.toLowerCase() == 'text' -->
										<input type="text" data-bind="value: text, valueUpdate: ['afterkeydown','propertychange','input'], css: { required: required && !text() }" />
										<div class="required-text" data-bind="if: required && !text()">required</div>
										<!-- /ko -->
									</div>
								</li>
							</ul>
							<!-- /ko --> 
							<!-- /ko -->
						</div>
						<!-- ko if: $parent.transportation().length && (($index() == 0 && !$parent.copyToAll()) || ($index != 0 && !$parent.copyToAll())) -->
						
						<div class="transportation" data-bind="attr: { id: row_id }, with: transportView">
							<div class="transportation-header">
								<input type="checkbox" data-bind="checked: wantsTransport, attr: {id:'guest-trans-' + row_id + '-' + $index()}" />
								<label class="checkbox-label" data-bind="attr: {for:'guest-trans-' + row_id+ '-' +  $index()}"><?php _e('Yes, I need transportation.','arez'); ?></label>
							</div>
							<div class="transportation-options" data-bind="visible: wantsTransport">
								<select class="trans-type-select" data-bind="options: transportationTypes, value: selectedTransType, optionsCaption: '<?php _e('Please select which type of vehicle you would prefer.', 'arez'); ?>'"></select>
								<div class="label"><?php _e("We need to know where you're staying at so we can suggest the nearest transportation option.", 'arez'); ?></div>
								<div class="clearfix">
									<div class="trans-option-wrap">
										<input type="radio" value="hotel" data-bind="checked: locationSelect, attr: {id:'guest-trans-hotel-' + row_id + $index()}">
										<label class="radio-label" data-bind="attr: {for:'guest-trans-hotel-' + row_id + $index()}"><?php _e('Hotel', 'arez'); ?></label>
									</div>
									<div class="trans-option-wrap">
										<input type="radio" value="address" data-bind="checked: locationSelect, attr: {id:'guest-trans-address-' + row_id + $index()}">
										<label class="radio-label" data-bind="attr: {for:'guest-trans-address-' + row_id + $index()}"><?php _e('Local Address', 'arez'); ?></label>
									</div>
								</div>
								<!-- ko if: locationSelect() == 'hotel' -->
								<div class="hotel">
									<div>
										<div class="label has-typeahead"><?php _e('Please select a hotel.','arez'); ?> <span><?php _e("Type to search...",'arez'); ?></span></div>
										<input type="text" data-bind="hotelTypeahead: { value: hotel }, valueUpdate: ['afterkeydown','propertychange','input']" data-provide="typeahead" />
									</div>
									
									<div>
										<div class="label"><?php _e('Hotel Room/Confirmation Number','arez'); ?></div>
										<input type="text" data-bind="value: room, valueUpdate: ['afterkeydown','propertychange','input']" />
									</div>
								</div>
								<!-- /ko -->
								<!-- ko if: locationSelect() == 'address' -->
								<div class="address clearfix">
									<div>
										<div class="label"><?php _e('Address','arez'); ?></div>
										<input type="text" data-bind="value: home.address, valueUpdate: ['afterkeydown','propertychange','input']" />
									</div>
									<div>
										<div class="label"><?php _e('City','arez'); ?></div>
										<input type="text" data-bind="value: home.city, valueUpdate: ['afterkeydown','propertychange','input']" />
									</div>
									<div>
										<div class="label"><?php _e('State/Province','arez'); ?></div>
										<input type="text" data-bind="value: home.state, valueUpdate: ['afterkeydown','propertychange','input']" />
									</div>
									<div>
										<div class="label"><?php _e('Zip/Postal Code','arez'); ?></div>
										<input type="number" data-bind="value: home.postal, valueUpdate: ['afterkeydown','propertychange','input']" />
									</div>
									<div>
										<div class="label"><?php _e('Country','arez'); ?></div>
										<select data-bind="options: WebBooker.bootstrap.wb_countries, value: home.country, optionsText: 'name', optionsCaption: '<?php _e('Choose...','arez'); ?>'"></select>
									</div>
									<div>
										<button data-bind="click: function(){doGeocode(); acceptGeocode();}">
											<!-- ko if: !stored_lat() -->
											<span><?php _e('Verify Address','arez'); ?></span>
											<!-- /ko -->
											<!-- ko if: stored_lat -->
											<span><?php _e('Address OK','arez'); ?></span>
											<!--/ko-->
										</button>
									</div>
									<div class="map-canvas" style="display: none !important;"></div>
								</div>
								<!-- /ko -->
								<!-- ko if: wantsTransport() && locationSelect() && ((locationSelect() == 'hotel' && hotel()) || (locationSelect() == 'address' && lat() && lng())) -->
								<ul class="pickups" data-bind="foreach: transportsToShow">
									<li class="clearfix">
										<input type="checkbox" data-bind="checked: selected, attr: {id: 'transport-' + $parentContext.$parentContext.$parentContext.$index() + $parentContext.$index() + $index()}" />
										<label class="checkbox-label" data-bind="attr: {for: 'transport-' + $parentContext.$parentContext.$parentContext.$index() + $parentContext.$index() + $index()}">
											<span class="money" data-bind="money: amount"></span>
											<span class="vehicle" data-bind="text: vehicle"></span>
											<!-- ko if: distance -->
											- <span data-bind="text: distance"></span> <?php _e('miles from your location.','arez'); ?>
											<!-- /ko -->
										</label>
										<div class="pickup-extended">
											<div class="pickup-name" data-bind="html: name"></div>
											<!-- ko if: address -->
											<div class="pickup-address" data-bind="html: address"></div>
											<!-- /ko -->
											<!-- ko if: instructions -->
											<div class="pickup-instructions">&ldquo;<span data-bind="html: instructions"></span>&rdquo;</div>
											<!-- /ko -->
										</div>
									</li>
								</ul>
								<!-- /ko -->
							</div>
						</div>
						<!-- /ko -->
						<!-- ko if: $parent.transportation().length && $index() == 0 && $parent.copyToAll() -->
						<div class="transportation" data-bind="attr: { id: row_id }, with: $parent.transportView">
							<div class="transportation-header">
								<input type="checkbox" data-bind="checked: wantsTransport, attr: {id:'guest-trans-' + row_id + '-' +  $index()}" />
								<label class="checkbox-label" data-bind="attr: {for:'guest-trans-' + row_id + '-' +  $index()}"><?php _e('Yes, I need transportation.','arez'); ?></label>
							</div>
							<div class="transportation-options" data-bind="visible: wantsTransport">
								<select class="trans-type-select" data-bind="options: transportationTypes, value: selectedTransType, optionsCaption: '<?php _e('Please select which type of vehicle you would prefer.', 'arez'); ?>'"></select>
								<div class="label"><?php _e("We need to know where you're staying at so we can suggest the nearest transportation option.", 'arez'); ?></div>
								<div class="clearfix">
									<div class="trans-option-wrap">
										<input type="radio" value="hotel" data-bind="checked: locationSelect, attr: {id:'guest-trans-hotel-' + row_id + $index()}">
										<label class="radio-label" data-bind="attr: {for:'guest-trans-hotel-' + row_id + $index()}"><?php _e('Hotel', 'arez'); ?></label>
									</div>
									<div class="trans-option-wrap">
										<input type="radio" value="address" data-bind="checked: locationSelect, attr: {id:'guest-trans-address-' + row_id + $index()}">
										<label class="radio-label" data-bind="attr: {for:'guest-trans-address-' + row_id + $index()}"><?php _e('Local Address', 'arez'); ?></label>
									</div>
								</div>
								<!-- ko if: locationSelect() == 'hotel' -->
								<div class="hotel">
									<div>
										<div class="label has-typeahead"><?php _e('Please select a hotel.','arez'); ?> <span><?php _e("Type to search...",'arez'); ?></span></div>
										<input type="text" data-bind="hotelTypeahead: { value: hotel }, valueUpdate: ['afterkeydown','propertychange','input']" data-provide="typeahead" />
									</div>
									
									<div>
										<div class="label"><?php _e('Hotel Room/Confirmation Number','arez'); ?></div>
										<input type="text" data-bind="value: room, valueUpdate: ['afterkeydown','propertychange','input']" />
									</div>
								</div>
								<!-- /ko -->
								<!-- ko if: locationSelect() == 'address' -->
								<div class="address clearfix">
									<div>
										<div class="label"><?php _e('Address','arez'); ?></div>
										<input type="text" data-bind="value: home.address, valueUpdate: ['afterkeydown','propertychange','input']" />
									</div>
									<div>
										<div class="label"><?php _e('City','arez'); ?></div>
										<input type="text" data-bind="value: home.city, valueUpdate: ['afterkeydown','propertychange','input']" />
									</div>
									<div>
										<div class="label"><?php _e('State/Province','arez'); ?></div>
										<input type="text" data-bind="value: home.state, valueUpdate: ['afterkeydown','propertychange','input']" />
									</div>
									<div>
										<div class="label"><?php _e('Zip/Postal Code','arez'); ?></div>
										<input type="number" data-bind="value: home.postal, valueUpdate: ['afterkeydown','propertychange','input']" />
									</div>
									<div>
										<div class="label"><?php _e('Country','arez'); ?></div>
										<select data-bind="options: WebBooker.bootstrap.wb_countries, value: home.country, optionsText: 'name', optionsCaption: '<?php _e('Choose...','arez'); ?>'"></select>
									</div>
									<div>
										<button data-bind="click: function(){doGeocode(); acceptGeocode();}">
											<!-- ko if: !stored_lat() -->
											<span><?php _e('Verify Address','arez'); ?></span>
											<!-- /ko -->
											<!-- ko if: stored_lat -->
											<span><?php _e('Address OK','arez'); ?></span>
											<!--/ko-->
										</button>
									</div>
									<div class="map-canvas" style="display: none !important;"></div>
								</div>
								<!-- /ko -->
								<!-- ko if: wantsTransport() && locationSelect() && ((locationSelect() == 'hotel' && hotel()) || (locationSelect() == 'address' && lat() && lng())) -->
								
								<ul class="pickups" data-bind="foreach: transportsToShow">
									<li class="clearfix">
										<input type="checkbox" data-bind="checked: selected, attr: {id: 'transport-' + $parentContext.$parentContext.$parentContext.$index() + $parentContext.$index() + $index()}" />
										<label class="checkbox-label" data-bind="attr: {for: 'transport-' + $parentContext.$parentContext.$parentContext.$index() + $parentContext.$index() + $index()}">
											<span class="money" data-bind="money: amount"></span>
											<span class="vehicle" data-bind="text: vehicle"></span>
											<!-- ko if: distance -->
											- <span data-bind="text: distance"></span> <?php _e('miles from your location.','arez'); ?>
											<!-- /ko -->
										</label>
										<div class="pickup-extended">
											<div class="pickup-name" data-bind="html: name"></div>
											<!-- ko if: address -->
											<div class="pickup-address" data-bind="html: address"></div>
											<!-- /ko -->
											<!-- ko if: instructions -->
											<div class="pickup-instructions">&ldquo;<span data-bind="html: instructions"></span>&rdquo;</div>
											<!-- /ko -->
										</div>
									</li>
								</ul>
								<!-- /ko -->
							</div>
						</div>
						<!-- /ko -->
					</div>
				</div>
				<!-- /ko -->
			</div>
		</div>
	</div>
	<div id="checkout-payment" class="clearfix" data-bind="visible: WebBooker.CheckoutNav.showPayment() && sale.items().length">
		<!-- ko if: WebBooker.bootstrap.payment_types -->
		<div data-bind="visible: WebBooker.bootstrap.payment_types.length > 1">
			<select id="select-pay-type" data-bind="options: WebBooker.bootstrap.payment_types, optionsText: function(item){ return item.label; }, value: WebBooker.Checkout.paymentType, optionsCaption: '<?php _e("Choose Payment Type&hellip;",'arez');?>'"></select>
		</div>
		<!-- /ko -->
		<!-- ko foreach: WebBooker.Checkout.sale.payments -->
		<!-- ko if: type == 'voucher' -->
		<div class="voucher">
			<div class="label" data-bind="text: label"></div>
			<div><?php _e('Voucher value:','arez'); ?> <span data-bind="money: default_amount"></span></div>
			<div><?php _e('Amount applied:','arez'); ?> <span data-bind="money: amount"></span></div>
			<div class="required-wrapper" data-bind="if: require_authorization_id">
				<input type="text" placeholder="<?php _e('Authorization Code:'); ?>" data-bind="value: authorization_ID, valueUpdate: ['afterkeydown','propertychange','input'], css: {required: !authorization_ID()}" />
				<div class="required-text" data-bind="if: !authorization_ID()">required</div>
			</div>
		</div>
		<!-- /ko -->
		<!-- ko if: type == 'credit' -->
		<div id="billing-address">
			<div class="label"><?php _e('Billing Address','arez'); ?></div>
			<fieldset class="clearfix">
				<div class="required-wrapper">
					<input type="text" data-bind="value: payee.first_name, valueUpdate: ['afterkeydown','propertychange','input'], css: {required: !payee.first_name()}" placeholder="<?php _e('First Name','arez'); ?>" />
					<div class="required-text" data-bind="if: !payee.first_name()">required</div>
				</div>
				<div class="required-wrapper">
					<input type="text" data-bind="value: payee.last_name, valueUpdate: ['afterkeydown','propertychange','input'], css: {required: !payee.last_name()}" placeholder="<?php _e('Last Name','arez'); ?>" />
					<div class="required-text" data-bind="if: !payee.last_name()">required</div>
				</div>
				<div class="required-wrapper">
					<input type="text" data-bind="value: payee.address, valueUpdate: ['afterkeydown','propertychange','input'], css: {required: !payee.address()}" placeholder="<?php _e('Address','arez'); ?>" />
					<div class="required-text" data-bind="if: !payee.address()">required</div>
				</div>
				<div class="required-wrapper">
					<input type="tel" data-bind="value: payee.phone, valueUpdate: ['afterkeydown','propertychange','input'], css: {required: !payee.phone()}" placeholder="<?php _e('Phone Number','arez'); ?>" />
					<div class="required-text" data-bind="if: !payee.phone()">required</div>
				</div>
				<div class="required-wrapper">
					<select data-bind="value: payee.country, options: WebBooker.bootstrap.wb_countries, optionsText: 'name', css: {required: !payee.country()}"></select>
					<div class="required-text" data-bind="if: !payee.country()">required</div>
				</div>
				<div class="required-wrapper city">
					<input type="text" class="city" data-bind="value: payee.city, valueUpdate: ['afterkeydown','propertychange','input'], css: {required: !payee.city()}" placeholder="<?php _e('City','arez'); ?>" />
					<div class="required-text" data-bind="if: !payee.city()">required</div>
				</div>
				<!-- ko if: payee.country -->
				<!-- ko if: payee.country().name == 'United States' -->
				<div class="required-wrapper state">
					<select class="state" data-bind="value: payee.state, options: WebBooker.us_states, optionsCaption: '<?php _e('State&hellip;','arez'); ?>', css: {required: !payee.state()}"></select>
				</div>
				<!-- /ko -->
				<!-- ko if: payee.country().name != 'United States' -->
				<div class="required-wrapper state">
					<input type="text" class="state" data-bind="value: payee.state, valueUpdate: ['afterkeydown','propertychange','input'], css: {required: !payee.state()}" placeholder="<?php _e('State/Province','arez'); ?>" />
				</div>
				<!-- /ko -->
				<!-- /ko -->
				<div class="required-wrapper zip">
					<input type="number" class="zip" data-bind="value: payee.postal, valueUpdate: ['afterkeydown','propertychange','input'], css: {required: !payee.postal()}" placeholder="<?php _e('Postal Code','arez'); ?>" />
				</div>
			</fieldset>
		</div>
		<div id="credit-info">
			<!-- ko if: useHostedPage -->
			<h2><?php _e('Your card information will be collected on the next page.','arez'); ?></h2>
			<!-- /ko -->
			<!-- ko if: !useHostedPage -->
			<div class="label"><?php _e('Card Information','arez'); ?></div>
			<fieldset>
				<div class="cc-number-holder required-wrapper">
					<input type="text" data-bind="value: card.number, css: {required: !card.number()}" placeholder="<?php _e("Credit Card Number",'arez');?>" />
					<div class="required-text" data-bind="if: !card.number()">required</div>
					<div class="cc-valid-indicator" data-bind="css: { 'invalid': card.errors().length, 'valid': !card.errors().length && card.number()}"></div>
				</div>
				<div class="required-wrapper card-month">
					<select data-bind="value: card.month, options: months, optionsCaption: '<?php _e('Exp. Month','arez'); ?>', optionsValue: 'index', optionsText: 'label', css: {required: !card.month()}"></select>
					<div class="required-text" data-bind="if: !card.month()">required</div>
				</div>
				<div class="required-wrapper card-year">
					<select data-bind="value: card.year, options: years, optionsCaption: '<?php _e('Exp. Year','arez'); ?>', css: {required: !card.year()}"></select>
					<div class="required-text" data-bind="if: !card.year()">required</div>
				</div>
				<div class="required-wrapper sec-code">
					<input type="text" class="sec-code" data-bind="value: card.code, valueUpdate: ['afterkeydown','propertychange','input'], css: {required: !card.code()}" placeholder="<?php _e('CVV','arez'); ?>" />
					<div class="required-text" data-bind="if: !card.code()">req.</div>
				</div>
				<div class="cc-id">
					<span class="cc-type amex" data-bind="fadeOpacity: card.type()=='amex'" alt="Amex Card"></span>
					<span class="cc-type mc" data-bind="fadeOpacity: card.type()=='mastercard'" alt="Master Card"></span>
					<span class="cc-type visa" data-bind="fadeOpacity: card.type()=='visa'" alt="Visa"></span>
					<span class="cc-type disc" data-bind="fadeOpacity: card.type()=='discover'" alt="Discover"></span>
				</div>
			</fieldset>
			<!-- /ko -->
		</div>
		<!-- /ko -->
		<!-- /ko -->
		<div id="payment-summary" class="clearfix" data-bind="with: WebBooker.Checkout.sale">
			<?php if(isset($wb['cancellation'])){ ?>
				<div id="cancellation-notice"><div class="alert-sign"></div>IMPORTANT! Read our <a id="cancellation-link" href="#">Cancellation Policy</a></div>
			<?php }?>
			<div id="purchases">
				<div class="label"><?php _e('Purchase Summary','arez'); ?></div>
				<!-- ko foreach: items -->
				<div class="item">
					<div class="title clearfix"><span data-bind="text: title"></span><span data-bind="clean_money: subtotal"></span></div>
					<div class="guests" data-bind="foreach: guests">
						<div class="guest-name clearfix"><span><span data-bind="text: qty"></span>&nbsp;<span data-bind="text: name"></span></span><span data-bind="clean_money: total"></span></div>
					</div>
					<div class="fees clearfix"><span>Fees</span><span data-bind="clean_money: optionTotal"></span></div>
					<div class="transport clearfix"><span>Transportation</span><span data-bind="clean_money: transportTotal"></span></div>
				</div>
				<!-- /ko -->
			</div>
			<div id="payments" data-bind="with: WebBooker.Checkout.sale">
				<div class="subtotal clearfix"><span><?php _e('Subtotal:','arez'); ?></span><span data-bind="clean_money: subtotal"></span></div>
				<div class="discounts clearfix"><span><?php _e('Discounts:','arez'); ?></span><span data-bind="clean_money: discountTotal"></span></div>
				<div class="tax clearfix"><span><?php _e('Tax:','arez'); ?></span><span data-bind="clean_money: taxes"></span></div>
				<div class="total clearfix"><span><?php _e('Total:','arez'); ?></span><span data-bind="clean_money: total"></span></div>
				<div class="payments-list" data-bind="foreach: WebBooker.Checkout.sale.payments">
					<div class="payment clearfix"><span data-bind="text: label"></span><span data-bind="clean_money: amount"></span></div>
				</div>
				<input type="checkbox" id="terms-accept" data-bind="checked: WebBooker.Checkout.termsAccepted" /><label class="checkbox-label" for="terms-accept"><?php _e('I accept the','arez'); ?> <a id="terms-and-conditions-link" href="#" title="<?php _e('View the terms and conditions','arez'); ?>">terms and conditions</a></label>
				<button data-bind="enable: WebBooker.Checkout.enableSubmit, click: function(){WebBooker.showProcessing(); WebBooker.Checkout.process()}" disabled="">Make Payment</button>
				<div id="checkout-payment-error" class="alert alert-error" data-bind="fadeVisible: WebBooker.Checkout.errorMsg">
					<span data-bind="text: WebBooker.Checkout.errorMsg"></span>
				</div>
			</div>
		</div>
	</div>
	<div id="checkout-sidebar">
		<!-- ko if: !WebBooker.isPhone -->
		<div id="lead-guest-info" data-bind="with: WebBooker.Checkout.sale.leadGuest">
			<h3><?php _e('Lead guest contact information'); ?></h3>
			<div>
				<div class="required-wrapper">
					<input placeholder="<?php _e('First Name','arez'); ?>" type="text" data-bind="value: first_name, css: {required: !first_name()}" />
					<div class="required-text" data-bind="if: !first_name()">required</div>
				</div>
				<div class="required-wrapper">
					<input placeholder="<?php _e('Last Name','arez'); ?>" type="text" data-bind="value: last_name, css: {required: !last_name()}" />
					<div class="required-text" data-bind="if: !last_name()">required</div>
				</div>
				<div class="required-wrapper">
					<input placeholder="<?php _e('Phone Number','arez'); ?>" type="tel" data-bind="value: phone, css: {required: !phone()}" />
					<div class="required-text" data-bind="if: !phone()">required</div>
				</div>
				<div class="required-wrapper">
					<input placeholder="<?php _e('Email Address','arez'); ?>" type="email" data-bind="value: email, css: {required: !email()}" />
					<div class="required-text" data-bind="if: !email()">required</div>
				</div>
				<input id="global-guest-name" type="checkbox" data-bind="checked: WebBooker.Checkout.copyNames" />
				<label class="checkbox-label" for="global-guest-name"><?php _e("Use this name for each guest",'arez');?></label>
			</div>
		</div>
		<!-- /ko -->
		<form id="discounts" data-bind="submit: getDiscount">
			<h2><?php _e('Got a discount?'); ?></h2>
			<div><?php _e('Enter the code below'); ?></div>
			<input type="text" placeholder="<?php _e("Promo code",'arez');?>" data-bind="value: discountCode" />
			<input type="submit" hidden="hidden" />
			<div data-bind="visible: sale.discount()">
				<button type="reset" data-bind="click: clearDiscount">&#10006;</button>
			</div>
			<!-- ko if: sale.discount() && !verifying() -->
			<div class="clearfix">
				<span><?php _e('Name:','arez'); ?></span> <span data-bind="text: sale.discount().name"></span><br />
				<span><?php _e('Amount:','arez'); ?></span>
				<!-- ko if: sale.discount().amount --><span data-bind="money: sale.discount().amount"></span><!-- /ko -->
				<!-- ko if: sale.discount().rate --><span data-bind="text: sale.discount().rate"></span><!-- /ko -->
			</div>
			<!-- /ko -->
			<div data-bind="visible: !codeGood()"><?php _e('Sorry, that discount or promotion code isn\'t valid.','arez'); ?></div>
		</form>
		<div id="sale-summary" data-bind="with: WebBooker.Checkout.sale">
			<div class="subtotal clearfix"><span><?php _e('Subtotal:','arez'); ?></span><span data-bind="clean_money: subtotal"></span></div>
			<div class="discounts clearfix"><span><?php _e('Discounts:','arez'); ?></span><span data-bind="clean_money: discountTotal"></span></div>
			<div class="tax clearfix"><span><?php _e('Tax:','arez'); ?></span><span data-bind="clean_money: taxes"></span></div>
			<div class="total clearfix"><span><?php _e('Total:','arez'); ?></span><span data-bind="clean_money: total"></span></div>
			<!-- ko if: WebBooker.CheckoutNav.showCustomize() -->
			<button data-target="Payment" data-bind="click: WebBooker.CheckoutNav.goToStep, scrollTopOnClick: true"><?php _e('Proceed to Payment','arez'); ?></button>
			<!-- /ko -->
			<!-- ko if: WebBooker.CheckoutNav.showPayment() -->
			<button data-target="Customize" data-bind="click: WebBooker.CheckoutNav.goToStep, scrollTopOnClick: true"><?php _e('Back to Options','arez'); ?></button>
			<!-- /ko -->
		</div>
	</div>
</div><!-- /webbooker-checkout -->