<?php

/*
 * @Author: Héctor Fernández
 * @Twitter: Nekira_Daft
 * @Company: Z Networks Group
 * @Email: hfernandez@znetworksgroup.com
 * @Email: hector.nek@gmail.com
 * DBmanager for PDO Version 1 Alpha
 */

class Dbmanager {

    private $DBMS = 'mysql';
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $flag = false;
    protected $dbh;

    function __construct() {
        
    }

    public function __call($name, $arguments) {
        switch ($name) {
            case 'conect':
                $this->DBMS = $arguments[0]['DBMS'];
                $this->host = $arguments[0]['host'];
                $this->user = $arguments[0]['user'];
                $this->pass = $arguments[0]['pass'];
                $this->dbname = $arguments[0]['dbname'];
                try {
                    $this->flag = true;
                    $this->dbh = new PDO($this->DBMS . ':host=' . $this->host . ';dbname=' . $this->dbname, $this->user, $this->pass);
                } catch (PDOException $e) {
                    print "Error!: " . $e->getMessage() . "<br/>";
                    die();
                }
                break;
            case 'insert':
                // Notes: this method need 2 arguments "table name" and a array with the column's names as keys and respective values 
                if ($this->flag) {
                    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->dbh->exec("SET CHARACTER SET utf8");
                    foreach ($arguments[1] as $field => $v) {
                        $ins[] = ':' . $field;
                    }
                    $ins = implode(',', $ins);
                    $fields = implode(',', array_keys($arguments[1]));
                    $sql = "INSERT INTO " . $arguments[0] . " ($fields) VALUES ($ins)";
                    $sth = $this->dbh->prepare($sql);
                    foreach ($arguments[1] as $f => $v) {
                        $sth->bindValue(':' . $f, $v);
                    }
                    $sth->execute();
                } else {
                    die('no conectado aún');
                }
                break;
            case 'update':
                //Notes: this method need 3 arguments: 1"Table name", 2 array with a key with identifier column's name and respective value, 3 and an array with column's names as keys with respective values.
                if ($this->flag) {
                    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->dbh->exec("SET CHARACTER SET utf8");
                    try {

                        foreach ($arguments[1] as $field => $v) {
                            $ins_id = $field . ' = :' . $field;
                        }

                        foreach ($arguments[2] as $field => $v) {
                            $ins[] = $field . ' = :' . $field;
                        }
                        $ins = implode(', ', $ins);
                        $sql = "UPDATE $arguments[0] SET $ins WHERE $ins_id";

                        $sth = $this->dbh->prepare($sql);

                        foreach ($arguments[1] as $f => $v) {
                            $sth->bindValue(':' . $f, $v);
                        }
                        foreach ($arguments[2] as $f => $v) {
                            $sth->bindValue(':' . $f, $v);
                        }
                        $sth->execute();
                    } catch (Exception $exc) {
                        echo $exc->getTraceAsString();
                    }
                } else {
                    echo 'Database error';
                }
                break;
            case 'delete':
                // Notes: This method need 2 arguments, 1 table name 2 identifier array
                if ($this->flag) {
                    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->dbh->exec("SET CHARACTER SET utf8");
                    try {
                        foreach ($arguments[1] as $field => $v) {
                            $ins_id = $field . ' = :' . $field;
                        }
                        $sql = "DELETE FROM $arguments[0] WHERE $ins_id";
                        $sth = $this->dbh->prepare($sql);
                        foreach ($arguments[1] as $f => $v) {
                            $sth->bindParam(':' . $f, $v);
                        }
                        $sth->execute();
                    } catch (Exception $exc) {
                        echo $exc->getTraceAsString();
                    }
                } else {
                    echo 'Database Lost Conection';
                }
                break;
            case 'read':
                //Notes: This method need 3 arguments: Type of select string, table, optional array where
                if ($this->flag) {
                    try {

                        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $this->dbh->exec("SET CHARACTER SET utf8");
                        if (isset($arguments[2]) && !empty($arguments[2])) {

                            foreach ($arguments[2] as $field => $v) {
                                $ins_id = $field . ' = :' . $field;
                            }
                            $sql = "SELECT $arguments[0] FROM $arguments[1] WHERE $ins_id";
                            $sth = $this->dbh->prepare($sql);
                            foreach ($arguments[2] as $f => $v) {
                                $sth->bindParam(':' . $f, $v);
                            }
                        } else {
                            $sql = "SELECT $arguments[0] FROM $arguments[1] ";
                            $sth = $this->dbh->prepare($sql);
                        }
                        $sth->execute();
                        $result = $sth->fetchAll();
                        if (count($result)) {
                            return $result;
                        } else {
                            return false;
                        }
                    } catch (Exception $exc) {
                        echo $exc->getTraceAsString();
                    }
                } else {
                     echo 'Database Lost Conection';
                }
                break;

            case 'test':
                echo '<pre>';
                var_dump($arguments);
                die('</pre>');
                break;

            default :
                echo 'No es una function definida';
                break;
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}
