<?php
class TgBot {
	private $_token = "" ;
	function __construct($token) {
		$this->_token = $token;
	}
	function getMe(){
		$response = wp_remote_get("https://api.telegram.org/bot$this->_token/getMe");
		$object = json_decode($response['body']);
		return $object->ok ;
	}
	function postTo($channel , $post ){
		$options = array(
			'method' => 'POST',
			'body' => array(
				"chat_id" => $channel,
				"text" => $post,
				"parse_mode" => "HTML"
			)
		);
		$response = wp_remote_post("https://api.telegram.org/bot$this->_token/sendMessage",$options);
		$object = json_decode($response['body']);
		return $object->ok;
	}
}