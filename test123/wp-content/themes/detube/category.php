<?php
/**
 * Category Template
 *
 * The template for displaying Category Archive pages.
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */

get_header(); ?>

<?php get_template_part('cat-featured'); ?>

<div id="main"><div class="wrap cf">
	
	<div id="content" role="main">
	<?php 
		get_template_part('loop-header');
	
		if (have_posts()) :
			get_template_part('loop-actions');
			get_template_part('loop-content');
			get_template_part('loop-nav');
		else :
			get_template_part('loop-error');
		endif; 
	?>
	</div><!-- end #content -->

	<?php get_sidebar(); ?>

</div></div><!-- end #main -->

<?php get_footer(); ?>