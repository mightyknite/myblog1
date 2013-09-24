<?php

defined('MYBLOG') or die("Restricted access");

class db
{
    public $db;
    protected static $_instance;
    
    private function __construct () {
        $dbname = 'mysql:host=localhost;dbname=' . 'myblog';
        $user = 'root';
        $pass = 'as';
        $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
        try {
            $this->db = new PDO($dbname, $user, $pass, $options);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
    
    private function __clone () {
        
    }
    
    private function __wakeup () {
        
    }
    
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;   
        }
 
        return self::$_instance;
    }

    public function fetchAll($query, $bind, $rowset = true) {
        $stmt = $this->db->prepare($query);
        if (is_array($bind))
          foreach ($bind as $name=>$value)
            $stmt->bindValue($name, $value);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            print_r($e);
            return false;
        }
        if ($stmt->rowCount() > 0) {
            if ($rowset) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $rows;
            } else {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row;
            }
        } else {
            $stmt->closeCursor();
            $stmt = null;
            return array();
        }
    }
    
    public function fetchOne($query, $bind) {
        return $this->fetchAll($query, $bind, false);
    }

}
