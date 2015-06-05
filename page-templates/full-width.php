<?php
/**
 * Template Name: Full-width Page Template, No Sidebar
 *
 * Description:
 *
 * @package WordPress
 * @subpackage ArtivityRez
 * @since ArtivityRez 1.0
 */

get_header(); ?>

	<div role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'content', 'page' ); ?>
			<?php comments_template( '', true ); ?>
		<?php endwhile; // end of the loop. ?>
	</div>

<?php get_footer(); ?>