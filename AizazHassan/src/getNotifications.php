<?php

$id=$_GET['id'];
require "db.php";

try{
    $shared=$db->query("select * from share where userId=$id")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($shared);
}catch(PDOException $ex){
    echo json_encode(["error" => "select query error"]);
}