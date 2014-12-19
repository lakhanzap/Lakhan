<?php
/**
 * Video Functions
 *
 * @package deTube
 * @subpackage Functions
 * @since 1.0
 */
/**
 * Output video html based on user's settings
 *
 * @param int $post_id Post ID.
 * @since 1.0
 */
function dp_video($post_id, $autoplay = false) {
	$file = get_post_meta($post_id, 'dp_video_file', true);
	$files = !empty($file) ? explode("\n", $file) : array();
	$url = trim(get_post_meta($post_id, 'dp_video_url', true));
	$code = trim(get_post_meta($post_id, 'dp_video_code', true));
	// Automatic Youtube Video Post plugin
	$tern_wp_youtube_video = get_post_meta($post_id, '_tern_wp_youtube_video', true);
	
	// Define RELATIVE_PATH for Flowplayer in Ajax Call
	if (!defined('RELATIVE_PATH') && defined('DOING_AJAX') && DOING_AJAX)
		define('RELATIVE_PATH', plugins_url().'/fv-wordpress-flowplayer');
	
	if(!empty($code)) {
		$video = do_shortcode($code);
		$video = apply_filters('dp_video_filter', $video);
		$video = extend_video_html($video, $autoplay);
		
		if(has_shortcode($code, 'fvplayer') || has_shortcode($code, 'flowplayer'))
			wp_ajax_flowplayer_script();
		
		echo $video;
	} 
	elseif(!empty($url)) {
		$url = trim($url);
		$video = '';
		$youtube_player = '';
		
		// Youtube List
		if(preg_match('/http:\/\/www.youtube.com\/embed\/(.*)?list=(.*)/', $url)) {
			$video = '<iframe width="560" height="315" src="'.$url.'" frameborder="0" allowfullscreen></iframe>';
		
		} 
		// Youtube Player
		elseif(strpos($url, 'youtube.com') !== false && !empty($youtube_player)) {
			$args = array(
				'files' => array($url),
				'poster' => $poster,
				'autoplay' => $autoplay
			);
			dp_player($youtube_player, $args);
		} 
		// WordPress Embeds
		else {
			global $wp_embed;
			$orig_wp_embed = $wp_embed;
			
			$wp_embed->post_ID = $post_id;
			$video = $wp_embed->autoembed($url);
			
			if(trim($video) == $url) {
				$wp_embed->usecache = false;
				$video = $wp_embed->autoembed($url);
			}
			
			$wp_embed->usecache = $orig_wp_embed->usecache;
			$wp_embed->post_ID = $orig_wp_embed->post_ID;
		}
		
		$video = extend_video_html($video, $autoplay);

		echo $video;
	} 
	elseif(!empty($files)) {
		$poster	= get_post_meta($post_id, 'dp_video_poster', true);
		if(empty($poster) && has_post_thumbnail($post_id) && $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'custom-large'))
			$poster = $thumb[0];
			
		$player = get_option('dp_default_player');
		$player = !empty($player['video_file']) ? $player['video_file'] : 'mediaelement';
		
		$args = array(
			'files' => $files,
			'poster' => $poster,
			'autoplay' => $autoplay
		);
		dp_player($player, $args);
	}
	elseif(!empty($tern_wp_youtube_video) && function_exists('tern_wp_youtube_video')) {
		// TODO: Add Custom Player Support 
		global $post;
		$post = get_post($post_id);
		$youtube_player = '';
		
		if(!empty($youtube_player)) {
			$v = get_post_meta($post->ID,'_tern_wp_youtube_video',true);
			$youtube_video_link = 'http://www.youtube.com/watch?v='.$v;
			
			$args = array(
				'files' => array($youtube_video_link),
				'poster' => $poster,
				'autoplay' => $autoplay
			);
			dp_player($youtube_player, $args);
		} else {
			$video = tern_wp_youtube_video(false);
		}
		
		$video = extend_video_html($video, $autoplay);
		echo $video;
	}
}

function dp_player($player = '', $args = array()) {
	if(empty($player) || empty($args['files']))
		return;
	
	$defaults = array(
		'files' => array(),
		'poster' => '',
		'autoplay' => false
	);
	$args = wp_parse_args($args, $defaults);
	
	extract($args);
	
	/* WordPress Native Player: MediaElement */
	if($player == 'mediaelement') {
		$atts = array();
		foreach($files as $file) {
			$file = trim($file);
			
			if(strpos($file, 'youtube.com') !== false)
				$atts['youtube'] = $file;
			else {
				$type = wp_check_filetype($file, wp_get_mime_types());
				$atts[$type['ext']] = $file;
			}
		}
			
		echo wp_video_shortcode($atts);
	} 
		
	/* JWPlayer */
	elseif($player == 'jwplayer') {
		$options = array(
			'file' => trim($files[0]), // JWPlayer WordPress Plugin doesn't support multiple codecs
			'image' => $poster
		);
		$atts = arr2atts($options);
		$jwplayer_shortcode = '[jwplayer'.$atts.']';
		echo apply_filters('dp_video_filter', $jwplayer_shortcode);
	}
		
	/* FlowPlayer */
	elseif($player == 'flowplayer') {
		$atts = array(
			'splash' => $poster
		);
		foreach($files as $key => $file) {
			// $type = wp_check_filetype(trim($file), wp_get_mime_types());
			$att = ($key == 0) ? 'src' : 'src'.$key;
			$atts[$att] = $file;
		}
		echo flowplayer_content_handle($atts, '', '');
		wp_ajax_flowplayer_script();
	}
		
	/* jPlayer */
	elseif($player == 'jplayer') {
		echo dp_jplayer(array(
			'src' => $files,
			'poster' => $poster,
			'type' => 'video',
			'autoplay' => $autoplay
		));
	}
}

/**
 * Determines if the specified post is a video post.
 *
 * @package deTube
 * @since 1.1
 *
 * @param int|object $post The post to check. If not supplied, defaults to the current post if used in the loop.
 * @return bool|int False if not a video, ID of video post otherwise.
 */
function is_video($post = null){
	$post = get_post($post);
	if(!$post)
		return false;
	
	// Back compat, if the post has any video field, it also is a video. 
	$video_file = get_post_meta($post->ID, 'dp_video_file', true);
	$video_url = get_post_meta($post->ID, 'dp_video_url', true);
	$video_code = get_post_meta($post->ID, 'dp_video_code', true);
	// Post meta by Automatic Youtube Video Post plugin
	$tern_wp_youtube_video = get_post_meta($post->ID, '_tern_wp_youtube_video', true);
	if(!empty($video_code) || !empty($video_url) || !empty($video_file) || (!empty($tern_wp_youtube_video) && function_exists('tern_wp_youtube_video')))
		return $post->ID;
	
	return has_post_format('video', $post);
}

/**
 * Add extra parameters to video url to control video
 * Fix iframe z-index bug and Make video Autoplay
 *
 * @since 1.0
 */
function extend_video_html($html, $autoplay = false, $wmode = 'opaque') {
	$replace = false;
	
	preg_match('/src=[\"|\']([^ ]*)[\"|\']/', $html, $matches);
	
	if(isset($matches[1])) {
		$url = $matches[1];
		
		// Vimeo
		if(strpos($url, 'vimeo.com')) {
			// Remove the title, byline, portrait on Vimeo video
			$url = add_query_arg(array('title'=>0,'byline'=>0,'portrait'=>0), $url);
			
			// Set autoplay
			if($autoplay)
				$url = add_query_arg('autoplay', '1', $url);
				
			$replace = true;
		}
			
		// Youtube
		if(strpos($url, 'youtube.com')) {
			// Set autoplay
			if($autoplay)
				$url = add_query_arg('autoplay', '1', $url);
		
			// Add wmode
			if($wmode)
				$url = add_query_arg('wmode', $wmode, $url);
			
			// Disabled suggested videos on YouTube video when the video finishes
			$url = add_query_arg(array('rel'=>0), $url);
			// Remove top info bar
			$url = add_query_arg(array('showinfo'=>0), $url);
			// Remove YouTube Logo
			$url = add_query_arg(array('modestbranding'=>0), $url);
			// Remove YouTube video annotations
			// $url = add_query_arg('iv_load_policy', 3, $url);
			
			$replace = true;
		}
		
		if($replace) {
			$url = esc_attr($url);	
			$html = preg_replace('/src=[\"|\']([^ ]*)[\"|\']/', 'src="'.$url.'"', $html);
		}
	}
	
	return $html;
}

/**
 * Ajax inline video action for list large view
 *
 * @since 1.0
 */
add_action( 'wp_ajax_nopriv_ajax-video', 'dp_ajax_video' );
add_action( 'wp_ajax_ajax-video', 'dp_ajax_video');
function dp_ajax_video() {
	if(!isset($_REQUEST['action']) || !isset($_REQUEST['id']) || $_REQUEST['action'] != 'ajax-video')
		return false;

	$post_id = $_REQUEST['id'];

	dp_video($post_id, true);
	
	die();
}

/*
 * Reinit MediaElement for Ajax calls
 * 
 * @since deTube 1.4
 */
add_filter( 'wp_video_shortcode', 'wp_ajax_mediaelement_script', 11, 5);
function wp_ajax_mediaelement_script($html, $atts, $video, $post_id, $library) {
	if(!defined('DOING_AJAX') || !DOING_AJAX || $library !== 'mediaelement')
		return $html;

	$html .= "
	<script type='text/javascript'>
	(function ($) {
		// add mime-type aliases to MediaElement plugin support
		mejs.plugins.silverlight[0].types.push('video/x-ms-wmv');
		mejs.plugins.silverlight[0].types.push('audio/x-ms-wma');

		$(function () {
			var settings = {};

			if ( typeof _wpmejsSettings !== 'undefined' )
				settings.pluginPath = _wpmejsSettings.pluginPath;

			$('.wp-audio-shortcode, .wp-video-shortcode').mediaelementplayer( settings );
		});
	}(jQuery));
	</script>
	";
	
	return $html;
}

/*
 * Output Flowplayer script for use it later in ajax
 * 
 * @since deTube 1.4
 */
function wp_ajax_flowplayer_script(){
	if(!defined('DOING_AJAX') || !DOING_AJAX)
		return;

	echo '
	<script type="text/javascript">
		(function ($) {
			$(function(){typeof $.fn.flowplayer=="function"&&$("video").parent(".flowplayer").flowplayer()});
		}(jQuery));
	</script>
	';
	
	flowplayer_display_scripts();
}

/*
 * Add a classname to <div> element which wrapped
 * wp video shortcode, so we can use it later 
 * 
 * @since deTube 1.4
 */
add_filter( 'wp_video_shortcode', 'wp_video_shortcode_wrapper', 10, 5);
function wp_video_shortcode_wrapper($html, $atts, $video, $post_id, $library) {
	$class .= 'wp-video-shortcode-wrapper';
	if($library === 'mediaelement')
		$class .= ' meplayer';
	$html = str_replace('<div style="', '<div class="'.$class.'" style="', $html);
	
	return $html;
}

/*== Add Youtube support to [video] shortcode */

/**
 * Add youtbue format to the list of WP-supported video formats
 *
 * @since deTube 1.4
 */
add_filter( 'wp_video_extensions', 'add_youtube_extension' );
function add_youtube_extension($exts) {
	$exts[] = 'youtube';
	
	return $exts;
}

/**
 * Add youtbue mime type
 *
 * @since deTube 1.4
 */
add_filter( 'mime_types', 'add_youtube_mime_type');
function add_youtube_mime_type($types){
	$types['youtube'] = 'video/youtube';
	
	return $types;
}

/**
 * Add youtube ext
 *
 * @since deTube 1.4
 */
add_filter( "shortcode_atts_video", 'add_youtube_ext', 10, 3 );
function add_youtube_ext($out, $pairs, $atts) {
	if(strpos($out['src'], 'youtube.com') !== false)
		$out['src'] .= '.youtube';
	if(!empty($out['youtube']))
		$out['youtube'] .= '.youtube';

	return $out;
}

/**
 * Remove youtube ext
 *
 * @since deTube 1.4
 */
add_filter( 'wp_video_shortcode', 'remove_youtube_ext', 10, 5);
function remove_youtube_ext($html, $atts, $video, $post_id, $library) {
	$html = str_replace('.youtube"', '"', $html);
	$html = str_replace('.youtube</a>', '</a>', $html);
	
	return $html;
}

// add_filter( 'wp_mediaelement_fallback', 'youtube_fallback', 10, 2);
function youtube_fallback($html, $url) {
	if(strpos($url, 'youtube.com') !== false)
		$html = '<iframe>';
		
	return $html; 
}