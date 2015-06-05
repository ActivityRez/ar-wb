<?php

/**
 *	ActivityRez Web Booking Engine
 *	Confirmation PHP File
 *
 *	@author Ryan Freeman <ryan@stoked-industries.com>
 *	@package ActivityRez
 *	@subpackage Web Booking Engine
 */
?>

<div id="webbooker-confirmation" data-bind="visible: WebBooker.CheckoutNav.showConfirmation, with: WebBooker.Checkout" style="display:none">
	<div id="congratulations">
		<div class="container">
			<h2><?php _e('Congratulations','arez'); ?>!</h2>
			<div><?php _e('Thank you for booking your activities with us! Your payment has been successfully received and processed. The details are below.','arez'); ?></div>
		</div>
	</div>
	<div class="container clearfix" data-bind="visible: !errorMsg() && !moreErrorMsg()">
		<div id="reservation-number"><span><?php _e('Reservation Number','arez'); ?>:</span> <span data-bind="text: sale.id"></span></div>
		<div id="cfa-activities" data-bind="visible: sale.cfa_activities().length > 0">
			<div><?php _e('Notice: The following activities are pending confirmation from the vendor. An agent will get back to you within 72 hours regarding this reservation.','arez'); ?></div>
			<ul data-bind="foreach: sale.cfa_activities">
				<li><span class="cfa-title" data-bind="html: title"></span>: <span data-bind="text: date"></span> <?php _e('at','arez'); ?> <span data-bind="text: time"></span></li>
			</ul>
			<div><?php printf( __('You may call %s to inquire about them.','arez'), $wb['reseller_cs_phone'] ); ?></div>
		</div>
		
		<div id="reserved-activities" data-bind="foreach: sale.items">
			<div class="activity">
				<div class="title" data-bind="text: title"></div>
				<div class="date-time"><span data-bind="text: date"></span> | <span data-bind="text: time"></span> | <span data-bind="text: destination"></span></div>
				<div class="guests" data-bind="foreach: guests">
					<div class="guest"><span data-bind="text: name"></span> <?php _e('Guests', 'arez'); ?>: <span data-bind="text: qty"></span></div>
				</div>
				<div class="special-instructions">&ldquo;<span data-bind="html: instructions"></span>&rdquo;</div>
			</div>
		</div>
		
		<div id="lead-guest" data-bind="with: sale.leadGuest">
			<div class="name"><span><?php _e('Name', 'arez'); ?>:</span> <span><span data-bind="text: first_name"></span> <span data-bind="text: last_name"></span></span></div>
			<div class="address">
				<span><?php _e('Address', 'arez'); ?>:</span>&nbsp;
				<span>
					<div data-bind="text: address"></div>
					<span data-bind="text: city"></span>, 
					<span data-bind="text: state"></span> 
					<span data-bind="text: postal"></span>
					<div data-bind="text: country"></div>
				</span>
			</div>
			<div class="email"><span><?php _e('Email', 'arez'); ?>:</span> <span data-bind="text: email"></span></div>
			<div class="phone"><span><?php _e('Phone', 'arez'); ?>:</span> <span data-bind="text: phone"></span></div>
		</div>
		
		<div id="payments">
			<div class="payments-title"><?php _e('Payments', 'arez'); ?></div>
			<div class="payments" data-bind="foreach: sale.payments">
				<div class="payment"><span data-bind="text: type"></span> <span data-bind="clean_money: amount"></span></div>
			</div>
			<div class="sale-id"><span><?php _e('Sale ID', 'arez'); ?>:</span><span data-bind="text: sale.id"></span></div>
			<button data-bind="click: printTickets"><?php _e('PRINT eTICKETS','arez'); ?></button>
		</div>
	</div><!-- /content -->
	
	<div class="content" data-bind="visible: errorMsg">
		<div class="alert alert-error">
			<strong data-bind="text: errorMsg"></strong><br>
			<?php _e('There was a problem processing your payment. Please call us at our Customer Support number to complete the sale. Have the following information ready when you call.','arez'); ?>
		</div>
		<p><strong><?php _e('Reservation Number:','arez'); ?></strong> <span data-bind="text: sale.id"></span></p>
		<p><strong><?php _e('Amount:','arez'); ?></strong> <span data-bind="clean_money: sale.total"></span></p>
	</div>
	<div class="content" data-bind="visible: moreErrorMsg">
		<div class="alert alert-error">
			<strong data-bind="text: moreErrorMsg()"></strong><br>
		</div>
	</div>
</div><!-- /webbooker-confirmation -->
