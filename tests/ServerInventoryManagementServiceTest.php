<?php
use PHPUnit\Framework\TestCase;
include(dirname(__FILE__)."/../ServerInventoryManagementService.php");;

class ServerInventoryManagementServiceTest extends TestCase { 

	/**
 	* To get access to private/protected methods for testing.
 	*/
	public static function getMethod($name) {
 		 $class = new ReflectionClass('ServerInventoryManagementService');
  		 $method = $class->getMethod($name);
  		 $method->setAccessible(true);
  		 return $method;
	}


	/**
 	* To check the instance of ServerInventoryManagementService.
 	*/
	public function testserverobj() {
		 $serverObj = new ServerInventoryManagementService();
		 $this->assertInstanceOf(ServerInventoryManagementService::class, $serverObj);
	}


	/**
 	* @covers getDBServer
 	*/
	public function testgetDBServer() {
		$serverObj = new ServerInventoryManagementService();
		$result = $serverObj->getDBServer();
		$this->assertSame("localhost",$result);
	}


	/**
 	* @covers getDatabase
 	*/
	public function testgetDatabase() {
		$serverObj = new ServerInventoryManagementService();
		$result = $serverObj->getDatabase();
		$this->assertSame("ServerInventory",$result);
	}


	/**
 	* @covers getDBUsername
 	*/
	public function testgetDBUsername() {
		$serverObj = new ServerInventoryManagementService();
		$result = $serverObj->getDBUsername();
		$this->assertSame("servermanager",$result);
	}


	/**
 	* @covers getDBPassword
 	*/
	public function testgetDBPassword() {
		$serverObj = new ServerInventoryManagementService();
		$result = $serverObj->getDBPassword();
		$this->assertSame("servermanager",$result);
	}


	/**
 	* @covers getTable
 	*/
	public function testgetTable() {
		$serverObj = new ServerInventoryManagementService();
		$result = $serverObj->getTable();
		$this->assertSame("Servers",$result);
	}


	/**
 	* @covers getPrimaryColumn
 	*/
	public function testgetPrimaryColumn() {
		$serverObj = new ServerInventoryManagementService();
		$result = $serverObj->getPrimaryColumn();
		$this->assertSame("ServerId",$result);
	}


	/**
 	* @covers getDBConnection 
 	*
 	* check to ensure same PDO object is being returned.
 	*/
	public function testgetDBConnection() {
		$method = self::getMethod('getDBConnection');
  		$serverObj1 = new ServerInventoryManagementService();	
  		$result1 = $method->invokeArgs($serverObj1,array());
  		$serverObj2 = new ServerInventoryManagementService();	
  		$result2 = $method->invokeArgs($serverObj2,array());
  		$this->assertSame($result1,$result2);
	}



}

