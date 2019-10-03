<?php
namespace W2W\LINE;

use GuzzleHttp\Client;

class Notify {
	const API_URL = "https://notify-api.line.me/api/notify";
	
	private $objTOK = null;
	private $objHTT = null;
	
	public function __construct($objTOK) {
		$this->token = $objTOK;
		$this->http = new Client();
	}
	
	public function setToken($objTOK) {
		$this->token = $objTOK;
	}
	
	public function getToken() {
		return $this->token;
	}
	
	public function send($objTEX, $objIMG = null, $objSTK = null) {
		if(empty($objTEX)) {
			return false;
		}
		
		$objREQ = [
			"headers" => [
				"Authorization" => "Bearer " . $this->token,
			],
		];
		$objREQ["multipart"] = [
			[
				"name" => "message",
				"contents" => $objTEX
			]
		];
		
		if(!empty($objIMG) && preg_match("#^https?://#", $objIMG)) {
			$objREQ["multipart"][] = [
				"name" => "imageThumbnail",
				"contents" => $objIMG
			];
			$objREQ["multipart"][] = [
				"name" => "imageFullsize",
				"contents" => $objIMG
			];
		} elseif(!empty($objIMG) && file_exists($objIMG)) {
			$objREQ["multipart"][] = [
				"name" => "imageFile",
				"contents" => fopen($objIMG, "r")
			];
		}
		
		if(!empty($objSTK) && !empty($objSTK["stickerPackageId"]) && !empty($objSTK["stickerId"])) {
			$objREQ["multipart"][] = [
				"name" => "stickerPackageId",
				"contents" => $objSTK["stickerPackageId"]
			];
			$objREQ["multipart"][] = [
				"name" => "stickerId",
				"contents" => $objSTK["stickerId"]
			];
		}
		
		$objRES = $this->http->request("POST", Notify::API_URL, $objREQ);
		
		if($objRES->getStatusCode() != 200) {
			return false;
		}
		
		$objBOD = (string)$objRES->getBody();
		$objJSO = json_decode($objBOD, true);
		
		if(empty($objJSO["status"]) || empty($objJSO["message"])) {
			return false;
		}
		
		return true;
	}
}
