<?php
/**
 * The template for displaying featured posts on home page
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */
?>

<?php
	$args = (array)get_option('dp_home_featured');
	$args = dp_parse_query_args($args);
	$first_post_media = !empty($args['first_post_media']) ? $args['first_post_media'] : 'video';
	$ajaxload = !empty($args['ajaxload']) ? true : false;
	$autoplay = !empty($args['autoplay']) ? true : false;
	$autoscroll = !empty($args['autoscroll']) ? $args['autoscroll'] : false;

	$query = new WP_Query($args);
?>

<div class="home-featured wall">
<div class="wrap cf">

<?php if($query->have_posts()): ?>

	<?php
		/* Load scripts only when needed */
		wp_enqueue_script('jquery-carousel');
	?>

	<div class="stage" data-ajaxload="<?php echo (string)$ajaxload; ?>">
		<div class="carousel" data-autoscroll-interval="<?php echo $autoscroll; ?>">
		<div class="carousel-list">
			<?php $items = ''; $i = 0; while ($query->have_posts()) : $query->the_post(); global $post; $i++; ?>
			<div class="item <?php echo is_video() ? 'item-video' : 'item-post'; ?>" data-id="<?php the_ID(); ?>">
				<?php
					if($i == 1 && !$autoscroll && is_video($post->ID) && $first_post_media == 'video') {
						echo '<div class="video fluid-width-video-wrapper">';
							dp_video($post->ID, $autoplay);
						echo '</div>';
					} 
				?>
				
				<?php	
					dp_thumb_html('custom-large');
				?>
			
				<div class="caption"<?php if(empty($args['autoscroll']) && is_video($post->ID) && $i == 1) echo 'style="display:none;"'; ?>>
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php printf(__('Permalink to %s', 'dp'), get_the_title()); ?>"><?php the_title(); ?></a></h2>
				</div>
			</div><!-- end .item -->
			<?php endwhile; ?>
		</div><!-- end .carousel-list -->
		</div><!-- end .carousel -->
	</div><!-- end .stage -->
		
	<div class="nav">
	<div class="carousel">
		<div class="carousel-clip">
			<ul class="carousel-list">
				<?php $items = ''; $i = 0; while ($query->have_posts()) : $query->the_post(); global $post; ?>
				<li data-id="<?php the_ID(); ?>" class="<?php echo is_video() ? 'item-video' : 'item-post'; ?>">
				<div class="inner">
					<?php
					$thumb_size = 'custom-small';
					dp_thumb_html($thumb_size);
					?>
			
					<div class="data">
						<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php printf(__('Permalink to %s', 'dp'), get_the_title()); ?>"><?php the_title(); ?></a></h2>
			
						<p class="meta">
							<span class="time"><?php printf(__('%s ago', 'dp'), relative_time(get_post_time('U', true))); ?></span>
						</p>
					</div>
				</div>
				</li>
				<?php $i++; endwhile; ?>
			</ul>
		</div><!-- end .carousel-clip -->
		
		<a class="carousel-prev" href="#"></a>
		<a class="carousel-next" href="#"></a>
	</div><!-- end .carousel -->
	</div><!-- end .nav -->

<?php endif; wp_reset_query(); ?>

</div><!-- end .wrap -->
</div><!-- end #wall -->