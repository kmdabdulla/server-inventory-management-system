 <?php

header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");


/**
* Server inventory management API. 
* It receives requests from the Client and performs requested operation using server inventory management service
* and returns the resposne back to the Client.
*
* @author Mohamed Abdulla
*/

require 'reallySimpleJWT/autoload.php';
require_once("ServerInventoryManagementService.php");

use ReallySimpleJWT\Build;
use ReallySimpleJWT\Validate;
use ReallySimpleJWT\Encode;
use ReallySimpleJWT\Token;
use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Jwt;
use ReallySimpleJWT\Secret;


/**
* Secret for signing and verifying the sign of JSON Web Token (JWT) transmitted between client and server service API.
*
* @global string secretForSigningToken in $GLOBALS array.
*/
$GLOBALS['secretForSigningToken'] = '!canBeCoolsecReT$978*';


/**
* Encryption algorithm to encrypt the signed JWT. 
*
* @global string cipher in $GLOBALS array.
*/
$GLOBALS['cipher'] = "AES-128-CBC";


/**
* Symmetric secret key for encrypting and decrypting the signed JWT.
*
* @global string key in $GLOBALS array.
*/
$GLOBALS['key'] = "06f0eb165278f59cae99b1a801eb3bf6ce2bf594a88d18d0b7431a506ec78d80";


/**
* Initialization vector for encrypting and decrypting the signed JWT.
*
* @global string iv in $GLOBALS array.
*/
$GLOBALS['iv'] = "8a4c87c4487997a54f28479b9072688a";


/**
* Issuer of the signed response JWT to Client.
*
* @global string tokenIssuer in $GLOBALS array. 
*/
$GLOBALS['tokenIssuer'] = "ServerInventoryServiceApi";


/**
* Intended audience for the signed response JWT issued by server inventory service API.
*
* @global string tokenAudience in $GLOBALS array.
*/
$GLOBALS['tokenAudience'] = "Client";


/**
* Subject of the signed response JWT. 
*
* @global string tokenSubject in $GLOBALS array.
*/
$GLOBALS['tokenSubject'] = "ResponseFromServer";


/**
* Holds response data to be sent to Client.
*
* @global Array $responseData
*/
$responseData = array();

/** 
* To prevent GET Request.
*/
if ($_SERVER['REQUEST_METHOD'] === 'GET') { 
	$responseData['status'] = "Failed";
	$responseData['message'] = "GET method not allowed.";
	goto respond;
}

/** 
* To check for requestData parameter in $_POST array.
*/
if(!isset($_POST['requestData'])) {
	$responseData['status'] = "Failed";
	$responseData['message'] = "requestData parameter not available.";
	goto respond;	
}


/** 
* Invokes method for decrypting request signed JWT and verifying it.
*/
$requestJWSToken = decryptRequestJWSToken(urldecode($_POST['requestData']));
$requestData = verifyRequestJWSToken($requestJWSToken);


/** 
* Perform requested database operation if request data is valid.
* otherwise return error message.
*/
if($requestData) {
	$serverServiceObj = new ServerInventoryManagementService();
		switch($requestData['action']) {
			case 'findServer':
				$response = $serverServiceObj->find($requestData['columnName'],$requestData['value']);
				if(empty($response)) {
				$responseData['status'] = "Failed";	
				$responseData['message'] = "No Matching Records Found.";	
				} else {
				$responseData['status'] = "Success";	
				$responseData['message'] = $response;
				}
				break;
			case 'listAllServers':
				$response = $serverServiceObj->listAll($requestData);
				if(empty($response)) {
				$responseData['status'] = "Failed";
				$responseData['message'] = "No Matching Records Found.";	
				} else {
				$responseData['status'] = "Success";
				$responseData['message'] = $response;
				}
				break;
			case 'addServer':
				unset($requestData['action']);
				$response = $serverServiceObj->add($requestData);
				if($response>0) {
				$responseData['status'] = "Success";	
				$responseData['message'] = $requestData['ServerId']." Server details successfully added to the inventory.";		
				} else {
				$responseData['status'] = "Failed";
				$responseData['message'] = "Add operation not performed.";
				}
				break;
			case 'updateServer':
				unset($requestData['action']);
				$id = $requestData['ServerId'];
				unset($requestData['ServerId']);
				$response = $serverServiceObj->update($id,$requestData);
				if($response>0) {
				$responseData['status'] = "Success";	
				$responseData['message'] = $id." Server details successfully updated in the inventory.";		
				} else {
				$responseData['status'] = "Failed";	
				$responseData['message'] = "Update operation not performed.";
				}				
				break;
			case 'deleteServer':
				$response = $serverServiceObj->delete($requestData['ServerId']);
				if($response>0) {
				$responseData['status'] = "Success";	
					if(is_array($requestData['ServerId'])) {
						$responseData['message'] = implode(", ",$requestData['ServerId'])." Servers details are deleted from the inventory.";		
					} else {
						$responseData['message'] = $requestData['ServerId']." Server details deleted from the inventory.";		
					}
				} else {
				$responseData['status'] = "Failed";
				$responseData['message'] = "Delete operation failed.";
				}
				break;	
			default:
				$responseData['status'] = "Failed";
				$responseData['message'] = "Invalid Database operation.";
		}
} else {
		$responseData['status'] = "Failed";
		$responseData['message'] = "Invalid Request Token.";
}

/** 
* Invokes method for creating signed response JWT and encrypting it 
* and sending it back to Client.
*/
respond:
$responseJWSToken = createResponseJWSToken($responseData);
$encryptedResponseJWSToken = encryptResponseJWSToken($responseJWSToken);
sendResponse($encryptedResponseJWSToken);


/**
* Create a signed JWT containing the response data.
*
* @param Array $responseData.  Array containing the response message depending on database operation
* $responseData = [
*      'status'     => (string) Success or Failed.
*      'message' 	=> (string) Success or Failure message or (Array) the response data.
*    ]
*
* @return string $responseJWSToken.  Signed JWT containing necessary claims and $responseData payload.
*/
function createResponseJWSToken($responseData) {
		$build = new Build('JWT', new Validate(), new Secret(), new Encode());
		$jwt = $build->setContentType('JWT')
    				 ->setSecret($GLOBALS['secretForSigningToken'])
    				 ->setIssuer($GLOBALS['tokenIssuer'] )
    				 ->setSubject($GLOBALS['tokenSubject'])
    				 ->setAudience($GLOBALS['tokenAudience'])
    				 ->setExpiration(time() + 30)
    				 ->setNotBefore(time()-10)
    				 ->setIssuedAt(time())
    				 ->setJwtId('ServerResponseToken'.mt_rand())
    				 ->setPayloadClaim('responseData', $responseData)
    				 ->build();
		$responseJWSToken = $jwt->getToken();
		return $responseJWSToken;
}


/**
* Encryptes a signed JWT containing the response data.
*
* @param string $responseJWStoken. Signed JWT containing necessary claims and $responseData payload
*
* @return string $encryptedResponseJWSToken. Encrypted signed response JWT. 
*/
function encryptResponseJWSToken($responseJWStoken) {
    	$encryptedResponseJWSToken = openssl_encrypt($responseJWStoken, $GLOBALS['cipher'], hex2bin($GLOBALS['key']),$options=0, hex2bin($GLOBALS['iv']));
		return $encryptedResponseJWSToken;
}


/**
* Sends the encrypted signed JWT containing the response data to the Client
*  
* @param string $encryptedResponseJWSToken. Encrypted signed response JWT.
*
*/
function sendResponse($encryptedResponseJWSToken) {
		echo $encryptedResponseJWSToken;
}


/**
* Decryptes the encrypted request JWT.
*
* @param string $encryptedRequestJWSToken. Encrypted signed request JWT.
*
* @return string $requestJWSToken. Decrypted signed request JWT. 
*/
function decryptRequestJWSToken($encryptedRequestJWSToken) {
    	$requestJWSToken = openssl_decrypt($encryptedRequestJWSToken, $GLOBALS['cipher'],hex2bin($GLOBALS['key']),$options=0,hex2bin($GLOBALS['iv']));
		return $requestJWSToken;
}


/**
* validates the decrypted request JWT
*
* @param string $requestJWSToken. signed request JWT.
*
* @return Array or Bool. If token is valid, returns request data is extracted from the validated token as Array.
* If not, then returns false
*
* @throws Exception\ValidateException. If validating the token claims like expiration, audience fails.
*/
function verifyRequestJWSToken($requestJWSToken) {
		$jwt = new Jwt($requestJWSToken, $GLOBALS['secretForSigningToken']);
		$parse = new Parse($jwt, new Validate(), new Encode());
		try{
					$parsed = $parse->validate()
    					->validateExpiration()
    					->validateNotBefore()
    					->validateAudience('ServerInventoryServiceApi')
    					->parse();			
		} catch (Exception $e) {
				return false;
		}
		$payload = $parsed->getPayload();
		if($payload['iss']!="Client" || $payload['sub']!="ServerInventoryManagement") {
			return false;
		}
		return $payload['requestData'];
}
