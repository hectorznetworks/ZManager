<?php require_once 'class/zdbmanager.php';
$config = array(
    'DBMS' => 'mysql',
    'host' => 'localhost',
    'user' => 'znetworks',
    'pass' => 'asdf1234',
    'dbname' => 'znetworks_nm'
);
$test = Zdbmanager::conection($config);

var_dump($test);
