<?php
namespace Codeigniter;

define('BASEPATH', realpath(dirname(__FILE__)) . '/system/');
define('APPPATH', realpath(dirname(__FILE__)) . '/');
define('ENVIRONMENT', '');

class Service
{
    /**
     * @return \CI_DB_mysql_driver|\CI_DB_query_builder
     */
    public static function db()
    {
        require_once ('system/database/DB.php');
        $db =& DB('', true);

        return $db;
    }
}