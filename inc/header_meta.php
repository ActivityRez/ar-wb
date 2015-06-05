<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="description" content="<?php echo __("Conveniently plan and book Activities or discover the best attractions for you vacation!",'arez');?>">
<?php if (is_search()) { echo '<meta name="robots" content="noindex, nofollow" />'; } ?>
<link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/images/favicon.ico" type="image/x-icon" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<title><?php wp_title( ' | ', true, 'right' ); ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); ?>" />
<?php wp_head(); ?>
<script>
	jQuery(document).ready(function(){
		jQuery('#page-loading').hide();
	});
</script>

<?php if(isset($wb['style'])) {?>
<style>
	<?php echo $wb['style']; ?>
</style>
<?php }; ?>
