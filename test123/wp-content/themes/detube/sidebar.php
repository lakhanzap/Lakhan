<?php
/**
 * Sidebar Template
 *
 * @package deTube
 * @subpackage Tempalte
 * @since deTube 1.0
 */
?>

<div id="sidebar" role="complementary"<?php if(get_option('dp_masonry', true)) echo ' class="masonry"'; ?>>
	<?php
	
		if(is_front_page() && is_active_sidebar('home'))
			dynamic_sidebar('home');
		elseif(function_exists('is_buddypress') && is_buddypress() && is_active_sidebar('buddypress'))
			dynamic_sidebar('buddypress');
		elseif(function_exists('is_bbpress') && is_bbpress() && is_active_sidebar('bbpress'))
			dynamic_sidebar('bbpress');
		elseif(function_exists('is_woocommerce') && is_woocommerce() && is_active_sidebar('woocommerce'))
			dynamic_sidebar('woocommerce');
		elseif(is_single() && is_video() && is_active_sidebar('single-video'))
			dynamic_sidebar('single-video');
		elseif(is_single() && is_active_sidebar('single-post'))
			dynamic_sidebar('single-post');
		elseif(is_page_template('page-template-likes.php') && is_active_sidebar('likes-page'))
			dynamic_sidebar('likes-page');
		elseif(is_page() && is_active_sidebar('page'))
			dynamic_sidebar('page');
		elseif(is_category() && is_active_sidebar('category'))
			dynamic_sidebar('category');
		elseif(is_author() && is_active_sidebar('author'))
			dynamic_sidebar('author');
		else
			dynamic_sidebar('main');
	?>
</div><!--end #sidebar-->