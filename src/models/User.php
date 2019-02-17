<?php
/**
 * Class user
 * 
 * Represents Object model for 
 * user table
 */
use SimpleSQL\ORM as ORM;
 class user extends ORM
{
    protected static $table = 'user';
    protected static $key = 'userId';
}