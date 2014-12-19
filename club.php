<?php
/*
Plugin Name: Clubhouse Manager
Plugin URI: http://dev.flyingdogsbaseball.com/baseballnuke
Description: The Clubhouse manager turns your wordpress website into a baseball team management system.It provides League, Season, Team, Game and player information and statastics. 
Version: 1.0.0
Author: DML Webservices
License: GPL2
*/
@ini_set(display_errors, 0);

global $wpdb,
       $responses,
       $club_db_version;

$club_db_version = 1.0;

define('BBNPURL', WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) );
define('BBNPDIR', WP_PLUGIN_DIR . '/' . str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) );

// relative path to WP_PLUGIN_DIR where the translation files will sit:
$plugin_path = dirname(plugin_basename(__FILE__)) . '/language';
load_plugin_textdomain( 'club', false, $plugin_path );


if ( function_exists('load_plugin_textdomain') )
{
  if ( !defined('WP_PLUGIN_DIR') )
  {
    load_plugin_textdomain( 'club', str_replace( ABSPATH, '', dirname(__FILE__)) . '/lang');
  }
  else
  {
    load_plugin_textdomain( 'club', false, dirname(plugin_basename(__FILE__)) . '/lang');
  }
}



require_once( dirname(__FILE__) . '/club-db.php');
require_once( dirname(__FILE__) . '/club-functions.php');
require_once( dirname(__FILE__) . '/club-widgets.php');
require_once( dirname(__FILE__) . '/club-option-page.php');
require_once( dirname(__FILE__) . '/includes/classes.php');
//require_once( dirname(__FILE__) . '/club-edit.php');

register_activation_hook(   __FILE__, 'club_plugin_activation'  );
register_deactivation_hook( __FILE__, 'club_plugin_deactivation');

add_action('plugins_loaded', 'club_update_db_check');
add_action( 'wp_print_scripts', 'club_print_scripts');
add_action( 'wp_print_styles',  'club_print_styles');
add_action( 'admin_init',   'club_admin_init_method');
add_action( 'admin_menu',   'club_plugin_add_option_page');
add_action( 'widgets_init', create_function('', 'return register_widget("club_Widget");'));
add_action('init', 'club_set_cookies');

if (isset($_GET['page']) && $_GET['page'] == 'club-players') {
add_action('admin_print_scripts', 'upload_admin_scripts');
add_action('admin_print_styles', 'upload_admin_styles');
}

add_filter( 'cron_schedules', 'club_more_reccurences', 11);
add_filter('admin_head','show_tinyMCE');


//  ajax calls
add_action( 'wp_ajax_club_ajax_action', 'club_ajax_func');
add_action( 'wp_ajax_nopriv_club_ajax_action', 'club_ajax_func');

$plugin_url = BBNPURL;


////////////////////////////////////////////////////////////////////////////////
// plugin activation hook
////////////////////////////////////////////////////////////////////////////////
function club_plugin_activation()
{
  global $wpdb;

  //   check if tables exists and create
  club_db_delta();

  //   check if tables are empty and fill with default values
  club_check_tables();

  //   update schedule type field for version 1.2
  //club_update_tables();

  add_option( 'club_plugin_options', array(), '', 'no');
  club_set_option_defaults();

  return;
}

///////////////////////////////////////////////////////////////////////////////
// upgrade activation hook
////////////////////////////////////////////////////////////////////////////////
function club_update_db_check() {
    global $club_db_version;
    if (get_site_option('club_db_version') != $club_db_version) {
       // club_db_delta();
	club_update_tables();
    }
}

////////////////////////////////////////////////////////////////////////////////
// plugin deactivation hook
////////////////////////////////////////////////////////////////////////////////
function club_plugin_deactivation()
{
  delete_option('club_plugin_options');

  return;
}


function club_plugin_uninstall()
{
  global $wpdb;

  // Deactivate Plugin
  $current = get_settings('active_plugins');
  array_splice($current, array_search( "clubhouseManager/club.php", $current), 1 );
  update_option('active_plugins', $current);
  do_action('deactivate_' . trim( $_GET['plugin'] ));

  // Drop MySQL Tables
  club_drop_tables();

  // Delete Options
//  delete_option('club_plugin_options');

//  wp_redirect(get_option('siteurl').'/wp-admin/plugins.php?deactivate=true');

  return;
}


////////////////////////////////////////////////////////////////////////////////
// add plugin option page
////////////////////////////////////////////////////////////////////////////////
/*
function club_plugin_add_option_page()
{

 //  add_menu_page   ('Clubhouse Manager Plugin Options', 'clubhouseManager', 8, 'club-option-page',  'club_plugin_create_option_page', BBNPURL . 'images/clubhouseManager_16x16.gif');
  
  add_menu_page('Clubhouse Manager Plugin Options', 'Clubhouse Managers', 8, 'club-option-page', 'club_plugin_create_option_page',  BBNPURL . 'images/clubhouseManager_16x16.gif');
   add_submenu_page( 'club-option-page', 'Setting',                      'Setting',       5, 'club-setting',       'club_plugin_create_option_page');
    add_submenu_page( 'club-option-page', 'Players',                     'Players',      8, 'club-players',      'club_plugin_create_players_page');
 //add_submenu_page('club-option-page', 'Submenu Page Title', 'Players', 8, 'club-players', 'club_plugin_create_players_page');
    //add_submenu_page('club-option-page', 'Submenu Page Title2', 'Whatever You Want2', 5,'club-option-page', 'my-menu2' );
//  $club_admin_page =
   //add_menu_page   ('Settings', 'Clubhouse Manager', 8, 'club-option-page',  'club_plugin_create_option_page',  'my_menu_output');
 //  add_submenu_page( 'club-option-page', 'Players',                     'Players',      8, 'club-players',      'club_plugin_create_players_page');
 add_submenu_page( 'club-option-page', 'Fields',                      'Fields',       5, 'club-fields',       'club_plugin_create_fields_page');
   add_submenu_page( 'club-option-page', 'Schedule',                    'Schedule',     5, 'club-schedule',     'club_plugin_create_schedules_page');
   //add_submenu_page( 'club-option-page', 'Tournaments',                 'Tournaments',  5, 'club-tournaments',  'club_plugin_create_tournaments_page');
  add_submenu_page( 'club-option-page', 'Practices',                   'Practices',    5, 'club-practice',     'club_plugin_create_practice_page');
  add_submenu_page( 'club-option-page', 'Game Results',                'Game Results', 5, 'club-game-results', 'club_plugin_create_game_results_page');
//   //add_submenu_page( 'club-option-page', 'Edit',                'Edit', 5, 'club-edit', 'club-edit');
// //  add_submenu_page( 'club-option-page', 'Import',                'Import', 5, 'club-import', 'club_plugin_create_import_page');
   add_submenu_page( 'club-option-page', 'Uninstsll',                   'Uninstall',    5, 'club-uninstall',    'club_plugin_create_uninstall_page');

//   return;
 //  add_menu_page('My Page Title', 'My Menu Title', 8, 'club-option-page', 'club_plugin_create_option_page', 'my_menu_output' );
    //add_submenu_page('my-menu', 'Submenu Page Title', 'Whatever You Want', 'manage_options', 'my-menu' );
    //add_submenu_page('my-menu', 'Submenu Page Title2', 'Whatever You Want2', 'manage_options', 'my-menu2' );
    return;
}
*/



 function club_plugin_add_option_page()
{
  
  add_menu_page   ('Clubhouse Manager Plugin Options', 'Clubhouse Manager', 8, 'club-option-page',  'club_plugin_create_option_page', BBNPURL . 'images/clubhouseManager_16x16.gif');
  add_submenu_page( 'club-option-page', 'Settings',                     'Settings',      8, 'club-option-page',      'club_plugin_create_option_page');
  add_submenu_page( 'club-option-page', 'Players',                     'Players',      8, 'club-players',      'club_plugin_create_players_page');
  add_submenu_page( 'club-option-page', 'Fields',                      'Fields',       5, 'club-fields',       'club_plugin_create_fields_page');
  add_submenu_page( 'club-option-page', 'Schedule',                    'Schedule',     5, 'club-schedule',     'club_plugin_create_schedules_page');
 // add_submenu_page( 'club-option-page', 'Statastics and Result',                 'Statastics and Result',  5, 'club-game-results',  'club_plugin_create_game_results_page');
 add_submenu_page( 'club-option-page', 'Statastics and Result',                 'Statastics and Result',  5, 'club-statsticsgame',  'club_plugin_create_statsticsgame_page');
 // add_submenu_page( 'club-option-page', 'Practices',                   'Practices',    5, 'club-practice',     'club_plugin_create_practice_page');
  add_submenu_page( 'club-option-page', 'Game Results',                'Game Results', 5, 'club-game-results', 'club_plugin_create_game_results_page');
  //add_submenu_page( 'club-option-page', 'Edit',                'Edit', 5, 'club-edit', 'club-edit');
//  add_submenu_page( 'club-option-page', 'Import',                'Import', 5, 'club-import', 'club_plugin_create_import_page');
  add_submenu_page( 'club-option-page', 'Uninstsll',                   'Uninstall',    5, 'club-uninstall',    'club_plugin_create_uninstall_page');

  return;
}







function  club_admin_head()
{
}


function  club_wp_head()
{
}



////////////////////////////////////////////////////////////////////////////////
// load plugin wp-admin css and js
////////////////////////////////////////////////////////////////////////////////
function club_plugin_load_header_tags()
{
}


function  club_init_method()
{
}


function  club_print_scripts()
{
  if ( is_admin() )
  {
//echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>';
 wp_enqueue_script( 'jPicker_script', plugin_dir_url( __FILE__ ) . 'includes/js/jpicker-1.1.5.js', array('jquery', 'json2'), false, false);
 wp_enqueue_script( 'form_script', plugin_dir_url( __FILE__ ) . 'includes/js/jquery.form.js', array('jquery', 'json2'), false, false);
 wp_enqueue_script( 'csv2table_script', plugin_dir_url( __FILE__ ) . 'includes/js/jquery.csvToTable.js', array('jquery', 'json2'), false, false);
 wp_enqueue_script( 'club_admin_script', plugin_dir_url( __FILE__ ) . 'includes/js/club_admin_scripts.js', array('jquery', 'json2'), false, false);
 echo '<script type="text/javascript">
$tabs = jQuery.noConflict();
$tabs(document).ready(function() {
  $tabs(".tab_content").hide(); //Hide all content
  $tabs("ul.tabs li:first").addClass("active").show(); //Activate first tab
  $tabs(".tab_content:first").show(); //Show first tab content

  $tabs("ul.tabs li").click(function() {
    $tabs("ul.tabs li").removeClass("active"); //Remove any "active" class
    $tabs(this).addClass("active"); //Add "active" class to selected tab
    $tabs(".tab_content").hide(); //Hide all tab content
    var activeTab = $tabs(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
    $tabs(activeTab).fadeIn(10); //Fade in the active content
    return false;
  });
});
</script>';
  }
  else
  {
    //  print scripts for the public and frontend
    wp_enqueue_script( 'json2' );
    wp_enqueue_script('tablesorter_script', plugin_dir_url( __FILE__ ) .'includes/js/jquery.tablesorter.js', array('jquery'));
    wp_enqueue_script( 'club_script', plugin_dir_url( __FILE__ ) . 'includes/js/club_scripts.js', array('jquery', 'json2', 'tablesorter_script'), 1.2, false);
    wp_enqueue_script('thickbox');

    echo
    '
      <script type="text/javascript" language="javascript">
      //<![CDATA[
        var ajaxurl  = "' . admin_url('admin-ajax.php') . '";
      //]]>
      </script>
    ';
  }

  return;
}

function load_tablesorter_scripts() {
 wp_enqueue_script('tablesorter_script', plugin_dir_url( __FILE__ ) .'includes/js/jquery.tablesorter.js', array('jquery'));
 wp_enqueue_script('club_tablesorter_script', plugin_dir_url( __FILE__ ) .'includes/js/club_tablesorter.js', array('jquery'));
}

function upload_admin_scripts() {
 wp_enqueue_script('media-upload');
 wp_enqueue_script('thickbox');
 wp_register_script('club_upload_script', plugin_dir_url( __FILE__ ) .'includes/js/club_upload_script.js', array('jquery','media-upload','thickbox'));
 wp_enqueue_script('club_upload_script');
}


function show_tinyMCE() {
    wp_enqueue_script( 'common' );
    wp_enqueue_script( 'jquery-color' );
    wp_print_scripts('editor');
    if (function_exists('add_thickbox')) add_thickbox();
    wp_print_scripts('media-upload');
    if (function_exists('wp_tiny_mce')) wp_tiny_mce();
    wp_admin_css();
    wp_enqueue_script('utils');
    do_action("admin_print_styles-post-php");
    do_action('admin_print_styles');
    remove_all_filters('mce_external_plugins');
}


function  club_print_styles()
{
  if ( is_admin() )
  {
    wp_enqueue_style( 'jPicker_styles', BBNPURL . 'css/jPicker-1.1.5.min.css');
//  wp_register_style('club_admin_styles', BBNPURL . 'css/club-admin-plugin.css');
    wp_enqueue_style( 'club_admin_styles', BBNPURL . 'css/club-admin-plugin.css');
  }
  else
  {
    wp_register_style('table_sorter_styles', BBNPURL . 'css/blue/style.css');
    wp_register_style('club_frontend_styles', BBNPURL . 'css/club-frontend-plugin.php');
    wp_enqueue_style( 'club_frontend_styles' );
    wp_enqueue_style('thickbox');
  }

  return;
}


function upload_admin_styles() {
    wp_enqueue_style('thickbox');
}


////////////////////////////////////////////////////////////////////////////////
// plugin init method
////////////////////////////////////////////////////////////////////////////////
function club_admin_init_method()
{
  if ( get_magic_quotes_gpc() )
  {
    $_POST      = array_map( 'stripslashes_deep', $_POST );
    $_GET       = array_map( 'stripslashes_deep', $_GET );
    $_COOKIE    = array_map( 'stripslashes_deep', $_COOKIE );
    $_REQUEST   = array_map( 'stripslashes_deep', $_REQUEST );
  }

  wp_enqueue_style( 'club_admin_styles', BBNPURL . 'css/club-admin-plugin.css');
  wp_enqueue_style( 'jPicker_styles', BBNPURL . 'css/jPicker-1.1.5.min.css');

  wp_enqueue_script('dashboard');
  wp_enqueue_script('postbox');
  wp_enqueue_script('jquery-ui-resizable');
  wp_enqueue_script('jquery-ui-droppable');
  wp_enqueue_script('wp-ajax-response');
  wp_enqueue_script('tiny_mce');

  register_widget('club_Widget');

/*
  if ( function_exists('wp_tiny_mce') )
  {
    wp_tiny_mce(
                  true , // true makes the editor "teeny"
                  array(
                          "editor_selector" => "club_textarea",
                          'width'           => '75%',
                          'theme_advanced_resize_horizontal' => ture,
                          'theme'           => 'advanced'
                       )
             );
  }
*/
  //  check if user admin_club exists 
  $user_name = 'admin_club';
  $user_id = username_exists( $user_name );
  if ( $user_id )
  {
    // delete user if exists - no longer needed 
    $user = get_userdatabylogin($user_name);
    wp_delete_user( $user->ID );
  }

  return;
}


////////////////////////////////////////////////////////////////////////////////
// plugin options functions
////////////////////////////////////////////////////////////////////////////////
function club_get_option($field)
{
  if (!$options = wp_cache_get('club_plugin_options'))
  {
    $options = get_option('club_plugin_options');
    wp_cache_set('club_plugin_options',$options);
  }
  return $options[$field];
}

function club_update_option($field, $value)
{
  club_update_options(array($field => $value));
}

function club_update_options($data)
{
  $options = array_merge(get_option('club_plugin_options'),$data);
  update_option('club_plugin_options',$options);
  wp_cache_set('club_plugin_options',$options);
}

function club_migrate_old_options()
{
  global $wpdb;

  //  check for a old Option
  if ( (get_option('club_plugin_version') === false) )
  {
    return;
  }

  $old_fields = array(
       	'0'   => 'club_plugin_version',
       	'1'   => 'club_players_season',
       	'2'   => 'club_players_team',
       	'3'   => 'club_players_edit_id',
       	'4'   => 'club_location_edit_name',
       	'5'   => 'club_game_edit_id',
       	'6'   => 'club_schedules_season',
       	'7'   => 'club_practice_season',
       	'8'   => 'club_tournaments_season',
       	'9'   => 'club_results_season',
       	'10'  => 'club_team_leaders',
       	'11'  => 'club_post_user',
       	'12'  => 'club_widget_bg_color',
       	'13'  => 'club_widget_txt_color',
       	'14'  => 'club_widget_hover_color',
       	'15'  => 'club_widget_header_txt_color',
       	'16'  => 'club_game_results_page',
       	'17'  => 'club_player_stats_page',
       	'18'  => 'club_locations_page',
       	'19'  => 'club_widget_header_bg_color',
       	'20'  => 'club_era_innings',
       	'21'  => 'club_roster_img',
        '21'  => 'club_roster_num',
       	'22'  => 'club_roster_name',
       	'23'  => 'club_roster_pos',
       	'24'  => 'club_roster_bats',
       	'25'  => 'club_roster_throws',
       	'26'  => 'club_roster_home',
       	'27'  => 'club_roster_school',
	'28'  => 'club_batting_num',
	'29'  => 'club_batting_name',
	'30'  => 'club_batting_ab',
	'31'  => 'club_batting_r',
	'32'  => 'club_batting_h',
	'33'  => 'club_batting_2b',
	'34'  => 'club_batting_3b',
	'35'  => 'club_batting_hr',
	'36'  => 'club_batting_re',
	'37'  => 'club_batting_fc',
	'38'  => 'club_batting_sf',
	'39'  => 'club_batting_hp',
	'40'  => 'club_batting_rbi',
	'41'  => 'club_batting_ba',
	'42'  => 'club_batting_obp',
	'43'  => 'club_batting_slg',
	'44'  => 'club_batting_ops',
	'45'  => 'club_batting_bb',
	'46'  => 'club_batting_k',
	'47'  => 'club_batting_lob',
	'48'  => 'club_batting_sb',
	'49'  => 'club_pitching_num',
	'50'  => 'club_pitching_name',
	'51'  => 'club_pitching_w',
	'52'  => 'club_pitching_l',
	'53'  => 'club_pitching_s',
	'54'  => 'club_pitching_ip',
	'55'  => 'club_pitching_h',
	'56'  => 'club_pitching_r',
	'57'  => 'club_pitching_er',
	'58'  => 'club_pitching_bb',
	'59'  => 'club_pitching_k',
	'60'  => 'club_pitching_era',
        '61'  => 'club_pitching_whip'
       );

  $new_fields = array(
       	'0'   => 'club_plugin_version',
       	'1'   => 'club_players_season',
       	'2'   => 'club_players_team',
       	'3'   => 'club_players_edit_id',
       	'4'   => 'club_location_edit_name',
       	'5'   => 'club_game_edit_id',
       	'6'   => 'club_schedules_season',
       	'7'   => 'club_practice_season',
       	'8'   => 'club_tournaments_season',
       	'9'   => 'club_results_season',
       	'10'  => 'club_team_leaders',
       	'11'  => 'club_post_user',
       	'12'  => 'club_widget_bg_color',
       	'13'  => 'club_widget_txt_color',
       	'14'  => 'club_widget_hover_color',
       	'15'  => 'club_widget_header_txt_color',
       	'16'  => 'club_game_results_page',
       	'17'  => 'club_player_stats_page',
       	'18'  => 'club_locations_page',
       	'19'  => 'club_widget_header_bg_color',
       	'20'  => 'club_era_innings',
       	'21'  => 'club_roster_img',
        '21'  => 'club_roster_num',
       	'22'  => 'club_roster_name',
       	'23'  => 'club_roster_pos',
       	'24'  => 'club_roster_bats',
       	'25'  => 'club_roster_throws',
       	'26'  => 'club_roster_home',
       	'27'  => 'club_roster_school',
	'28'  => 'club_batting_num',
	'29'  => 'club_batting_name',
	'30'  => 'club_batting_ab',
	'31'  => 'club_batting_r',
	'32'  => 'club_batting_h',
	'33'  => 'club_batting_2b',
	'34'  => 'club_batting_3b',
	'35'  => 'club_batting_hr',
	'36'  => 'club_batting_re',
	'37'  => 'club_batting_fc',
	'38'  => 'club_batting_sf',
	'39'  => 'club_batting_hp',
	'40'  => 'club_batting_rbi',
	'41'  => 'club_batting_ba',
	'42'  => 'club_batting_obp',
	'43'  => 'club_batting_slg',
	'44'  => 'club_batting_ops',
	'45'  => 'club_batting_bb',
	'46'  => 'club_batting_k',
	'47'  => 'club_batting_lob',
	'48'  => 'club_batting_sb',
	'49'  => 'club_pitching_num',
	'50'  => 'club_pitching_name',
	'51'  => 'club_pitching_w',
	'52'  => 'club_pitching_l',
	'53'  => 'club_pitching_s',
	'54'  => 'club_pitching_ip',
	'55'  => 'club_pitching_h',
	'56'  => 'club_pitching_r',
	'57'  => 'club_pitching_er',
	'58'  => 'club_pitching_bb',
	'59'  => 'club_pitching_k',
	'60'  => 'club_pitching_era',
        '61'  => 'club_pitching_whip'
       );

  foreach($old_fields as $index=>$field)
  {
    if ( $index == 3 )
    {
      $cats = get_option($old_fields[$index]);
      if ( is_array($cats) )
        club_update_option($new_fields[$index], $cats);
      else
        club_update_option($new_fields[$index], array($cats));
    }
    else
      club_update_option($new_fields[$index], get_option($old_fields[$index]));
    delete_option($old_fields[$index]);
  }
  $wpdb->query("OPTIMIZE TABLE `" . $wpdb->options . "`");

  return;
}

function club_set_option_defaults()
{
  $current_user_id=1;
  global $current_user;
  get_currentuserinfo();

  if ( $current_user->ID != '' )
    $current_user_id=$current_user->ID;

  $default_options = array(
       	'club_plugin_version'           => '1.0.0',
       	'club_players_season'           => '2008',
       	'club_players_team'             => 'Flying Dogs',
       	'club_players_edit_id'          => 0,
       	'club_location_edit_name'       => '',
       	'club_game_edit_id'             => 0,
       	'club_schedules_season'         => '2008',
       	'club_practice_season'          => '2008',
       	'club_tournaments_season'       => '2008',
       	'club_results_season'           => '2008',
       	'club_team_leaders'             => 3,
       	'club_post_user'                => 1,
       	'club_widget_playerstats_player_id'  => NULL,
       	'club_widget_game_results_player_id' => NULL,
       	'club_widget_game_results_game_id'   => NULL,
       	'club_widget_bg_color'          => 'ffffff',
       	'club_widget_txt_color'         => '000000',
       	'club_widget_hover_color'	 => 'e5e5e5',
       	'club_widget_header_txt_color'  => '000000',
       	'club_game_results_page' => 'game-results',
       	'club_player_stats_page' => 'player-stats',
       	'club_locations_page'    => 'fields',
       	'club_widget_header_bg_color'   => 'b2b2b2',
       	'club_era_innings'             => 9,
       	'club_roster_img'		=> 'true',
          'club_roster_num'   => 'true',
       	'club_roster_name'              => 'true',
       	'club_roster_pos'              => 'true',
       	'club_roster_bats'              => 'true',
       	'club_roster_throws'              => 'true',
       	'club_roster_home'              => 'true',
       	'club_roster_school'              => 'true',
	'club_batting_num'	 => 'true',
	'club_batting_name'	 => 'true',
	'club_batting_ab'	 => 'true',
	'club_batting_r'	 => 'true',
	'club_batting_h'	 => 'true',
	'club_batting_2b'	 => 'true',
	'club_batting_3b'	 => 'true',
	'club_batting_hr'	 => 'true',
	'club_batting_re'	 => 'true',
	'club_batting_fc'	 => 'true',
	'club_batting_sf'	 => 'true',
	'club_batting_hp'	 => 'true',
	'club_batting_rbi'	 => 'true',
	'club_batting_ba'	 => 'true',
	'club_batting_obp'	 => 'true',
	'club_batting_slg'	 => 'true',
	'club_batting_ops'	 => 'true',
	'club_batting_bb'	 => 'true',
	'club_batting_k'	 => 'true',
	'club_batting_lob'	 => 'true',
	'club_batting_sb'	 => 'true',
	'club_pitching_num'	 => 'true',
	'club_pitching_name'	 => 'true',
	'club_pitching_w'	 => 'true',
	'club_pitching_l'	 => 'true',
	'club_pitching_s'	 => 'true',
	'club_pitching_ip'	 => 'true',
	'club_pitching_h'	 => 'true',
	'club_pitching_r'	 => 'true',
	'club_pitching_er'	 => 'true',
	'club_pitching_bb'	 => 'true',
	'club_pitching_k'	 => 'true',
	'club_pitching_era'	 => 'true',
        'club_pitching_whip'    => 'true'
        );

  $club_options = get_option('club_plugin_options');

  foreach ($default_options as $def_option => $value )
  {
    if ( !$club_options[$def_option] )
    {
      club_update_option( $def_option, $value );
    }
  }

  return;
}



////////////////////////////////////////////////////////////////////////////////
// print players page
////////////////////////////////////////////////////////////////////////////////
function club_plugin_create_players_page()
{
  if ( $_POST['club_set_season_team_btn'] )
  {
    //  set season and team for players list
    $team   = $_POST['club_select_season_team'];
    $season = $_POST['club_select_season'];
    club_update_option('club_players_team', $team);
    club_update_option('club_players_season', $season);
  }

  //  check if one edit button is pressed
  $edit_player = false;
    foreach( $_POST as $key => $value )
    {
      if ( !(strpos( $key, 'club_edit_player_') === false) )
      {
        $pos = strpos( $key, 'club_edit_player_');
        $pos1= strpos( $key, '_btn', $pos);
        $id = (int)substr( $key, ($pos + 19), ($pos1 - ($pos + 19)) );
        club_update_option('club_players_edit_id', $id);
        $edit_player = true;
        break;
      }
   }
   {
     if ( !(strpos( $key, 'club_delete_player_') === false) )
      {
        $pos = strpos( $key, 'club_delete_player_');
        $pos1= strpos( $key, '_btn', $pos);
        $id = (int)substr( $key, ($pos + 21), ($pos1 - ($pos + 21)) );
	$season  = club_get_option('club_players_season');
    	club_delete_player($id, $season);
    echo '<div id="message" class="updated fade">';
    echo '<strong>Player deleted !!!</strong></div>';
      }
   }
  {
  if ( $_POST['club_save_player_btn'] )
  {
      //  new player
      $player_id = $_POST['club_delete_player_id'];
      $season    = $_POST['club_player_edit_season'];
      $ret       = club_add_player($player_id, $season);


      // print_r($ret); die;


      if ($ret)
      {
        echo '<div id="message" class="updated fade">';
        echo '<strong>Player added !!!</strong></div>';
      }
      else
      {
        echo '<div id="message" class="error fade">';
        echo '<strong>Player not added !!!</strong></div>';
      }
   }
  }
  {
      //  player update
    if ( !(strpos( $key, 'club_update_player_') === false) )
    {
        $pos = strpos( $key, 'club_update_player_');
        $pos1= strpos( $key, '_btn', $pos);
        $player_id = (int)substr( $key, ($pos + 21), ($pos1 - ($pos + 21)) );
        $season  = club_get_option('club_players_season');
        $ret = club_update_player($player_id, $season);
        if ($ret)
      {
        echo '<div id="message" class="updated fade">';
	echo '<strong>' . $season . 'Player updated !!!</strong></div>';
      }
      else
      {
        echo '<div id="message" class="error fade">';
        echo '<strong>Player not updated !!!' . $player_id . ',' . $season . '</strong></div>';
echo mysql_error();
      }
    }
  }

  if ( $_POST['club_players_file_upload_btn'] )
  {
    $ret = club_upload_file();
    if ($ret)
    {
      echo '<div id="message" class="error fade">';
      echo '<strong>Error during file uploaded - players not added !!!</strong></div>';
    }
    else
    {
      echo '<div id="message" class="updated fade">';
      echo '<strong>File uploaded and players added !!!</strong></div>';
    }
  }

  if ( $_POST['club_assign_players_team_btn'] )
  {
    if ( empty($_POST['club_players_assign_select']) )
    {
      echo '<div id="message" class="error fade">';
      echo '<strong>No players selected !!!</strong></div>';
    }
    else
    {
      $season = $_POST['club_players_select_season'];
      $team   = $_POST['club_players_select_season_team'];
      $players_selected = $_POST['club_players_assign_select'];
      club_assign_players_team($team, $season, $players_selected);

      echo '<div id="message" class="updated fade">';
      echo '<strong>Players added to team !!!</strong></div>';
echo mysql_error();
    }
  }

  if ( $_POST['club_del_players_season_team_btn'] )
  {
    $team   = $_POST['club_select_season_team'];
    $season = $_POST['club_select_season'];
    club_delete_all_players_team($team, $season);
  }


  $ret = club_plugin_print_players_option_page($edit_player);

  switch ($ret)
  {
    case -1:
      echo '<div id="message" class="error fade">';
      echo '<strong>No Players assigned to team !!!</strong></div>';
      break;
    default:
      break;
  }

  return;
}




////////////////////////////////////////////////////////////////////////////////
// print tournaments page
////////////////////////////////////////////////////////////////////////////////
function  club_plugin_create_Statsticsgame_page()
{
  //  check if one edit button is pressed
  $edit_tournament = false;
  foreach( $_POST as $key => $value )
  {
    if ( !(strpos( $key, 'club_edit_tournament_') === false) )
    {
      $pos = strpos( $key, 'club_edit_practise_');
      $pos1= strpos( $key, '_btn', $pos);
      $id = (int)substr( $key, ($pos + 23), ($pos1 - ($pos + 23)) );
      club_update_option('club_game_edit_id', $id);
      $edit_tournament = true;
      break;
    }
  }

  //  check if one delete button is pressed
  $tournament_deleted = false;
  if ( $_POST['club_game_delete_id'] == 'none' )
  {
    foreach( $_POST as $key => $value )
    {
      if ( !(strpos( $key, 'club_delete_tournament_') === false) )
      {
        $pos = strpos( $key, 'club_delete_tournament_');
        $pos1= strpos( $key, '_btn');
        $id = (int)substr( $key, ($pos + 25), ($pos1 - ($pos + 25)) );

        club_delete_game( $id );
        $tournament_deleted = true;
        $game_id = $id;
        break;
      }
    }
  }
  if ($tournament_deleted)
  {
    echo '<div id="message" class="updated fade">';
    echo '<strong>Tournament entry deleted !!!</strong></div>';
  }

  if ( $_POST['club_tournaments_list_set_season_btn'] )
  {
    //  set season for practises list
    $season = $_POST['club_tournaments_edit_select_season'];
    club_update_option('club_tournaments_season', $season);
  }



  if ( $_POST['club_save_tournament_btn'] )
  {
    //  check if new tournament or edit
    if ( $_POST['club_game_delete_id'] == 'none' )
    {
      //  new tournament
      $ret        = club_add_tournament($fieldname, $directions);
      if ($ret)
      {
        echo '<div id="message" class="updated fade">';
        echo '<strong>Tournament added !!!</strong></div>';
      }
      else
      {
        echo '<div id="message" class="error fade">';
        echo '<strong>Tournament not added !!!</strong></div>';
      }
    }
    else
    {
      //  practise update
      $game_id = $_POST['club_game_delete_id'];
      $ret        = club_update_tournament($game_id);
      if ($ret)
      {
        echo '<div id="message" class="updated fade">';
        echo '<strong>Tournament updated !!!</strong></div>';
      }
      else
      {
        echo '<div id="message" class="error fade">';
        echo '<strong>Tournament not updated !!!</strong></div>';
      }
    }
  }

  club_plugin_print_Statsticsgame_page($edit_tournament);

  return;
}



// ////////////////////////////////////////////////////////////////////////////////
// // print practice page
// ////////////////////////////////////////////////////////////////////////////////
// function  club_plugin_create_practice_page()
// {
//   //  check if one edit button is pressed
//   $edit_practice = false;
//   foreach( $_POST as $key => $value )
//   {
//     if ( !(strpos( $key, 'club_edit_practice_') === false) )
//     {
//       $pos = strpos( $key, 'club_edit_practice_');
//       $pos1= strpos( $key, '_btn', $pos);
//       $id = (int)substr( $key, ($pos + 21), ($pos1 - ($pos + 21)) );
//       club_update_option('club_game_edit_id', $id);
//       $edit_practice = true;
//       break;
//     }
//   }

//   //  check if one delete button is pressed
//   $practice_deleted = false;
//   if ( $_POST['club_game_delete_id'] == 'none' )
//   {
//     foreach( $_POST as $key => $value )
//     {
//       if ( !(strpos( $key, 'club_delete_practice_') === false) )
//       {
//         $pos = strpos( $key, 'club_delete_practice_');
//         $pos1= strpos( $key, '_btn');
//         $id = (int)substr( $key, ($pos + 23), ($pos1 - ($pos + 23)) );

//         club_delete_game( $id );
//         $practice_deleted = true;
//         $game_id = $id;
//         break;
//       }
//     }
//   }
//   if ($practice_deleted)
//   {
//     echo '<div id="message" class="updated fade">';
//     echo '<strong>Practice entry deleted !!!</strong></div>';
//   }


//   if ( $_POST['club_practice_file_upload_btn'] )
//   {
//     club_upload_practices();

//     echo '<div id="message" class="updated fade">';
//     echo '<strong>Practices uploaded !!!</strong></div>';
//   }

//   if ( $_POST['club_save_practice_btn'] )
//   {
//     //  check if new practice or edit practice
//     if ( $_POST['club_game_delete_id'] == 'none' )
//     {
//       //  new practice
//       $ret        = club_add_practice();
//       if ($ret)
//       {
//         echo '<div id="message" class="updated fade">';
//         echo '<strong>Practice added !!!</strong></div>';
//       }
//       else
//       {
//         echo '<div id="message" class="error fade">';
//         echo '<strong>Practice not added !!!</strong></div>';
//       }
//     }
//     else
//     {
//       //  practice update
//       $game_id = $_POST['club_game_delete_id'];
//       $ret        = club_update_practice($game_id);
//       if ($ret)
//       {
//         echo '<div id="message" class="updated fade">';
//         echo '<strong>Practice updated !!!</strong></div>';
//       }
//       else
//       {
//         echo '<div id="message" class="error fade">';
//         echo '<strong>Practice not updated !!!</strong></div>';
//       }
//     }
//   }

//   if ( $_POST['club_del_all_practice_btn'] )
//   {
//     $ret = club_delete_all_practices();
//     if ($ret)
//     {
//       echo '<div id="message" class="updated fade">';
//       echo '<strong>All practices deleted !!!</strong></div>';
//     }
//     else
//     {
//       echo '<div id="message" class="error fade">';
//       echo '<strong>Error during delete process of practices !!!</strong></div>';
//     }
//   }

//   if ( $_POST['club_practices_list_set_season_btn'] )
//   {
//     //  set season for practices list
//     $season = $_POST['club_practice_edit_select_season'];
//     club_update_option('club_practice_season', $season);
//   }


//   club_plugin_print_practice_page($edit_practise);

//   return;
// }




////////////////////////////////////////////////////////////////////////////////
// print locations page
////////////////////////////////////////////////////////////////////////////////
function  club_plugin_create_fields_page()
{
  //  check if one edit button is pressed
  $edit_field = false;
  foreach( $_POST as $key => $value )
  {
    if ( !(strpos( $key, 'club_edit_field_') === false) )
    {
      $pos = strpos( $key, 'club_edit_field_');
      $pos1= strpos( $key, '_btn', $pos);
      $id = (int)substr( $key, ($pos + 18), ($pos1 - ($pos + 18)) );
      club_update_option('club_location_edit_id', $id);
      $edit_field = true;
      break;
    }
  }

  //  check if one delete button is pressed
  $field_deleted = false;
  if ( !($_POST['club_delete_field_id'] == 'none') )
  {
    foreach( $_POST as $key => $value )
    {
      if ( !(strpos( $key, 'club_delete_field_') === false) )
      {
        $pos = strpos( $key, 'club_delete_field_');
        if ( !(strpos( $key, '_btn') === false) )
        {
          $pos1= strpos( $key, '_btn');
          $id = (int)substr( $key, ($pos + 20), ($pos1 - ($pos + 20)) );

          $fields = club_get_locations();
          club_delete_location( $fields[$id]['fieldname'] );
          $field_deleted = true;
          break;
        }
      }
    }
  }
  if ($field_deleted)
  {
    echo '<div id="message" class="updated fade">';
    echo '<strong>Location deleted !!!</strong></div>';
  }

  if ( $_POST['club_save_location_btn'] )
  {
    //  check if new field or edit field
    if ( $_POST['club_delete_field_id'] == 'none' )
    {
      //  new player
      $fieldname  = $_POST['club_field_edit_fieldname'];
      $directions = $_POST['club_field_edit_directions'];
      $ret        = club_add_location($fieldname, $directions);
      if ($ret)
      {
        echo '<div id="message" class="updated fade">';
        echo '<strong>Location added !!!</strong></div>';
      }
      else
      {
        echo '<div id="message" class="error fade">';
        echo '<strong>Location not added !!!</strong></div>';
      }
    }
    else
    {
      //  location update
      $field_id = $_POST['club_delete_field_id'];
      $fieldname  = $_POST['club_field_edit_fieldname'];
      $directions = $_POST['club_field_edit_directions'];
      $ret        = club_update_location($fieldname, $directions);
      if ($ret)
      {
        echo '<div id="message" class="updated fade">';
        echo '<strong>Location updated !!!</strong></div>';
      }
      else
      {
        echo '<div id="message" class="error fade">';
        echo '<strong>Location not updated !!!</strong></div>';
      }
    }
  }

  if ( $_POST['club_del_all_fields_btn'] )
  {
    $ret = club_delete_all_locations();
    if ($ret)
    {
      echo '<div id="message" class="updated fade">';
      echo '<strong>Locations deleted !!!</strong></div>';
    }
    else
    {
      echo '<div id="message" class="error fade">';
      echo '<strong>Error during delete process of locations !!!</strong></div>';
    }
  }

  club_plugin_print_fields_page($edit_field);

  return;
}


////////////////////////////////////////////////////////////////////////////////
// print schedules page
////////////////////////////////////////////////////////////////////////////////
function  club_plugin_create_schedules_page()
{
  if ( $_POST['club_schedules_set_season_btn'] )
  {
    //  set season and team for players list
    $season = $_POST['club_schedules_list_select_season'];
    club_update_option('club_schedules_season', $season);
  }

  //  check if one edit button is pressed
  $edit_game = false;
  foreach( $_POST as $key => $value )
  {
    if ( !(strpos( $key, 'club_edit_game_') === false) )
    {
      $pos = strpos( $key, 'club_edit_game_');
      $pos1= strpos( $key, '_btn');
      $id = (int)substr( $key, ($pos + 17), ($pos1 - ($pos + 17)) );
      club_update_option('club_game_edit_id', $id);
      $edit_game = true;
      break;
    }
  }

  //  check if one delete button is pressed
  $game_deleted = false;
  if ( $_POST['club_game_delete_id'] != 'none' )
  {
    foreach( $_POST as $key => $value )
    {
      if ( !(strpos( $key, 'club_delete_game_') === false) )
      {
        $pos = strpos( $key, 'club_delete_game_');
        $pos1= strpos( $key, '_btn');
        $id = (int)substr( $key, ($pos + 19), ($pos1 - ($pos + 19)) );

        club_delete_game( $id );
        $game_deleted = true;
        break;
      }
    }
  }
  if ($game_deleted)
  {
    echo '<div id="message" class="updated fade">';
    echo '<strong>Game entry deleted !!!</strong></div>';
  }

  if ( $_POST['club_save_game_btn'] )
  {
    //  check if new player or edit player
    if ( $_POST['club_game_delete_id'] == 'none' )
    {
      //  new entry
      $ret       = club_add_schedule();
      if ($ret)
      {
        echo '<div id="message" class="updated fade">';
        echo '<strong>Game entry added !!!</strong></div>';
      }
      else
      {
        echo '<div id="message" class="error fade">';
        echo '<strong>Game entry not added !!!</strong></div>';
      }
    }
    else
    {
      //  schedule update
      $game_id = club_get_option('club_game_edit_id');
      $ret = club_update_schedule($game_id);
      if ($ret)
      {
        echo '<div id="message" class="updated fade">';
        echo '<strong>Game entry updated !!!</strong></div>';
      }
      else
      {
        echo '<div id="message" class="error fade">';
        echo '<strong>Game entry ' .  $game_id . 'not updated !!!</strong></div>';
      }
    }
  }

  if ( $_POST['club_schedules_file_upload_btn'] )
  {
    $season = club_get_option('club_schedules_season');
    $ret = club_upload_schedules($season);
    if ($ret)
    {
      echo '<div id="message" class="error fade">';
      echo '<strong>Error during file uploaded - schedule not uploaded!!!</strong></div>';
    }
    else
    {
      echo '<div id="message" class="updated fade">';
      echo '<strong>File uploaded and games added ' . $season . '!!!</strong></div>';
    }
  }

  if ( $_POST['club_del_all_schedules_btn'] )
  {
    $season = $_POST['club_schedules_list_select_season'];
    club_delete_all_schedules($season);
  }

  club_plugin_print_schedules_page($edit_game);

  return;
}




////////////////////////////////////////////////////////////////////////////////
// print game results page
////////////////////////////////////////////////////////////////////////////////
function  club_plugin_create_game_results_page()
{
  $edit_results = false;

  if ( $_POST['club_results_list_set_season_btn'] )
  {
    //  set season for games list
    $season = $_POST['club_results_list_select_season'];
    club_update_option('club_results_season', $season);

    echo '<div id="message" class="updated fade">';
    echo '<strong>Season set sucessfully !!!</strong></div>';
  }

  if ( $_POST['club_save_results_btn'] )
  {
    $ret = club_update_game_results();
    if (!$ret)
    {
      echo '<div id="message" class="error fade">';
      echo '<strong>Error - game results not updated !!!</strong></div>';
echo  mysql_error();
    }
    else
    {
      echo '<div id="message" class="updated fade">';
      echo '<strong>Game results updated!!!</strong></div>';
    }
  }

  if ( $_POST['club_edit_game_btn'] )
  {
    $edit_results = true;
    $game_id = $_POST['club_results_edit_game_select'];
    club_update_option('club_game_edit_id', $game_id);
  }

  if ( $_POST['club_del_all_games_btn'] )
  {
    $season = $_POST['club_results_list_select_season'];
    club_delete_all_schedules($season);
  }
/*
  if ( $_POST['club_gamechanger_upload_btn'] )
  {
    $game_id = club_get_option('club_game_edit_id');
    $season = club_get_option('club_schedules_season');
    $ret = club_upload_gamechanger_stats($game_id,$season);
    if ($ret)
    {
      echo '<div id="message" class="error fade">';
      echo '<strong>Error during GameChanger stats upload ' . $game_id . '- ' . $season . ' Game results not added !!!</strong></div>';
    }
    else
    {
      echo '<div id="message" class="updated fade">';
      echo '<strong>GameChanger stats uploaded !' . $game_id . '' . $season . ' !!</strong></div>';
    }
  }
*/
  if ( $_POST['club_stats_upload_btn'] )
  {
    $game_id = club_get_option('club_game_edit_id');
    $team = $_POST['club_home_or_away'];
    $ret = club_upload_stats($game_id,$team);
    if ($ret)
    {
      echo '<div id="message" class="error fade">';
      echo '<strong>Error during stats upload ' . $game_id . '- ' . $team . ' Stats not added !!!</strong></div>';
    }
    else
    {
      echo '<div id="message" class="updated fade">';
      echo '<strong>stats uploaded !' . $game_id . '' . $team . ' !!</strong></div>';
    }
  }
  if ( $_POST['club_iScore_batting_upload_btn'] )
  {
    $game_id = club_get_option('club_game_edit_id');
    $season = club_get_option('club_schedules_season');
    $ret = club_upload_iScore_battingstats($game_id,$season);
    if ($ret)
    {
      echo '<div id="message" class="error fade">';
      echo '<strong>Error during iScore batting stats upload ' . $game_id . '- ' . $season . ' Game results not added !!!</strong></div>';
    }
    else
    {
      echo '<div id="message" class="updated fade">';
      echo '<strong>iScore batting stats uploaded !' . $game_id . '' . $season . ' !!</strong></div>';
    }
  }

  if ( $_POST['club_iScore_pitching_upload_btn'] )
  {
    $game_id = club_get_option('club_game_edit_id');
    $season = club_get_option('club_schedules_season');
    $ret = club_upload_iScore_pitchingstats($game_id,$season);
    if ($ret)
    {
      echo '<div id="message" class="error fade">';
      echo '<strong>Error during iScore pitching stats upload ' . $game_id . '- ' . $season . ' Game results not added !!!</strong></div>';
    }
    else
    {
      echo '<div id="message" class="updated fade">';
      echo '<strong>iScore pitching stats uploaded !' . $game_id . '' . $season . ' !!</strong></div>';
    }
  }


  club_plugin_print_game_results_page($edit_results);

  return;
}


////////////////////////////////////////////////////////////////////////////////
// print uninstall page
////////////////////////////////////////////////////////////////////////////////
function club_plugin_create_uninstall_page()
{
  if ( $_POST['club_uninstall_plugin_btn'] )
  {
     club_plugin_uninstall();
  }
  else 
  {
  club_plugin_print_uninstall_page();
  }
}

////////////////////////////////////////////////////////////////////////////////
// print plugin option page and check post data
////////////////////////////////////////////////////////////////////////////////
function club_plugin_create_option_page()
{
  $seasons_list = club_get_seasons();

  if ( $_POST['club_add_season_btn'] )
  {
    if ( empty($_POST['club_season_new']) )
    {
      echo '<div id="message" class="error fade">';
      echo '<strong>' . __('No season - please fill the season input field!', 'club') . '</strong></div>';
    }
    else
    {
      $upid = $_POST['club_season_upid'];
      $season = $_POST['club_season_new'];
      if($upid!="") {
        $ret = club_updateSeason($season,$upid);
      } else{
        $ret = club_addSeason($season);
      }
      
      if ( $ret === false )
      {
        echo '<div id="message" class="error fade">';
        echo '<strong>' . __('season already exists!', 'club') . '</strong></div>';
      }
      else
      {
        echo '<div id="message" class="updated fade">';
        echo '<strong>' . __('Season added!', 'club') . '</strong></div>';
      }
    }
  }

  if ( $_POST['club_del_season_btn'] || $_POST['club_season_delid'] )
  {
    if($_POST['club_select_season']){
      $season = $seasons_list[$_POST['club_select_season']];
    }
    if($_POST['club_season_delid']) {
      $season = $_POST['club_season_delid'];
    }
    //echo "<pre>";
    //print_r($season); die;
    if ( !empty($season) )
      $ret = club_delete_season($season);
    switch ($ret)
    {
      case -10:
        echo '<div id="message" class="error fade">';
        echo '<strong>' . __('Season not deleted - this is the default season!', 'club') . '</strong></div>';
        break;
      case -20:
        echo '<div id="message" class="error fade">';
        echo '<strong>' . __('Season not deleted - at least one season must exists!', 'club') . '</strong></div>';
        break;
      case 10:
        echo '<div id="message" class="updated fade">';
        echo '<strong>' . __('Season deleted!', 'club') . '</strong></div>';
        break;
    }
  }

  if ( $_POST['club_set_defs_btn'] )
  {
    
    $teams_list   = club_get_teams();
    $defs['defaultTeam']   = $teams_list[$_POST['club_def_team_select']];
    $defs['defaultSeason'] = $seasons_list[$_POST['club_def_season_select']];
    $defs['defaultLeague'] = $_POST['club_def_league_select'];
    $defs['defaultCheck'] = $_POST['club_def_check_select'];

    if(isset($_POST['club_plugin_option_plugin_credit'])) {
      $defs['defaultCredit'] = 1;
    }

    $ret = club_set_defaults($defs);
    echo '<div id="message" class="updated fade">';
    echo '<strong>' . __('Default values changed!', 'club') . '</strong></div>';
  }

  if ( $_POST['club_add_new_team_btn'] )
  {
    if ( empty($_POST['club_add_new_team']) )
    {
      echo '<div id="message" class="error fade">';
      echo '<strong>' . __('No team name - please fill out the team name input field!', 'club') . '</strong></div>';
    }
    else
    {
      $team = $_POST['club_add_new_team'];
      club_add_team_season( $team, $season = NULL);
      echo '<div id="message" class="updated fade">';
      echo '<strong>' . __('Team added!', 'club') . '</strong></div>';
    }
  }

  if ( $_POST['club_select_team_delete_btn'] )
  {
    $team = $_POST['club_select_team_delete'];
    club_delete_team( $team );
    echo '<div id="message" class="updated fade">';
    echo '<strong>' . __('Team deleted!', 'club') . '</strong></div>';
  }

  if ( $_POST['club_add_new_league_btn'] )
  { 
    if ( empty($_POST['club_add_new_league']) )
    {
      $league_msg='<div id="message" class="error fade">';
      $league_msg.='<strong>' . __('No League name - please fill out the League name input field!', 'club') . '</strong></div>';
    }
    else
    { 
      $upid = $_POST['club_league_upid'];
      $league = $_POST['club_add_new_league'];
      
      if($upid!="")
        $rstl = club_add_league_season( $league, $season = NULL, $upid);
      else
        $rstl = club_add_league_season( $league, $season = NULL, '');

      $league_msg ='<div id="message" class="updated fade">';
      if($rstl == 0)
        $league_msg.='<strong>' . __(':League allready exist!', 'club') . '</strong></div>';
      elseif($rstl == 1)
        $league_msg.='<strong>' . __(':League added!', 'club') . '</strong></div>';
      elseif($rstl == 2)
      { 
        //$url = admin_url('admin.php?page=club-option-page');
        //header("Location:$url");die;
        //wp_redirect( admin_url('admin.php?page=club-option-page'), ':League Updated!' );die;
        $league_msg.='<strong>' . __(':League Updated!', 'club') . '</strong></div>';
      }
      else
        $league_msg.='<strong>' . __(':Error!', 'club') . '</strong></div>';
    }
    echo $league_msg;
    /*$value = "Test Cookie";
    setcookie("TestCookie", $value, time()+3600);
    echo $_COOKIE['TestCookie'];
    die;*/
  }

  if ( $_POST['club_select_league_delete_btn'] || $_POST['club_league_delid'])
  {
    if($_POST['club_select_league_delete_btn'])
      $league = $_POST['club_select_league_delete'];
    else if($_POST['club_league_delid'])
      $league = $_POST['club_league_delid'];

    club_delete_league( $league );
    echo '<div id="message" class="updated fade">';
    echo '<strong>' . __('League deleted!', 'club') . '</strong></div>';
  }

  if ( $_POST['club_add_season_teams_btn'] )
  {
    $season    = $seasons_list[$_POST['club_select_season']];
    $team_list = $_POST['club_select_season_teams'];
    if ( empty($season) OR empty($team_list) )
    {
      echo '<div id="message" class="error fade">';
      echo '<strong>' . __('Please select teams and season!', 'club') . '</strong></div>';
    }
    else
    {
      club_add_team_season( $team_list, $season);
      echo '<div id="message" class="updated fade">';
      echo '<strong>' . __('Teams added to season!', 'club') . '</strong></div>';
    }
  }


  if ( $_POST['club_update_options_btn'] )
  {
    club_save_plugin_options();

    echo '<div id="message" class="updated fade">';
    echo '<strong>Plugin Settings saved !!!</strong></div>';
  }


  club_plugin_print_option_page();

  return;
}



function club_is_min_wp($version)
{
  return version_compare( $GLOBALS['wp_version'], $version. 'alpha', '>=');
}




function club_more_reccurences()
{
    return array(
        'weekly' => array('interval' => 604800, 'display' => 'Once Weekly'),
        'monthly' => array('interval' => 2592000, 'display' => 'Once Monthly'),
        );
}

///////////////////////////////////////////////////////
// print import page
//////////////////////////////////////////////////////
function club_plugin_create_import_page()
{
  if ( $_POST['club_test_csv_upload_btn'] ){
    if ($_FILES["club_plugin_test_csv_upload"]["error"] > 0)
      {
      echo "Error: " . $_FILES["club_plugin_test_csv_upload"]["error"] . "<br />";
      }
      else
      {
      echo "Upload: " . $_FILES["club_plugin_test_csv_upload"]["name"] . "<br />";
      echo "Type: " . $_FILES["club_plugin_test_csv_upload"]["type"] . "<br />";
      echo "Size: " . ($_FILES["club_plugin_test_csv_upload"]["size"] / 1024) . " Kb<br />";
      echo "Stored in: " . $_FILES["club_plugin_test_csv_upload"]["tmp_name"];
      move_uploaded_file($_FILES["club_plugin_test_csv_upload"]["tmp_name"],BBNPDIR."upload_tmp");
      echo "Stored in:". BBNPDIR ."upload_tmp";

      }
    }

  if ( $_POST['club_import_data_btn'] )
  {
    $array = $_POST['row'];
    if (isset( $_POST['club_plugin_upload_schedule'])) {
      $dbschedule = array("HOME"=>"homeTeam","AWAY"=>"visitingTeam","DATE"=>"gameDate","TIME"=>"gameTime","FIELD"=>"field");
    }

    for ($line = 1; $line < sizeof($array); $line++)
    {
    $query = "INSERT INTO wp_clubhouseManager_schedule SET ";
      foreach($array[$line] as $key => $value)
      {
          if (isset($dbschedule[$array[0][$key]])) {
            $value = trim($value," \t\n\r\x0B\'\"");
            $value = mysql_real_escape_string($value);
	  $query .= $dbschedule[$array[0][$key]].' = "' . $value . '",';
	  }
      }
	$tmpquery = substr($query,0,-1);
echo $query.'<br>';
//     echo $tmpquery.';<br>';
    }
    unlink(BBNPDIR ."upload_tmp");
  }

  club_plugin_print_import_page();

  return;
}


?>
