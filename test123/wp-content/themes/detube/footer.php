<?php
/**
 * Footer Template 
 *
 * The footer template is generally used on every page of your site. Nearly all other
 * templates call it somewhere near the bottom of the file. It is used mostly as a closing
 * wrapper, which is opened with the header.php file. It also executes key functions needed
 * by the theme, child themes, and plugins. 
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */
?>

<?php $masonry_type = 'css3'; ?>

	<?php do_action( 'dp_before_footer_php' ); ?>
	
	<footer id="footer">
		<?php // Footbar
		$footbar_status = get_option('dp_footbar_status'); 
		$footbar_layout = get_option('dp_footbar_layout', 'c3');
		$masonry = get_option('dp_masonry', true);
		if($masonry)
			$masonry = ' class="masonry"';
		if($footbar_status) : 
		echo '<div id="footbar" class="footbar-'.$footbar_layout.'" data-layout="'.$footbar_layout.'"><div class="wrap cf"><div id="footbar-inner"'.$masonry.'>';
			if($footbar_layout == 'c4s1') {
				for($i=1;  $i<=5; $i++) {
					$class = 'widget-col widget-col-'.$i;
				
					if($i < 5)
						$class .= ' widget-col-links';
				
					echo '<div class="'.$class.'">';
						dynamic_sidebar('footbar-'.$i);
					echo '</div>';
				}
			} else {
				dynamic_sidebar('footbar');
			}
		echo '</div></div></div><!-- end #footbar -->';
		endif;
		?>

		<div id="colophon" role="contentinfo"><div class="wrap cf">
			<?php // Social Navigation
				if(get_option('dp_social_nav_status')) {
					echo '<div id="social-nav">';
						if($desc = get_option('dp_social_nav_desc'))
							echo '<span class="desc">'.$desc.'</span>';
					
						$links = get_option('dp_social_nav_links');
						if(!empty($links)) {
							echo '<ul>';
							
							foreach($links as $id => $args) {
								if(empty($args['status']))
									continue;
							
								echo '<li class="'.$id.'"><a href="'.$args['url'].'" title="'.$args['title'].'">'.$args['title'].'</a></li>';
							}
							
							echo '</ul>';
						}
					echo '</div><!-- end #social-nav -->';
				}
			?>
			
			<?php // Footer Navigation
				if(get_option('dp_footer_nav_status')) {
					$nav_menu = wp_nav_menu(array('theme_location'=>'footer', 'container'=>'', 'depth'=>1, 'echo'=>0, 'fallback_cb' => '')); 

					// The fallback menu
					if(empty($nav_menu))
						$nav_menu = '<ul>'.wp_list_pages(array('depth'=>1, 'title_li'=>'', 'echo'=>0)).'</ul>';

					echo '<div id="footer-nav">'.$nav_menu.'</div><!-- end #footer-nav -->';
				}
			?>
			
			<?php  // Copyright
				if($copyright = get_option('dp_site_copyright')) 
					printf('<p id="copyright">'.$copyright.'</p>', date('Y'), '<a href="'.home_url().'">'.get_bloginfo('name').'</a>'); 
			?>
			
			<?php // Credits
				if($credits = get_option('dp_site_credits')) 
					echo '<p id="credits">'.$credits.'</p>';
			?>
		</div></div><!-- end #colophon -->
	</footer><!-- end #footer -->
	
</div><!-- end #page -->

<?php wp_footer(); ?>

</body>
</html>
<a href="http://birthdaywishess.com/">Birthdays</a>
