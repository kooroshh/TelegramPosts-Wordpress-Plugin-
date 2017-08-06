<?php
class TgBot {
	private $_token = "" ;
	function __construct($token) {
		$this->_token = $token;
	}
	function getMe(){
		$response = wp_remote_get("https://api.telegram.org/bot$this->_token/getMe");
		if(gettype($response) == gettype(WP_Error::class)){
			return false;
		}
		$object = json_decode($response['body']);
		return $object->ok ;
	}
	function postTo($channel , $post ){
		try {
			$options  = array(
				'method' => 'POST',
				'body'   => array(
					"chat_id"    => $channel,
					"text"       => $post,
					"parse_mode" => "HTML"
				)
			);
			$response = wp_remote_post( "https://api.telegram.org/bot$this->_token/sendMessage", $options );
			if(gettype($response) == gettype(WP_Error::class)){
				return false;
			}
			$object   = json_decode( $response['body'] );
			return $object->ok;
		}catch (Exception $e){
			return false;
		}
	}
	function postImage($channel , $image ){
		try {
			$options  = array(
				'method' => 'POST',
				'body'   => array(
					"chat_id"    => $channel,
					"photo"       => $image,
				)
			);
			$response = wp_remote_post( "https://api.telegram.org/bot$this->_token/sendPhoto", $options );
			if(gettype($response) == gettype(WP_Error::class)){
				return -1;
			}
			$object   = json_decode( $response['body'] );

			if($object->ok){
				return $object->result->message_id;
			}
		}catch (Exception $e){
			return -1;
		}
	}
}