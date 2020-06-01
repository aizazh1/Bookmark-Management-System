<?php
require "db.php";
$id=$_GET["id"];
try{
    $stmt=$db->prepare("delete from category where id=?");
    $stmt->execute([$id]);
    if ( $stmt->rowCount() > 0) {
        addMessage("Success");
      } else {
        addMessage("Delete Failed");
      }
}catch(PDOException $ex){
    addMessage("Delete Failed");
}

header('Location: main');