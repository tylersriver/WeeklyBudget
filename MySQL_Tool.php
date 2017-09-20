<?php

/**
 * Created by PhpStorm.
 * User: tyler
 * Date: 4/11/2017
 * Time: 12:58 AM
 *
 * Class MySQL_Tool
 *
 * This is used to build and maintain
 * a sql connection and different associated
 * functions
 */

// Includes
include_once ("lib-utils.php");

class MySQL_Tool
{
    // -- Fields
    // ---------------------------------------------------
    /** @string */
    private $servername;
    /** @string */
    private $username;
    /** @string */
    private $password;
    /** @string */
    private $db;
    /** @mysqli */
    private $conn;

    // -- Methods
    // ---------------------------------------------------
    /**
     * MySQL_Tool constructor.
     * Build and setup connection
     */
    public function __construct()
    {
        require "lib-config.php";
        $this->servername = $global_servername;
        $this->username = $global_username;
        $this->password = $global_password;
        $this->db = $global_db;
        $this->setupConnection();
    }

    /**
     * Setup Connection to database
     */
    private function setupConnection()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->db);
        if($this->conn->connect_error){
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    /**
     * MySQLi bound query function
     *
     * @param $sql string
     * @param $params array
     * @return bool | mysqli_result
     */
    public function query($sql, $params = array())
    {
        $sql = trim($sql); // Trim extra whitespace
        $params = (array)$params;
        $result = false;

        // Initiate statement
        $stmt = $this->conn->prepare($sql);
        if(!$stmt) {
            return $result;
        }
        
        // Build types array
        $types = buildTypeStringFromArray($params);

        // Bind params
        if (!empty($params)) {
            $binds = array($types);
            $binds = array_merge($binds, $params);
            $binds = makeValuesReferenced($binds);
            call_user_func_array(array($stmt, 'bind_param'), $binds);
        }

        // Execute SQL
        if($stmt->execute()) {
            if($stmt->affected_rows >= 0 ) {
                $result = true;
            } else {
                $result = $stmt->get_result();
            }
        }

        return $result;
    }

    /**
     * close mysql conn
     */
    public function close()
    {
        $this->conn->close();
    }

    // -- Functions
    // --------------------------------

    /**
     * Get the total sum of transactions for the week
     */
    public function getWeeklyTotal()
    {   
        $sql = "SELECT sum(amount) from Transactions where WEEKOFYEAR(dateOccured) = WEEKOFYEAR(NOW())";
        $result = $this->query($sql);
        $arr = $result->fetch_row();
        $weeklyTotal = $arr[0];
        return $weeklyTotal;
    }

    public function getRemaining($description)
    {
        $total = $this->getWeeklyTotal();
        
        $sql = 'SELECT amount from weekMaxValues where description = ?';
        $result = $this->query($sql, array($description));
        $arr = $result->fetch_row();
        $budget = $arr[0];

        return $budget - $total;
    }
}
