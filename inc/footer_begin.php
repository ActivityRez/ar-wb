<div id="footer" role="contentinfo">
	<div class="darken">
		<div id="footer-inner">
			<div class="container clearfix">
				<div id="footer-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( get_bloginfo( 'name' ), 'blankslate' ); ?>" rel="home"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a></div>			
				<ul class="footer-nav clearfix">
					<li><span>Company</span>
						<?php wp_nav_menu( array( 'theme_location' => 'footer-menu' ) ); ?>
					</li>
					<li><span>Partnerships</span>
					</li>
					<li><span>Languages</span>
						<ul data-bind="foreach: WebBooker.available_langs">
							<li><a href="#" data-bind="text: title, click: function(item){WebBooker.selectedLanguage(item)}"></a></li>
						</ul>
					</li>
					<li id="footer-contact"><span>Got Questions?</span>
						<?php if(get_theme_mod('phone_number_setting')){ ?>
							<h6>Give us a call</h6>
							<h5><?php echo get_theme_mod('phone_number_setting'); ?></h5>
						<?php } if(get_theme_mod('support_email_setting')){ ?>
							<h6>Or drop us a line</h6>
							<h5><a href="mailto: <?php echo get_theme_mod('support_email_setting'); ?>"><?php echo get_theme_mod('support_email_setting'); ?></a></h5>
						<?php }?>
						
						<div id="footer-social" class="clearfix">
							<?php if(get_theme_mod('company_facebook_setting')){ ?>
								<a class="social-icon facebook" href="<?php echo get_theme_mod('company_facebook_setting'); ?>" target="_blank">FaceBook</a>
							<?php } if(get_theme_mod('company_twitter_setting')){ ?>
								<a class="social-icon twitter" href="<?php echo get_theme_mod('company_twitter_setting'); ?>" target="_blank">Twitter</a>
							<?php } if(get_theme_mod('company_gplus_setting')){ ?>
								<a class="social-icon gplus" href="<?php echo get_theme_mod('company_gplus_setting'); ?>" target="_blank">Google +</a>
							<?php } if(get_theme_mod('company_linkedin_setting')){ ?>
								<a class="social-icon linkedin" href="<?php echo get_theme_mod('company_linkedin_setting'); ?>" target="_blank">LinkedIn</a>
							<?php } ?>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<div class="container">
			<div id="pb_arez">Powered By <a href="https://www.activityrez.com/?utm_source=booking+engine&utm_medium=referral&utm_campaign=powered+by">ActivityRez</a></div>
			<?php if(get_theme_mod('company_legal_setting')){ ?>
				<div id="legal"><?php echo get_theme_mod('company_legal_setting'); ?></div>
			<?php } ?>
		</div>
	</div>
</div>