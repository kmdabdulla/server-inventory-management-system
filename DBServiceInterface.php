
<?php

/**
* Defines a contract all DB services must implement.
* 
* Enables the basic CRUD database operations.
*
* @author Mohamed Abdulla
*/
interface DBServiceInterface {

	/**
    * find matching rows for the specified value and column name.
    *
    * @param string $findby. Column name of the table. Required 
    * @param string $value. value in the column to be matched. Required 
    *
    * @return Array. Containing matching rows.
    */
	public function find($findby, $value): Array;


	/**
    * List all the matching rows from the table for the specified limit and offset. 
    *
    * @param Array $data. Array containing limit and offset. Required 
    *
    * @return Array. Containing matching rows.
    */
	public function listAll($data): Array;


	/**
    * Insert entry into the table.
    *
    * @param Array $data. Array containing necessary parameters for insertion. Required 
    *
    * @return int. count of successfully inserted rows.
    */
	public function add($data): int;


	/**
    * Update entry in the table.
    *
    * @param string $id. unique id of the row to be updated.
    * @param Array $data. Array containing necessary columns to be updated in the table. Required 
    *
    * @return int. count of successfully updated rows.
    */
	public function update($id, $data): int;


	/**
    * Delete entry in the table.
    *
    * @param string or Array $id. unique id(s) of the row(s) to be deleted. Required 
    *
    * @return int. count of successfully deleted rows.
    */
	public function delete($id): int;

}