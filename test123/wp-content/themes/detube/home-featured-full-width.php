<?php
/**
 * The template for displaying featured posts on home page
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.1
 */
?>

<?php
	$args = (array)get_option('dp_home_featured');
	$args = dp_parse_query_args($args);
	$first_post_media = !empty($args['first_post_media']) ? $args['first_post_media'] : 'video';
	$ajaxload = !empty($args['ajaxload']) ? true : false;
	$autoplay = !empty($args['autoplay']) ? true : false;
	$autoscroll = !empty($args['autoscroll']) ? $args['autoscroll'] : false;
	if(!empty($args['autoscroll'])) {
		$autoplay = false;
		$ajaxload = false;
	}

	$query = new WP_Query($args);
?>
	
<?php if($query->have_posts()): ?>
	<?php
		/* Load scripts only when needed */
		wp_enqueue_script('jquery-carousel');
	?>
		
<div class="home-featured-full wall">
	
	<div class="stage-header cf">
		<?php $items = ''; $i = 0;
		while ($query->have_posts()) : $query->the_post(); global $post; $i++; ?>
		<div class="entry-header wrap cf"<?php if($i > 1) echo ' style="display:none;"'; ?> data-id="<?php the_ID(); ?>">
				<h1 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
	
				<?php dp_post_actions($post->ID); ?>
			</div><!-- end .entry-header -->
	
		<?php endwhile; ?>
	</div>
	
	<div class="stage wrap cf" data-ajaxload="<?php echo (string)$ajaxload; ?>">
	
	
	
		<div class="carousel" data-autoscroll-interval="<?php echo $autoscroll; ?>">
		<div class="carousel-list">
	<?php
		$items = ''; $i = 0;
		while ($query->have_posts()) : $query->the_post(); global $post; $i++; ?>
		<div class="item <?php echo is_video() ? 'item-video' : 'item-post'; ?>" data-id="<?php the_ID(); ?>" id="item-<?php the_ID(); ?>">	
			<div class="entry-header cf" style="display:none;">
				<h1 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
	
				<?php dp_post_actions($post->ID); ?>
			</div><!-- end .entry-header -->
				
			<div class="screen">
				<?php
					if($i == 1 && !$autoscroll && is_video($post->ID) && $first_post_media == 'video') {
						echo '<div class="video fluid-width-video-wrapper" data-ratio="16:9">';
							dp_video($post->ID, $autoplay);
						echo '</div>';
					} 
				?>
				
				<?php dp_thumb_html('custom-full'); ?>
			</div>
			
		</div><!-- end .item -->
		<?php endwhile; ?>
		</div><!-- end .carousel-list -->
		</div><!-- end .carousel -->
	</div><!-- end .stage -->
	
	<div class="nav">
		<?php // Output carousel ?>
		<div class="carousel fcarousel fcarousel-6 wrap cf">
		<div class="carousel-container">
			<div class="carousel-clip">
				<ul class="carousel-list">
				<?php $items = ''; $i = 0; while ($query->have_posts()) : $query->the_post(); global $post; $i++; ?>
				
				<?php
				/* Get carousel items
			 *============================================*/
			
			// Get Thumbnail html
			$thumb_html = dp_thumb_html('custom-small', '', '', false);
			
			// Build classname
			$classes = array('item');
			$classes[] = ($i == 1) ? 'current' : ''; // Add 'current' class to first post
			$classes[] = is_video() ? 'item-video' : 'item-post'; // Add item form class
			$class = implode(' ', $classes);
			
			echo '<li class="'.$class.'">'.$thumb_html.'</li>';
				?>
				<?php endwhile; ?>
				</ul>
			</div><!-- end .carousel-clip -->
			
			<a class="carousel-prev" href="#"></a>
			<a class="carousel-next" href="#"></a>
		</div><!-- end .carousel-container -->
		</div><!-- end .carousel -->
	</div><!-- end .nav -->

</div><!-- end #wall -->
<?php endif; wp_reset_query(); ?>