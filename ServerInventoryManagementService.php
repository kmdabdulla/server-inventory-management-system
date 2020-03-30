<?php
require_once("DatabaseService.php");


/**
* server inventory management service class. It extends DatabaseService class.
* It provides service for managing the server inventory.
*
* @author Mohamed Abdulla
*/
class ServerInventoryManagementService extends DatabaseService {
    
    /**
    * Holds PDO object.
    *
    * @var object $pdo.
    */	
    protected static $pdo = NULL; 


    /**
    * Return Database host for this service.
    *
    * @return string.
    */
    public function getDBServer() {
	   return 'localhost';
    }


    /**
    * Return Database name for this service.
    *
    * @return string.
    */
    public function getDatabase() {
	   return 'ServerInventory';
    }


    /**
    * Return Database username for this service.
    *
    * @return string.
    */
    public function getDBUsername() {
	   return 'servermanager';
    }


    /**
    * Return Database password for this service.
    *
    * @return string.
    */
    public function getDBPassword() {
	   return 'servermanager';
    }


    /**
    * Return Table name for this service.
    *
    * @return string.
    */
    public function getTable() {
	   return 'Servers';
    }


    /**
    * Return primary column name of the table for this service.
    *
    * @return string.
    */
    public function getPrimaryColumn() {
	   return 'ServerId';
    }


    /**
    * Return PDO object for the database of this service. 
    * Only one PDO connection object is created for this service.
    * It is re-used for multiple requests. 
    *
    * @return $pdo. PDO object.
    */
    protected static function getDBConnection() {
	    if(static::$pdo===NULL) {
        	$connection = "mysql:host=".self::getDBServer().";dbname=".self::getDatabase();
		static::$pdo = new PDO($connection, self::getDBUsername(), self::getDBPassword());
	    }
	    return static::$pdo;
    }
}
