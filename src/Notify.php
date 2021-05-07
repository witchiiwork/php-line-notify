<?php
namespace WITCH2WORK\Line;

use GuzzleHttp\Client;

class Notify {
	public const URI = "https://notify-api.line.me/api/notify";
	
	private $token = null;
	private $client = null;
	
	public function __construct($token) {
		$this->token = $token;
		$this->http = new Client();
	}
	
	public function setToken($token) {
		$this->token = $token;
	}
	
	public function getToken() {
		return $this->token;
	}
	
	public function send($message, $image = null, $sticker = null) {
		if(empty($message)) {
			return false;
		}
		
		$request = [
			"headers" => [
				"Authorization" => "Bearer " . $this->token
			]
		];
		$request["multipart"] = [
			[
				"name" => "message",
				"contents" => $message
			]
		];
		
		if(!empty($image) && preg_match("#^https?://#", $image)) {
			$request["multipart"][] = [
				"name" => "imageThumbnail",
				"contents" => $image
			];
			$request["multipart"][] = [
				"name" => "imageFullsize",
				"contents" => $image
			];
		} elseif(!empty($image) && file_exists($image)) {
			$request["multipart"][] = [
				"name" => "imageFile",
				"contents" => fopen($image, "r")
			];
		}
		
		if(!empty($sticker) && !empty($sticker["stickerPackageId"]) && !empty($sticker["stickerId"])) {
			$request["multipart"][] = [
				"name" => "stickerPackageId",
				"contents" => $sticker["stickerPackageId"]
			];
			$request["multipart"][] = [
				"name" => "stickerId",
				"contents" => $sticker["stickerId"]
			];
		}
		
		$response = $this->http->request("POST", Notify::URI, $request);
		
		if($response->getStatusCode() != 200) {
			return false;
		}
		
		$body = (string)$response->getBody();
		$json = json_decode($body, true);
		
		if(empty($json["status"]) || empty($json["message"])) {
			return false;
		}
		
		return true;
	}
}
