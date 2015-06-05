<?php
/**
 * Template Name: Front Page Template
 *
 * Description:
 *
 * @package WordPress
 * @subpackage ActivityRez
 * @since ActivityRez 1.0
 */

get_header(); ?>

	<div role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'content', 'page' ); ?>
			<?php comments_template( '', true ); ?>
		<?php endwhile; // end of the loop. ?>
	</div><!-- #primary -->

<?php get_footer(); ?>