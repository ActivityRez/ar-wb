<?php global $wbSlim; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<script id="wb_bootstrapper" type="text/javascript">
		var wb_global_vars = <?php echo json_encode($wbSlim);?>;
		if(typeof console == 'undefined'){
			window.console = { log: function(){} };
		}
	</script>
	
	<?php require_once('wp-content/themes/ActivityRez/inc/header_meta.php'); ?>
	
	<?php
		// Queue your styles.
		global $wb;
		global $wp_styles;
		include_once('datepicker.php');
		global $jqueryLang;
		if(isset($jqueryLang[$wb['i18n']])){
			echo "<script type='text/javascript' src='".ACTIVITYREZWB_PLUGIN_PATH.'js/jquery-ui/jquery.ui.datepicker-'.$jqueryLang[$wb['i18n']].".js'></script>\n";
		}
?>
</head>

<?php require_once('wp-content/themes/ActivityRez/inc/header_begin.php'); ?>
