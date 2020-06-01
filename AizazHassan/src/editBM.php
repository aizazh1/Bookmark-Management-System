<?php
require "db.php";
if ( $_SERVER["REQUEST_METHOD"] == "POST") {
    // var_dump($_POST);
    extract($_POST) ; //$id,$title,$url,$note,$category
    $sql="update bookmark set title=?,url=?,note=?,category=? where id=?";
    // echo "$sql";
    try{
        if(!isset($cat)){
            addMessage("You need to select a category!");
        }else{
            $stmt=$db->prepare($sql);
            $stmt->execute([$title,$url,$note,$cat,$id]);
        }
      addMessage("Successfully Updated") ;
    }catch(PDOException $ex) {
       addMessage("Update Failed!") ;
    }
}

header("Location: main") ;