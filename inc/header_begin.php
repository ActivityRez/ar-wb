<body <?php body_class(); ?>>
	<div id="page-loading">
		<div></div>
	</div>
	<div id="header" role="banner">
		<div id="toolbar">
			<div class="container clearfix">
				<div id="multi-everything" class="clearfix">
					<div id="lang-picker" data-bind="if: WebBooker.available_langs().length > 1">
						<select data-bind="options: WebBooker.available_langs,optionsCaption:'<?php _e('Language','arez'); ?>',value:WebBooker.selectedLanguage,optionsText: 'title'"></select>
					</div>
					<div id="curr-picker" data-bind="if: WebBooker.available_currencies().length > 1">
						<select data-bind="options: WebBooker.available_currencies, optionsCaption:'<?php _e('Currency','arez'); ?>', value: WebBooker.selectedCurrency, optionsText: 'symbol'"></select>
					</div>
				</div>
				<div id="header-cart" data-bind="with: WebBooker.Cart">
					<a data-bind="money: WebBooker.Cart.subtotal()" href="/wb/<?php echo $wb['name']; ?>/#/Checkout"></a>
				</div>
				<div id="header-login" data-bind="with: WebBooker.Agent">
					<!-- ko if: !user_id() || user_id() == 0 -->
					<a href="<?php echo $wb['wb_url']; ?>/#/Dashboard/signup"><?php _e('Sign Up','arez'); ?></a>
					<form data-bind="submit: login">
						<label><?php _e('Login','arez'); ?></label>
						<input type="text" placeholder="<?php _e('Username','arez'); ?>" autocorrect="off" autocapitalize="off" data-bind="value: email" />
						<input type="password" autocomplete="off" placeholder="<?php _e('Password','arez'); ?>" data-bind="value: password" />
						<input class="hidden-submit" type="submit" />
					</form>
					<!-- /ko -->
					<!-- ko if: user_id() > 0 -->
					<div class="user-logged-in" data-bind="click: function(){window.location = '<?php echo $wb['wb_url']; ?>/#/Dashboard'}"><?php _e('Welcome back', 'arez'); ?> <span data-bind="text: name"></span>!</div>
					<!-- /ko -->
				</div>
			</div>
		</div>
		<div id="main-header" class="container clearfix">
			<h1 id="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( get_bloginfo( 'name' ), 'blankslate' ); ?>" rel="home"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a></h1>
			<div id="log-in-out" data-bind="with: WebBooker.Agent">
				<!-- ko if: !user_id() || user_id() == 0 -->
				<div data-bind="click: function(){window.location = '<?php echo $wb['wb_url']; ?>/#/Dashboard'}"><?php _e('Log In', 'arez'); ?></div>
				<!-- /ko -->
				<!-- ko if: user_id() > 0 -->
				<div data-bind="click: WebBooker.Agent.logout"><?php _e('Sign Out', 'arez'); ?></div>
				<!-- /ko -->
			</div>
			<div id="menu" role="navigation">
				<div id="header-search">
					<?php get_search_form(); ?>
				</div>
				<?php wp_nav_menu( array( 'theme_location' => 'main-menu' ) ); ?>
			</div>
		</div>
		<?php
			if(!is_front_page())
				require_once('wp-content/themes/ActivityRez/inc/header_search.php');
		?>
		<script>
			if(window.location.hash === "#/Home" && jQuery('#header-mini-search').length){
				jQuery('#header-mini-search').hide();
			}
			jQuery(window).on('hashchange', function() {
				if(window.location.hash === "#/Home" && jQuery('#header-mini-search').length){
					jQuery('#header-mini-search').hide();
				}
				else{
					jQuery('#header-mini-search').show();
				}
			});
		</script>
	</div>