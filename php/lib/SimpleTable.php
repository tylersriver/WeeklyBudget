<?php

/**
 * Constructs a Bootstrap table with set properties
 */
class SimpleTable
{
    private $tableId;                   // Used as DOM Id
    private $data;                      // Associative array of data
    private $columns;                   // Column names
    private $tableClasses;              // Sets the bootstrap table type
    private $theadColor;                // Sets thead light or dark


    function __construct($tableId)
    {
        $this->tableId = $tableId;
        $this->tableClasses = array();
        $this->theadColor = '';
    }
    
    function display($return = false)
    {
        $html  = "<div id='".$this->tableId."'>                            \n";
        $html .= "    <table class='".$this->buildTableClassesString()."'> \n";
        $html .= "        <thead class='".$this->theadColor."'>            \n";
        $html .= "            <tr>                                         \n";
        $html .=                  $this->buildColumnHeader();
        $html .= "            </tr>                                        \n";
        $html .= "        </thead>                                         \n";
        $html .= "        <tbody>                                          \n";
        $html .=              $this->buildTableBody();
        $html .= "        </tbody>                                         \n";
        $html .= "    </table>                                             \n";
        $html .= "</div>                                                   \n";

        if($return) {
            return $html;
        } else {
            echo $html;
        }
        
    }

    // ** Display Helper Functions ** //
    // ******************************************************************************************************************* //
    // ******************************************************************************************************************* //
    
    /**
     * @return string
     */
    private function buildTableClassesString()
    {
        $str = 'table ';
        foreach($this->tableClasses as $class) {
            $str .= " $class ";
        }
        return $str;
    }

    /**
     * @return string
     */
    private function buildColumnHeader()
    {
        if(count($this->columns) < 1){
            return "";
        }

        $str = "<th scope='col'>#</th> \n";
        foreach($this->columns as $column) {
            $str .= "<th scope='col'>".$column."</th> \n";
        }
        return $str;
    }

    /**
     * @return  string
     */
    private function buildTableBody()
    {
        if(count($this->data) < 1) {
            return "";
        }

        $str = '';
        foreach($this->data as $i=>$row) {
            $str .= "<tr>                              \n";
            $str .= "    <th scope='row'>".($i+1)."</th>   \n";
            foreach($row as $val) {
                $str .= "<td>".$val."</td>             \n";
            }
            $str .= "</tr>                             \n";
        }
        return $str;
    }

    // ** Setter Functions ** //
    // ******************************************************************************************************************* //
    // ******************************************************************************************************************* //

    /**
     * Takes array of BootstrapTableClasses
     * 
     * @param array $classes
     */
    function setTableClasses($classes)
    {
        $this->tableClasses = $classes;
    }

    /**
     * Set the table header to dark or light theme
     */
    function setTheadDarkOrLight($color)
    {
        if($color == 'dark') {
            $this->theadColor = 'thead-dark';
        } else if ($color == 'light') {
            $this->theadColor = 'thead-light';
        } else {
            $this->theadColor = '';
        }
    }

    /**
     * Date should be an array of associated arrays
     * 
     * Example:
     *      $data = 
     *      0 => [
     *          'name' => 'Tyler',
     *          'age' => 22
     *      ], 
     *      1 => [
     *          'name' => 'Jennifer',
     *          'age' => 23
     *      ]
     * 
     * @param array $data
     */
    function setData($data)
    {
        if(count($data) < 1){
            return;
        }

        $this->data = $data;

        // Set columns
        $this->columns = array();
        foreach($data[0] as $key=>$val){
            $this->columns[] = $key;
        }
    }
}

/**
 * Defines the basic bootstrap table class types
 */
abstract class BootStrapTableClasses
{
    const Striped = 'table-striped';
    const Bordered = 'table-bordered';
    const Hover = 'table-hover';
    const Small = 'table-sm';
    const Dark = 'table-dark';
}