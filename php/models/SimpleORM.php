<?php
/**
 * Class SimpleORM
 *
 * Adds simple ORM functionality, designed to
 * be extended by another class defining table
 * properties
 *
 * Created By: Tyler Sriver
 * @author <tyler.w.sriver@eagles.oc.edu>
 */
require_once "SimpleSQL.php";
class SimpleORM
{
    // -- DB Table Details
    protected static $table;
    protected static $key;
    protected static $fields = "*";

    /**
     * Get record by primary key
     * @param int $id
     * @return array
     */
    public static function Get($id)
    {
        $sql = "SELECT ".static::$fields." FROM ".static::$table." WHERE ".static::$key." = ? LIMIT 1";
        $result = query($sql, [$id]);
        return $result[0];
    }

    /**
     * Get list of records filtered
     * @param array $where
     * @return array
     */
    public static function GetList($where = array())
    {
        $sql = "SELECT ".static::$fields." FROM ".static::$table." ";

        // Add where if set
        if(count($where) > 0) {
            $sql .= " WHERE ";
        }

        // Add where clause
        $params = array();
        $i = 0;
        foreach($where as $key => $val)
        {
            $sql .= $key ." = ? ";
            $params[] = $val;

            $i++;
            if($i < count($where)) {
                $sql .= " AND ";
            }
        }

        return query($sql, $params);
    }

    /**
     * Get one record filtered
     * @param array $where
     * @return array
     */
    public static function GetOne($where = array())
    {
        $result = static::GetList($where);
        if(count($result) < 1) {
            return array();
        }
        return $result[0];
    }

    /**
     * Inserts a record into the table
     * @param array values - associative array of field => value
     * @return array|bool - The record added
     */
    public static function Add($values = array())
    {
        if(count($values) == 0){
            return false;
        }

        $sql = "INSERT INTO ".static::$table;

        $cols = " ( "; // String for insert columns
        $vals = " ( "; // String for vals to insert
        $params = array(); // Parameters for query
        $i = 0; // Count for iteration
        foreach($values as $key => $val){
            $cols .= "$key";
            
            $vals .= "? ";
            $params[] = $val;

            $i++;
            if($i < count($values)) {
                $cols .= ", ";
                $vals .= ", ";
            }
        }
        $cols .= ") ";
        $vals .= ") ";

        $sql .= $cols ." VALUES ". $vals;
        $id = query($sql, $params);

        return static::Get($id);
    }

    /**
     * Updates a record
     * @param $id - id of the record we are updating
     * @param $set - associative array of which fields to update
     * @return array|bool
     */
    public static function Update($id, $set = array())
    {
        $sql = "UPDATE ".static::$table." SET ";
        $params = array();

        // Build fields to update string
        $i = 0;
        foreach($set as $key => $val) {
            $sql .= $key ." = ?";
            $params[] = $val;

            $i++;
            if($i < count($set)) {
                $sql .= ", ";
            }
        }
        $sql .= " WHERE ".static::$key." = ?";   
        $params[] = $id; 
        return query($sql, $params);
    }

    /**
     * Updates many records based on where criteria
     * @param array $set - associative array of which fields to update
     * @param array $where - associative array of what fields to match in where clause
     * @return array|bool
     */
    public static function UpdateMany($set = array(), $where = array())
    {
        $sql = "UPDATE ".static::$table." SET ";
        $params = array();

        // Build fields to update string
        $i = 0;
        foreach($set as $key => $val) {
            $sql .= $key ." = ?";
            $params[] = $val;

            $i++;
            if($i < count($set)) {
                $sql .= ", ";
            }
        }
        $sql .= " ";

        $sql .= " WHERE ";                                            
        $i = 0;
        foreach($where as $key => $val) {
            $sql .= $key ." = ? ";
            $params[] = $val;

            $i++;                                                    
            if($i < count($where)) {
                $sql .= "AND ";
            }
        }
        
        return query($sql, $params);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
    }

    /**
     * Deletes a record and returns the deleted record
     * @param $id - The id of the record being deleted
     * @return array - returns the deleted record
     */
    public static function Delete($id)
    {
        // Get the record to delete
        $toDelete = static::Get($id);

        // Delete the record
        $sql = "DELETE FROM ".static::$table." WHERE ".static::$key." = ?";
        query($sql, [$id]);

        // return the deleted record
        return $toDelete;
    }

    /**
     * Deletes many records based on where criteria
     * @param array $where - associative array of what fields to match in where clause
     * @return array - records deleted
     */
    public static function DeleteMany($where = array())
    {
        // Prevent deleting all records
        if(count($where) == 0) {
            return array();
        }

        // Get records to delete
        $toDelete = static::GetList($where);

        // Build delete query
        $sql = "DELETE FROM ".static::$table." WHERE ";
        $i = 0;
        $params = array();
        foreach($where as $key => $val) {
            $sql .= $key ." = ? ";
            $params[] = $val;

            $i++;
            if($i < count($where)) {
                $sql .= "AND ";
            }
        }
        query($sql, $params);

        return $toDelete;
    }
}