<?php
/**
 * Password Reset Interface
 */
?>
<div id="passwordResetRequest" class="container" data-bind="if: WebBooker.Agent.passwordResetRequest">
	<div  data-bind="visible: WebBooker.Agent.passwordResetRequest" style="display:none">
		<div class="header">
			<h2><?php _e("Request New Password",'arez');?></h2>
		</div>
		<div class="content">
			<div class="reset-instructions"><?php _e("Please provide your email or login name to reset your password.",'arez');?></div>
			<form class="newPassword">
				<input type="text" data-bind="value: WebBooker.Agent.user">
				<button type="submit" data-bind="click: WebBooker.Agent.PasswordResetRequest"><?php _e("Request Reset",'arez');?></button>
			</form>	
		</div>
	</div>
</div>

<div id="passwordReset" class="container" data-bind="if: WebBooker.Agent.passwordReset">
	<div  data-bind="visible: WebBooker.Agent.passwordReset" style="display:none">
		<div class="header">
			<h2><?php _e("Reset Password",'arez');?></h2>
		</div>
		<div class="content">
			<input type="password" placeholder="<?php _e("New Password",'arez');?>" data-bind="value: WebBooker.Agent.password">
			<input type="password" placeholder="<?php _e("Confirm New Password",'arez');?>" data-bind="value: WebBooker.Agent.password2">
			<button data-bind="click: WebBooker.Agent.PasswordReset"><?php _e("Change Password",'arez');?></button>
		</div>
	</div>
</div>

