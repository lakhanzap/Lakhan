<?php
/**
 * Index Template
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */

get_header(); ?>

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