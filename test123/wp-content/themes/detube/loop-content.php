	<?php 
		global $loop_view; 
		$ajaxload = get_option('dp_archive_ajaxload');
	?>
		
	<div class="loop-content switchable-view <?php echo $loop_view; ?>" data-view="<?php echo $loop_view; ?>" data-ajaxload=<?php echo $ajaxload; ?>>
		<div class="nag cf">
			<?php while (have_posts()) : the_post();
				get_template_part('item-video');
			endwhile; ?>
		</div>
	</div><!-- end .loop-content -->