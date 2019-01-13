<?php
/**
 * Created by PhpStorm.
 * User: mk990
 * Date: 1/12/2019
 * Time: 9:30 PM
 */

namespace MkOrm\Configs;

use PDO;

class Connection
{
    private $db_host = 'localhost';
    private $db_port = '3306';
    private $db_name = 'mytestdb';
    private $db_user = 'root';
    private $db_pass = '';

    public function connect($options = null)
    {
        $dbConnection = new PDO('mysql:host=' . $this->db_host . ';port=' . $this->db_port . ';dbname=' . $this->db_name,
            $this->db_user,
            $this->db_pass,
            $options);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbConnection;
    }
}
