<?php

/**
 * Created by PhpStorm.
 * User: tyler
 * Date: 4/11/2017
 * Time: 12:58 AM
 *
 * Class MySQL
 *
 * This is used to build and maintain
 * a sql connection and run parameterized
 * queries
 */
class MySQLTool
{
    // -- Connection Strings
    private  $servername = 'localhost';
    private  $username = 'dev_user';
    private  $password = 'ILoveJennifer712';
    private  $db = 'WeeklyBudget';

    private $conn; // MySQLi Connection
    private static $instance = null;


    // -- Methods
    // ---------------------------------------------------
    private function __construct()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->db);
        if($this->conn->connect_error){
            die("Connection failed: " . $this->conn->connect_error);
        }    }

    public static function getInstance()
    {
        if(!self::$instance) {
            self::$instance = new MySQLTool();
        }
        return self::$instance;
    }

    private function __clone() {}
    private function __wakeup() {}

    /**
     * MySQLi bound query function
     *
     * @param $sql string
     * @param $params array
     * @return bool | mysqli_result
     */
    public static function query($sql, $params = array())
    {
        $db = MySQLTool::getInstance();
        $sql = trim($sql); // Trim extra whitespace
        $params = (array)$params;
        $result = false;

        // Initiate statement
        $stmt = $db->conn->prepare($sql);
        if(!$stmt) {
            return $result;
        }
        
        // Build types array
        $types = $db->buildTypeStringFromArray($params);

        // Bind params
        if (!empty($params)) {
            $binds = array($types);
            $binds = array_merge($binds, $params);
            $binds = $db->makeValuesReferenced($binds);
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

    public function close()
    {
        $this->conn->close();
    }

    // -- Utilities
    // ------------------------------------------------------------------------------
    
    /**
     * Make array pass by reference
     * 
     * @param $arr array
     * @return array
     */
    private function makeValuesReferenced(&$arr)
    {
        $refs = array();
        foreach($arr as $key => $value){
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }

    /**
     * Build string of types from an array
     *
     * Possible types:
     *  boolean b
     *  double d
     *  integer i
     *  string s
     *
     * @param $parms array
     * @return string
     */
    private function buildTypeStringFromArray($params)
    {
        $types = "";
        foreach($params as &$p){
            switch (gettype($p)) {
                case 'boolean':
                    $bind = $bind ? 1 : 0;
                    $types .= 'i';
                    break;
                case 'double':
                    $types .= 'd';
                    break;
                case 'integer':
                    $types .= 'i';
                    break;
                case 'string':
                default:
                    $types .= 's';
            }
        }
        return $types;
    }
}
