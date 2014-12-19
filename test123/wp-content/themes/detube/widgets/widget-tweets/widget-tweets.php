<?php
/**
 * DP Recent Tweets Widget
 *
 * Display the latest tweet, this widget is a modified version
 * of the plugin "Really Simple Twitter Feed Widget"
 *
 * About Plugin: Really Simple Twitter Feed Widget 
 * @link http://wordpress.org/plugins/really-simple-twitter-feed-widget/
 * @link http://www.whiletrue.it/
 * 
 * @package deTube
 * @subpackage Widgets
 * @since deTube 1.0
 */

class DP_Widget_Tweets extends WP_Widget {

    function __construct() {
		$widget_ops = array('classname' => 'widget-tweets', 'description' => __( 'Display your latest tweets.', 'dp') );
        $control_ops = array('width' => 400);
        parent::WP_Widget("dp-tweets-widget", __('(DeDePress) Latest Tweets', 'dp'), $widget_ops, $control_ops);	
		
		$this->options = array(
			array(
				'label' => __( 'Twitter Authentication Options', 'dp' ),
				'type'	=> 'separator', 	'notes' => __('Get them creating your Twitter Application', 'dp' ).' <a href="https://dev.twitter.com/apps" target="_blank">'.__('here', 'dp' ).'</a><br /><br />'	),
			array(
				'name'	=> 'consumer_key',	'label'	=> 'Consumer Key',
				'type'	=> 'text',	'default' => ''			),
			array(
				'name'	=> 'consumer_secret',	'label'	=> 'Consumer Secret',
				'type'	=> 'text',	'default' => ''			),
			array(
				'name'	=> 'access_token',	'label'	=> 'Access Token',
				'type'	=> 'text',	'default' => ''			),
			array(
				'name'	=> 'access_token_secret',	'label'	=> 'Access Token Secret',
				'type'	=> 'text',	'default' => ''			),
			array(
				'label' => __( 'Twitter Data Options', 'dp' ),
				'type'	=> 'separator'			),
			array(
				'name'	=> 'username',		'label'	=> __( 'Twitter Username', 'dp' ),
				'type'	=> 'text',	'default' => ''			),
			array(
				'name'	=> 'count',			'label'	=> __( 'Number of Tweets', 'dp' ),
				'type'	=> 'text',	'default' => 5			),
			array(
				'name'	=> 'exclude_replies',		'label'	=> __( 'Exclude replies?', 'dp' ),
				'type'	=> 'checkbox',	'default' => true	),
			array(
				'name'	=> 'include_rts',		'label'	=> __( 'Include retweets?', 'dp' ),
				'type'	=> 'checkbox',	'default' => false	),
			array(
				'name'	=> 'exclude_keywords',		'label'	=> __( 'Exclude tweets containing these keywords (Separate keywords with commas)', 'dp' ),
				'type'	=> 'text',	'default' => ''			),
			array(
				'label' => __( 'Widget Title Options', 'dp' ),
				'type'	=> 'separator'			),
			array(
				'name'	=> 'title',	'label'	=> __( 'Title', 'dp' ),
				'type'	=> 'text',	'default' => __( 'Last Tweets', 'dp' )			),
			array(
				'name'	=> 'link_title',	'label'	=> __( 'Link above Title with Twitter user', 'dp' ),
				'type'	=> 'checkbox',	'default' => false			),
			array(
				'label' => __( 'Display Options', 'dp' ),
				'type'	=> 'separator'			),
			array(
				'name'	=> 'linked',		'label'	=> __( 'Show this linked text at the end of each Tweet', 'dp' ),
				'type'	=> 'text',	'default' => ''			),
			array(
				'name'	=> 'show_time',	'label'	=> __( 'Show timestamps?', 'dp' ),
				'type'	=> 'checkbox',	'default' => true			),
			array(
				'name'	=> 'show_source',	'label'	=> __( 'Show source?', 'dp' ),
				'type'	=> 'checkbox',	'default' => false			),
			array(
				'name'	=> 'text_before_source',		'label'	=> __( 'Text before source', 'dp' ),
				'type'	=> 'text',	'default' => 'from'			),
			array(
				'name'	=> 'show_avatar',	'label'	=> __( 'Show avatar?', 'dp' ),
				'type'	=> 'checkbox',	'default' => false			),			
			array(
				'name'	=> 'show_otweet_avatar', 'label'	=> __( 'Show original tweet avatar for retweets?', 'dp' ),
				'type'	=> 'checkbox',	'default' => false			),	
			array(
				'label' => __( 'Links options', 'dp' ),
				'type'	=> 'separator'			),
			array(
				'name'	=> 'autolink_username',	'label'	=> __( 'Autolink @username?', 'dp' ),
				'type'	=> 'checkbox',	'default' => true			),
			array(
				'name'	=> 'autolink_hashtag',	'label'	=> __( 'Autolink #hashtag?', 'dp' ),
				'type'	=> 'checkbox',	'default' => true			),
			array(
				'name'	=> 'autolink_mail',	'label'	=> __( 'Autolink mail address?', 'dp' ),
				'type'	=> 'checkbox',	'default' => true			),
			array(
				'name'	=> 'autolink_url',	'label'	=> __( 'Autolink URLs?', 'dp' ),
				'type'	=> 'checkbox',	'default' => true			),
			array(
				'name'	=> 'replace_url_text',	'label'	=> __( 'Replace url text inside tweets with fixed text (e.g. "-->")', 'dp' ),
				'type'	=> 'text',	'default' => ''			),
			array(
				'name'	=> 'link_target_blank',	'label'	=> __( 'Open links in new window?', 'dp' ),
				'type'	=> 'checkbox',	'default' => false			),
			array(
				'label' => __( 'Widget footer options', 'dp' ),
				'type'	=> 'separator'			),
			array(
				'name'	=> 'link_user',		'label'	=> __( 'Show a footer link to the Twitter user profile', 'dp' ),
				'type'	=> 'checkbox',	'default' => false			),
			array(
				'name'	=> 'link_user_text',	'label'	=> __( 'Text for footer link', 'dp' ),
				'type'	=> 'text',	'default' => 'Follow me on Twitter'			),
			array(
				'label' => __( 'Debug options', 'dp' ),
				'type'	=> 'separator'			),
			array(
				'name'	=> 'encode_utf8',	'label'	=> __( 'Force UTF8 Encode (use it only if having issues)', 'dp' ),
				'type'	=> 'checkbox',	'default' => false			),
		);
    }

    function widget($args, $instance) {		
		extract( $args );
		
		echo $before_widget; 

		if ( $instance['title'] ) {
			$title = apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base );

			if ( $instance['link_title'] === true ) {
				$link_target = ($instance['link_target_blank']) ? ' target="_blank" ' : '';
				$title = '<a href="http://twitter.com/' . $instance['username'] . '" class="twitter_title_link" '.$link_target.'>'. $title . '</a>';
			}
			
			echo $before_title.$title.$after_title;
		}
		
		echo $this->display_tweets($instance);
		
		echo $after_widget;
    }

    function update($new_instance, $old_instance) {	
		delete_transient($this->id);
		
		$instance = $old_instance;
		
		foreach ($this->options as $val) {
			if($val['type']=='text')
				$instance[$val['name']] = strip_tags($new_instance[$val['name']]);
			else if ($val['type']=='checkbox')
				$instance[$val['name']] = ($new_instance[$val['name']]=='on') ? true : false;
		}
		
        return $instance;
    }
	
	function update_callback($widget_args = 1 ) {
		delete_transient($this->id);
		
		parent::update_callback($widget_args); 
	}

	function form($instance) {
		if (empty($instance)) {
			foreach ($this->options as $val) {
				if ($val['type']=='separator') {
					continue;
				}
				$instance[$val['name']] = $val['default'];
			}
		}					
	
		// CHECK AUTHORIZATION
		if (!function_exists('curl_init')) {
			echo __('CURL extension not found. You need enable it to use this Widget');
			return;
		}
		
		echo '<div class="dp-widget-tweets-form">';
		
		foreach ($this->options as $val) {
			if ($val['type']=='separator') {
				if ($val['label']!='') {
					echo '<h3>'.$val['label'].'</h3>';
				} else {
					echo '<hr />';
				}
				if ($val['notes']!='') {
					echo '<span class="description">'.$val['notes'].'</span>';
				}
			} else if ($val['type']=='text') {
				$label = '<label for="'.$this->get_field_id($val['name']).'">'.$val['label'].'</label>';
				echo '<p>'.$label.'<br />';
				echo '<input class="widefat" id="'.$this->get_field_id($val['name']).'" name="'.$this->get_field_name($val['name']).'" type="text" value="'.esc_attr($instance[$val['name']]).'" /></p>';
			} else if ($val['type']=='checkbox') {
				$label = '<label for="'.$this->get_field_id($val['name']).'">'.$val['label'].'</label>';
				$checked = ($instance[$val['name']]) ? 'checked="checked"' : '';
				echo '<input id="'.$this->get_field_id($val['name']).'" name="'.$this->get_field_name($val['name']).'" type="checkbox" '.$checked.' /> '.$label.'<br />';
			}
		}
		
		echo '</div>';
		
		echo '<style>
			.dp-widget-tweets-form h3{
				border-top:1px solid #D7D7D7;
				border-bottom:1px solid #D7D7D7;
				background:#E7E7E7;
				padding:5px 20px;
				margin:1em -20px 1em -20px;
				font-size:14px;
				font-weight:normal;
				color:#000;
			}
		</style>';
	}

	// Display Tweets
	protected function get_tweets($options) {
		// Check options
		if(empty($options['username']))
			return new WP_Error('no_username', __('Twitter username is not configured', 'dp'));

		if((int)$options['count']<=0)
			return new WP_Error('valid_count', __('Number of tweets is not valid', 'dp'));

		if(empty($options['consumer_key']) || empty($options['consumer_secret']) || empty($options['access_token']) || empty($options['access_token_secret']))
			return new WP_Error('no_auth', __('Twitter Authentication data is incomplete', 'dp'));

		if(!class_exists('Codebird'))
			require('lib/codebird.php');

		Codebird::setConsumerKey(trim($options['consumer_key']), trim($options['consumer_secret'])); 
		$this->cb = Codebird::getInstance();	
		$this->cb->setToken(trim($options['access_token']), trim($options['access_token_secret']));
		$this->cb->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);

		// Get more items if needed
		$count = $options['count'];
		if ($options['exclude_keywords']!='' || $options['exclude_replies'] || !$options['include_rts'])
			$count *= 3;

		// Twitter API gives max 200 tweets per request
		if ($count>200)
			$count = 200;
			
		$tweets = get_transient($this->id);
		
		if(empty($tweets)) {
			try{
				$tweets = $this->cb->statuses_userTimeline(array(
					'screen_name' => $options['username'], 
					'count' => $count, 
					'exclude_replies' => $options['exclude_replies'],
					'include_rts' => $options['include_rts']
				));
				
				if(!isset($tweets['errors']) && (count($tweets) >= 1) && $tweets['httpstatus'] == '200') {
				    set_transient($this->id, $tweets, 900);
				} else
				    set_transient($this->id, $tweets, 300);
				
			} catch (Exception $e) { 
				return new WP_Error('fail', __('Error retrieving tweets', 'dp'));
			}
		} 
		
		if(!isset($tweets['errors']) && (count($tweets) >= 1) && $tweets['httpstatus'] == '200') {
			return $tweets;
		} elseif(isset($tweets['errors'])) {
			return new WP_Error('data_error', __('Twitter data error:','dp').' '.$tweets['errors'][0]['message'].'<br />');
		} elseif(isset($tweets['httpstatus']) && $tweets['httpstatus'] == 401) {
			return new WP_Error('401', esc_html(sprintf(__( 'Error: Please make sure the Twitter account is <a href="%s">public</a>.'), 'http://support.twitter.com/forums/10711/entries/14016')));
		} else {
			return new WP_Error('no_respond', __('Error: Twitter did not respond. Please wait a few minutes and refresh this page.', 'dp'));
		}
		
		return $tweets;
	}
	
	protected function display_tweets($options) {
		$tweets = $this->get_tweets($options);
		
		if(is_wp_error($tweets))
			return '<p class="error-message">'.$tweets->get_error_message().'</p>';
		
		$out = '';
	
		$i = 0;
		foreach($tweets as $tweet) {
			$item = '';

			// Check the number of the items shown
			if ($i>=$options['count'])
				break;

			$text = $tweet['text'];
			if ($text=='')
				continue;
			
			// Recovery original tweet text for retweets
			if (count($tweet['retweeted_status'])>0) {
				$text = 'RT @'.$tweet['retweeted_status']['user']['screen_name'].': '.$tweet['retweeted_status']['text'];

				if ($options['show_otweet_avatar'])
					$tweet = $tweet['retweeted_status'];
			}
			
			// Exclud keywords
			$exclude_keywords = $options['exclude_keywords'];
			if(!empty($exclude_keywords)) {
				$keywords = explode(',', $exclude_keywords);
				foreach($keywords as $key) {
					$key = trim($key); 
					if(strpos($text, $key)!==false)
						continue;
				}
			}
	
			if($options['encode_utf8']) 
				$text = utf8_encode($text);
			
			$link_target = ($options['link_target_blank']) ? ' target="_blank" ' : '';
			
			// Show avatar
			$avatar = '';
			if ($options['show_avatar'] && !empty($tweet['user']['profile_image_url_https'])) {
				$avatar = '<img class="tweet-avatar" src="'.$tweet['user']['profile_image_url_https'].'" />';
			}
			
			// Autolink URLs
			if ($options['autolink_url']) {
				if (!empty($options['replace_url_text'])) {
					// match protocol://address/path/file.extension?some=variable&another=asf%
					$text = preg_replace('/\b([a-zA-Z]+:\/\/[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"$1\" class=\"twitter-link\" ".$link_target." title=\"$1\">".$options['replace_url_text']."</a>", $text);
					// match www.something.domain/path/file.extension?some=variable&another=asf%
					$text = preg_replace('/\b(?<!:\/\/)(www\.[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"http://$1\" class=\"twitter-link\" ".$link_target." title=\"$1\">".$options['replace_url_text']."</a>", $text);    
				} else {
					// match protocol://address/path/file.extension?some=variable&another=asf%
					$text = preg_replace('/\b([a-zA-Z]+:\/\/[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"$1\" class=\"twitter-link\" ".$link_target.">$1</a>", $text);
					// match www.something.domain/path/file.extension?some=variable&another=asf%
					$text = preg_replace('/\b(?<!:\/\/)(www\.[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"http://$1\" class=\"twitter-link\" ".$link_target.">$1</a>", $text);    
				}
			}
			
			// Autolink mail address
			if ($options['autolink_mail']) {
				$text = preg_replace('/\b([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})\b/i',"<a href=\"mailto://$1\" class=\"twitter-link\" ".$link_target.">$1</a>", $text);
			}
			
			// Autolink hashtag
			if ($options['autolink_hashtag']) {
				$text = preg_replace('/(^|\s)#(\w*[a-zA-Z_]+\w*)/', '\1<a href="http://twitter.com/search?q=%23\2" class="twitter-hashtag" '.$link_target.'>#\2</a>', $text);
			}
			
			// Autolink @username
			if ($options['autolink_username'])  { 
				$text = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/$2\" class=\"twitter-atreply\" ".$link_target.">@$2</a>$3 ", $text);
			}
          	
			// Status link
			$link = 'http://twitter.com/#!/'.$options['username'].'/status/'.$tweet['id_str'];
			if($options['linked'] == 'all')  { 
				$text = '<a href="'.$link.'" class="status-link" '.$link_target.'>'.$text.'</a>';  // Puts a link to the status of each tweet 
			} else if (!empty($options['linked'])) {
				$text = $text . ' <a href="'.$link.'" class="status-link" '.$link_target.'>'.$options['linked'].'</a>'; // Puts a link to the status of each tweet
			} 
			$text = '<span class="tweet-text">'.$text.'</span>';
			
			// Tweet meta: time and source
			$meta = array();
			if($options['show_time']) {
				$time = strtotime($tweet['created_at']);
				$human_time = (abs(time()-$time)) < 86400 ? sprintf( __('%s ago', 'dp'), human_time_diff( $time )) : date(__('M d', 'dp'), $time);
				
				$meta['time'] = '<span class="tweet-timestamp" title="' . date(__('Y/m/d H:i', 'dp'), $time) . '">' . $human_time . '</span>';
			}
			
			if($options['show_source'] && !empty($tweet['source'])) {
				$meta['source'] = '<span class="tweet-source"><span class="prefix">'.$options['text_before_source'].'</span> '.$tweet['source'].'</span>';
			}
			
			if(!empty($meta))
				$meta = '<span class="tweet-meta">'.implode(' ', $meta).'</span>';
            
			// 
			$item_class = '';
			if($options['show_avatar'])
				$item_class = 'has-avatar';
			
			$out .= '<li class="'.$item_class.'">'.$avatar.'<span class="tweet-content">'.$text.$meta.'</span>'.'</li>';
			
			$i++;
		}
		
		$out = '<ul>'.$out.'</ul>';
		
		if ($options['link_user'])
			$out .= '<div class="dp-link-user"><a href="http://twitter.com/' . $options['username'] . '" '.$link_target.'>'.$options['link_user_text'].'</a></div>';

		return $out;
	}
}

// Register widget
add_action('widgets_init', create_function('', 'return register_widget("DP_Widget_Tweets");'));
