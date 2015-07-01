<?php
function getBrowserMore() 
{ 
    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
    
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Internet Explorer'; 
        $ub = "MSIE"; 
    } 
    elseif(preg_match('/Firefox/i',$u_agent)) 
    { 
        $bname = 'Mozilla Firefox'; 
        $ub = "Firefox"; 
    } 
    elseif(preg_match('/Chrome/i',$u_agent)) 
    { 
        $bname = 'Google Chrome'; 
        $ub = "Chrome"; 
    } 
    elseif(preg_match('/Safari/i',$u_agent)) 
    { 
        $bname = 'Apple Safari'; 
        $ub = "Safari"; 
    } 
    elseif(preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Opera'; 
        $ub = "Opera"; 
    } 
    elseif(preg_match('/Netscape/i',$u_agent)) 
    { 
        $bname = 'Netscape'; 
        $ub = "Netscape"; 
    } 
    
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
    
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}

add_action('after_setup_theme', 'blankslate_setup');
function blankslate_setup(){
    load_theme_textdomain('blankslate', get_template_directory() . '/languages');
    add_theme_support('automatic-feed-links');
    add_theme_support('post-thumbnails');
    global $content_width;
    if (!isset($content_width))
        $content_width = 640;
    register_nav_menus(array(
        'main-menu' => __('Main Menu', 'blankslate'),
        'footer-menu' => __('Footer Navigation', 'blankslate')
    ));
}

add_action('wp_enqueue_scripts', 'blankslate_load_scripts', 100);
function blankslate_load_scripts(){
    //wp_enqueue_script('jquery');
    
	//if(!wp_script_is('ar-webbooker', 'enqueued'))
	//    	wp_enqueue_script('ar-webbooker',ACTIVITYREZWB_PLUGIN_PATH.'js/app/webbooker.min.js',array('jquery','jquery-ui-datepicker'));
    if(is_page_template('page-templates/cats.php')){
		wp_enqueue_script('kojs', '//cdnjs.cloudflare.com/ajax/libs/knockout/3.3.0/knockout-min.js');
		wp_enqueue_script('ar-core', get_template_directory_uri() . '/js/ar-core.js');
	}else{
		wp_enqueue_script('main', get_template_directory_uri() . '/js/main.js');
	}
    wp_enqueue_script('bx-slider', get_template_directory_uri() . '/js/jquery.bxslider/jquery.bxslider.min.js');
    //wp_enqueue_script('gmaps', "https://maps.google.com/maps/api/js?sensor=true&ver=3.9");
    wp_deregister_style('ar-vendor');
    wp_deregister_style('ar');
}

add_action('comment_form_before', 'blankslate_enqueue_comment_reply_script');
function blankslate_enqueue_comment_reply_script(){
    if (get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

add_filter('the_title', 'blankslate_title');
function blankslate_title($title){
    if ($title == '') {
        return '&rarr;';
    } else {
        return $title;
    }
}

add_filter('wp_title', 'blankslate_filter_wp_title');
function blankslate_filter_wp_title($title){
    return $title . esc_attr(get_bloginfo('name'));
}

add_action('widgets_init', 'blankslate_widgets_init');
function blankslate_widgets_init(){
    register_sidebar(array(
        'name' => __('Sidebar Widget Area', 'blankslate'),
        'id' => 'primary-widget-area',
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => "</li>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));
    
    register_sidebar(array(
        'name' => __('Activity Widget Area', 'blankslate'),
        'id' => 'activity-widget-area',
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => "</li>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));
}

function blankslate_custom_pings($comment){
    $GLOBALS['comment'] = $comment;?>
	<li <?phpcomment_class();?> id="li-comment-<?php comment_ID(); ?>"><?php
    	echo comment_author_link();
 ?></li>
<?php }

add_filter('get_comments_number', 'blankslate_comments_number');
function blankslate_comments_number($count){
    if (!is_admin()) {
        global $id;
        $comments_by_type =& separate_comments(get_comments('status=approve&post_id=' . $id));
        return count($comments_by_type['comment']);
    } else {
        return $count;
    }
}

function arez_customize_register($wp_customize){
    $wp_customize->add_setting('primary_color', array(
        'default' => '#5674b9',
        'transport' => 'refresh'
    ));
    
    $wp_customize->add_setting('secondary_color', array(
        'default' => '#678631',
        'transport' => 'refresh'
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color_control', array(
        'label' => __('Primary Color', 'blankslate'),
        'section' => 'colors',
        'settings' => 'primary_color'
    )));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_color_control', array(
        'label' => __('Secondary Color', 'blankslate'),
        'section' => 'colors',
        'settings' => 'secondary_color'
    )));
    
    $wp_customize->add_setting('logo', array(
        'default' => get_template_directory_uri() . '/wp-content/themes/ActivityRez/images/logo.png',
        'transport' => 'refresh'
    ));
    
    $wp_customize->add_section('logo_section', array(
        'title' => __('Company Logo', 'blankslate'),
        'priority' => 30
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'logo_control', array(
        'label' => __('Upload a logo', 'blankslate'),
        'section' => 'logo_section',
        'settings' => 'logo'
    )));
    
    $wp_customize->add_section('social_proof_section', array(
        'title' => __('Social Proof', 'blankslate'),
        'priority' => 30
    ));
    
    $wp_customize->add_setting('social_proof_image', array(
        'default' => get_template_directory_uri() . '/wp-content/themes/ActivityRez/images/social-proof-default.jpg',
        'transport' => 'refresh'
    ));
    
    $wp_customize->add_setting('social_proof_quote', array(
        'default' => '',
        'transport' => 'refresh'
    ));
    
    $wp_customize->add_setting('social_proof_person', array(
        'default' => '',
        'transport' => 'refresh'
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'social_proof_image_control', array(
        'label' => __('Upload A User Image', 'blankslate'),
        'section' => 'social_proof_section',
        'settings' => 'social_proof_image'
    )));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'social_proof_quote_control', array(
        'label' => __('Quote', 'blankslate'),
        'section' => 'social_proof_section',
        'settings' => 'social_proof_quote'
    )));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'social_proof_person_control', array(
        'label' => __('Quote', 'blankslate'),
        'section' => 'social_proof_section',
        'settings' => 'social_proof_person'
    )));
    
    $wp_customize->add_section('banner_section', array(
        'title' => __('Homepage Banner', 'blankslate'),
        'priority' => 30
    ));
    
	$wp_customize->add_section('company_info', array(
        'title' => __('Company Information', 'blankslate'),
        'priority' => 30
    ));
    
	$wp_customize->add_setting('phone_number_setting', array(
    	'default' => '',
		'transport' => 'refresh'
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'phone_number_control', array(
        'label' => __('Company Phone Number', 'blankslate'),
        'section' => 'company_info',
        'settings' => 'phone_number_setting'
    )));
    
    $wp_customize->add_setting('support_email_setting', array(
    	'default' => '',
		'transport' => 'refresh'
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'support_email_control', array(
        'label' => __('Support Email', 'blankslate'),
        'section' => 'company_info',
        'settings' => 'support_email_setting'
    )));
    
    $wp_customize->add_setting('company_gplus_setting', array(
    	'default' => 'http://',
		'transport' => 'refresh'
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'company_gplus_control', array(
        'label' => __('Company Google+', 'blankslate'),
        'section' => 'company_info',
        'settings' => 'company_gplus_setting'
    )));
    
    $wp_customize->add_setting('company_twitter_setting', array(
    	'default' => 'http://',
		'transport' => 'refresh'
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'company_twitter_control', array(
        'label' => __('Company Twitter', 'blankslate'),
        'section' => 'company_info',
        'settings' => 'company_twitter_setting'
    )));
    
    $wp_customize->add_setting('company_facebook_setting', array(
    	'default' => 'http://',
		'transport' => 'refresh'
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'company_facebook_control', array(
        'label' => __('Company FaceBook', 'blankslate'),
        'section' => 'company_info',
        'settings' => 'company_facebook_setting'
    )));
    
    $wp_customize->add_setting('company_legal_setting', array(
    	'default' => '',
		'transport' => 'refresh'
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'company_legal_control', array(
        'label' => __('Legal', 'blankslate'),
        'section' => 'company_info',
        'settings' => 'company_legal_setting'
    )));
    
    $wp_customize->add_setting('company_linkedin_setting', array(
    	'default' => 'http://',
		'transport' => 'refresh'
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'company_linkedin_control', array(
        'label' => __('Company LinkedIn', 'blankslate'),
        'section' => 'company_info',
        'settings' => 'company_linkedin_setting'
    )));
    
    $banner_images = 3;
    for ($i = 0; $i < $banner_images; $i++) {
        $wp_customize->add_setting('banner_image_' . $i, array(
            'default' => get_template_directory_uri() . '/wp-content/themes/ActivityRez/images/banner.jpg',
            'transport' => 'refresh'
        ));
        
        $wp_customize->add_setting('banner_title_' . $i, array(
            'default' => '',
            'transport' => 'refresh'
        ));
        
        $wp_customize->add_setting('banner_desc_' . $i, array(
            'default' => '',
            'transport' => 'refresh'
        ));
        
        $wp_customize->add_setting('banner_link_' . $i, array(
            'default' => '',
            'transport' => 'refresh'
        ));
        
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'banner_image_control_' . $i, array(
            'label' => __('Activity ' . ($i + 1) . ' Image', 'blankslate'),
            'section' => 'banner_section',
            'settings' => 'banner_image_' . $i,
            'priority' => $i * 7
        )));
        
        $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'banner_title_control_' . $i, array(
            'label' => __('Activity ' . ($i + 1) . ' Title', 'blankslate'),
            'section' => 'banner_section',
            'settings' => 'banner_title_' . $i,
            'priority' => $i * 7 + 1
        )));
        
        $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'banner_desc_control_' . $i, array(
            'label' => __('Activity ' . ($i + 1) . ' Description', 'blankslate'),
            'section' => 'banner_section',
            'settings' => 'banner_desc_' . $i,
            'priority' => $i * 7 + 2
        )));
        
        $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'banner_link_control_' . $i, array(
            'label' => __('Activity ' . ($i + 1) . ' Link', 'blankslate'),
            'section' => 'banner_section',
            'settings' => 'banner_link_' . $i,
            'priority' => $i * 7 + 3
        )));
    }
}
add_action('customize_register', 'arez_customize_register');

function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
    $rgbArray = array();
    if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
        return false; //Invalid hex color code
    }
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
}

function arez_customize_css_more(){
		$hexRGB = hex2RGB(get_theme_mod('secondary_color'));
		$browser = getBrowserMore();
?>
         <style type="text/css">
         	 .chart .cols .val-container .val{background-color: <?php echo get_theme_mod('primary_color'); ?>;}
         	 .chart .cols .val-container .val:nth-of-type(3){background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #header{ background:<?php echo get_theme_mod('primary_color'); ?>; }
             
			 <?php if (get_theme_mod('logo')) { ?> 
         	 #header #main-header #site-title a{
         		 background-image: url(<?php echo get_theme_mod('logo'); ?>);
		 		 background-size: contain;
		 	 }
		 	 <?php } ?>
		 	 <?php if (get_theme_mod('logo')) { ?> 
         	 #footer .darken #footer-inner #footer-logo a{
         		 background-image: url(<?php echo get_theme_mod('logo'); ?>);
		 		 background-size: contain;
		 	 }
		 	 <?php } ?>
		 	 #header #header-mini-search{border-bottom-color: <?php echo get_theme_mod('secondary_color'); ?>;}
		 	 #header #header-mini-search button{background-color: <?php echo get_theme_mod('secondary_color'); ?>; }
		 	 #webbooker-home #home-banner .container.search_wrap #home-main-search{border-color: <?php echo get_theme_mod('secondary_color'); ?>;}
		 	 #webbooker-home #home-main #sidebar h3{ background-color: <?php echo get_theme_mod('primary_color'); ?>;}
		 	 #webbooker-home #home-banner .container.search_wrap #home-main-search button{ background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-home #quick-tags{border-bottom-color: <?php echo get_theme_mod('secondary_color'); ?>; }
             #webbooker-home #home-banner #home-main-search{border-color: <?php echo get_theme_mod('secondary_color'); ?>; }
             #webbooker-home #home-banner #home-main-search button{background-color: <?php echo get_theme_mod('secondary_color'); ?>; }
             .cal-holder .calendar:before{border-bottom-color: <?php echo get_theme_mod('primary_color'); ?>;}
             .cal-holder .calendar .cal-header{ background-color: <?php echo get_theme_mod('primary_color'); ?>;}
             .cal-holder .calendar .body .day.selected{ background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             .steps .step span{ background-color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-home #home-main #featured-activities .featured-destination .featured-items li > a{border-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-home #home-main #featured-activities .featured-destination .featured-items li .feat-item-title{ background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-home #social-proof{ background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-home #home-main #featured-activities .featured-destination .featured-items li .feat-item-desc a{color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-search #webbooker-search-results .results .activity .activity-thumbnail{border-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-search #webbooker-search-results .results .activity .activity-price .amount{color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-search #webbooker-search-results .results .activity .activity-price a{background-color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-search #webbooker-search-footer button{border-top-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-search #webbooker-search-footer button:before{border-top-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #page-taxonomy #taxonomy-results .activity .activity-thumbnail{border-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #page-taxonomy #taxonomy-results .activity .activity-price .amount{color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #page-taxonomy #taxonomy-results .activity .activity-price a{background-color: <?php echo get_theme_mod('primary_color'); ?>;}
             #page-taxonomy .navigation a{background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-activity #activity-header .activity-duration{color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-activity #webbooker-activity-media{ background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-activity #activity-guest-add #activity-datepicker .calendar .days{background-color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-activity #activity-guest-add #activity-datepicker .calendar .body .day.selected{background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-activity #activity-guest-add #activity-checkout #activity-cart .subtotal span{color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-activity #activity-guest-add #activity-checkout #activity-cart button{background-color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-activity .child-activity .activity-thumbnail{border-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-activity .child-activity .activity-price .amount{color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-activity .child-activity .activity-price a{background-color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-checkout #lead-guest-info h3{background-color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-checkout #checkout-sidebar #sale-summary div.total{color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-checkout #checkout-sidebar #sale-summary button{background-color: <?php echo get_theme_mod('secondary_color'); ?>; }
             #webbooker-checkout #checkout-activities .activity .activity-header{background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-checkout #checkout-activities .activity .guest .guest-options-container .options-header{background-color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-checkout #checkout-activities .activity .guest .guest-options-container .transportation .transportation-header{background-color: <?php echo get_theme_mod('primary_color'); ?>;}
             .has-typeahead ~ ul.typeahead li.active{background-color: <?php echo get_theme_mod('primary_color'); ?>;}
             .has-typeahead ~ ul.typeahead li:hover{background-color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-checkout #checkout-header a{ color: <?php echo get_theme_mod('primary_color'); ?>; }
             #webbooker-checkout #checkout-activities .activity .guest .guest-options-container .transportation .transportation-options .pickups li label .money{color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-checkout #checkout-activities .activity .guest .guest-options-container .transportation .transportation-options .pickups li .pickup-extended .pickup-instructions{color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-checkout #checkout-payment #payment-summary #cancellation-notice a{color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-checkout #checkout-payment #payment-summary #purchases .item .title{color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-checkout #checkout-payment #payment-summary #payments .total{color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-checkout #checkout-payment #payment-summary #payments label a{color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-checkout #checkout-payment #payment-summary #payments button{background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-confirmation #congratulations{background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-confirmation #reservation-number{color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-confirmation #reserved-activities .activity .special-instructions{color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-confirmation #payments button{background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-dashboard #dash-signup #signup-form button[type="submit"]{background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-dashboard #dash-signup-confirm .signup-congrats{color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-dashboard #dash-login #login-form button[type="submit"]{background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
			 #webbooker-dashboard .header .logout{color: <?php echo get_theme_mod('primary_color'); ?>;}
			 #webbooker-dashboard #dash-main #dash-commissions .range-container .commission-range.selected{background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
			 #webbooker-dashboard #dash-main #commissions-stats .total-commissions{color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-dashboard #dash-main #commissions-stats button{background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #passwordResetRequest button, #passwordReset button{background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #footer{ background:<?php echo get_theme_mod('primary_color'); ?>; }
             #webbooker-modals{border-left-color: <?php echo get_theme_mod('primary_color'); ?>;}
             #webbooker-modals .modal .buttons button:nth-child(2){background-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-modals .modal#checkout-processing .progress{border-color: <?php echo get_theme_mod('secondary_color'); ?>;}
             #webbooker-modals .modal#checkout-processing .progress .bar{
	             background-image: -webkit-gradient(linear, 0 0, 100% 100%, color-stop(0.25, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2)), color-stop(0.75, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2)), color-stop(0.75, transparent), to(transparent));
	             
				background-image: -webkit-linear-gradient(-45deg, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2) 25%, transparent 25%, transparent 50%, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2) 50%, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2) 75%, transparent 75%, transparent);
				
				background-image: -moz-linear-gradient(-45deg, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2) 25%, transparent 25%, transparent 50%, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2) 50%, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2) 75%, transparent 75%, transparent);
				
				background-image: -ms-linear-gradient(-45deg, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2) 25%, transparent 25%, transparent 50%, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2) 50%, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2) 75%, transparent 75%, transparent);
				
				background-image: -o-linear-gradient(-45deg, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2) 25%, transparent 25%, transparent 50%, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2) 50%, rgba(<?php echo $hexRGB['red']; ?>, <?php echo $hexRGB['green']; ?>, <?php echo $hexRGB['blue']; ?>, .2) 75%, transparent 75%, transparent);
			}
			
			<?php if( (strpos($browser['name'], 'Chrome') != false && $browser['version'] >= '35') || (strpos($browser['name'], 'Firefox') != false && $browser['version'] >= '31')){ ?>
				#webbooker-home #social-proof, #webbooker-activity #webbooker-activity-media, #webbooker-confirmation #congratulations{
					background-image: url(<?php echo get_template_directory_uri() ?>/images/wood-pattern.jpg);
					background-blend-mode: color-burn;
				}
			<?php } ?>
			@media (max-width: 979px){
				#webbooker-home #home-main #sidebar h3{background: none; color: #5d5d5d;}
			}
         </style>
<?php }
add_action('wp_head', 'arez_customize_css_more');
add_filter('show_admin_bar', '__return_false');
remove_action('wp_head','print_emoji_detection_script',7);
remove_action('admin_print_scripts','print_emoji_detection_script');
remove_action('wp_print_styles','print_emoji_styles');
remove_action('admin_print_styles','print_emoji_styles');
