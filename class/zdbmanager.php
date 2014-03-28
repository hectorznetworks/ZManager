<?php

class Zdbmanager {

    private static $dbh = null;

    private function __construct($config) {
        if (!self::$dbh) {
            try {
                self::$dbh = new PDO($config['DBMS'] . ':host=' . $config['host'] . ';dbname=' . $config['dbname'], $config['user'], $config['pass']);
                 self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                 self::$dbh->exec("SET CHARACTER SET utf8");
            } catch (PDOException $e) {
                die("PDO CONNECTION ERROR: " . $e->getMessage() . "<br/>");
            }
        }
       
    }

    public static function conection($config) {
        if (!self::$dbh) {
        //new connection object.
        new Zdbmanager($config);
        }
        return self::$dbh;
    }


}
