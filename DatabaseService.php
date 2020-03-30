<?php
require_once("DBServiceInterface.php");

/**
* Abstract database service class. It implements DBServiceInterface.
* It provides all the methods that are common for the services performing basic CRUD database operations. 
*
* @author Mohamed Abdulla
*/
abstract class DatabaseService implements DBServiceInterface  {

    /**
    * Returns the table name (implemented by respective services) for performing requested CRUD operation.
    *
    */
    abstract protected function getTable();

    /**
    * Returns the primary column name (implemented by respective services) for performing requested CRUD operation.
    *
    */
    abstract protected function getPrimaryColumn();


    /**
    * Returns the database connection object (implemented by respective services) for performing requested CRUD operation.
    *
    */
    abstract protected static function getDBConnection();


    /**
    * find matching rows for the specified value and column name.
    *
    * @param string $findby. Column name of the table. Required 
    * @param string $value. value in the column to be matched. Required 
    *
    * @return Array $results. Containing matching rows.
    */
    public function find($columnName, $value): Array {
		$query = "select * from ".$this->getTable(). " where ".$columnName."=".'"'.$value.'"';
		$pdostmt = $this->executeQuery($query);
		$results = $pdostmt->fetchAll( PDO::FETCH_ASSOC);
		return $results;
    }


    /**
    * List all the matching rows from the table for the specified limit and offset. 
    *
    * @param Array $data. Array containing limit and offset. Required 
    *
    * @return Array $results. Containing matching rows.
    */
    public function listAll($data): Array {
		$query = "select * from ".$this->getTable()." order by ".$this->getPrimaryColumn()." limit ".$data['offset'].",".$data['limit'];		
		$pdostmt = $this->executeQuery($query);
		$results = $pdostmt->fetchAll( PDO::FETCH_ASSOC);
		return $results;
    }


    /**
    * Insert entry into the table.
    *
    * @param Array $data. Array containing necessary parameters for insertion. Required
    *
    * @return int $affectedRowCount. count of successfully inserted rows.
    */
    public function add($data): int {
		$valueString = "(";
		foreach ($data as $key => $value) {
			$valueString.=":".$key.",";
		}
		$valueString = rtrim($valueString, ',');
		$valueString .=")";
		$query = "INSERT INTO ".$this->getTable()." (".implode(',', array_keys($data)).")"." VALUES ".$valueString;
		$pdostmt = $this->executeQuery($query,$data);
		$affectedRowCount = $pdostmt->rowCount();
		return $affectedRowCount;
    }


    /**
    * Update entry in the table.
    *
    * @param string $id. unique id of the row to be updated.
    * @param Array $data. Array containing necessary columns to be updated in the table. Required
    * 
    * @return int $affectedRowCount. count of successfully updated rows.
    */
    public function update($id, $data): int {
		$updateString = "";
		foreach ($data as $key => $value) {
			$updateString.=$key."=:".$key.",";
		}
		$updateString = rtrim($updateString, ',');
		$query = "UPDATE ".$this->getTable()." SET ".$updateString." WHERE ".$this->getPrimaryColumn()."=".'"'.$id.'"';
		$pdostmt = $this->executeQuery($query,$data);
		$affectedRowCount = $pdostmt->rowCount();
		return $affectedRowCount;
    }

    /**
    * Delete entry in the table.
    *
    * @param string or Array $id. unique id(s) of the row to be deleted. Required
    *
    * @return int $affectedRowCount. count of successfully deleted rows.
    */
    public function delete($ids): int {
		$deleteIds = "";
		if(is_array($ids)) {
			foreach ($ids as $value) {
				$deleteIds .= '"'.$value.'",';
			}
			$deleteIds = rtrim($deleteIds, ',');
		} else {
			$deleteIds = '"'.$ids.'"';
		}
		$query = "delete from ".$this->getTable()." where ".$this->getPrimaryColumn()." in (".$deleteIds.")";
		$pdostmt = $this->executeQuery($query);
		$affectedRowCount = $pdostmt->rowCount();
		return $affectedRowCount;
		
     }	



    /**
    * Returns the PDO statement object after performing query execution.
    *
    * @param $query. Query to be executed on the provided Database and Table. Required
    * @param $data. Data to be binded to the query statement before execution. Required only for add and update.
    *
    * @return $pdostmt. PDO object.
    */
    public function executeQuery($query, $data = NULL) {
		$pdo = static::getDBConnection();
		$pdostmt = $pdo->prepare($query);
		$pdostmt->execute($data);
		return $pdostmt;
    }
}
?>
