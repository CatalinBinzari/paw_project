<?php
session_start();
define("HOST","localhost");
define("USER","root");
define("PASSWORD","");
define("DBNAME","paw");
define("CHARSET","utf8");
define("SALT","projectpaw");

class TableRows extends RecursiveIteratorIterator {
  function __construct($it) {
    parent::__construct($it, self::LEAVES_ONLY);
  }
  function current() {
    return "<style='width:150px;border:1px solid black;'>" . parent::current();
  }

}



$dsn = "mysql:host=".HOST.";dbname=".DBNAME.";charset=".CHARSET;
try{
  $dbConn = new PDO($dsn, USER, PASSWORD);
} catch(PDOException $e){
  die('Cannot connect: ' . $e->getMessage());
}