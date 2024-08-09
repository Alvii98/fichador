<?php
require_once __DIR__ . '/../settings/definiciones.php';

class SingletonConexion
{
    private static $instance = null;
    private $conn = null;
    private $conn2 = null;

    public function __construct()
    {
        $this->conn = new PDO('mysql:host='._SERVER_.';dbname='._BDD_, _BDD_USER_, _BDD_PASS_);
        $this->conn2 = new PDO('oci:dbname='._SERVER_ORA_, _USER_ORA_, _PASS_ORA_);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new SingletonConexion;
        }
        return self::$instance;
    }
    /** BASE PERSONAL1 **/
    public function getConnection()
    {
        return $this->conn;
    }
    /** BASE ORACLE FICHADOR **/
    public function getConnection2()
    {
        return $this->conn2;
    }
}
?>