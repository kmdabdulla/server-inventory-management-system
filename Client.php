<?php
require 'reallySimpleJWT/autoload.php';

use ReallySimpleJWT\Build;
use ReallySimpleJWT\Validate;
use ReallySimpleJWT\Encode;
use ReallySimpleJWT\Token;
use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Jwt;
use ReallySimpleJWT\Secret;

	/**
 	* Client to validate request data and send it securely to server inventory service API for performing
 	* the server inventory management operation.
 	*
 	* @author Mohamed Abdulla
 	*/
class Client {


	/**
	* Secret for signing and verifying the sign of JSON Web Token (JWT) transmitted between client and server service API.
	*
	* @var string $secretForSigningToken.
	*/
	private $secretForSigningToken = "!canBeCoolsecReT$978*"; 


	/**
	* Encryption algorithm to encrypt the signed JWT. 
	*
	* @var string $cipher.
	*/
	private $cipher = "AES-128-CBC"; 


	/**
	* Symmetric secret key for encrypting and decrypting the signed JWT.
	*
	* @var string $key.
	*/
	private $key = "06f0eb165278f59cae99b1a801eb3bf6ce2bf594a88d18d0b7431a506ec78d80";


	/**
	* Initialization vector for encrypting and decrypting the signed JWT.
	*
	* @var string $iv.
	*/
	private $iv =  "8a4c87c4487997a54f28479b9072688a";


	/**
	* Issuer of the signed request JWT to server inventory service API.
	*
	* @var string $tokenIssuer.
	*/
	private $tokenIssuer = "Client";


	/**
	* Intended audience for the signed request JWT issued by Client.  
	*
	* @var string $tokenAudience.
	*/
	private $tokenAudience = "ServerInventoryServiceApi";


	/**
	* Subject of the signed request JWT.
	*
	* @var string $tokenSubject.
	*/
	private $tokenSubject = "ServerInventoryManagement";


	/**
	* URL for reaching the server inventory service API. 
	*
	* @var string $url.
	*/
	private $url = "http://localhost/ServerInventoryServiceApi.php";


	/**
	* Return the server details matching the condition.
	*
	* @param Array $requestData. Array containing the necessary parameters
	* $requestData = [
 	*      'columnName'     => (string) Column name of the table Servers. Required.
 	*      'value' 			=> (string) Value to be matched. Required.
 	*    ]
 	*
 	* @return Array $responseData. Array containing the response
 	* $responseData = [
 	*      'status'     => (string) Success or Failed.
 	*      'message' 	=> (Array) matching server details (can be empty) or (string) failure message.
 	*    ]
	*/
	public function findServers($requestData) {
		$requestData['action'] = "findServer";
		if(!isset($requestData['columnName'],$requestData['value'])) {
			$responseData['status'] = "Failed";
			$responseData['message'] =  "columnName or value fields are not supplied";
			goto respond;	
		}
		if($requestData['columnName']=="IP") {
			if (filter_var($requestData['value'], FILTER_VALIDATE_IP)) {
				$requestData['value'] = ip2long($requestData['value']);
			} else {
				$responseData['status'] = "Failed";
				$responseData['message'] =  "Improper IP address";
				goto respond;
			}
		}
		$responseData = $this->handleDataTransfer($requestData);
		if($responseData['status']=="Success") {
			$responseData = $this->convertIPToHumanReadable($responseData['message']);
		}
		respond:
		return $responseData;
	}


	/**
	* Returns all the server details with the provided limit and offset.
	* 
	* @param Array $requestData. Array containing the necessary parameters
	* $requestData = [
 	*      'limit'     => (int) Number of Server details to be returned. Default:10
 	*      'offset'    => (int) Specifies row position in table. Default:0 (from the beginning).
 	*    ]
 	*
 	* @return Array $responseData. Array containing the response
 	* $responseData = [
 	*      'status'     => (string) Success or Failed.
 	*      'message' 	=> (Array) matching server details (can be empty) or (string) failure message.
 	*    ]
	*/
	public function listAllServers($requestData=NULL) {
		$requestData['action'] = "listAllServers";
		if(!isset($requestData['limit'])) {
			$requestData['limit'] = "10";
		}
		if(!isset($requestData['offset'])) {
			$requestData['offset'] = "0";
		}
		$responseData = $this->handleDataTransfer($requestData);
		if($responseData['status']=="Success") {
			$responseData = $this->convertIPToHumanReadable($responseData['message']);
		}
		return $responseData;
	}


	/**
	* Add the server details to the server inventory database.
	* 
	* @param Array $requestData. Array containing the necessary parameters
	* $requestData = [
 	*      'ServerId'     => (string) Unique identifier for the server. Required
 	*      'ServerName'   => (string) Server Name. Required
 	*	   'IP'   		  => (string) Server IP address. Required
 	*	   'Location'     => (string) Location of the Server. Required
 	*	   'Description'  => (string) Description about the Server. Required	
 	*    ]
 	*
 	* @return Array $responseData. Array containing the response.
 	* $responseData = [
 	*      'status'     => (string) Success or Failed.
 	*      'message' 	=> (string) success or failure message.
 	*    ]
	*/
	public function addServer($requestData) {
		$requestData['action'] = "addServer";
		if(!isset($requestData['ServerName'],$requestData["IP"],$requestData["ServerId"],$requestData['Location'],$requestData['Description'])) {
			$responseData['status'] = "Failed";
			$responseData['message'] =  "Please make sure all fields: ServerName,ServerId,IP,Location and Description are supplied";
			goto respond;
		}
		if (filter_var($requestData['IP'], FILTER_VALIDATE_IP)) {
				$requestData['IP'] = ip2long($requestData['IP']);
		} else {
				$responseData['status'] = "Failed";
				$responseData['message'] =  "Improper IP address";
				goto respond;
		}
		$responseData = $this->handleDataTransfer($requestData);
		respond:
		return $responseData;
	}


	/**
	* Update the server details in the server inventory database.
	* 
	* @param Array $requestData. Array containing the necessary parameters.
	* At least one parameter other than ServerId is required.
	* $requestData = [
 	*      'ServerId'     => (string) Unique identifier for the server. Required
 	*      'ServerName'   => (string) Server Name. Optional
 	*	   'IP'   		  => (string) Server IP address. Optional
 	*	   'Location'     => (string) Location of the Server. Optional
 	*	   'Description'  => (string) Description about the Server. Optional	
 	*    ]
 	*
 	* @return Array $responseData. Array containing the response
 	* $responseData = [
 	*      'status'     => (string) Success or Failed.
 	*      'message' 	=> (string) success or failure message.
 	*    ]
	*/
	public function updateServer($requestData) {
		$requestData['action'] = "updateServer";
		if(!isset($requestData['ServerId'])) {
			$responseData['status'] = "Failed";
			$responseData['message'] =  "ServerId not found";
			goto respond;
		}
		if(isset($requestData['IP'])) {
			if (filter_var($requestData['IP'], FILTER_VALIDATE_IP)) {
				$requestData['IP'] = ip2long($requestData['IP']);
			} else {
				$responseData['status'] = "Failed";
				$responseData['message'] =  "Improper IP address";
				goto respond;
			}
		}
		$responseData = $this->handleDataTransfer($requestData);

		respond:
		return $responseData;
	}


	/**
	* Delete the server details from the server inventory database.
	* 
	* @param Array $requestData. Array containing the necessary parameters.
	*
	* $requestData = [
 	*      'ServerId'     => (string or Array) one or many unique identifier of the server(s). Required
 	*    ]
 	*
 	* @return Array $responseData. Array containing the response
 	* $responseData = [
 	*      'status'     => (string) Success or Failed.
 	*      'message' 	=> (string) success or failure message.
 	*    ]
	*/
	public function deleteServers($requestData) {
		$requestData['action'] = "deleteServer";
		if(!isset($requestData['ServerId'])) {
			$responseData['status'] = "Failed";
			$responseData['message'] =  "ServerId field not found";
			return $responseData;
		}
		$responseData = $this->handleDataTransfer($requestData);
		return $responseData;
	}


	/**
	* Invokes the necessary funciton to create signed JWT out of request data, encrypting it and
	* sending it to the server.
	*
	* Invokes the function to decrypt and verify the received JWT from server inventory service API.
	* 
	* @param Array $requestData. Array containing the parameters depending on database operation.
	* see above functions for the $requestData contents.
	*
 	* @return array $responseData. Array containing the response
 	* $responseData = [
 	*      'status'     => (string) Success or Failed.
 	*      'message' 	=> (string) success or failure message or (Array) the response data.
 	*    ]
	*/
	private function handleDataTransfer($requestData) {
		$requestJWStoken = $this->createRequestJWSToken($requestData);
		$encryptedRequestJWSToken = $this->encryptRequestJWSToken($requestJWStoken);
		$encryptedResponseJWSToken = $this->sendRequest($encryptedRequestJWSToken);
		$responseJWSToken = $this->decryptResponseJWSToken($encryptedResponseJWSToken);
		$responseData = $this->verifyResponseJWSToken($responseJWSToken);
		return $responseData;
	}


	/**
	* Sends the encrypted signed JWT containing the request data to the server inventory service Api.
	* 
	* @param string $encryptedRequestJWSToken. Encrypted request signed JWT. 
	*
 	* @return string $encryptedResponseJWSToken. Encrypted response signed JWT.
	*/
	private function sendRequest($encryptedRequestJWSToken) {
	$post =  array('requestData'=>urlencode(($encryptedRequestJWSToken)));
	$curl = curl_init($this->url);
	curl_setopt($curl, CURLOPT_TIMEOUT, 3);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
	$encryptedResponseJWSToken = curl_exec($curl);
	curl_close($curl);
	return $encryptedResponseJWSToken;
	}


	/**
	* Create a signed JWT containing the request data.
	*
	* @param Array $requestData. Array containing the parameters depending on database operation.
	* see above database CRUD functions for the requestData contents. 
	*
 	* @return string $requestJWStoken. Signed JWT containing necessary claims and $requestData payload.
	*/
	private function createRequestJWSToken($requestData) {
		$build = new Build('JWT', new Validate(), new Secret(), new Encode());
		$jwt = $build->setContentType('JWT')
    				 ->setSecret($this->secretForSigningToken)
    				 ->setIssuer($this->tokenIssuer)
    				 ->setSubject($this->tokenSubject)
    				 ->setAudience($this->tokenAudience)
    				 ->setExpiration(time() + 30)
    				 ->setNotBefore(time()-10)
    				 ->setIssuedAt(time())
    				 ->setJwtId('ClientRequestToken'.mt_rand())
    				 ->setPayloadClaim('requestData', $requestData)
    				 ->build();
		$requestJWStoken = $jwt->getToken();
		return $requestJWStoken;
	}


	/**
	* Encryptes a signed JWT containing the request data.
	*
	* @param string $requestJWStoken. Signed JWT containing necessary claims and $requestData payload.
	*
 	* @return string $encryptedRequestJWSToken. Encrypted signed request JWT. 
	*/
	private function encryptRequestJWSToken($requestJWStoken) {
    	$encryptedRequestJWSToken = openssl_encrypt($requestJWStoken, $this->cipher, hex2bin($this->key), $options=0, hex2bin($this->iv));
		return $encryptedRequestJWSToken;
	}


	/**
	* Decryptes the encrypted response JWT.
	*
	* @param string $encryptedResponseJWSToken. Encrypted signed response JWT.
	*
 	* @return string $responseJWSToken. Decrypted signed response JWT. 
	*/
	private function decryptResponseJWSToken($encryptedResponseJWSToken) {
    	$responseJWSToken = openssl_decrypt($encryptedResponseJWSToken, $this->cipher, hex2bin($this->key), $options=0, hex2bin($this->iv));
		return $responseJWSToken;
	}


	/**
	* validates the decrypted response JWT.
	*
	* @param string $responseJWSToken. signed response JWT.
	*
 	* @return Array $responseData. if token is valid, returns response data extracted from the validated token.
 	* If not, then returns Failed status with the appropiate error message.
 	*
 	* @throws Exception\ValidateException. If validating the token claims like expiration, audience fails.
	*/
	private function verifyResponseJWSToken($responseJWSToken) {
		$jwt = new Jwt($responseJWSToken, $this->secretForSigningToken);
		$parse = new Parse($jwt, new Validate(), new Encode());
		try{
					$parsed = $parse->validate()
    					->validateExpiration()
    					->validateNotBefore()
    					->validateAudience('Client')
    					->parse();			
		} catch (Exception $e) {
				$responseData['status'] = "Failed";
				$responseData['message'] =  $e->getMessage();
				goto respond;
		}
		$payload = $parsed->getPayload();
		if($payload['iss']!="ServerInventoryServiceApi" || $payload['sub']!="ResponseFromServer") {
			$responseData['status'] = "Failed";
			$responseData['message'] =  "Response package not intended for Client";
			goto respond;
		}
		$responseData = $payload['responseData'];

		respond:
		return $responseData;
	}


	/**
	* convert the IP address from long integer representation to human readable IPv4 form.
	*
	* @param Array $responseData. Array containing server details with IP address in long integer format.
	*
 	* @return Array $responseData. Array containing server details with IP address in human readable IPv4 format.
	*/
	private function convertIPToHumanReadable($responseData) {
			foreach ($responseData as $key => $datum) {
					$responseData[$key]['IP'] = long2ip($datum['IP']);		
			}
			return $responseData;
	}
}