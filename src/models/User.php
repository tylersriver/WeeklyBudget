<?php
/**
 * Class user
 * 
 * Represents Object model for 
 * user table
 */
use SimpleSQL\SQL as SQL;
class user extends SimpleSQL\ORM
{
    protected static $table = 'user';
    protected static $key = 'userId';
}