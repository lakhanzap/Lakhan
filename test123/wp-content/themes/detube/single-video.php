<?php
/**
 * The Template for displaying all single video posts with standard layout.
 *
 * @package deTube
 * @subpackage Template
 * @since deTbue 1.1
 */

get_header(); 

$info_toggle = (int)get_option('dp_info_toggle');
?>

<div id="main"><div class="wrap cf">
	
	<div class="entry-header cf">
	<div class="inner cf">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	
		<?php dp_post_actions($post->ID); ?>
	</div><!-- end .entry-header>.inner -->
	</div><!-- end .entry-header -->
	
	<div id="content" role="main">
		<?php while (have_posts()) : the_post(); global $post;?>
		
		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
		
		<div id="video">
			<div class="screen fluid-width-video-wrapper">
				<?php dp_video($post->ID, get_option('dp_single_video_autoplay')); ?>
			</div><!-- end .screen -->
		</div><!-- end #video-->
		
		<div id="details" class="section-box">
			<div class="section-content">
			<div id="info"<?php if(!empty($info_toggle)) echo ' class="" data-less-height="'.$info_toggle.'"'; ?>>
				<p class="entry-meta">
					<span class="author"><?php _e('Added by', 'dp'); ?> <?php the_author_posts_link(); ?></span>
					<span class="time"><?php _e('on', 'dp'); ?> <?php the_date(); ?></span>
					
					<?php edit_post_link(__('Edit', 'dp'), ' <span class="sep">/</span> '); ?>
				</p>

				<div class="entry-content rich-content">
					<?php the_content(); ?>
					<?php wp_link_pages(array('before' => '<p class="entry-nav pag-nav"><span>'.__('Pages:', 'dp').'</span> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				</div><!-- end .entry-content -->
			
				<div id="extras">
					<h4><?php _e('Category:', 'dp'); ?></h4> <?php the_category(', '); ?>
					<?php the_tags('<h4>'.__('Tags:', 'dp').'</h4>', ', ', ''); ?>
				</div>
			</div><!-- end #info -->
			</div><!-- end .section-content -->
			
			<?php if($info_toggle > 0) { ?>
			<div class="info-toggle">
				<a href="#" class="info-toggle-button info-more-button">
					<span class="more-text"><?php _e('Show more', 'dp'); ?></span> 
					<span class="less-text"><?php _e('Show less', 'dp'); ?></span>
				</a>
			</div>
			<?php } ?>
		</div><!--end #deatils-->
		
		</div><!-- end #post-<?php the_ID(); ?> -->
		
		<?php 
			dp_related_posts(array(
				'number'=>get_option('dp_related_posts'), 
				'view'=>get_option('dp_related_posts_view', 'grid-mini')
			)); 
		?>

        <?php comments_template('', true); ?>

		<?php endwhile; ?>
	</div><!-- end #content -->

	<?php get_sidebar(); ?>

</div></div><!-- end #main -->
	
<?php get_footer(); ?>