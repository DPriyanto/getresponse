<?php 
namespace DPriyanto\GetResponse;
//use DPriyanto\Curl\Curl;

use DPriyanto\Curl\Curl;
//require_once(ROOT.DS.'vendor'.DS.'DPriyanto'.DS.'curl'.DS.'src'.DS.'Curl.php');

//require_once('Curl.php');


class GetResponse {
    var $mode = "key";
    var $apiKey = "";
	var $clientId = "";
	var $clientSecret = "";
	var $accessToken = null;
	var $refreshToken = null;
	var $apiUrl = "https://api.getresponse.com/v3";
	var $authUrl = "https://app.getresponse.com/oauth2_authorize.html?response_type=code&client_id=[CLIENTID]&state=xyz";
	
	function __construct($mode='key',$clientId,$clientSecret=null,$accessToken=null,$refreshToken=null) {
	    $this->mode = $mode;
	    switch($this->mode) {
	        case 'key':
	            $this->apiKey = $clientId;
	        break;
	        case 'oauth':
	            $this->clientId = $clientId;
	            $this->clientSecret = $clientSecret;
	            
	            //simplest way
	            $this->apiUrl = str_replace('https://','https://'.$this->clientId.':'.$this->clientSecret.'@',$this->apiUrl);
	            
	            $this->authUrl = str_replace('[CLIENTID]',$this->clientId,$this->authUrl);
	            
	            if($accessToken != null && $refreshToken != null) {
	                $this->accessToken = $accessToken;
	                $this->refreshToken = $refreshToken;
	            }
	        break;
	    }
	}
	
	function call($path,$method='GET',$params = array()) {
		$Curl = new Curl();
		switch($this->mode) {
		    case 'key':
		        $Curl->httpheader = [
		            'X-Auth-Token: api-key ' . $this->apiKey,
		            'Content-Type: application/json'
		        ];
		    break;
		    case 'oauth':
		        if($this->accessToken != null)
		            $Curl->httpheader = array('Authorization: Bearer '.$this->accessToken);
		    break;
		}

		$q = "";
		switch($method) {
			case 'GET':
				$q = "?".http_build_query($params,'','&');
			break;
			case 'POST':
			    $Curl->multipart = true;
				$Curl->posts = json_encode($params);
			break;
			case 'DELETE':
				$Curl->set(CURLOPT_CUSTOMREQUEST,'DELETE');
			break;
		}
		
		$url = $this->apiUrl.$path.$q;
		//pr($url);
		$Curl->url = $url;
		
		$Curl->header = true;

		$Curl->exec();
		//pr($Curl->posts);
		//pr($Curl->info);
		//exit;
		
		$json = json_decode($Curl->content,true);
		//pr($json);
		return $json;
		//The access token provided is expired
		//The access token provided is invalid
		//httpStatus
	}
}
?>
