<?php
/*
Plugin Name: Telegram Auto Post
Plugin URI: http://plugins.8thbit.net/
Description: this plugin provides you auto post for Telegram Channels via Telegram Bot
Author: Koorosh
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
		global $options;
		$options['tgwp_token'] = $token;
		$options['tgwp_channels'] = $channels;
		$tg = new TgBot($token);
		if($tg->getMe()){
			echo "<div class=\"notice notice-success\"><p>New Bot Configurations has been saved successfully</p></div>";
		}else{
			echo "<div class=\"notice notice-error\"><p>Invalid token :/</p></div>";
		}
		update_option('telegram_posts',$options);
	}


	require 'includes/options_page.php';
}
function tgwp_backendStyles(){
	wp_enqueue_style('tgwp_backend_style',plugins_url('telegram-posts/includes/back.css'));
}
function tgwp_renderMetaBox(){
	echo '<input type="checkbox" name="tgwp_send" checked />Send To Telegram<br /><br /><span>it doesn\'t send updates</span><br/>';
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
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;
	if ( wp_is_post_revision( $post_id ) )
		return;
	if ( isset( $_POST['tgwp_send'] )){
		$myPost = get_post($post_id);
		if( $myPost->post_modified_gmt == $myPost->post_date_gmt ){
			$tg = new TgBot($options['tgwp_token']);
			if(!isset($options['tgwp_channels']) || $options['tgwp_channels'] == "" )
				return;
			$channels = explode("\r\n",$options['tgwp_channels']);
			if (count($channels) == 0)
				$channels = explode("\n",$options['tgwp_channels']); //TODO FIX THIS FOR UNIX SYSTEMS
			for ($i = 0 ; $i < count($channels) ; $i++){
				try{
					$tg->postTo($channels[$i],wp_strip_all_tags($post->post_content) . "\n\n <a href='" . get_post_permalink($post_id) . "'>[🌐] Read More...</a>");
				}catch (\Exception $er){

				}
			}
		}
	}
}
add_action('admin_menu','tgwp_options_menu');
add_action('admin_head','tgwp_backendStyles');
add_action('add_meta_boxes','tgwp_options_post' );
add_action('save_post','tgwp_onPostSaved' ,10,3);
