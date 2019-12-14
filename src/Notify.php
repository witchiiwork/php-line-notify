<?php
namespace W2W\LINE;

use GuzzleHttp\Client;

class Notify {
	public const conAPIURLADR = "https://notify-api.line.me/api/notify";
	
	private $strLINETOKEN = null;
	
	public function __construct($strLINETOKEN) {
		$this->token = $strLINETOKEN;
		$this->http = new Client();
	}
	
	public function setToken($strLINETOKEN) {
		$this->token = $strLINETOKEN;
	}
	
	public function getToken() {
		return $this->token;
	}
	
	public function send($strINPTXTMSG, $strINPTXTIMG = null, $strINPTXTSTK = null) {
		if(empty($strINPTXTMSG)) {
			return false;
		}
		
		$strREQUESTXX = ["headers" => ["Authorization" => "Bearer " . $this->token]];
		$strREQUESTXX["multipart"] = [["name" => "message", "contents" => $strINPTXTMSG]];
		
		if(!empty($strINPTXTIMG) && preg_match("#^https?://#", $strINPTXTIMG)) {
			$strREQUESTXX["multipart"][] = ["name" => "imageThumbnail", "contents" => $strINPTXTIMG];
			$strREQUESTXX["multipart"][] = ["name" => "imageFullsize", "contents" => $strINPTXTIMG];
		} elseif(!empty($strINPTXTIMG) && file_exists($strINPTXTIMG)) {
			$strREQUESTXX["multipart"][] = ["name" => "imageFile", "contents" => fopen($strINPTXTIMG, "r")];
		}
		
		if(!empty($strINPTXTSTK) && !empty($strINPTXTSTK["stickerPackageId"]) && !empty($strINPTXTSTK["stickerId"])) {
			$strREQUESTXX["multipart"][] = ["name" => "stickerPackageId", "contents" => $strINPTXTSTK["stickerPackageId"]];
			$strREQUESTXX["multipart"][] = ["name" => "stickerId", "contents" => $strINPTXTSTK["stickerId"]];
		}
		
		$strRESPONSEX = $this->http->request("POST", Notify::conAPIURLADR, $strREQUESTXX);
		
		if($strRESPONSEX->getStatusCode() != 200) {
			return false;
		}
		
		$strGETTXTBOD = (string)$strRESPONSEX->getBody();
		$strJSONDCODE = json_decode($strGETTXTBOD, true);
		
		if(empty($strJSONDCODE["status"]) || empty($strJSONDCODE["message"])) {
			return false;
		}
		
		return true;
	}
}
