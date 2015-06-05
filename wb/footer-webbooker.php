	<?php require_once('wp-content/themes/ActivityRez/inc/footer_begin.php'); ?>
	<div id="webbooker-modals">
		<div class="container">	
			<div id="reseller-privacy-policy" class="modal hide">
				<button class="close">&#10006;</button>
				<div class="title"><?php _e('Privacy Policy','arez'); ?></div>
				<div class="modal-body">
					<?php echo $wb['privacy']; ?>
				</div>
			</div>
		
			<div id="reseller-agreement" class="modal hide">
				<button class="close" >&#10006;</button>
				<div class="title"><?php _e('Terms and Conditions','arez'); ?></div>
				<div class="modal-body">
					<?php echo $wb['terms']; ?>
				</div>
				<!--<div class="buttons">
					<button data-bind="click: WebBooker.closeModal"><?php _e('Cancel','arez'); ?></button>
					<button data-bind="click: WebBooker.closeModal"><?php _e('I Accept','arez'); ?></button>
				</div>-->
			</div>
		
			<div id="checkout-processing" class="modal hide">
				<div class="title"><?php _e('Processing Payment','arez'); ?></div>
				<div class="modal-body">
					<p><?php _e('We are processing your payment information. Please be patient.', 'arez'); ?></p>
					<p class="small"><?php _e('You might be redirected to a different website to finish your payment.', 'arez'); ?></p>
					<div class="progress">
						<div class="bar"></div>
					</div>
				</div>
			</div>

			<div id="cancellation-policy" class="modal hide">
				<button class="close">&#10006;</button>
				<div class="title"><?php _e('Cancellation Policy','arez'); ?></div>
				<div class="modal-body">
					<?php echo $wb['cancellation']; ?>
				</div>
			</div>
		</div>
	</div>
	<?php 
		global $wb;
		
		wp_footer();
		do_action('webbooker_footer_event', $wb);
	?>
	<div id="webbooker-sidebar"><div id="agents-sidebar"></div></div>
	<div id="backdrop"></div>
</body>
</html>