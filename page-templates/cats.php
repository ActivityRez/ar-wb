<?php
/**
 * Template Name: Categories Page Template, No Sidebar
 *
 *
 * @package Wordpress
 * @subpackage ActivityRez
 * @since Twenty Twelve 1.0
 */

$custom_fields = get_post_custom();
get_header();
?>
	<div id="primary" class="site-content">
		<div id="content" role="main">
			<div id="page-category" class="container">
			<?php foreach($custom_fields['activity_category'] as $k=>$my_custom_field){ ?>
				<script>
					var search_model_<?php echo $k+1; ?> = $ar.models.create('activity_search',{
					    api: $ar.api.searchActivities,
					}),
					
					search_view_<?php echo $k+1; ?> = $ar.views.create('basic_search',search_model_<?php echo $k+1; ?>);
				</script>
				<h1 class="categoryTitle"><?php echo $my_custom_field ?></h1>
				
				<div class="clearfix categorySection-<?php echo $my_custom_field; ?>" data-bind="foreach: items">
		            <div class="categoryTour clearfix">
		                <a data-bind="attr: {href: url}" class="categoryImgWrap">
		                    <img data-bind="attr: { src: thumbnail_url, alt: title}" />
		                </a>
		                <h4 data-bind="html: title"></h4>
		                <a class="button buttonRed" data-bind="attr: {href: url}">Prices From $<span data-bind="html: display_price.toFixed(2)"></span></a>
		                <p data-bind="text: shortDesc"></p>
		            </div>
		        </div>
		        <script type="text/javascript">
					jQuery(document).ready(function(){
						ko.applyBindings(search_view_<?php echo $k+1; ?>, $ar.dom('.categorySection-<?php echo $my_custom_field; ?>')[0]);
						search_model_<?php echo $k+1; ?>.serialize({
							featured: false,
							tags: [],
							categories: [],
							moods: [],
							keywords: '<?php echo $my_custom_field; ?>',
							destinations: [],
							sorts: [{
						        id: 1,
						        sort: 'title',
						        sort_dir: 'asc',
						        selected: true,
						        label: 'Alphabetical: A to Z'
						    }]
						});
						search_view_<?php echo $k+1; ?>.search();
					});
				</script>
	        <?php } ?>
	        <h4 class="searchMore"><a href="/cms/wb/digital-medium/#/Search">More Tours</a></h4>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
				<?php comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>
			</div>
		</div><!-- #content -->
	</div><!-- #primary -->
<?php get_footer(); ?>