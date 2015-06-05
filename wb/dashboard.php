<?php
/**
 *	ActivityRez Web Booking Engine
 *	Reseller Dashboard File
 *
 *	@author Ryan Freeman <ryan@stoked-industries.com>
 *	@package ActivityRez
 *	@subpackage Web Booking Engine
 */
global $wb;
?>

<div id="webbooker-dashboard" class="container" data-bind="if: Dashboard.show">
	<div data-bind="with: Dashboard">
	
		<div id="dash-login" data-bind="visible: !WebBooker.Agent.user_id() && showMain()">
			<div class="header clearfix">
				<h2><?php _e('Log In','arez'); ?></h2>
			</div>
			<form id="login-form" data-bind="with: WebBooker.Agent">
				<input type="text" title="<?php _e('Username','arez'); ?>" autocorrect="off" autocapitalize="off" placeholder="<?php _e('Username','arez'); ?>" data-bind="value: email" />
				<input type="password" title="<?php _e('Password','arez'); ?>" placeholder="<?php _e('Password','arez'); ?>" data-bind="value: password" />
				<button data-bind="click: doShowSignup, scrollTopOnClick: true"><?php _e('Sign Up','arez'); ?></button>
				<button type="submit" data-bind="click: login"><?php _e('Log In','arez'); ?></button>
				<a class="forgot-password" href="<?php echo $wb['wb_url']; ?>#/PasswordResetRequest"><?php _e("Forgot Password?",'arez');?></a>
				<div class="alert alert-error" data-bind="text: loginError, visible: loginError"></div>
				<div><?php _e('Sign Up! Take advantage of our proprietary online booking technology and earn commissions.','arez'); ?></div>
			</form>
		</div>
		
		<div id="dash-main" data-bind="visible: WebBooker.Agent.user_id() > 0 && showMain()">
			<div class="header clearfix">
				<h2><?php _e('Commissions Chart','arez'); ?></h2>
				<div class="logout" data-bind="click: WebBooker.Agent.logout"><?php _e('Log Out','arez'); ?></div>
			</div>
				<div id="dash-commissions" class="clearfix">
					<div class="range-container clearfix">
						<div id="today" class="commission-range">Today</div>
						<div id="week" class="commission-range">This Week</div>
						<div id="month" class="commission-range selected">This Month</div>
						<div id="year" class="commission-range">This Year</div>
					</div>
						<div id="chart-container" data-bind="visible: agentCommissionsData">
						<div class="chart">
							<div class="scale"></div>
							<div class="cols"></div>
						</div>
					</div>
					<div id="dash-commissions-nodata" data-bind="visible: !agentCommissionsData()" style="display: none;">
						<p><?php _e('No data found for this chart.','arez'); ?></p>
					</div>
					<div id="commissions-stats">
						<div class="total-commissions-header"><?php _e('Total commissions for'); ?></div>
						<div class="timeframe" data-bind="text: WebBooker.comm_range"></div>
						<div class="total-commissions" data-bind="clean_money: agentCommissionsTotal"></div>
						<div class="average-commissions"><?php _e('Daily Average'); ?> (<span data-bind="clean_money: WebBooker.comm_avg"></span>)</div>
						<button title="Download CSV" data-bind="click: downLoadCSV"><?php _e('Download report', 'arez')?></button><div class="custom-range clearfix">
						<div>Custom Date Range</div>
							<div class="cal-holder">
								<input id="comm-date-start" placeholder="End Date" type="text" readonly="true" data-bind="value: agentCommissionsStartDate" />
							</div>
							<div class="cal-holder">
								<input id="comm-date-end" placeholder="End Date" type="text" readonly="true" data-bind="value: agentCommissionsEndDate" />
							</div>
						</div>
					</div>
				</div>
				<div class="header clearfix">
					<h2><?php _e('Commissions Report','arez'); ?></h2>
				</div>
				<table id="commissions-report" cellspacing="0" data-bind="visible: agentCommissionsReport">
				<thead>
					<tr>
						<th>Sale Date</th>
						<th>Sale ID</th>
						<th>Activity Name</th>
						<th>Commission Amount</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="4" cellspacing="0">
							<div>
								<table cellspacing="0">
									<tbody data-bind="foreach: agentCommissionsReport">
										<tr>
											<td data-bind="text: date, css:{negative: amount < 0}"></td>
											<td data-bind="text: saleID, css:{negative: amount < 0}"></td>
											<td data-bind="text: activityID, css:{negative: amount < 0}"></td>
											<td data-bind="money: amount, css:{negative: amount < 0}"></td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="4">Total: <span data-bind="money: agentCommissionsTotal, css:{negative: agentCommissionsTotal < 0}"></span></th>
					</tr>
				</tfoot>
			</table>
			<div data-bind="visible: !agentCommissionsReport()">
				<p><?php _e('No data to display.','arez'); ?></p>
			</div>
		</div><!-- /dash-main -->
		
		<div id="dash-signup" class="clearfix" data-bind="visible: showSignup">
			<div class="header clearfix">
				<h2><?php _e('Travel Agent Signup','arez'); ?></h2>
			</div>
			<form id="signup-form" data-bind="with: WebBooker.Agent">
				<div class="blurb"><?php _e('Are you a travel agent and sell stuff online? then sign up! This is Photoshop\'s version  of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit.', 'arez'); ?></div>
				<div class="left-side">
					<div class="required-wrapper">
						<input type="text" data-bind="value: signup_fields.first_name, css: {required: !signup_fields.first_name()}" placeholder="<?php _e('First Name','arez'); ?>" />
						<div class="required-text" data-bind="if: !signup_fields.first_name()">required</div>
					</div>
					<div class="required-wrapper">
						<input type="text" data-bind="value: signup_fields.last_name, css: {required: !signup_fields.last_name()}" placeholder="<?php _e('Last Name','arez'); ?>" />
						<div class="required-text" data-bind="if: !signup_fields.last_name()">required</div>
					</div>
					<div class="required-wrapper">
						<input type="email" data-bind="value: signup_fields.email, css: {required: !signup_fields.email()}" placeholder="<?php _e('Email','arez'); ?>" />
						<div class="required-text" data-bind="if: !signup_fields.email()">required</div>
					</div>
					<div class="required-wrapper">
						<input type="text" data-bind="value: signup_fields.arc, css: {required: !signup_fields.arc()}" placeholder="<?php _e('A.R.C. Number','arez'); ?>" />
						<div class="required-text" data-bind="if: !signup_fields.arc()">required</div>
					</div>
				</div>
				<div class="right-side">
					<div class="required-wrapper">
						<input type="text" data-bind="value: signup_fields.user_name, css: {required: !signup_fields.user_name()}" placeholder="<?php _e('User Name','arez'); ?>" />
						<div class="required-text" data-bind="if: !signup_fields.user_name()">required</div>
					</div>
					<div class="required-wrapper">
						<input type="password" placeholder="<?php _e('Password','arez'); ?>" data-bind="value: signup_fields.password, css: {required: !signup_fields.password()}" />
						<div class="required-text" data-bind="if: !signup_fields.password()">required</div>
					</div>
					<div class="required-wrapper">
						<input type="password" placeholder="<?php _e('Confirm Password','arez'); ?>" data-bind="value: signup_fields.verify_password, css: {required: !signup_fields.verify_password()}" />
						<div class="required-text" data-bind="if: !signup_fields.verify_password()">required</div>
					</div>
					<button type="reset" data-bind="click: resetSignupFields, scrollTopOnClick: true"><?php _e('Reset','arez'); ?></button>
					<button type="submit" data-bind="click: doSignup, scrollTopOnClick: true"><?php _e('Submit','arez'); ?></button>
				</div>
				<!-- ko if: signup_error -->
				<div class="signup-error" data-bind="text: signup_error"></div>
				<!-- /ko -->
			</form>
		</div><!-- /dash-signup -->
		
		<div id="dash-signup-confirm" class="dash-forms" data-bind="visible: signupSuccessMsg">
			<div class="header clearfix">
				<h2><?php _e('Sign-up Successful','arez'); ?></h2>
			</div>
			<div class="signup-congrats"><?php _e('Congratulations!','arez');?></div>
			<div><?php _e('Your travel agent sign-up is complete. You may now log in at the top of the page.', 'arez'); ?></div>
		</div>
	</div>
</div><!-- /webbooker-dashboard -->