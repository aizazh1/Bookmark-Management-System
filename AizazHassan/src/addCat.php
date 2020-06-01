<?php
require "db.php";

if ( $_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST) ; //$name, $owner
    $sql = "insert into category (name,user) values (?,?)" ;
    try{
      $stmt = $db->prepare($sql) ;
      $stmt->execute([$name, $owner ?? ""]) ;
      addMessage("Success") ;
    }catch(PDOException $ex) {
       addMessage("Insert Failed!") ;
    }

    header("Location: main") ;
}