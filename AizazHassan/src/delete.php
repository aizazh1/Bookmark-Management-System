<?php
  require "db.php" ;

  $id = $_GET["id"] ?? "";

  try {
      $stmt = $db->prepare("delete from bookmark where id = :id") ;
      $stmt->execute(["id" => $id]) ;
      if ( $stmt->rowCount() > 0) {
        echo json_encode(["status" => "ok", "message" => "$id bookmark deleted."]) ;
      } else {
        echo json_encode(["status" => "error", "message" => "id is missing or invalid"]) ;
      }
  } catch(PDOException $ex) {
    echo json_encode(["status" => "error", "message" => "Query syntax error"]) ;
  }

  
  // Redirection
  // header("Location: index.php?page=main") ; // reloading main page.