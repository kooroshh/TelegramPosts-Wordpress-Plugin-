<?php
/*
Plugin Name: Telegram Auto Post
Plugin communicate/plugins.8thbit.net/
Description: this plugin provides you auto post for Telegram Channels via Telegram Bots
Author: Koorosh Ghorbani
Version: 1.0
Author URI: http://8thbit.net/
*/
if ( ! defined( 'ABSPATH' ) )
	die('You Should not be here');
require_once 'includes/tg-bot.php';
$PATH = WP_PLUGIN_URL . '/telegram-posts';
$options = array();
function tgwp_options_menu(){
	add_options_page('Telegram Posts','Bot Options','manage_options','TelegramBotOptions','tgwp_RenderBotOptions');
}
function tgwp_RenderBotOptions(){
	if(!current_user_can('manage_options'))
		wp_die('You Have No Permission to this page');
	if(isset($_POST['tgwp_submit'])){
		$token = isset($_POST['tgwp_token']) ? esc_html($_POST['tgwp_token']) : "" ;
		$channels = isset($_POST['tgwp_channels']) ? esc_html($_POST['tgwp_channels']) : "" ;
		$readmore = isset($_POST['tgwp_readmore']) ? esc_html($_POST['tgwp_readmore']) : "[🌐] Read More..." ;
		global $options;
		$options['tgwp_token'] = $token;
		$options['tgwp_channels'] = $channels;
		$options['tgwp_readmore'] = $readmore;
		$tg = new TgBot($token);
		if($tg->getMe()){
			echo "<div class=\"notice notice-success\"><p>New Bot Configurations has been saved successfully</p></div>";
		}else{
			echo "<div class=\"notice notice-error\"><p>Unable to connect to your bot :(</p></div>";
		}
		update_option('telegram_posts',$options);
	}


	require 'includes/options_page.php';
}
function tgwp_backendStyles(){
	wp_enqueue_style('tgwp_backend_style',plugins_url('telegram-posts/includes/back.css'));
}
function tgwp_renderMetaBox(){
	global $options ;
	$options = get_option('telegram_posts');
	if(!isset($options['tgwp_channels']) || $options['tgwp_channels'] == "")
	{
		echo '<span>There are no active channels :/</span>';
		return;
	}

	$channels = explode("\r\n",$options['tgwp_channels']);
	if (count($channels) == 0)
		$channels = explode("\n",$options['tgwp_channels']); //TODO FIX THIS FOR UNIX SYSTEMS
	if(count($channels) == 1){
		echo '<input type="checkbox" name="tgwp_send" checked />Send To Telegram<br /><br /><span>Post updates will not posts to your telegram channels :)</span><br/>';
	}else{
		$output = "<select name=\"tgwp_channels[]\" multiple class='full_width'>";
		for($i = 0 ; $i < count($channels) ; $i++){
			$output .= "<option value='$channels[$i]' selected=\"selected\">$channels[$i]</option>";
		}
		$output .= "</select>";
		$output .= "<br/><br/><span>Post updates will not posts to your telegram channels :)</span>";
		echo $output;
	}
	echo '<br><br><input type="checkbox" name="tgwp_force" />Send This Update (Force)';
}
function tgwp_options_post() {
	add_meta_box(
		'tgwp_page',
		'Telegram Post',
		'tgwp_renderMetaBox',
		'post',
		'side',
		'high'
	);
}
function tgwp_onPostSaved($post_id ,$post,$update) {
	global $options;
	$options = get_option('telegram_posts');
	if($options['tgwp_readmore'] == "" )
		$options['tgwp_readmore'] = "[🌐] Read More...";
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;
	if ( wp_is_post_revision( $post_id ) )
		return;
	$myPost = get_post($post_id);
	$forcemode = isset($_POST['tgwp_force']) && $_POST['tgwp_force'] == "on" ? true  :false;
	if( $myPost->post_modified_gmt != $myPost->post_date_gmt && !$forcemode)
		return;
	$tg = new TgBot($options['tgwp_token']);
	$ps = wp_strip_all_tags( $post->post_content ) . "\r\n\r\n<a href='" . get_post_permalink( $post_id ) . "'>". $options['tgwp_readmore'] ."</a>";

	if ( isset( $_POST['tgwp_send'] )){
		if(!isset($options['tgwp_channels']) || $options['tgwp_channels'] == "" )
			return;
		$channel = $options['tgwp_channels'];
		$imgsrc = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), "Full" );
		if($imgsrc != false){
			$tg->postImage($channel,$imgsrc[0]);
		}
		$tg->postTo( $channel, $ps );
	}else if(isset($_POST['tgwp_channels']) && count($_POST['tgwp_channels']) > 0) {
		$channels = $_POST['tgwp_channels'];
		for ( $i = 0; $i < count( $channels ); $i ++ ) {
			try {
				$imgsrc = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), "Full" );
				if($imgsrc != false){
					$tg->postImage($channels[ $i ],$imgsrc[0]);
				}
				$tg->postTo( $channels[$i], $ps );
			} catch ( \Exception $er ) {

			}
		}
	}
}
add_action('admin_menu','tgwp_options_menu');
add_action('admin_head','tgwp_backendStyles');
add_action('add_meta_boxes','tgwp_options_post' );
add_action('save_post','tgwp_onPostSaved' ,10,3);
