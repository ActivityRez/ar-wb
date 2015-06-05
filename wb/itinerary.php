<?php 

/**
 *	ActivityRez Web Booking Engine
 *	Itinerary PHP File.
 *
 *	@author Ryan Freeman <ryan@stoked-industries.com>
 *	@package ActivityRez
 *	@subpackage Web Booking Engine
 */
global $wb;
?>
<div class="popup-error" style="display:none;" data-bind="visible: Itinerary.popupError()">
	<?php _e('Window was blocked from opening. Please check your settings.'); ?>
	<div data-bind="click: Itinerary.popupErrorClose">X</div>
</div>
<div id="webbooker-itinerary" data-bind="visible: Itinerary.show, with: Itinerary" style="display:none">
	<div class="header gradient-light">
		<h3><?php _e('Itinerary','arez'); ?></h3>
	</div>
	<div class="cb"></div>
	<div class="ribbonFold"></div>
	
	<?php include("mini-sidebar.php"); ?>
	
	<div id="itinerary-retrieve" class="content">
		<form class="form-vertical">
			<p><?php _e('To retrieve your itinerary, please enter the reservation number and the e-mail address associated with the reservation.','arez'); ?></p><br>
			<label><?php _e('Reservation Number*','arez'); ?></label>
			<input type="text" data-bind="value: sale.id">
			
			<label><?php _e('E-mail Address*','arez'); ?></label>
			<input type="text" data-bind="value: sale.leadGuest.email">
			
			<p>
				<button class="buttonBlue" type="submit" data-bind="click: load"><i class="icon-ok icon-white"></i> <?php _e('Retrieve','arez'); ?></button> &nbsp;
				<button class="buttonGray" data-bind="click: reset"><i class="icon-remove icon-white"></i> <?php _e('Clear','arez'); ?></button>
			</p>
		</form>
	</div><!-- itinerary-retrieve -->
	
	<div id="itinerary-loading" class="content" data-bind="fadeVisible: loading">
		<img src="<?php echo PLUGIN_DIR_URL . 'images/ajax-loader.gif'; ?>" alt="loading image"><br><br>
		<?php echo __('Finding Your Itinerary','arez') . '...'; ?>
	</div><!-- /itinerary-loading -->
	
	<div id="itinerary-error" class="content" data-bind="fadeVisible: errorMsg">
		<div class="alert alert-error">
			<strong><?php _e('Error','arez'); ?></strong><br>
			<span data-bind="text: errorMsg()"></span>
		</div>
	</div><!-- /itinerary-error -->
	
	<div id="itinerary-display" class="content" data-bind="fadeVisible: !errorMsg() && !loading() && loaded">
		<div id="itinerary-payment" class="box-rounded">
			<p><strong><?php _e('Reservation','arez'); ?>:</strong> <span data-bind="text: sale.id"></span></p>
			<p><strong><?php _e('Sale Date','arez'); ?>:</strong> <span data-bind="text: sale.i18n_modified()"></span></p>
			<div>
				<strong><?php _e('Payments','arez'); ?>:</strong>
				<ul data-bind="foreach: sale.payments">
					<li><strong data-bind="text: label"></strong>: <span data-bind="clean_money: amount"></span></li>
				</ul>
			</div>
			
			<hr />
			<p><strong><?php _e('Name:','arez'); ?></strong> <span data-bind="text: sale.leadGuest.full_name"></span></p>
			<div data-bind="foreach: sale.payments">
			<!-- ko if: type == 'credit' -->
			<?php if( $wb['i18n'] != 'ja' ) { ?>
			<p><strong><?php _e('Address:','arez'); ?></strong> <span data-bind="text: payee.address"></span></p>
			<p><strong><?php _e('City:','arez'); ?></strong> <span data-bind="text: payee.city"></span></p>
			<p><strong><?php _e('State:','arez'); ?></strong> <span data-bind="text: payee.state"></span></p>
			<p><strong><?php _e('Postal/Zip Code:','arez'); ?></strong> <span data-bind="text: payee.postal"></span></p>
			<?php } ?>
			<p><strong><?php _e('Country:','arez'); ?></strong> <span data-bind="text: payee.country()"></span></p>
			<?php if( $wb['i18n'] == 'ja' ) { ?>
			<p><strong><?php _e('Postal/Zip Code:','arez'); ?></strong> <span data-bind="text: payee.postal"></span></p>
			<p><strong><?php _e('State:','arez'); ?></strong> <span data-bind="text: payee.state"></span></p>
			<p><strong><?php _e('City:','arez'); ?></strong> <span data-bind="text: payee.city"></span></p>
			<p><strong><?php _e('Address:','arez'); ?></strong> <span data-bind="text: payee.address"></span></p>
			<?php } ?>
			<!-- /ko -->
			</div>
			<p><strong><?php _e('Phone:','arez'); ?></strong> <span data-bind="text: sale.leadGuest.phone"></span></p>
			<p><strong><?php _e('E-mail Address:','arez'); ?></strong> <span data-bind="text: sale.leadGuest.email"></span></p>
			
			<div class="actions">
				<button class="buttonGray" data-bind="click: printTickets"><i class="icon-print icon-white"></i> <?php _e('Print','arez'); ?></button>
			</div>
		</div><!-- /itinerary-payment -->
		
		<div id="itinerary-activities" data-bind="foreach: sale.items">
			<div class="activity box-rounded">
				<h3><a data-bind="html: title, attr: { 'href': url }"></a></h3>

				<div class="info">
					<strong><?php _e('Date:','arez'); ?></strong> <span data-bind="text: i18n_date()"></span> &nbsp;
					<strong><?php _e('Time:','arez'); ?></strong> <span data-bind="text: time"></span> &nbsp;
					<strong><?php _e('Location:','arez'); ?></strong> <span data-bind="text: destination"></span>
					<!-- ko if: directions_url -->
					<p><strong><?php _e('Address','arez'); ?>:</strong> <span data-bind="text: address"></span></p>
			   		<p><a data-bind="attr: { href: directions_url }" target="_blank"><?php _e('Click here to get directions.','arez'); ?></a> <?php _e('(opens new window)','arez'); ?></p>
					<!-- /ko -->
				</div>
				
				<br><strong><?php _e('Guest Summary','arez'); ?></strong><br><br>
				<!-- ko foreach: guests -->
				<div class="guest clearfix">
					<div class="padding">
						<strong><span data-bind="text: name"></span>:</strong> <span data-bind="text: qty"></span>
					</div>
				</div>
				<!-- /ko -->
				
				<br><br><strong><?php _e('Special Instructions','arez'); ?></strong><br><br>
				<div class="instructions">
					<span data-bind="html: instructions"></span>
				</div>
				
				<!-- ko if: transportation.length > 0 -->
				<br><br><strong><?php _e('Transportation Pickup','arez'); ?></strong><br><br>
				<div class="transportation" data-bind="foreach: transportation">
					<p><span data-bind="text: name"></span></p>
					<span data-bind="html: instructions"></span><br><br>
				</div>
				<!-- /ko -->
			</div><!-- /activity -->
		</div><!-- /itinerary-activities -->
	</div><!-- /itinerary-display -->
</div><!-- /webbooker-itinerary -->
