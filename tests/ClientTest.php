<?php
use PHPUnit\Framework\TestCase;
include(dirname(__FILE__)."/../Client.php");;

class ClientTest extends TestCase { 


	/**
 	* To get access to private/protected methods for testing.
 	*/
	public static function getMethod($name) {
 		 $class = new ReflectionClass('Client');
  		 $method = $class->getMethod($name);
  		 $method->setAccessible(true);
  		 return $method;
	}

	/**
 	* To check the instance of Client.
 	*/
	public function testclientobj() {
		 $cli = new Client();
		 $this->assertInstanceOf(Client::class, $cli);
	}


	/**
 	* @covers findServers
 	*/
	public function testfindServers() {
		 $cli = new Client();
		 $data['value'] = "Payment";
		 $result = $cli->findServers($data);
		 $this->assertSame("Failed",$result['status']);
		 $this->assertSame("columnName or value fields are not supplied",$result['message']);
		 $data['columnName'] = "IP";
		 $data['value'] = "12.22.2";
		 $result = $cli->findServers($data);
		 $this->assertSame("Failed",$result['status']);
		 $this->assertSame("Improper IP address",$result['message']);
	}


	/**
 	* @covers addServer
 	*/
	public function testaddServer() {
		 $cli = new Client();
		 $data['ServerName'] = "SMS";
		 $data['IP'] = "10.10.10.1";
		 $result = $cli->addServer($data);
		 $this->assertSame("Failed",$result['status']);
		 $this->assertSame("Please make sure all fields: ServerName,ServerId,IP,Location and Description are supplied",$result['message']);
		 $data['IP'] = "10.10.10";
		 $data['ServerId'] = "serv1";
		 $result = $cli->addServer($data);
		 $this->assertSame("Failed",$result['status']);
		 $this->assertSame("Please make sure all fields: ServerName,ServerId,IP,Location and Description are supplied",$result['message']);
	}


	/**
 	* @covers updateServer
 	*/
	public function testupdateServer() {
		 $cli = new Client();
		 $data['IP'] = "10.10.10.1";
		 $result = $cli->updateServer($data);
		 $this->assertSame("Failed",$result['status']);
		 $this->assertSame("ServerId not found",$result['message']);
		 $data['IP'] = "10.10.10";
		 $data['ServerId'] = "serv1";
		 $result = $cli->updateServer($data);
		 $this->assertSame("Failed",$result['status']);
		 $this->assertSame("Improper IP address",$result['message']);
	}


	/**
 	* @covers deleteServers
 	*/
	public function testdeleteServers() {
		 $cli = new Client();
		 $data = array();
		 $result = $cli->deleteServers($data);
		 $this->assertSame("Failed",$result['status']);
		 $this->assertSame("ServerId field not found",$result['message']);
	}


	/**
 	* @covers encryptRequestJWSToken
 	*/
	public function testencryptRequestJWSToken() {
		$method = self::getMethod('encryptRequestJWSToken');
  		$cli = new Client();
  		$data = "This is test encryption";
  		$encryptedData = "+JpWxC4rMcgf6IJMKZ+oVenbP7/g9NYFHY2kSF2lGi4=";	
  		$result = $method->invokeArgs($cli,array($data));
  		$this->assertSame($encryptedData,$result);
	}


	/**
 	* @covers decryptResponseJWSToken
 	*/
	public function testdecryptResponseJWSToken() {
		$method = self::getMethod('decryptResponseJWSToken');
  		$cli = new Client();
  		$data = "This is test encryption";
  		$encryptedData = "+JpWxC4rMcgf6IJMKZ+oVenbP7/g9NYFHY2kSF2lGi4=";	
  		$result = $method->invokeArgs($cli,array($encryptedData));
  		$this->assertSame($data,$result);
	}


	/**
 	* @covers convertIPToHumanReadable
 	*/
	public function testconvertIPToHumanReadable() {
		$method = self::getMethod('convertIPToHumanReadable');
  		$cli = new Client();
  		$data[0]['IP'] = "3499263947";
  		$humanReadableIp = "208.146.135.203";	
  		$result = $method->invokeArgs($cli,array($data));
  		$this->assertSame($humanReadableIp,$result[0]['IP']);
	}


	/**
 	* @covers verifyResponseJWSToken
 	*/
	public function testverifyResponseJWSToken() {
		$method = self::getMethod('verifyResponseJWSToken');
  		$cli = new Client();
		//token containing different signature
   		$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE1ODU0MjgwNTQsInVpZCI6MSwiZXhwIjoxNTg1NDI4MDY0LCJpc3MiOiJsb2NhbGhvc3QiLCJkYXRhIjp7ImFjdGlvbiI6ImFkZCJ9fQ.b3r3IfNXJQuaszBevI1xxOfv3PiPhgIGqqqnbnwHrCM";
   		$result = $method->invokeArgs($cli,array($token));
  		$this->assertSame("Failed",$result['status']);
		$this->assertSame("Signature is invalid.",$result['message']);
	}
}