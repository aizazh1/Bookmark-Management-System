<?php
require "db.php" ;
//var_dump($_POST);
if ( $_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST) ; //$title,$note,$owner, $cat
    try{
      if(!isset($cat)){
        addMessage("You need to select a category!");
      }else if(empty($title) || empty($url) || empty($note)){
        addMessage("Please fill all the fields!");
      }
      else{
        $sql = "insert into bookmark (title, url, note, owner, category) values (?,?,?,?,?)" ;
        $stmt = $db->prepare($sql) ;
        $stmt->execute([$title, $url, $note, $owner, $cat ?? ""]) ;
        addMessage("Success") ;
      }
    }catch(PDOException $ex) {
       addMessage("Insert Failed!") ;
    }
}

header("Location: main") ;

