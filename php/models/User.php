<?php
/**
 * Class user
 * 
 * Represents Object model for 
 * user table
 */
class user extends SimpleORM
{
    protected static $table = 'user';
    protected static $key = 'userId';
}