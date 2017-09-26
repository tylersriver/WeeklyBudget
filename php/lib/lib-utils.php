<?php
/**
 * Created by VSCode.
 * User: tyler
 * Date: 8/7/2017
 * Time: 10:47 AM
 *
 * lib-utils
 *
 * This is a general file for functions
 * that can be used anywhere
 */

/**
 * Make array pass by reference
 * 
 * @param $arr array
 * @return array
 */
function makeValuesReferenced(&$arr)
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
function buildTypeStringFromArray($params)
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